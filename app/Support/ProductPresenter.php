<?php

namespace App\Support;

use App\Helpers\Constant;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseDetail;
use App\Models\ProductPurchaseRebate;
use App\Models\ProductSale;
use App\Models\ProductSaleDetail;
use App\Models\ProductSaleRebate;
use App\Models\ProductType;

class ProductPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function product(Product $product): array
    {
        $product->loadMissing(['company', 'category', 'productstore']);
        $type = ProductType::query()->find($product->product_type_id ?: $product->product_type);

        return [
            'id' => $product->id,
            'product_number' => $product->product_number,
            'product_code' => $product->product_code,
            'bar_code' => $product->bar_code,
            'product_group' => $product->product_group,
            'product_group_label' => Constant::PRODUCT_GROUP_VAL[$product->product_group] ?? null,
            'product_group_color' => Constant::PRODUCT_GROUP_COLOR[$product->product_group] ?? 'secondary',
            'product_name' => $product->product_name,
            'company_id' => $product->party_company_id,
            'company_name' => $product->company?->company_name,
            'product_category_id' => $product->product_category_id,
            'category_name' => $product->category?->name,
            'batch_number' => $product->batch_number,
            'serial_number' => $product->serial_number,
            'product_type' => $product->product_type_id ?: $product->product_type,
            'product_type_name' => $type?->name,
            'vaccination_group' => $product->vaccination_group_id,
            'pack_size_unit' => $product->pack_size,
            'pack_size_unit_type' => $product->pack_size_unit_type,
            'store_id' => $product->product_store_id,
            'store_name' => $product->productstore?->store_name,
            'rack_number' => $product->rack_number,
            'min_level' => $product->min_inventory_level,
            'max_level' => $product->max_inventory_level,
            'quantity' => $product->quantity,
            'mrp_price' => $product->mrp_price,
            'whole_sale_price' => $product->whole_sale_price,
            'full_less_price' => $product->full_less_price,
            'store_price' => $product->store_price,
            'retail_price' => $product->retail_price,
            'trade_price' => $product->trade_price,
            'purchase_price' => $product->purchase_price,
            'sale_price' => $product->sale_price,
            'discount_amount' => $product->discount_amount,
            'discount_percentage' => $product->discount_percentage,
            'tax_amount' => $product->tax_amount,
            'tax_percentage' => $product->tax_percentage,
            'warranty_period' => $product->warranty_period,
            'is_taxable' => (bool) $product->is_taxable,
            'is_sale_on_tp' => (bool) $product->is_sale_on_tp,
            'is_claimable' => (bool) $product->is_claimable,
            'is_fridged' => (bool) $product->is_fridged,
            'is_narcotic' => (bool) $product->is_narcotic,
            'is_unwaranted' => (bool) $product->is_unwarranted,
            'description' => $product->description,
            'product_picture' => $product->product_picture,
            'product_picture_url' => $product->product_picture
                ? asset('storage/products/'.$product->product_picture)
                : null,
            'purchase_date' => $product->purchase_date?->format('Y-m-d'),
            'is_active' => (bool) $product->is_active,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function purchaseList(ProductPurchase $purchase): array
    {
        return self::purchasePayload($purchase, includeItems: false);
    }

    /**
     * @return array<string, mixed>
     */
    public static function purchase(ProductPurchase $purchase): array
    {
        return self::purchasePayload($purchase, includeItems: true);
    }

    /**
     * @return array<string, mixed>
     */
    private static function purchasePayload(ProductPurchase $purchase, bool $includeItems): array
    {
        $relations = ['company', 'productcategory'];
        if ($includeItems) {
            $relations[] = 'detail';
        }

        $purchase->loadMissing($relations);
        $status = $purchase->status_value;

        $payload = [
            'id' => $purchase->id,
            'purchase_code' => $purchase->purchase_code,
            'party_company_id' => $purchase->party_company_id,
            'company_name' => $purchase->company?->company_name,
            'product_category_id' => $purchase->product_category_id,
            'category_name' => $purchase->productcategory?->name,
            'purchase_date' => $purchase->purchase_date?->format('Y-m-d'),
            'purchase_date_label' => $purchase->purchase_date?->format('d M, Y'),
            'manual_number' => $purchase->manual_number,
            'total_amount' => $purchase->total_amount,
            'discount_amount' => $purchase->discount_amount,
            'discount_percentage' => $purchase->discount_percentage,
            'other_charges' => $purchase->other_charges,
            'final_amount' => $purchase->final_amount,
            'is_rebate' => (bool) $purchase->is_rebate,
            'rebate_amount' => $purchase->rebate_amount,
            'payment_status' => $purchase->payment_status,
            'payment_status_label' => is_array($status) ? ($status['value'] ?? null) : $status,
            'payment_status_color' => is_array($status) ? ($status['color_name'] ?? 'secondary') : 'secondary',
            'description' => $purchase->description,
            'is_active' => (bool) $purchase->is_active,
            'invoice_url' => route('inertia.product-purchases.invoice', $purchase->id, false),
        ];

        if ($includeItems) {
            $payload['items'] = $purchase->detail
                ->map(fn (ProductPurchaseDetail $item): array => self::purchaseItem($item))
                ->all();
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public static function purchaseItem(ProductPurchaseDetail $item): array
    {
        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'product_code' => $item->product_code,
            'product_name' => $item->product_name,
            'product_purchase_price' => $item->product_purchase_price,
            'product_qty' => $item->product_qty,
            'product_bonus_qty' => $item->product_bonus_qty,
            'rebate_qty' => $item->rebate_qty,
            'product_total_qty' => $item->product_total_qty,
            'product_discount' => $item->product_discount,
            'product_discount_percentage' => $item->product_discount_percentage,
            'product_total_price' => $item->product_total_price,
            'is_rebate' => (bool) $item->is_rebate,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rebate(ProductPurchaseRebate $rebate): array
    {
        return [
            'id' => $rebate->id,
            'rebate_item_id' => $rebate->rebate_item_id,
            'product_id' => $rebate->product_id,
            'rebate_qty' => $rebate->rebate_qty,
            'rebate_reason' => $rebate->rebate_reason,
            'rebate_description' => $rebate->rebate_description,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function saleList(ProductSale $sale): array
    {
        return self::salePayload($sale, includeItems: false);
    }

    /**
     * @return array<string, mixed>
     */
    public static function sale(ProductSale $sale): array
    {
        return self::salePayload($sale, includeItems: true);
    }

    /**
     * @return array<string, mixed>
     */
    private static function salePayload(ProductSale $sale, bool $includeItems): array
    {
        $relations = ['party', 'company', 'productcategory'];
        if ($includeItems) {
            $relations[] = 'detail';
        }

        $sale->loadMissing($relations);
        $payment = self::salePaymentStatus($sale->payment_status);

        $payload = [
            'id' => $sale->id,
            'sale_code' => $sale->sale_code,
            'division_id' => $sale->division_id,
            'party_id' => $sale->party_id,
            'party_name' => $sale->party?->name,
            'party_cnic' => $sale->party?->cnic_no,
            'party_company_id' => $sale->party_company_id,
            'company_name' => $sale->company?->company_name,
            'product_category_id' => $sale->product_category_id,
            'category_name' => $sale->productcategory?->name,
            'sale_date' => $sale->sale_date?->format('Y-m-d'),
            'sale_date_label' => $sale->sale_date?->format('d M, Y'),
            'due_date_option' => $sale->due_date_option,
            'manual_number' => $sale->manual_number,
            'sale_type' => $sale->sale_type,
            'total_amount' => $sale->total_amount,
            'discount_amount' => $sale->discount_amount,
            'discount_percentage' => $sale->discount_percentage,
            'other_charges' => $sale->other_charges,
            'final_amount' => $sale->final_amount,
            'is_rebate' => (bool) $sale->is_rebate,
            'rebate_amount' => $sale->rebate_amount,
            'payment_status' => $sale->payment_status,
            'payment_status_label' => $payment['label'],
            'payment_status_color' => $payment['color'],
            'description' => $sale->description,
            'is_active' => (bool) $sale->is_active,
            'invoice_url' => route('inertia.product-sales.invoice', $sale->id, false),
        ];

        if ($includeItems) {
            $payload['items'] = $sale->detail
                ->map(fn (ProductSaleDetail $item): array => self::saleItem($item))
                ->all();
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public static function saleItem(ProductSaleDetail $item): array
    {
        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'product_code' => $item->product_code,
            'product_name' => $item->product_name,
            'product_sale_price' => $item->product_sale_price,
            'product_qty' => $item->product_qty,
            'product_bonus_qty' => $item->product_bonus_qty,
            'rebate_qty' => $item->rebate_qty,
            'product_total_qty' => $item->product_total_qty,
            'product_discount' => $item->product_discount,
            'product_discount_percentage' => $item->product_discount_percentage,
            'product_total_price' => $item->product_total_price,
            'is_rebate' => (bool) $item->is_rebate,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function saleRebate(ProductSaleRebate $rebate): array
    {
        return [
            'id' => $rebate->id,
            'rebate_item_id' => $rebate->rebate_item_id,
            'product_id' => $rebate->product_id,
            'rebate_qty' => $rebate->rebate_qty,
            'rebate_reason' => $rebate->rebate_reason,
            'rebate_description' => $rebate->rebate_description,
        ];
    }

    /**
     * @return array{label: string, color: string}
     */
    private static function salePaymentStatus(mixed $status): array
    {
        return match ((int) $status) {
            ProductSale::PAYMENT_UNPAID => ['label' => 'Un paid', 'color' => 'danger'],
            ProductSale::PAYMENT_PENDING => ['label' => 'Pending', 'color' => 'warning'],
            ProductSale::PAYMENT_PAID => ['label' => 'Paid', 'color' => 'success'],
            default => ['label' => 'Un paid', 'color' => 'secondary'],
        };
    }
}
