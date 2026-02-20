<?php

namespace App\Repository;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function create(array $orderData, array $items): Order;

    public function findById(int $id): ?Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function updateStatus(int $id, string $status): Order;

    public function updatePaymentStatus(int $id, string $paymentStatus, ?string $stripePaymentIntentId = null): Order;
}
