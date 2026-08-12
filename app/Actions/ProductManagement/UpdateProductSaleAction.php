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
                Arr::except($data, [
                    'product_id', 'product_code', 'product_name', 'invoice_picture',
                    'product_sale_price', 'product_qty', 'product_bonus_qty', 'product_total_qty',
                    'product_discount', 'product_discount_percentage', 'product_total_price',
                ]),
                [
                    'invoice_picture' => $imageName,
                    'updatedby' => $userId,
                ]
            ));

            $number = count($data['product_name'] ?? []);
            for ($i = 0; $i < $number; $i++) {
                $totalQty = (int) ($data['product_total_qty'][$i] ?? $data['product_qty'][$i] ?? 0);

                ProductSaleDetail::create([
                    'product_sale_id' => $sale->id,
                    'product_id' => $data['product_id'][$i],
                    'product_code' => $data['product_code'][$i],
                    'product_name' => $data['product_name'][$i],
                    'product_sale_price' => $data['product_sale_price'][$i],
                    'product_qty' => $data['product_qty'][$i],
                    'product_bonus_qty' => $data['product_bonus_qty'][$i] ?? 0,
                    'product_total_qty' => $totalQty,
                    'product_discount' => $data['product_discount'][$i] ?? 0,
                    'product_discount_percentage' => $data['product_discount_percentage'][$i] ?? 0,
                    'product_total_price' => $data['product_total_price'][$i],
                    'addedby' => $userId,
                ]);

                $this->inventoryService->decreaseProductStock(
                    (int) $data['product_id'][$i],
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
