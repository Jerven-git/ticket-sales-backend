<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'organizer_id' => Organizer::factory(),
            'event_title' => fake()->sentence(3),
            'event_type' => fake()->boolean(),
            'event_location' => fake()->address(),
            'event_link' => fake()->url(),
            'event_note' => fake()->sentence(),
            'event_description' => fake()->paragraphs(2, true),
            'event_refund' => 'No refunds',
            'event_category' => 'Music',
            'event_sub_category' => 'Concert',
            'event_code' => fake()->unique()->bothify('EVT-####'),
            'event_start_date' => now()->addDays(7)->toDateString(),
            'event_start_time' => '10:00:00',
            'event_end_date' => now()->addDays(7)->toDateString(),
            'event_end_time' => '18:00:00',
        ];
    }
}
