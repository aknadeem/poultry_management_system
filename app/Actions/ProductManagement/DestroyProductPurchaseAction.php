<?php

namespace App\Actions\ProductManagement;

use App\Models\CompanyBalance;
use App\Models\ProductPurchase;
use App\Services\FinancialBalanceService;
use App\Services\InventoryService;
use App\Services\PayableService;
use Illuminate\Support\Facades\DB;

class DestroyProductPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private InventoryService $inventoryService,
        private PayableService $payableService,
    ) {
    }

    public function execute(ProductPurchase $purchase, int $userId): void
    {
        DB::transaction(function () use ($purchase, $userId) {
            $purchase->loadMissing('detail');

            $this->inventoryService->reverseProductPurchaseLines($purchase->detail, $userId);

            $companyBalance = CompanyBalance::where('type', 'product_purchase')
                ->where('model_id', $purchase->id)
                ->first();

            if ($companyBalance) {
                $this->payableService->deleteByCompanyBalance($companyBalance->id, 'product_purchase');
            }

            $this->balanceService->deleteProductPurchaseBalances($purchase->id);

            $purchase->detail()->delete();
            $purchase->delete();
        });
    }
}
