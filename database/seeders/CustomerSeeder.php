<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('it_IT');

        $customers = [
            [
                'name' => 'Mario Rossi',
                'email' => 'mario.rossi@example.com',
                'address' => 'Via Roma 123, Milano',
                'phone' => '+39 02 1234567',
            ],
            [
                'name' => 'Giulia Bianchi',
                'email' => 'giulia.bianchi@example.com',
                'address' => 'Corso Italia 45, Roma',
                'phone' => '+39 06 9876543',
            ],
            [
                'name' => 'Luca Verdi',
                'email' => 'luca.verdi@example.com',
                'address' => 'Piazza Garibaldi 8, Napoli',
                'phone' => '+39 081 5555666',
            ],
            [
                'name' => 'Anna Ferrari',
                'email' => 'anna.ferrari@example.com',
                'address' => 'Via Dante 22, Firenze',
                'phone' => '+39 055 7777888',
            ],
            [
                'name' => 'Marco Conti',
                'email' => 'marco.conti@example.com',
                'address' => null,
                'phone' => '+39 011 3333444',
            ],
        ];

        // Create predefined customers
        foreach ($customers as $customerData) {
            Customer::updateOrCreate(
                ['email' => $customerData['email']],
                $customerData
            );
        }

        // Create additional random customers
        for ($i = 0; $i < 5; $i++) {
            Customer::updateOrCreate(
                ['email' => $faker->unique()->email],
                [
                    'name' => $faker->name,
                    'email' => $faker->unique()->email,
                    'address' => $faker->boolean(70) ? $faker->address : null,
                    'phone' => $faker->boolean(80) ? $faker->phoneNumber : null,
                ]
            );
        }
    }
}
