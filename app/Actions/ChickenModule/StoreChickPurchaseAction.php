<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickPurchase;
use App\Models\PartyFarm;
use App\Models\PartyFarmChickHistory;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreChickPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, ?object $imageFile, int $userId): ChickPurchase
    {
        if ($data['customer_id'] == ($data['vendor_id'] ?? null)) {
            throw ValidationException::withMessages([
                'customer_id' => 'You Cannot Purchase and Sale with Same Party',
            ]);
        }

        $stagedPicture = null;

        try {
            $stagedPicture = $this->uploadService->store($imageFile, 'chicks');

            return DB::transaction(function () use ($data, $stagedPicture, $userId): ChickPurchase {
                $purchase = ChickPurchase::query()->create([
                    'customer_id' => $data['customer_id'],
                    'purchase_date' => $data['purchase_date'],
                    'chick_grade_id' => $data['chick_grade_id'],
                    'company_id' => $data['company_id'],
                    'chick_entry_age' => $data['chick_entry_age'],
                    'weight' => $data['chick_weight'],
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                    'discount_amount' => $data['discount_amount'] ?? 0,
                    'discount_percentage' => $data['discount_percentage'] ?? 0,
                    'total_price' => $data['total_price'],
                    'bilty_number' => $data['bilty_number'] ?? null,
                    'bilty_charges' => $data['bilty_charges'] ?? null,
                    'sale_order_number' => $data['sale_order_number'] ?? null,
                    'delivery_order_number' => $data['delivery_order_number'] ?? null,
                    'vehicle_number' => $data['vehicle_number'] ?? null,
                    'driver_name' => $data['driver_name'] ?? null,
                    'driver_contact' => $data['driver_contact'] ?? null,
                    'description' => $data['remarks'] ?? $data['description'] ?? null,
                    'picture' => $stagedPicture,
                    'addedby' => $userId,
                ]);

                if (! empty($data['customer_farm_id'])) {
                    $partyFarm = PartyFarm::query()->find($data['customer_farm_id']);
                    if ($partyFarm) {
                        $partyFarm->update([
                            'folk_quantity' => $data['quantity'],
                            'is_occupied' => 1,
                            'updatedby' => $userId,
                        ]);

                        PartyFarmChickHistory::query()->create([
                            'party_farm_id' => $data['customer_farm_id'],
                            'chick_purchase_id' => $purchase->id,
                            'quantity' => $data['quantity'],
                            'entry_date' => $data['purchase_date'],
                        ]);
                    }
                }

                $this->balanceService->recordChickPurchaseBalances(
                    $purchase->id,
                    (int) $data['company_id'],
                    (int) $data['customer_id'],
                    (float) $data['total_price'],
                    $data['purchase_date'],
                    $userId
                );

                return $purchase;
            });
        } catch (\Throwable $exception) {
            $this->uploadService->delete('chicks', $stagedPicture);

            throw $exception;
        }
    }
}
