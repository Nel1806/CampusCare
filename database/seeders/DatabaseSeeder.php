<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MaintenancePersonnel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@campuscare.test'],
            [
                'name' => 'CampusCare Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}
