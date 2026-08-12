<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->userRoleSeeder();
		$user = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'user_role_id' => 1,
                'password' => Hash::make(1234),
            ],
        ];
        if (User::where('email', 'admin@admin.com')->doesntExist()) {
            User::insert($user);
        }
    }

    protected function userRoleSeeder()
    {
    	if (UserRole::count() == 0){
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

			UserRole::insert($data);
		} else {
			echo "*UserRole* Table Already has Data\n";
		}
    }
}
