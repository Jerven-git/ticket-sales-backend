<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Modules\Payment\PaymentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentServiceInterface $paymentService
    ) {}

    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['error' => 'Order already paid'], 422);
        }

        $result = $this->paymentService->createPaymentIntent($order);

        return response()->json([
            'data' => [
                'client_secret' => $result['client_secret'],
                'publishable_key' => config('stripe.publishable_key'),
            ],
        ]);
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature', '');

        $this->paymentService->handleWebhook($payload, $sigHeader);

        return response()->json(['status' => 'ok']);
    }
}
