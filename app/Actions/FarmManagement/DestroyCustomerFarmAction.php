<?php

namespace App\Actions\FarmManagement;

use App\Models\ChickPurchase;
use App\Models\PartyFarm;
use App\Models\PartyFarmChickHistory;
use App\Models\VaccinationSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DestroyCustomerFarmAction
{
    public function execute(PartyFarm $farm): void
    {
        if (ChickPurchase::query()->where('party_farm_id', $farm->id)->exists()) {
            throw ValidationException::withMessages([
                'farm' => 'This farm cannot be deleted because it is linked to chick purchases.',
            ]);
        }

        if (VaccinationSchedule::query()->where('party_farm_id', $farm->id)->exists()) {
            throw ValidationException::withMessages([
                'farm' => 'This farm cannot be deleted because it is linked to vaccination schedules.',
            ]);
        }

        if (PartyFarmChickHistory::query()->where('party_farm_id', $farm->id)->exists()) {
            throw ValidationException::withMessages([
                'farm' => 'This farm cannot be deleted because it is linked to chick purchase history.',
            ]);
        }

        DB::transaction(function () use ($farm) {
            $farm->delete();
        });
    }
}
