<?php

namespace App\Repository\Eloquent;

use App\Repository\Eloquent\Base\BaseRepository;
use App\Repository\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\EventTickets;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    protected $order;

    public function __construct(Order $order)
    {
        parent::__construct($order);
        $this->order = $order;
    }

    public function create(array $orderData, array $items): Order
    {
        return DB::transaction(function () use ($orderData, $items) {
            $order = $this->order::create($orderData);

            foreach ($items as $item) {
                $ticket = EventTickets::where('id', $item['event_ticket_id'])
                    ->lockForUpdate()
                    ->first();

                if ($ticket->remaining_ticket < $item['quantity']) {
                    throw new \Exception("Insufficient tickets for {$ticket->ticket_name}. Only {$ticket->remaining_ticket} remaining.");
                }

                $ticket->decrement('remaining_ticket', $item['quantity']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'event_ticket_id' => $item['event_ticket_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return $order->load(['items.ticket', 'event', 'user']);
        });
    }

    public function findById(int $id): ?Order
    {
        return $this->order::with(['items.ticket', 'event', 'user'])->findOrFail($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->order::with(['items.ticket', 'event'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();
    }

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->order::with(['items.ticket', 'event'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updateStatus(int $id, string $status): Order
    {
        $order = $this->order::findOrFail($id);
        $order->update(['status' => $status]);
        return $order->fresh(['items.ticket', 'event']);
    }

    public function updatePaymentStatus(int $id, string $paymentStatus, ?string $stripePaymentIntentId = null): Order
    {
        $order = $this->order::findOrFail($id);
        $data = ['payment_status' => $paymentStatus];
        if ($stripePaymentIntentId) {
            $data['stripe_payment_intent_id'] = $stripePaymentIntentId;
        }
        if ($paymentStatus === 'paid') {
            $data['paid_at'] = now();
            $data['status'] = 'confirmed';
        }
        $order->update($data);
        return $order->fresh(['items.ticket', 'event']);
    }
}
