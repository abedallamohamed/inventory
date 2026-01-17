<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create application users
        $users = [
            [
                'name' => 'Selexi User',
                'email' => 'selexi@example.com',
                'password' => Hash::make('selexi'),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com', 
                'password' => Hash::make('admin'),
            ]
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
