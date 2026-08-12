<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenPurchase;
use App\Models\CompanyBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreChickenPurchaseAction
{
    public function execute(array $data, ?object $imageFile, int $userId): ChickenPurchase
    {
        return DB::transaction(function () use ($data, $imageFile, $userId) {
            $imageName = null;
            if ($imageFile) {
                $extension = $imageFile->extension();
                $imageName = time() . mt_rand(10, 99) . '.' . $extension;
            }

            $purchase = ChickenPurchase::create([
                'purchase_date' => $data['purchase_date'],
                'chick_grade_id' => $data['chick_grade_id'],
                'company_id' => $data['company_id'],
                'weight' => $data['chick_weight'],
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'discount_amount' => $data['discount_amount'] ?? null,
                'discount_percentage' => $data['discount_percentage'] ?? null,
                'total_price' => $data['total_price'],
                'bilty_number' => $data['bilty_number'] ?? null,
                'bilty_charges' => $data['bilty_charges'] ?? null,
                'sale_order_number' => $data['sale_order_number'] ?? null,
                'delivery_order_number' => $data['delivery_order_number'] ?? null,
                'vehicle_number' => $data['vehicle_number'] ?? null,
                'driver_name' => $data['driver_name'] ?? null,
                'driver_contact' => $data['driver_contact'] ?? null,
                'picture' => $imageName,
                'addedby' => $userId,
            ]);

            if ($imageFile && $imageName) {
                $imageFile->storeAs('chicks/', $imageName, 'public');
            }

            // Record Company Balance for this purchase
            CompanyBalance::create([
                'type' => 'chicken_purchase',
                'model_id' => $purchase->id,
                'company_id' => $data['company_id'],
                'total_amount' => $data['total_price'],
                'remaining_amount' => $data['total_price'],
                'dr' => $data['total_price'],
                'addedby' => $userId,
            ]);

            return $purchase;
        });
    }
}
