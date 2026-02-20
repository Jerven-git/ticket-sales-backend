<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Order\OrderServiceInterface;
use App\Models\EventTickets;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected OrderServiceInterface $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|integer|exists:events,id',
            'items' => 'required|array|min:1',
            'items.*.ticket_id' => 'required|integer|exists:event_tickets,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_email' => 'required|email',
            'customer_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $items = [];
        foreach ($request->input('items') as $item) {
            $ticket = EventTickets::findOrFail($item['ticket_id']);

            if ($item['quantity'] > $ticket->ticket_per_user) {
                return response()->json([
                    'error' => "Maximum {$ticket->ticket_per_user} tickets per user for {$ticket->ticket_name}"
                ], 422);
            }

            if ($item['quantity'] > $ticket->remaining_ticket) {
                return response()->json([
                    'error' => "Only {$ticket->remaining_ticket} tickets remaining for {$ticket->ticket_name}"
                ], 422);
            }

            $items[] = [
                'event_ticket_id' => $ticket->id,
                'quantity' => $item['quantity'],
                'unit_price' => $ticket->ticket_price * 100,
            ];
        }

        try {
            $order = $this->orderService->createOrder(
                $request->user()->id,
                $request->input('event_id'),
                $items,
                $request->input('customer_email'),
                $request->input('customer_name')
            );

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $orders = $this->orderService->getUserOrders($request->user()->id, $perPage);
        return response()->json(['data' => $orders], 200);
    }

    public function show(string $orderNumber)
    {
        try {
            $order = $this->orderService->getOrderByNumber($orderNumber);
            return response()->json(['data' => $order], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    }
}
