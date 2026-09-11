<?php

namespace App\Actions\ProductManagement;

use App\Models\CompanyBalance;
use App\Models\FinancialTransaction;
use App\Models\ProductPurchase;
use App\Services\FinancialBalanceService;
use App\Services\FinancialTransactionService;
use App\Services\InventoryService;
use App\Services\PayableService;
use App\Services\PaymentAllocationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DestroyProductPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private InventoryService $inventoryService,
        private PayableService $payableService,
        private PaymentAllocationService $allocationService,
        private FinancialTransactionService $transactionService,
    ) {
    }

    public function execute(ProductPurchase $purchase, int $userId): void
    {
        if (! config('financial.rollouts.product_purchase')) {
            throw ValidationException::withMessages([
                'purchase' => 'Product purchase reversal is disabled until the financial rollout flag is enabled.',
            ]);
        }

        DB::transaction(function () use ($purchase, $userId) {
            $purchase->loadMissing('detail');

            $companyBalance = CompanyBalance::query()
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

            if ($companyBalance && $this->allocationService->hasActiveAllocations($companyBalance)) {
                throw ValidationException::withMessages([
                    'purchase' => 'This purchase has allocated payments. Reverse those payments before reversing the purchase.',
                ]);
            }

            $this->inventoryService->reverseProductPurchaseLines($purchase->detail, $userId);

            $posted = FinancialTransaction::query()
                ->where('reference_type', $purchase->getMorphClass())
                ->where('reference_id', $purchase->id)
                ->where('transaction_type', 'product_purchase')
                ->where('status', FinancialTransaction::STATUS_POSTED)
                ->first();

            if ($posted) {
                $this->transactionService->reverse($posted, $userId, null, 'Purchase reversal #'.$purchase->id);
            }

            if ($companyBalance) {
                $this->payableService->deleteByCompanyBalance($companyBalance->id, 'product_purchase');
                $companyBalance->update([
                    'financial_status' => 'reversed',
                    'updatedby' => $userId,
                ]);
            }

            $this->balanceService->deleteProductPurchaseBalances($purchase->id);

            $purchase->detail()->delete();
            $purchase->delete();
        });
    }
}
