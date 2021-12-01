<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->userLevelSeeder();
		$user = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'user_level_id' => 1,
                'password' => Hash::make(1234),
            ],  
        ];
        User::insert($user);
    }

    protected function userLevelSeeder()
    {
    	if (UserLevel::count() == 0){
	        $data = [
			    [
			       'name' => 'Super Admin',
			       'slug' => 'super-admin',
			    ],[
			       'name' => 'Admin',
			       'slug' => 'admin',
			    ],[
			       'name' => 'HOD',
			       'slug' => 'hod',
			    ],
			];

			UserLevel::insert($data);
		} else {
			echo "*UserLevel* Table Already has Data\n";
		}
    }
}
