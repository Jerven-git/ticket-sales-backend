<?php

namespace App\Modules\Order;

use App\Models\Order;
use App\Repository\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService implements OrderServiceInterface
{
    protected OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(int $userId, int $eventId, array $items, string $email, string $name): Order
    {
        $subtotal = 0;
        foreach ($items as &$item) {
            $item['total_price'] = $item['quantity'] * $item['unit_price'];
            $subtotal += $item['total_price'];
        }

        $orderData = [
            'user_id' => $userId,
            'event_id' => $eventId,
            'order_number' => Order::generateOrderNumber(),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'currency' => 'usd',
            'customer_email' => $email,
            'customer_name' => $name,
        ];

        return $this->orderRepository->create($orderData, $items);
    }

    public function getOrder(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return $this->orderRepository->findByOrderNumber($orderNumber);
    }

    public function getUserOrders(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getByUser($userId, $perPage);
    }

    public function confirmPayment(int $orderId, string $stripePaymentIntentId): Order
    {
        return $this->orderRepository->updatePaymentStatus($orderId, 'paid', $stripePaymentIntentId);
    }

    public function cancelOrder(int $orderId): Order
    {
        return $this->orderRepository->updateStatus($orderId, 'cancelled');
    }
}
