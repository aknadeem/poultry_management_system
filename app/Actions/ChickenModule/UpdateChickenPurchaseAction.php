<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenPurchase;
use App\Models\CompanyBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateChickenPurchaseAction
{
    public function execute(ChickenPurchase $purchase, array $data, ?object $imageFile, int $userId): ChickenPurchase
    {
        return DB::transaction(function () use ($purchase, $data, $imageFile, $userId) {
            $imageName = $purchase->picture;

            if ($imageFile) {
                // Delete old image
                if ($imageName && Storage::disk('public')->exists('chicks/' . $imageName)) {
                    Storage::disk('public')->delete('chicks/' . $imageName);
                }
                $extension = $imageFile->extension();
                $imageName = time() . mt_rand(10, 99) . '.' . $extension;
                $imageFile->storeAs('chicks/', $imageName, 'public');
            }

            $purchase->update([
                'purchase_date' => $data['purchase_date'],
                'vehicle_number' => $data['vehicle_number'] ?? $purchase->vehicle_number,
                'driver_name' => $data['driver_name'] ?? $purchase->driver_name,
                'driver_contact' => $data['driver_contact'] ?? $purchase->driver_contact,
                'company_id' => $data['company_id'],
                'quantity' => $data['quantity'],
                'weight' => $data['weight'] ?? $purchase->weight,
                'price' => $data['price'],
                'discount_amount' => $data['discount_amount'],
                'discount_percentage' => $data['discount_percentage'],
                'total_price' => $data['total_price'],
                'picture' => $imageName,
                'updatedby' => $userId,
            ]);

            // Update matching CompanyBalance
            $companyBalance = CompanyBalance::where('type', 'chicken_purchase')
                ->where('model_id', $purchase->id)
                ->first();

            if ($companyBalance) {
                $paid = (float) $companyBalance->paid_amount;
                $companyBalance->update([
                    'company_id' => $data['company_id'],
                    'total_amount' => $data['total_price'],
                    'remaining_amount' => $data['total_price'] - $paid,
                    'dr' => $data['total_price'],
                    'updatedby' => $userId,
                ]);
            }

            return $purchase;
        });
    }
}
