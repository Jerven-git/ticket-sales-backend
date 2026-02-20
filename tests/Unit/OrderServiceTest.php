<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\EventTickets;
use App\Models\Order;
use App\Models\User;
use App\Modules\Order\OrderService;
use App\Repository\OrderRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(OrderService::class);
    }

    public function test_create_order_calculates_correct_total(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $ticket = EventTickets::factory()->create([
            'event_id' => $event->id,
            'ticket_price' => 25,
            'ticket_quantity' => 100,
            'remaining_ticket' => 100,
            'ticket_per_user' => 5,
        ]);

        $items = [
            [
                'event_ticket_id' => $ticket->id,
                'quantity' => 3,
                'unit_price' => 2500, // cents
            ],
        ];

        $order = $this->orderService->createOrder(
            $user->id,
            $event->id,
            $items,
            'test@example.com',
            'Test User'
        );

        $this->assertEquals(7500, $order->total); // 3 * 2500
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }

    public function test_create_order_decrements_ticket_inventory(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $ticket = EventTickets::factory()->create([
            'event_id' => $event->id,
            'ticket_quantity' => 100,
            'remaining_ticket' => 100,
            'ticket_per_user' => 10,
        ]);

        $items = [
            [
                'event_ticket_id' => $ticket->id,
                'quantity' => 5,
                'unit_price' => 1000,
            ],
        ];

        $this->orderService->createOrder(
            $user->id,
            $event->id,
            $items,
            'test@example.com',
            'Test User'
        );

        $ticket->refresh();
        $this->assertEquals(95, $ticket->remaining_ticket);
    }

    public function test_cannot_create_order_when_insufficient_tickets(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $ticket = EventTickets::factory()->create([
            'event_id' => $event->id,
            'ticket_quantity' => 100,
            'remaining_ticket' => 2,
            'ticket_per_user' => 10,
        ]);

        $items = [
            [
                'event_ticket_id' => $ticket->id,
                'quantity' => 5,
                'unit_price' => 1000,
            ],
        ];

        $this->expectException(\Exception::class);

        $this->orderService->createOrder(
            $user->id,
            $event->id,
            $items,
            'test@example.com',
            'Test User'
        );
    }

    public function test_get_user_orders_returns_only_user_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Order::factory()->count(3)->create(['user_id' => $user->id]);
        Order::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $orders = $this->orderService->getUserOrders($user->id);

        $this->assertEquals(3, $orders->total());
    }
}
