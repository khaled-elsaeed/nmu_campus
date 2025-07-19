<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'name_en' => 'Human Resources',
                'name_ar' => 'الموارد البشرية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Finance',
                'name_ar' => 'المالية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Admissions',
                'name_ar' => 'القبول والتسجيل',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'IT Services',
                'name_ar' => 'خدمات تقنية المعلومات',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Procurement',
                'name_ar' => 'المشتريات',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 