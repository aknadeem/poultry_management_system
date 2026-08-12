<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ { UserSeeder, ExpenseCategorySeed, CountryProvinceSeeder };

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CountryProvinceSeeder::class,
            UserSeeder::class,
            ExpenseCategorySeed::class,
	    ]);

        if (app()->environment('local') && env('SEED_DEMO', false)) {
            $this->call(DemoSeeder::class);
        }
    }
}
