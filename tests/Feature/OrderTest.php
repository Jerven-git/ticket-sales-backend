<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTickets;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Event $event;
    private EventTickets $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->event = Event::factory()->create();
        $this->ticket = EventTickets::factory()->create([
            'event_id' => $this->event->id,
            'ticket_price' => 50,
            'ticket_quantity' => 100,
            'remaining_ticket' => 100,
            'ticket_per_user' => 5,
        ]);
    }

    public function test_authenticated_user_can_create_order(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'event_id' => $this->event->id,
                'items' => [
                    ['ticket_id' => $this->ticket->id, 'quantity' => 2],
                ],
                'customer_email' => 'buyer@example.com',
                'customer_name' => 'Test Buyer',
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['order_number']]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
        ]);

        // Check ticket inventory was decremented
        $this->ticket->refresh();
        $this->assertEquals(98, $this->ticket->remaining_ticket);
    }

    public function test_cannot_order_more_than_per_user_limit(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'event_id' => $this->event->id,
                'items' => [
                    ['ticket_id' => $this->ticket->id, 'quantity' => 10],
                ],
                'customer_email' => 'buyer@example.com',
                'customer_name' => 'Test Buyer',
            ]);

        $response->assertStatus(422);
    }

    public function test_cannot_order_more_than_remaining_tickets(): void
    {
        $this->ticket->update(['remaining_ticket' => 1]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'event_id' => $this->event->id,
                'items' => [
                    ['ticket_id' => $this->ticket->id, 'quantity' => 3],
                ],
                'customer_email' => 'buyer@example.com',
                'customer_name' => 'Test Buyer',
            ]);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_user_cannot_create_order(): void
    {
        $response = $this->postJson('/api/v1/orders', [
            'event_id' => $this->event->id,
            'items' => [
                ['ticket_id' => $this->ticket->id, 'quantity' => 1],
            ],
            'customer_email' => 'buyer@example.com',
            'customer_name' => 'Test Buyer',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_list_their_orders(): void
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);
        Order::factory()->count(2)->create(); // other user's orders

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/orders');

        $response->assertStatus(200);
    }

    public function test_user_can_view_order_by_number(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/orders/{$order->order_number}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.order_number', $order->order_number);
    }

    public function test_order_validation_requires_items(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'event_id' => $this->event->id,
                'customer_email' => 'buyer@example.com',
                'customer_name' => 'Test Buyer',
            ]);

        $response->assertStatus(422);
    }
}
