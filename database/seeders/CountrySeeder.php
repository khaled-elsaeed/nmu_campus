<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Path to SQL file
            $sqlFile = database_path('sql/countries.sql');
            
            if (!File::exists($sqlFile)) {
                $this->command->error("SQL file not found: {$sqlFile}");
                $this->command->info("Please create the file at: database/sql/countries.sql");
                return;
            }

            $this->command->info("Seeding countries table...");
            
            // Truncate table first (optional)
            DB::table('countries')->truncate();
            
            // Read and execute SQL file
            $sql = File::get($sqlFile);
            

            DB::unprepared($sql);
            
            // Get count for verification
            $count = DB::table('countries')->count();
            $this->command->info("✓ Successfully seeded {$count} count records");

        } catch (\Exception $e) {
            $this->command->error("Error seeding countries: " . $e->getMessage());
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}