<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeed extends Seeder
{
    public function run()
    {
		$data = [
            [
                'name' => 'Employee Salary',
            ], 
            [
                'name' => 'Electricity',
            ], 
            [
                'name' => 'Water Material',
            ],
        ];
        ExpenseCategory::insert($data);
    }
}
