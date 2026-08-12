<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Province;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryProvinceSeeder extends Seeder
{
    public function run()
    {
  		$this->createCountries();
	    $this->createProvinces();
	    $this->createCities();
    }

    protected function createCountries()
    {
    	if (Country::count() == 0){
	        $data = [
			    [
			       'name' => 'Pakistan',
			       'slug' => 'pakistan',
			    ],
			];

			Country::insert($data);
		} else {
			echo "*Country* Table Already has Data\n";
		}
    }

    protected function createProvinces()
    {
        if (Province::count() == 0){
        $data = [
            [
                'name' => 'Azad Jammu & Kashmir',
                'country_id' => 1,
            ],[
                'name' => 'Balochistan',
                'country_id' => 1,
            ],[
                'name' => 'Gilgit Baltistan',
                'country_id' => 1,
            ],[
                'name' => 'Islamabad',
                'country_id' => 1,
            ],[
                'name' => 'Khyber Pakhtunkhwa',
                'country_id' => 1,
            ],[
                'name' => 'Punjab',
                'country_id' => 1,
            ],[
                'name' => 'Sindh',
                'country_id' => 1,
            ],
        ];

        Province::insert($data);
		} else {
			echo "*Province* Table Already has Data\n";
		}
    }

    protected function createCities()
    {
        if (City::count() > 0) {
            echo "*City* Table Already has Data\n";
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            City::insert([
                ['id' => 1, 'name' => 'Lahore', 'province_id' => 6],
                ['id' => 2, 'name' => 'Karachi', 'province_id' => 7],
                ['id' => 3, 'name' => 'Islamabad', 'province_id' => 4],
                ['id' => 4, 'name' => 'Peshawar', 'province_id' => 5],
                ['id' => 5, 'name' => 'Rawalpindi', 'province_id' => 6],
                ['id' => 6, 'name' => 'Faisalabad', 'province_id' => 6],
            ]);
            $this->command?->info('Cities seeded (SQLite subset)');
            return;
        }

        $path = base_path().'/database/seeders/cities.sql';
        $sql = file_get_contents($path);
        DB::statement($sql);
        $this->command?->info('Cities Seeded From SQL File');
    }
}
