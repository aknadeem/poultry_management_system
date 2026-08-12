<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenPurchase;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class StoreChickenPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(array $data, ?object $imageFile, int $userId): ChickenPurchase
    {
        return DB::transaction(function () use ($data, $imageFile, $userId) {
            $imageName = $this->uploadService->store($imageFile, 'chicks');

            $purchase = ChickenPurchase::create([
                'purchase_date' => $data['purchase_date'],
                'chick_grade_id' => $data['chick_grade_id'],
                'company_id' => $data['company_id'],
                'weight' => $data['chick_weight'] ?? $data['weight'] ?? null,
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
                'personal_farm_id' => $data['personal_farm_id'] ?? null,
                'picture' => $imageName,
                'addedby' => $userId,
            ]);

            $this->balanceService->recordChickenPurchaseBalances(
                $purchase->id,
                (int) $data['company_id'],
                (float) $data['total_price'],
                $userId
            );

            return $purchase;
        });
    }
}
