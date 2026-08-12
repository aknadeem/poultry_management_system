<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickPurchase;
use App\Models\PartyFarm;
use App\Models\PartyFarmChickHistory;
use App\Services\FileUploadService;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;

class DestroyChickPurchaseAction
{
    public function __construct(
        private FinancialBalanceService $balanceService,
        private FileUploadService $uploadService,
    ) {
    }

    public function execute(ChickPurchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $this->uploadService->delete('chicks', $purchase->picture);

            $histories = PartyFarmChickHistory::where('chick_purchase_id', $purchase->id)->get();
            foreach ($histories as $history) {
                $partyFarm = PartyFarm::find($history->party_farm_id);
                if ($partyFarm) {
                    $partyFarm->update([
                        'folk_quantity' => 0,
                        'is_occupied' => 0,
                    ]);
                }
                $history->delete();
            }

            $this->balanceService->deleteChickPurchaseBalances(
                $purchase->id,
                (int) $purchase->company_id,
                (int) $purchase->customer_id
            );

            $purchase->delete();
        });
    }
}
