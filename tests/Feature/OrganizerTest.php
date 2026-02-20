<?php

namespace Tests\Feature;

use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_organizers(): void
    {
        Organizer::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/organizers');

        $response->assertStatus(200);
    }

    public function test_can_show_single_organizer(): void
    {
        $organizer = Organizer::factory()->create();

        $response = $this->getJson("/api/v1/organizers/{$organizer->id}");

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_organizer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/organizers', [
                'organizer_name' => 'Test Org',
                'organizer_bio' => 'A great org',
                'status' => true,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('organizers', ['organizer_name' => 'Test Org']);
    }

    public function test_unauthenticated_user_cannot_create_organizer(): void
    {
        $response = $this->postJson('/api/v1/organizers', [
            'organizer_name' => 'Test Org',
        ]);

        $response->assertStatus(401);
    }
}
