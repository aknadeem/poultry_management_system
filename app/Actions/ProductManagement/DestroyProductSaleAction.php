<?php

namespace App\Actions\ProductManagement;

use App\Models\FinancialTransaction;
use App\Models\PartyBalance;
use App\Models\ProductSale;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use App\Services\FinancialTransactionService;
use App\Services\InventoryService;
use App\Services\PaymentAllocationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DestroyProductSaleAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private InventoryService $inventoryService,
        private FileUploadService $uploadService,
        private PaymentAllocationService $allocationService,
        private FinancialTransactionService $transactionService,
    ) {
    }

    public function execute(ProductSale $sale, int $userId): void
    {
        if (! config('financial.rollouts.product_sale')) {
            throw ValidationException::withMessages([
                'sale' => 'Product sale reversal is disabled until the financial rollout flag is enabled.',
            ]);
        }

        DB::transaction(function () use ($sale, $userId) {
            $sale->loadMissing('detail');

            $partyBalance = PartyBalance::query()
                ->where('reference_type', 'product_sale')
                ->where('reference_id', $sale->id)
                ->lockForUpdate()
                ->first();

            if (! $partyBalance) {
                $partyBalance = PartyBalance::query()
                    ->where('party_id', $sale->party_id)
                    ->where('narration', 'like', '%(ProductSale #'.$sale->id.')%')
                    ->lockForUpdate()
                    ->first();
            }

            if ($partyBalance && $this->allocationService->hasActiveAllocations($partyBalance)) {
                throw ValidationException::withMessages([
                    'sale' => 'This sale has allocated payments. Reverse those payments before reversing the sale.',
                ]);
            }

            $this->inventoryService->reverseProductSaleLines($sale->detail, $userId);

            $posted = FinancialTransaction::query()
                ->where('reference_type', $sale->getMorphClass())
                ->where('reference_id', $sale->id)
                ->where('transaction_type', 'product_sale')
                ->where('status', FinancialTransaction::STATUS_POSTED)
                ->first();

            if ($posted) {
                $this->transactionService->reverse($posted, $userId, null, 'Sale reversal #'.$sale->id);
            }

            if ($partyBalance) {
                $partyBalance->update([
                    'financial_status' => 'reversed',
                    'updatedby' => $userId,
                ]);
            }

            $this->balanceService->deleteProductSaleBalances(
                $sale->id,
                (int) $sale->party_id,
                (float) $sale->final_amount
            );
            $this->uploadService->delete('products/sales', $sale->invoice_picture);

            $sale->detail()->delete();
            $sale->delete();
        });
    }
}
