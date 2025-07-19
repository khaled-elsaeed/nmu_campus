<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('staff_categories')->insert([
            [
                'name_en' => 'Faculty Members',
                'name_ar' => 'أعضاء هيئة التدريس',
                'type' => 'faculty',
                'description' => 'Faculty staff members',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Faculty Assistance Members',
                'name_ar' => 'الهيئة المعاونة',
                'type' => 'faculty',
                'description' => 'Assistant teaching staff members',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Administrative',
                'name_ar' => 'إداري',
                'type' => 'administrative',
                'description' => 'Administrative staff members',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Campus Staff',
                'name_ar' => 'موظفو الحرم الجامعي',
                'type' => 'campus',
                'description' => 'Staff responsible for campus operations and maintenance',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 