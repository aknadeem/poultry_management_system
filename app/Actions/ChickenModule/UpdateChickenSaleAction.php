<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenSale;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateChickenSaleAction
{
    private $balanceService;

    public function __construct(FinancialBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function execute(ChickenSale $sale, array $data, ?object $imageFile, int $userId): ChickenSale
    {
        return DB::transaction(function () use ($sale, $data, $imageFile, $userId) {
            $imageName = $sale->picture;

            if ($imageFile) {
                // Delete old image if exists
                if ($sale->picture && Storage::disk('public')->exists('chickens/' . $sale->picture)) {
                    Storage::disk('public')->delete('chickens/' . $sale->picture);
                }

                $extension = $imageFile->extension();
                $imageName = time() . mt_rand(10, 99) . '.' . $extension;
                $imageFile->storeAs('chickens/', $imageName, 'public');
            }

            // Sync broker and manual number if passed, otherwise keep existing
            $brokerId = $data['broker_id'] ?? $sale->broker_id;
            $brokerCommission = $data['broker_commission'] ?? $sale->broker_commission;
            $manualNumber = $data['manual_number'] ?? $sale->manual_number;

            $sale->update([
                'manual_number' => $manualNumber,
                'sale_date' => $data['sale_date'],
                'vehicle_number' => $data['vehicle_number'],
                'driver_name' => $data['driver_name'],
                'driver_contact' => $data['driver_contact'],
                'party_id' => $data['customer_id'],
                'customer_id' => $data['customer_id'], // Sync both
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
