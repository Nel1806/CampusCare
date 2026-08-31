<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MaintenancePersonnel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'admin@campuscare.test'],
            ['name' => 'CampusCare Admin', 'password' => 'password', 'role' => 'admin']
        );

       
    }
}
