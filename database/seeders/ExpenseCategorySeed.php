<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeed extends Seeder
{
    public function run()
    {
        $names = [
            'Employee Salary',
            'Electricity',
            'Water Material',
        ];

        foreach ($names as $name) {
            ExpenseCategory::firstOrCreate(['name' => $name]);
        }
    }
}
