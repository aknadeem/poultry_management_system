<?php

namespace App\Actions\ProductManagement;

use App\Models\ProductPurchase;
use App\Models\ProductPurchaseDetail;
use App\Models\ProductPurchaseRebate;
use App\Models\ProductSale;
use App\Models\ProductSaleDetail;
use App\Models\ProductSaleRebate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordProductSaleRebateAction
{
    public function execute(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId) {
            $fromPage = $data['from_page'] ?? null;
            $productDetailId = $data['product_detail_id'] ?? null;
            $rebateQty = (int) ($data['rebate_qty'] ?? 0);
            $rebateReason = $data['rebate_reason'] ?? null;
            $now = Carbon::now();

            if ($fromPage === 'ProductSaleDetail') {
                $item = ProductSaleDetail::find($productDetailId);
                if (! $item) {
                    throw ValidationException::withMessages([
                        'product_detail_id' => 'Sale detail not found.',
                    ]);
                }

                $itemPrice = $item->product_sale_price;
                $salePurchase = ProductSale::find($item->product_sale_id);
                $rebateModel = new ProductSaleRebate();
            } else {
                $item = ProductPurchaseDetail::find($productDetailId);
                if (! $item) {
                    throw ValidationException::withMessages([
                        'product_detail_id' => 'Purchase detail not found.',
                    ]);
                }

                $itemPrice = $item->product_purchase_price;
                $salePurchase = ProductPurchase::find($item->product_purchase_id);
                $rebateModel = new ProductPurchaseRebate();
            }

            $updateQty = $item->product_total_qty - $rebateQty;
            $rebateAmount = $itemPrice * $rebateQty;
            $updatePrice = $item->product_total_price - $rebateAmount;

            $item->product_total_qty = $updateQty;
            $item->product_total_price = $updatePrice;
            $item->is_rebate = 1;
            $item->rebate_qty = $rebateQty;
            $item->updated_at = $now;
            $item->updatedby = $userId;
            $item->save();

            if ($salePurchase) {
                $salePurchase->final_amount = $salePurchase->final_amount - $rebateAmount;
                $salePurchase->is_rebate = 1;
                $salePurchase->rebate_amount = $rebateAmount;
                $salePurchase->updated_at = $now;
                $salePurchase->updatedby = $userId;
                $salePurchase->save();
            }

            $rebateModel->create([
                'rebate_item_id' => $item->id,
                'product_id' => $item->product_id,
                'rebate_reason' => $rebateReason,
                'rebate_qty' => $rebateQty,
                'rebate_description' => $data['rebate_description'] ?? null,
                'addedby' => $userId,
            ]);
        });
    }
}
