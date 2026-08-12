<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(protected Order $model)
    {
    }

    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->with('items')
            ->latest()
            ->paginate($perPage);
    }

    public function paginateAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('user');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->with(['items', 'user'])->where('order_number', $orderNumber)->first();
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }
}
