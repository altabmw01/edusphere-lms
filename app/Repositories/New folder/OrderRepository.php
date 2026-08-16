<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function paginateForUser(
        int $userId,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Order::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Order::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where(
                'payment_status',
                $filters['payment_status']
            );
        }

        if (!empty($filters['payment_method'])) {
            $query->where(
                'payment_method',
                $filters['payment_method']
            );
        }

        if (!empty($filters['user_id'])) {
            $query->where(
                'user_id',
                $filters['user_id']
            );
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('billing_name', 'like', "%{$search}%")
                    ->orWhere('billing_email', 'like', "%{$search}%")
                    ->orWhere('billing_phone', 'like', "%{$search}%");
            });
        }

        return $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findByOrderNumber(
        string $orderNumber
    ): ?Order {
        return Order::query()
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function create(array $data): Order
    {
        if (empty($data['order_number'])) {
            $data['order_number'] = $this->generateOrderNumber();
        }

        return Order::create($data);
    }

    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
        } while (
            Order::where('order_number', $orderNumber)->exists()
        );

        return $orderNumber;
    }
}