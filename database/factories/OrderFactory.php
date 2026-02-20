<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'order_number' => Order::generateOrderNumber(),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 5000,
            'total' => 5000,
            'currency' => 'usd',
            'customer_email' => fake()->safeEmail(),
            'customer_name' => fake()->name(),
        ];
    }
}
