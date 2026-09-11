<?php

namespace App\Actions\ProductManagement;

use App\Models\ProductSale;
use App\Models\ProductSaleDetail;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use App\Services\FinancialTransactionService;
use App\Services\InventoryService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StoreProductSaleAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private InventoryService $inventoryService,
        private FileUploadService $uploadService,
        private FinancialTransactionService $transactionService,
    ) {
    }

    public function execute(array $data, ?object $imageFile, int $userId): ProductSale
    {
        return DB::transaction(function () use ($data, $imageFile, $userId) {
            $data['addedby'] = $userId;
            $data['invoice_picture'] = $this->uploadService->store($imageFile, 'products/sales');

            $sale = ProductSale::create(
                Arr::except($data, [
                    'items', 'invoice_picture',
                ])
            );

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

            if (config('financial.rollouts.product_sale')) {
                $this->transactionService->post($sale, [
                    'transaction_type' => 'product_sale',
                    'direction' => 'debit',
                    'amount' => (string) $data['final_amount'],
                    'transaction_date' => (string) $data['sale_date'],
                    'narration' => 'Product sale #'.$sale->id,
                    'idempotency_key' => 'product-sale-'.$sale->id,
                ], $userId);
            }

            return $sale;
        });
    }
}
