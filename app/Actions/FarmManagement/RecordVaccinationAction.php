<?php

namespace App\Actions\FarmManagement;

use App\Models\VaccinationSchedule;
use Illuminate\Support\Facades\DB;

class RecordVaccinationAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(VaccinationSchedule $schedule, array $data, int $userId): VaccinationSchedule
    {
        return DB::transaction(function () use ($schedule, $data, $userId): VaccinationSchedule {
            $schedule->update([
                'is_vaccinated' => 1,
                'vaccination_date' => $data['vaccination_date'],
                'vaccinated_remarks' => $data['remarks'] ?? null,
                'updatedby' => $userId,
            ]);

            return $schedule->fresh();
        });
    }
}
