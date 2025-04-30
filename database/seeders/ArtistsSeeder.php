<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ArtistsSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('data/artists.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("CSV file not found at: {$csvPath}");
            return;
        }

        $file = fopen($csvPath, 'r');
        
        // Skip header row
        fgetcsv($file);
        
        $this->command->info("Importing artists...");
        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (!empty($row[0])) {  // Check if name exists
                DB::table('artists')->insert([
                    'name' => $row[0],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }

        fclose($file);
        $this->command->info("Imported {$count} artists successfully.");
    }
}