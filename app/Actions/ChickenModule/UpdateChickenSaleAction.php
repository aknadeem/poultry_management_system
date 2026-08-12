<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenSale;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class UpdateChickenSaleAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ChickenSale $sale, array $data, ?object $imageFile, int $userId): ChickenSale
    {
        return DB::transaction(function () use ($sale, $data, $imageFile, $userId) {
            $imageName = $this->uploadService->replace($imageFile, 'chickens', $sale->picture);

            $brokerId = $data['broker_id'] ?? $sale->broker_id;
            $brokerCommission = $data['broker_commission'] ?? 0;
            $manualNumber = $data['manual_number'] ?? $sale->manual_number;

            $sale->update([
                'manual_number' => $manualNumber,
                'sale_date' => $data['sale_date'],
                'vehicle_number' => $data['vehicle_number'],
                'driver_name' => $data['driver_name'],
                'driver_contact' => $data['driver_contact'],
                'party_id' => $data['customer_id'],
                'customer_id' => $data['customer_id'],
                'broker_id' => $brokerId,
                'first_weight' => $data['first_weight'] ?? $sale->first_weight,
                'second_weight' => $data['second_weight'] ?? $sale->second_weight,
                'net_weight' => $data['net_weight'] ?? $sale->net_weight,
                'total_weight' => $data['total_weight'] ?? $sale->total_weight,
                'per_kg_price' => $data['per_kg_price'],
                'discount_amount' => $data['discount_amount'],
                'discount_percentage' => $data['discount_percentage'],
                'total_price' => $data['total_price'],
                'picture' => $imageName,
                'updatedby' => $userId,
            ]);

            $this->balanceService->recordChickenSaleBalances(
                $sale->id,
                (int) $data['customer_id'],
                (int) $brokerId,
                (float) $data['total_price'],
                (float) $brokerCommission,
                $data['sale_date'],
                $userId
            );

            return $sale;
        });
    }
}
