<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_events(): void
    {
        Event::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/events');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['data']]);
    }

    public function test_can_show_single_event(): void
    {
        $event = Event::factory()->create();

        $response = $this->getJson("/api/v1/events/{$event->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.event_title', $event->event_title);
    }

    public function test_show_returns_404_for_nonexistent_event(): void
    {
        $response = $this->getJson('/api/v1/events/99999');

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_create_event(): void
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create();

        $eventData = [
            'event_title' => 'Test Concert',
            'event_type' => false,
            'event_location' => '123 Main St',
            'event_description' => 'A great event',
            'event_refund' => 'No refunds',
            'event_category' => 'Music',
            'event_sub_category' => 'Concert',
            'organizer_id' => $organizer->id,
            'event_start_date' => now()->addDays(7)->toDateString(),
            'event_start_time' => '10:00 AM',
            'event_end_date' => now()->addDays(7)->toDateString(),
            'event_end_time' => '06:00 PM',
            'ticket_name' => 'General Admission',
            'ticket_price' => 50,
            'ticket_quantity' => 100,
            'ticket_per_user' => 5,
            'event_publish_or_draft' => true,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/events', $eventData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('events', [
            'event_title' => 'Test Concert',
            'user_id' => $user->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_event(): void
    {
        $response = $this->postJson('/api/v1/events', []);

        $response->assertStatus(401);
    }

    public function test_owner_can_update_event(): void
    {
        $user = User::factory()->create();
        $organizer = Organizer::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id, 'organizer_id' => $organizer->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/events/{$event->id}", [
                'event_title' => 'Updated Title',
            ]);

        $response->assertStatus(200);
    }

    public function test_non_owner_cannot_update_event(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser, 'sanctum')
            ->putJson("/api/v1/events/{$event->id}", [
                'event_title' => 'Hacked Title',
            ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_event(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/events/{$event->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_non_owner_cannot_delete_event(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser, 'sanctum')
            ->deleteJson("/api/v1/events/{$event->id}");

        $response->assertStatus(403);
    }

    public function test_my_events_returns_only_user_events(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Event::factory()->count(2)->create(['user_id' => $user->id]);
        Event::factory()->count(3)->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/my-events');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.data');
    }

    public function test_can_search_events(): void
    {
        Event::factory()->create(['event_title' => 'Jazz Night']);
        Event::factory()->create(['event_title' => 'Rock Concert']);

        $response = $this->getJson('/api/v1/events/search?q=Jazz');

        $response->assertStatus(200);
    }

    public function test_events_are_paginated(): void
    {
        Event::factory()->count(20)->create();

        $response = $this->getJson('/api/v1/events?per_page=5');

        $response->assertStatus(200);
        $response->assertJsonPath('data.per_page', 5);
        $response->assertJsonCount(5, 'data.data');
    }
}
