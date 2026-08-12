<?php

namespace App\Actions\ProductManagement;

use App\Models\ProductSale;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class DestroyProductSaleAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private InventoryService $inventoryService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ProductSale $sale, int $userId): void
    {
        DB::transaction(function () use ($sale, $userId) {
            $sale->loadMissing('detail');

            $this->inventoryService->reverseProductSaleLines($sale->detail, $userId);
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
