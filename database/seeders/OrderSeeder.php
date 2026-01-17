<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('it_IT');
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        
        // Get customers (some will have no orders)
        $customers = Customer::all();
        
        // Select only some customers for orders (leave some without orders)
        $customersWithOrders = $customers->random(6);
        
        $orderNumber = 1000;
        
        foreach ($customersWithOrders as $customer) {
            // Random number of orders per customer (1-8)
            $orderCount = $faker->numberBetween(1, 8);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $orderNumber++;
                
                Order::create([
                    'order_number' => 'ORD-' . str_pad($orderNumber, 4, '0', STR_PAD_LEFT),
                    'customer_id' => $customer->id,
                    'order_date' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                    'total_amount' => $faker->randomFloat(2, 25.50, 1500.00),
                    'status' => $faker->randomElement($statuses),
                ]);
            }
        }
    }
}
