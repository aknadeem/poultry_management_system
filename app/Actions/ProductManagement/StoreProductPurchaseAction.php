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
                    'items', 'invoice_picture',
                ])
            );

            foreach ($data['items'] ?? [] as $item) {
                $totalQty = (int) ($item['product_total_qty'] ?? $item['product_qty'] ?? 0);

                ProductPurchaseDetail::create([
                    'product_purchase_id'         => $purchase->id,
                    'product_id'                  => $item['product_id'],
                    'product_code'                => $item['product_code'],
                    'product_name'                => $item['product_name'],
                    'product_purchase_price'      => $item['product_purchase_price'],
                    'product_qty'                 => $item['product_qty'],
                    'product_bonus_qty'           => $item['product_bonus_qty'] ?? 0,
                    'product_total_qty'           => $totalQty,
                    'product_discount'            => $item['product_discount'] ?? 0,
                    'product_discount_percentage' => $item['product_discount_percentage'] ?? 0,
                    'product_total_price'         => $item['product_total_price'],
                    'addedby'                     => $userId,
                ]);

                $this->inventoryService->increaseProductStock(
                    (int) $item['product_id'],
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
