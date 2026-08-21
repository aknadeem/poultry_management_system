<?php

namespace App\Actions\FarmManagement;

use App\Models\PersonalFarm;
use Illuminate\Support\Facades\DB;

class DestroyPersonalFarmAction
{
    public function execute(PersonalFarm $farm): void
    {
        DB::transaction(function () use ($farm): void {
            $farm->delete();
        });
    }
}
