<?php

namespace App\Actions\ProductManagement;

use App\Models\CompanyBalance;
use App\Models\PartyBalance;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseDetail;
use App\Models\ProductPurchaseRebate;
use App\Models\ProductSale;
use App\Models\ProductSaleDetail;
use App\Models\ProductSaleRebate;
use App\Services\FinancialTransactionService;
use App\Services\InventoryService;
use App\Services\PayableService;
use App\Services\PaymentAllocationService;
use App\Support\FinancialAmount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordProductSaleRebateAction
{
    public function __construct(
        private InventoryService $inventoryService,
        private PaymentAllocationService $allocationService,
        private PayableService $payableService,
        private FinancialTransactionService $transactionService,
    ) {
    }

    public function execute(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId) {
            $fromPage = $data['from_page'] ?? null;
            $productDetailId = $data['product_detail_id'] ?? null;
            $rebateQty = (int) ($data['rebate_qty'] ?? 0);
            $rebateReason = $data['rebate_reason'] ?? null;
            $now = Carbon::now();

            if ($fromPage === 'ProductSaleDetail') {
                $item = ProductSaleDetail::query()->lockForUpdate()->find($productDetailId);
                if (! $item) {
                    throw ValidationException::withMessages([
                        'product_detail_id' => 'Sale detail not found.',
                    ]);
                }

                $itemPrice = $item->product_sale_price;
                $salePurchase = ProductSale::query()->lockForUpdate()->find($item->product_sale_id);
                $rebateClass = ProductSaleRebate::class;
            } else {
                $item = ProductPurchaseDetail::query()->lockForUpdate()->find($productDetailId);
                if (! $item) {
                    throw ValidationException::withMessages([
                        'product_detail_id' => 'Purchase detail not found.',
                    ]);
                }

                $itemPrice = $item->product_purchase_price;
                $salePurchase = ProductPurchase::query()->lockForUpdate()->find($item->product_purchase_id);
                $rebateClass = ProductPurchaseRebate::class;
            }

            if (! $salePurchase) {
                throw ValidationException::withMessages([
                    'product_detail_id' => 'Parent record not found.',
                ]);
            }

            if ($rebateQty > (int) $item->product_total_qty) {
                throw ValidationException::withMessages([
                    'rebate_qty' => 'Rebate quantity cannot exceed remaining quantity.',
                ]);
            }

            $updateQty = $item->product_total_qty - $rebateQty;
            $rebateAmount = FinancialAmount::fromDecimalString((string) $itemPrice)
                ->cents() * $rebateQty;
            $rebateAmountValue = FinancialAmount::fromDecimalString(number_format($rebateAmount / 100, 2, '.', ''));
            $updatePrice = FinancialAmount::fromDecimalString((string) $item->product_total_price)
                ->subtract($rebateAmountValue);

            $item->product_total_qty = $updateQty;
            $item->product_total_price = $updatePrice->toDecimalString();
            $item->is_rebate = 1;
            $item->rebate_qty = $rebateQty;
            $item->updated_at = $now;
            $item->updatedby = $userId;
            $item->save();

            $newFinal = FinancialAmount::fromDecimalString((string) $salePurchase->final_amount)
                ->subtract($rebateAmountValue);

            $salePurchase->final_amount = $newFinal->toDecimalString();
            $salePurchase->is_rebate = 1;
            $salePurchase->rebate_amount = FinancialAmount::fromDecimalString((string) ($salePurchase->rebate_amount ?? 0))
                ->add($rebateAmountValue)
                ->toDecimalString();
            $salePurchase->updated_at = $now;
            $salePurchase->updatedby = $userId;
            $salePurchase->save();

            $rebateClass::create([
                'rebate_item_id' => $salePurchase->id,
                'product_id' => $item->product_id,
                'rebate_reason' => $rebateReason,
                'rebate_qty' => $rebateQty,
                'rebate_description' => $data['rebate_description'] ?? null,
                'addedby' => $userId,
            ]);

            if ($fromPage === 'ProductSaleDetail') {
                $this->inventoryService->increaseProductStock((int) $item->product_id, $rebateQty, $userId);
                $this->adjustPartyBalance($salePurchase, $newFinal, $userId);
            } else {
                $this->inventoryService->decreaseProductStock((int) $item->product_id, $rebateQty, $userId);
                $this->adjustCompanyBalance($salePurchase, $newFinal, $userId);
            }

            $this->transactionService->post($salePurchase, [
                'transaction_type' => $fromPage === 'ProductSaleDetail' ? 'product_sale_rebate' : 'product_purchase_rebate',
                'direction' => 'credit',
                'amount' => $rebateAmountValue->toDecimalString(),
                'transaction_date' => $now->toDateString(),
                'narration' => 'Rebate adjustment for #'.$salePurchase->id,
                'idempotency_key' => 'rebate-'.$fromPage.'-'.$item->id.'-'.$rebateQty.'-'.$now->timestamp,
            ], $userId);
        });
    }

    private function adjustPartyBalance(ProductSale $sale, FinancialAmount $newFinal, int $userId): void
    {
        $balance = PartyBalance::query()
            ->where('reference_type', 'product_sale')
            ->where('reference_id', $sale->id)
            ->lockForUpdate()
            ->first();

        if (! $balance) {
            $balance = PartyBalance::query()
                ->where('party_id', $sale->party_id)
                ->where('narration', 'like', '%(ProductSale #'.$sale->id.')%')
                ->lockForUpdate()
                ->first();
        }

        if (! $balance) {
            return;
        }

        $allocated = $this->allocationService->allocatedTotal($balance);
        if ($allocated->greaterThan($newFinal)) {
            throw ValidationException::withMessages([
                'rebate_qty' => 'Rebate would drop the sale total below already allocated payments.',
            ]);
        }

        $remaining = $newFinal->subtract($allocated);
        $balance->update([
            'total_amount' => $newFinal->toDecimalString(),
            'paid_amount' => $allocated->toDecimalString(),
            'remaining_amount' => $remaining->toDecimalString(),
            'updatedby' => $userId,
        ]);
    }

    private function adjustCompanyBalance(ProductPurchase $purchase, FinancialAmount $newFinal, int $userId): void
    {
        $balance = CompanyBalance::query()
            ->where(function ($query) use ($purchase): void {
                $query->where(function ($inner) use ($purchase): void {
                    $inner->where('reference_type', 'product_purchase')
                        ->where('reference_id', $purchase->id);
                })->orWhere(function ($inner) use ($purchase): void {
                    $inner->where('type', 'product_purchase')
                        ->where('model_id', $purchase->id);
                });
            })
            ->lockForUpdate()
            ->first();

        if (! $balance) {
            return;
        }

        $allocated = $this->allocationService->allocatedTotal($balance);
        if ($allocated->greaterThan($newFinal)) {
            throw ValidationException::withMessages([
                'rebate_qty' => 'Rebate would drop the purchase total below already allocated payments.',
            ]);
        }

        $remaining = $newFinal->subtract($allocated);
        $balance->update([
            'total_amount' => $newFinal->toDecimalString(),
            'paid_amount' => $allocated->toDecimalString(),
            'remaining_amount' => $remaining->toDecimalString(),
            'updatedby' => $userId,
        ]);

        $this->payableService->recordCompanyPayable(
            $balance->id,
            (float) $newFinal->toDecimalString(),
            'product_purchase',
            'company balance on product purchase',
            $userId,
            null,
            'product_purchase',
            (int) $purchase->id,
        );
    }
}
