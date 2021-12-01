<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ { UserSeeder, ExpenseCategorySeed, CountryProvinceSeeder };

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            CountryProvinceSeeder::class,  
            UserSeeder::class,  
            ExpenseCategorySeed::class,  
	    ]); 
    }
}
