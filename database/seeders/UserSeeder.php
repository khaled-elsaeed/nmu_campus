<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create or update user
        $admin = User::updateOrCreate(
            ['email' => 'admin@nmu.edu.eg'],
            [
                'name_en' => 'Admin User',
                'name_ar' => 'مسؤول',
                'gender' => 'male',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'force_change_password' => false,
                'last_login' => null,
                'remember_token' => null,
            ]
        );

        // Assign role
        $admin->assignRole($adminRole);
    }
}



