<?php

namespace App\Repositories\Eloquent;

use App\Models\Book;
use App\Models\CartItem;
use App\Models\Course;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Collection;

class CartRepository implements CartRepositoryInterface
{
    public function __construct(protected CartItem $model)
    {
    }

    public function getItems(?int $userId, ?string $sessionId): Collection
    {
        return $this->model->newQuery()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->with('purchasable')
            ->latest()
            ->get();
    }

    public function addItem(?int $userId, ?string $sessionId, string $type, int $purchasableId): void
    {
        $modelClass = $type === 'book' ? Book::class : Course::class;

        $exists = $this->model->newQuery()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->where('purchasable_type', $modelClass)
            ->where('purchasable_id', $purchasableId)
            ->exists();

        if ($exists) {
            return;
        }

        $this->model->create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'purchasable_type' => $modelClass,
            'purchasable_id' => $purchasableId,
            'quantity' => 1,
        ]);
    }

    public function removeItem(?int $userId, ?string $sessionId, int $cartItemId): void
    {
        $this->model->newQuery()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->where('id', $cartItemId)
            ->delete();
    }

    public function updateQuantity(?int $userId, ?string $sessionId, int $cartItemId, int $quantity): void
    {
        $this->model->newQuery()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->where('id', $cartItemId)
            ->update(['quantity' => max(1, $quantity)]);
    }

    public function clear(?int $userId, ?string $sessionId): void
    {
        $this->model->newQuery()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->delete();
    }

    public function mergeGuestCartIntoUser(string $sessionId, int $userId): void
    {
        $guestItems = $this->model->newQuery()->where('session_id', $sessionId)->get();

        foreach ($guestItems as $item) {
            $existing = $this->model->newQuery()
                ->where('user_id', $userId)
                ->where('purchasable_type', $item->purchasable_type)
                ->where('purchasable_id', $item->purchasable_id)
                ->exists();

            if ($existing) {
                $item->delete();
            } else {
                $item->update(['user_id' => $userId, 'session_id' => null]);
            }
        }
    }

    public function count(?int $userId, ?string $sessionId): int
    {
        return $this->model->newQuery()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when(! $userId, fn ($q) => $q->where('session_id', $sessionId))
            ->count();
    }
}
