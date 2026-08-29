<?php

namespace App\Support;

use App\Models\ChickPurchase;
use App\Models\ChickenSale;
use App\Models\Expense;
use App\Models\Feed;
use App\Models\FeedPurchase;

class InventoryPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function chickSale(ChickenSale $sale): array
    {
        $sale->loadMissing(['customer.farm', 'broker']);

        return [
            'id' => $sale->id,
            'manual_number' => $sale->manual_number,
            'sale_date' => $sale->sale_date?->format('Y-m-d'),
            'sale_date_label' => $sale->sale_date?->format('d M, Y'),
            'broker_id' => $sale->broker_id,
            'broker_name' => $sale->broker?->name,
            'customer_id' => $sale->party_id ?: $sale->customer_id,
            'customer_name' => $sale->customer?->name,
            'customer_contact' => $sale->customer?->contact_no,
            'customer_farm_name' => $sale->customer?->farm?->farm_name,
            'first_weight' => $sale->first_weight,
            'second_weight' => $sale->second_weight,
            'net_weight' => $sale->net_weight,
            'total_weight' => $sale->total_weight,
            'per_kg_price' => $sale->per_kg_price,
            'discount_amount' => $sale->discount_amount,
            'discount_percentage' => $sale->discount_percentage,
            'total_price' => $sale->total_price,
            'vehicle_number' => $sale->vehicle_number,
            'driver_name' => $sale->driver_name,
            'driver_contact' => $sale->driver_contact,
            'picture' => $sale->picture,
            'picture_url' => $sale->picture
                ? asset('storage/chickens/'.$sale->picture)
                : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function chickPurchase(ChickPurchase $purchase): array
    {
        $purchase->loadMissing(['company.vendor', 'customer.farm', 'grade', 'farmHistory.farm']);

        $farm = $purchase->farmHistory?->farm ?? $purchase->customer?->farm;

        return [
            'id' => $purchase->id,
            'purchase_date' => $purchase->purchase_date?->format('Y-m-d'),
            'purchase_date_label' => $purchase->purchase_date?->format('d M, Y'),
            'chick_grade_id' => $purchase->chick_grade_id,
            'chick_grade_name' => $purchase->grade?->name,
            'company_id' => $purchase->company_id,
            'company_name' => $purchase->company?->company_name,
            'vendor_id' => $purchase->company?->vendor?->id,
            'vendor_name' => $purchase->company?->vendor?->name,
            'customer_id' => $purchase->customer_id,
            'customer_name' => $purchase->customer?->name,
            'customer_farm_id' => $farm?->id,
            'customer_farm_name' => $farm?->farm_name,
            'personal_farm_capacity' => $farm?->farm_capacity,
            'personal_farm_address' => $farm?->farm_address,
            'is_occupied' => (bool) ($farm?->is_occupied),
            'folk_quantity' => $farm?->folk_quantity,
            'chick_entry_age' => $purchase->chick_entry_age,
            'chick_weight' => $purchase->weight,
            'quantity' => $purchase->quantity,
            'price' => $purchase->price,
            'discount_amount' => $purchase->discount_amount,
            'discount_percentage' => $purchase->discount_percentage,
            'total_price' => $purchase->total_price,
            'bilty_number' => $purchase->bilty_number,
            'bilty_charges' => $purchase->bilty_charges,
            'vehicle_number' => $purchase->vehicle_number,
            'driver_name' => $purchase->driver_name,
            'driver_contact' => $purchase->driver_contact,
            'sale_order_number' => $purchase->sale_order_number,
            'delivery_order_number' => $purchase->delivery_order_number,
            'remarks' => $purchase->description,
            'picture' => $purchase->picture,
            'picture_url' => $purchase->picture
                ? asset('storage/chicks/'.$purchase->picture)
                : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function feed(Feed $feed, bool $includePurchases = false): array
    {
        $relations = ['category:id,name'];
        if ($includePurchases) {
            $relations[] = 'purchases.company:id,company_name';
        }

        $feed->loadMissing($relations);

        $payload = [
            'id' => $feed->id,
            'feed_code' => $feed->feed_code,
            'feed_name' => $feed->feed_name,
            'feed_category_id' => $feed->feed_category_id,
            'category_name' => $feed->category?->name,
            'total_quantity' => $feed->total_quantity,
            'remaining_quantity' => $feed->remaining_quantity,
        ];

        if ($includePurchases) {
            $payload['purchases'] = $feed->purchases
                ->map(fn (FeedPurchase $purchase): array => self::feedPurchase($purchase))
                ->all();
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    public static function feedPurchase(FeedPurchase $purchase): array
    {
        $purchase->loadMissing('company:id,company_name');

        return [
            'id' => $purchase->id,
            'purchase_date' => $purchase->purchase_date?->format('Y-m-d'),
            'purchase_date_label' => $purchase->purchase_date?->format('d M, Y'),
            'company_id' => $purchase->company_id,
            'company_name' => $purchase->company?->company_name,
            'quantity' => $purchase->quantity,
            'remaining_quantity' => $purchase->remaining_quantity,
            'price' => $purchase->price,
            'discount_amount' => $purchase->discount_amount,
            'total_price' => $purchase->total_price,
            'sale_order_number' => $purchase->sale_order_number,
            'delivery_order_number' => $purchase->delivery_order_number,
            'picture' => $purchase->picture,
            'picture_url' => $purchase->picture
                ? asset('storage/feeds/'.$purchase->picture)
                : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function expense(Expense $expense): array
    {
        $expense->loadMissing('category:id,name');

        return [
            'id' => $expense->id,
            'expense_code' => $expense->expense_code,
            'category_id' => $expense->category_id,
            'category_name' => $expense->category?->name,
            'expense_date' => $expense->expense_date?->format('Y-m-d'),
            'expense_date_label' => $expense->expense_date?->format('d M, Y'),
            'amount' => $expense->amount,
            'remarks' => $expense->remarks,
            'picture' => $expense->picture,
            'picture_url' => $expense->picture
                ? asset('storage/expenses/'.$expense->picture)
                : null,
        ];
    }
}
