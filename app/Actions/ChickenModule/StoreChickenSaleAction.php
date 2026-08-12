<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickenSale;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class StoreChickenSaleAction
{
    private $balanceService;

    public function __construct(FinancialBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function execute(array $data, ?object $imageFile, int $userId): ChickenSale
    {
        return DB::transaction(function () use ($data, $imageFile, $userId) {
            $imageName = null;
            if ($imageFile) {
                $extension = $imageFile->extension();
                $imageName = time() . mt_rand(10, 99) . '.' . $extension;
            }

            $sale = ChickenSale::create([
                'manual_number' => $data['manual_number'],
                'sale_date' => $data['sale_date'],
                'vehicle_number' => $data['vehicle_number'],
                'driver_name' => $data['driver_name'],
                'driver_contact' => $data['driver_contact'],
                'party_id' => $data['customer_id'],
                'customer_id' => $data['customer_id'], // Keep both in sync to prevent relationship bugs
                'broker_id' => $data['broker_id'],
                'first_weight' => $data['first_weight'] ?? null,
                'second_weight' => $data['second_weight'] ?? null,
                'net_weight' => $data['net_weight'] ?? null,
                'total_weight' => $data['total_weight'],
                'per_kg_price' => $data['per_kg_price'],
                'discount_amount' => $data['discount_amount'],
                'discount_percentage' => $data['discount_percentage'],
                'total_price' => $data['total_price'],
                'picture' => $imageName,
                'addedby' => $userId,
            ]);

            if ($imageFile && $imageName) {
                $imageFile->storeAs('chickens/sales/', $imageName, 'public');
            }

            $this->balanceService->recordChickenSaleBalances(
                $sale->id,
                (int) $data['customer_id'],
                (int) $data['broker_id'],
                (float) $data['total_price'],
                (float) ($data['broker_commission'] ?? 0),
                $data['sale_date'],
                $userId
            );

            return $sale;
        });
    }
}
