<?php

namespace App\Actions\FarmManagement;

use App\Models\VaccinationSchedule;
use Illuminate\Support\Facades\DB;

class StoreVaccinationScheduleAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, int $userId): VaccinationSchedule
    {
        return DB::transaction(function () use ($data, $userId): VaccinationSchedule {
            return VaccinationSchedule::query()->create([
                'party_farm_id' => $data['farm_id'],
                'product_id' => $data['product_id'],
                'schedule_date' => $data['schedule_date'],
                'description' => $data['description'] ?? null,
                'addedby' => $userId,
            ]);
        });
    }
}
