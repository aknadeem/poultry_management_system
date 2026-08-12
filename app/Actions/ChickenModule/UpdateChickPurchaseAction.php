<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickPurchase;
use App\Models\PartyFarm;
use App\Models\PartyFarmChickHistory;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class UpdateChickPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ChickPurchase $purchase, array $data, ?object $imageFile, int $userId): ChickPurchase
    {
        return DB::transaction(function () use ($purchase, $data, $imageFile, $userId) {
            $imageName = $this->uploadService->replace($imageFile, 'chicks', $purchase->picture);

            $purchase->update([
                'purchase_date' => $data['purchase_date'],
                'vehicle_number' => $data['vehicle_number'],
                'driver_name' => $data['driver_name'],
                'driver_contact' => $data['driver_contact'],
                'company_id' => $data['company_id'],
                'quantity' => $data['quantity'],
                'weight' => $data['chick_weight'] ?? $purchase->weight,
                'price' => $data['price'],
                'discount_amount' => $data['discount_amount'],
                'discount_percentage' => $data['discount_percentage'],
                'total_price' => $data['total_price'],
                'picture' => $imageName,
                'updatedby' => $userId,
            ]);

            $history = PartyFarmChickHistory::where('chick_purchase_id', $purchase->id)->first();
            if ($history) {
                $history->update([
                    'quantity' => $data['quantity'],
                    'entry_date' => $data['purchase_date'],
                ]);

                $partyFarm = PartyFarm::find($history->party_farm_id);
                if ($partyFarm) {
                    $partyFarm->update([
                        'folk_quantity' => $data['quantity'],
                        'updatedby' => $userId,
                    ]);
                }
            }

            $this->balanceService->recordChickPurchaseBalances(
                $purchase->id,
                (int) $data['company_id'],
                (int) $purchase->customer_id,
                (float) $data['total_price'],
                $data['purchase_date'],
                $userId
            );

            return $purchase;
        });
    }
}
