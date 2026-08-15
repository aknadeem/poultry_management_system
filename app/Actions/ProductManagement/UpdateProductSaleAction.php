<?php

namespace App\Actions\ProductManagement;

use App\Models\ProductSale;
use App\Models\ProductSaleDetail;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use App\Services\InventoryService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateProductSaleAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private InventoryService $inventoryService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ProductSale $sale, array $data, ?object $imageFile, int $userId): ProductSale
    {
        return DB::transaction(function () use ($sale, $data, $imageFile, $userId) {
            $sale->loadMissing('detail');

            $this->inventoryService->reverseProductSaleLines($sale->detail, $userId);
            $this->balanceService->deleteProductSaleBalances($sale->id, (int) $sale->party_id);

            $imageName = $this->uploadService->replace(
                $imageFile,
                'products/sales',
                $sale->invoice_picture
            );

            $sale->detail()->delete();

            $sale->update(array_merge(
                Arr::except($data, ['items', 'invoice_picture']),
                [
                    'invoice_picture' => $imageName,
                    'updatedby' => $userId,
                ]
            ));

            foreach ($data['items'] ?? [] as $item) {
                $totalQty = (int) ($item['product_total_qty'] ?? $item['product_qty'] ?? 0);

                ProductSaleDetail::create([
                    'product_sale_id'             => $sale->id,
                    'product_id'                  => $item['product_id'],
                    'product_code'                => $item['product_code'],
                    'product_name'                => $item['product_name'],
                    'product_sale_price'          => $item['product_sale_price'],
                    'product_qty'                 => $item['product_qty'],
                    'product_bonus_qty'           => $item['product_bonus_qty'] ?? 0,
                    'product_total_qty'           => $totalQty,
                    'product_discount'            => $item['product_discount'] ?? 0,
                    'product_discount_percentage' => $item['product_discount_percentage'] ?? 0,
                    'product_total_price'         => $item['product_total_price'],
                    'addedby'                     => $userId,
                ]);

                $this->inventoryService->decreaseProductStock(
                    (int) $item['product_id'],
                    $totalQty,
                    $userId
                );
            }

            $this->balanceService->recordProductSaleBalances(
                $sale->id,
                (int) $data['party_id'],
                (float) $data['final_amount'],
                (string) $data['sale_date'],
                $userId
            );

            return $sale->fresh('detail');
        });
    }
}
