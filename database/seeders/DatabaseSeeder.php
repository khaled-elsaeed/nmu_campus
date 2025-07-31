<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            NationalitySeeder::class,
            GovernorateSeeder::class,
            CitySeeder::class,
            FacultySeeder::class,
            ProgramSeeder::class,
            DepartmentSeeder::class,
            StaffCategorySeeder::class,
            EquipmentSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
