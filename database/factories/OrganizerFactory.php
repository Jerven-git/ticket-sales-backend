<?php

namespace Database\Factories;

use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizerFactory extends Factory
{
    protected $model = Organizer::class;

    public function definition(): array
    {
        return [
            'organizer_name' => fake()->company(),
            'organizer_website' => fake()->url(),
            'organizer_bio' => fake()->paragraph(),
            'organizer_facebook_link' => fake()->url(),
            'organizer_twitter_link' => fake()->url(),
            'organizer_instagram_link' => fake()->url(),
            'status' => 'active',
        ];
    }
}
