<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventTickets;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventTicketsFactory extends Factory
{
    protected $model = EventTickets::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(50, 200);

        return [
            'event_id' => Event::factory(),
            'ticket_name' => fake()->randomElement(['General Admission', 'VIP', 'Early Bird']),
            'ticket_price' => fake()->numberBetween(10, 200),
            'ticket_quantity' => $quantity,
            'ticket_per_user' => fake()->numberBetween(1, 5),
            'ticket_description' => fake()->sentence(),
            'sale_start_date' => now()->toDateString(),
            'sale_start_time' => '09:00:00',
            'sale_end_date' => now()->addDays(6)->toDateString(),
            'sale_end_time' => '23:59:00',
            'event_publish_or_draft' => 'publish',
            'remaining_ticket' => $quantity,
        ];
    }
}
