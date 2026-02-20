<?php

namespace App\Modules\Payment;

use App\Models\Order;
use App\Repository\OrderRepositoryInterface;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StripePaymentService implements PaymentServiceInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
        Stripe::setApiKey(config('stripe.secret_key'));
    }

    public function createPaymentIntent(Order $order): array
    {
        if ($order->stripe_payment_intent_id) {
            $intent = PaymentIntent::retrieve($order->stripe_payment_intent_id);

            if (in_array($intent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action'])) {
                return [
                    'client_secret' => $intent->client_secret,
                    'payment_intent_id' => $intent->id,
                ];
            }
        }

        $intent = PaymentIntent::create([
            'amount' => $order->total,
            'currency' => $order->currency,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);

        $order->update(['stripe_payment_intent_id' => $intent->id]);

        return [
            'client_secret' => $intent->client_secret,
            'payment_intent_id' => $intent->id,
        ];
    }

    public function handleWebhook(string $payload, string $sigHeader): void
    {
        $webhookSecret = config('stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            throw new HttpException(400, 'Invalid signature');
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
        }
    }

    private function handlePaymentSucceeded(object $paymentIntent): void
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;
        if (!$orderId) return;

        $order = $this->orderRepository->findById((int) $orderId);
        if (!$order) return;

        $this->orderRepository->updatePaymentStatus($order, 'paid');
    }

    private function handlePaymentFailed(object $paymentIntent): void
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;
        if (!$orderId) return;

        $order = $this->orderRepository->findById((int) $orderId);
        if (!$order) return;

        $this->orderRepository->updatePaymentStatus($order, 'failed');
    }
}
