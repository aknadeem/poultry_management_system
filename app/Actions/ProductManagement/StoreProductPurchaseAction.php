<?php

namespace App\Actions\ProductManagement;

use App\Models\ProductPurchase;
use App\Models\ProductPurchaseDetail;
use App\Services\FinancialBalanceService;
use App\Services\InventoryService;
use App\Services\PayableService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StoreProductPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private InventoryService $inventoryService,
        private PayableService $payableService,
    ) {
    }

    public function execute(array $data, int $userId): ProductPurchase
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['addedby'] = $userId;

            $purchase = ProductPurchase::create(
                Arr::except($data, [
                    'product_id', 'product_code', 'product_name', 'invoice_picture',
                    'product_sale_price', 'product_qty', 'product_bonus_qty', 'product_total_qty',
                    'product_discount', 'product_discount_percentage', 'product_total_price',
                ])
            );

            $number = count($data['product_name'] ?? []);
            for ($i = 0; $i < $number; $i++) {
                $totalQty = (int) ($data['product_total_qty'][$i] ?? $data['product_qty'][$i] ?? 0);

                ProductPurchaseDetail::create([
                    'product_purchase_id' => $purchase->id,
                    'product_id' => $data['product_id'][$i],
                    'product_code' => $data['product_code'][$i],
                    'product_name' => $data['product_name'][$i],
                    'product_purchase_price' => $data['product_sale_price'][$i],
                    'product_qty' => $data['product_qty'][$i],
                    'product_bonus_qty' => $data['product_bonus_qty'][$i] ?? 0,
                    'product_total_qty' => $totalQty,
                    'product_discount' => $data['product_discount'][$i] ?? 0,
                    'product_discount_percentage' => $data['product_discount_percentage'][$i] ?? 0,
                    'product_total_price' => $data['product_total_price'][$i],
                    'addedby' => $userId,
                ]);

                $this->inventoryService->increaseProductStock(
                    (int) $data['product_id'][$i],
                    $totalQty,
                    $userId
                );
            }

            $companyBalance = $this->balanceService->recordProductPurchaseBalances(
                $purchase->id,
                (int) $data['party_company_id'],
                (float) $data['final_amount'],
                $userId,
                'Product Purchase balance'
            );

            $this->payableService->recordCompanyPayable(
                $companyBalance->id,
                (float) $data['final_amount'],
                'product_purchase',
                'company balance on product purchase',
                $userId
            );

            return $purchase;
        });
    }
}
