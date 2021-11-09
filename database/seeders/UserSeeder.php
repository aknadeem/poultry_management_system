<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
		$user = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make(1234),
            ],  
        ];
        User::insert($user);
    }
}
