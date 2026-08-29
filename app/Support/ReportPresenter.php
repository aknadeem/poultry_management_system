<?php

namespace App\Support;

use App\Helpers\Constant;
use App\Models\ChickPurchase;
use App\Models\ChickenSale;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductSale;

class ReportPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function chickSale(ChickenSale $sale, string $fromDate, string $toDate): array
    {
        $saleDate = $sale->sale_date;
        $saleDateValue = $saleDate instanceof \DateTimeInterface
            ? $saleDate->format('Y-m-d')
            : (string) $saleDate;

        $customer = $sale->customer;

        return [
            'id' => $sale->id,
            'date_from' => $fromDate,
            'date_to' => $toDate,
            'customer' => $customer
                ? $customer->name.' <br> <b> '.$customer->cnic_no.' </b>'
                : null,
            'party_id' => $sale->party_id,
            'manual_number' => $sale->manual_number,
            'sale_date' => $saleDateValue,
            'per_kg_price' => $sale->per_kg_price,
            'total_weight' => $sale->total_weight,
            'discount_amount' => $sale->discount_amount,
            'total_price' => $sale->total_price,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function chickPurchase(ChickPurchase $purchase, string $fromDate, string $toDate): array
    {
        $purchaseDate = $purchase->purchase_date;
        $purchaseDateValue = $purchaseDate instanceof \DateTimeInterface
            ? $purchaseDate->format('Y-m-d')
            : (string) $purchaseDate;

        return [
            'id' => $purchase->id,
            'date_from' => $fromDate,
            'date_to' => $toDate,
            'company_name' => $purchase->company?->company_name,
            'company_id' => $purchase->company_id,
            'purchase_date' => $purchaseDateValue,
            'price' => $purchase->price,
            'quantity' => $purchase->quantity,
            'discount_amount' => $purchase->discount_amount,
            'total_price' => $purchase->total_price,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function product(Product $product, string $fromDate, string $toDate): array
    {
        return [
            'id' => $product->id,
            'date_from' => $fromDate,
            'date_to' => $toDate,
            'company_name' => $product->company?->company_name,
            'party_company_id' => $product->party_company_id,
            'product_code' => $product->product_code,
            'product_group' => Constant::PRODUCT_GROUP_VAL[$product->product_group] ?? $product->product_group,
            'product_name' => $product->product_name,
            'quantity' => $product->quantity,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function productPurchase(ProductPurchase $purchase, string $fromDate, string $toDate): array
    {
        $purchaseDate = $purchase->purchase_date;
        $purchaseDateValue = $purchaseDate instanceof \DateTimeInterface
            ? $purchaseDate->format('Y-m-d')
            : (string) $purchaseDate;

        return [
            'id' => $purchase->id,
            'date_from' => $fromDate,
            'date_to' => $toDate,
            'company_name' => $purchase->company?->company_name,
            'party_company_id' => $purchase->party_company_id,
            'purchase_code' => $purchase->purchase_code,
            'purchase_date' => $purchaseDateValue,
            'total_amount' => $purchase->total_amount,
            'discount_amount' => $purchase->discount_amount,
            'final_amount' => $purchase->final_amount,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function productSale(ProductSale $sale, string $fromDate, string $toDate): array
    {
        $saleDate = $sale->sale_date;
        $saleDateValue = $saleDate instanceof \DateTimeInterface
            ? $saleDate->format('Y-m-d')
            : (string) $saleDate;

        return [
            'id' => $sale->id,
            'date_from' => $fromDate,
            'date_to' => $toDate,
            'company_name' => $sale->company?->company_name,
            'party_company_id' => $sale->party_company_id,
            'sale_code' => $sale->sale_code,
            'party_id' => $sale->party_id,
            'party_name' => $sale->party?->name,
            'sale_date' => $saleDateValue,
            'total_amount' => $sale->total_amount,
            'discount_amount' => $sale->discount_amount,
            'final_amount' => $sale->final_amount,
        ];
    }
}
