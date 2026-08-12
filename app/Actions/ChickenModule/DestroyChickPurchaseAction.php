<?php

namespace App\Actions\ChickenModule;

use App\Models\ChickPurchase;
use App\Models\PartyFarm;
use App\Models\PartyFarmChickHistory;
use App\Services\FinancialBalanceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DestroyChickPurchaseAction
{
    private $balanceService;

    public function __construct(FinancialBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function execute(ChickPurchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            // Delete image if exists
            $imgPath = 'chicks/' . $purchase->picture;
            if ($purchase->picture && Storage::disk('public')->exists($imgPath)) {
                Storage::disk('public')->delete($imgPath);
            }

            // Remove occupied status and delete history
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

            // Delete balances
            $this->balanceService->deleteChickPurchaseBalances(
                $purchase->id,
                (int) $purchase->company_id,
                (int) $purchase->customer_id
            );

            // Delete purchase
            $purchase->delete();
        });
    }
}
