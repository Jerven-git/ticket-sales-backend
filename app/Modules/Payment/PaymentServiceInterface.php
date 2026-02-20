<?php

namespace App\Modules\Payment;

use App\Models\Order;

interface PaymentServiceInterface
{
    public function createPaymentIntent(Order $order): array;

    public function handleWebhook(string $payload, string $sigHeader): void;
}
