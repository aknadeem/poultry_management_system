<?php

namespace App\Actions\FarmManagement;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class DestroyEmployeeAction
{
    public function execute(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            // Soft delete — images are retained (matches legacy destroy behaviour).
            $employee->delete();
        });
    }
}
