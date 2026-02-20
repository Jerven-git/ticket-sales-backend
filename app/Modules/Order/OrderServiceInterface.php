<?php

namespace App\Modules\Order;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    public function createOrder(int $userId, int $eventId, array $items, string $email, string $name): Order;

    public function getOrder(int $id): ?Order;

    public function getOrderByNumber(string $orderNumber): ?Order;

    public function getUserOrders(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function confirmPayment(int $orderId, string $stripePaymentIntentId): Order;

    public function cancelOrder(int $orderId): Order;
}
