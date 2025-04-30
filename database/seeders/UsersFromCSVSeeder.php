<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UsersFromCSVSeeder extends Seeder
{
    const RESEARCHER_ROLE_ID = 3;

    public function run()
    {
        $csvFile = database_path('data/researcher.csv');
        
        if (!File::exists($csvFile)) {
            $this->command->error("The CSV file does not exist at path: {$csvFile}");
            return;
        }

        $csvData = array_map('str_getcsv', file($csvFile));
        $header = array_shift($csvData); // Remove header row

        $this->command->info("Importing researchers from CSV...");

        foreach ($csvData as $row) {
            if (count($row) !== count($header)) {
                continue; // Skip invalid rows
            }

            $userData = array_combine($header, $row);
            
            // Insert user
            $userId = DB::table('users')->insertGetId([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'role_id'=>3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign researcher role
            DB::table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => self::RESEARCHER_ROLE_ID,
            ]);
        }

        $this->command->info("Successfully imported " . count($csvData) . " researchers.");
    }
}