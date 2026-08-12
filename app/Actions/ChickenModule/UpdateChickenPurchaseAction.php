<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenPurchase;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class UpdateChickenPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ChickenPurchase $purchase, array $data, ?object $imageFile, int $userId): ChickenPurchase
    {
        return DB::transaction(function () use ($purchase, $data, $imageFile, $userId) {
            $imageName = $this->uploadService->replace($imageFile, 'chicks', $purchase->picture);

            $purchase->update([
                'purchase_date' => $data['purchase_date'],
                'chick_grade_id' => $data['chick_grade_id'] ?? $purchase->chick_grade_id,
                'vehicle_number' => $data['vehicle_number'] ?? $purchase->vehicle_number,
                'driver_name' => $data['driver_name'] ?? $purchase->driver_name,
                'driver_contact' => $data['driver_contact'] ?? $purchase->driver_contact,
                'company_id' => $data['company_id'],
                'quantity' => $data['quantity'],
                'weight' => $data['chick_weight'] ?? $data['weight'] ?? $purchase->weight,
                'price' => $data['price'],
                'discount_amount' => $data['discount_amount'] ?? $purchase->discount_amount,
                'discount_percentage' => $data['discount_percentage'] ?? $purchase->discount_percentage,
                'total_price' => $data['total_price'],
                'picture' => $imageName,
                'updatedby' => $userId,
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
