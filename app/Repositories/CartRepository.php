<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\CartItem;
use App\Models\Course;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CartRepository implements CartRepositoryInterface
{
    public function getItems(?int $userId, ?string $sessionId): Collection
    {
        return CartItem::query()
            ->with('purchasable')
            ->when(
                $userId,
                fn ($query) => $query->where('user_id', $userId)
            )
            ->when(
                !$userId && $sessionId,
                fn ($query) => $query->where('session_id', $sessionId)
            )
            ->get()
            ->each(function ($item) {
                $item->line_total = $this->itemPrice($item);
            });
    }

    public function addItem(
        ?int $userId,
        ?string $sessionId,
        string $type,
        int $purchasableId
    ): void {
        $modelClass = $this->resolvePurchasableType($type);

        $purchasable = $modelClass::findOrFail($purchasableId);

        $query = CartItem::query()
            ->where('purchasable_type', $modelClass)
            ->where('purchasable_id', $purchasableId);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id')
                ->where('session_id', $sessionId);
        }

        $item = $query->first();

        if ($item) {
            $item->increment('quantity');
            return;
        }

        CartItem::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'purchasable_type' => $modelClass,
            'purchasable_id' => $purchasable->id,
            'quantity' => 1,
        ]);
    }

    public function removeItem(
        ?int $userId,
        ?string $sessionId,
        int $cartItemId
    ): void {
        $query = CartItem::query()
            ->where('id', $cartItemId);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id')
                ->where('session_id', $sessionId);
        }

        $query->delete();
    }

    public function clear(?int $userId, ?string $sessionId): void
    {
        $query = CartItem::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id')
                ->where('session_id', $sessionId);
        }

        $query->delete();
    }

    public function mergeGuestCartIntoUser(
        string $sessionId,
        int $userId
    ): void {
        DB::transaction(function () use ($sessionId, $userId) {
            $guestItems = CartItem::query()
                ->whereNull('user_id')
                ->where('session_id', $sessionId)
                ->get();

            foreach ($guestItems as $guestItem) {
                $existing = CartItem::query()
                    ->where('user_id', $userId)
                    ->where(
                        'purchasable_type',
                        $guestItem->purchasable_type
                    )
                    ->where(
                        'purchasable_id',
                        $guestItem->purchasable_id
                    )
                    ->first();

                if ($existing) {
                    $existing->increment(
                        'quantity',
                        $guestItem->quantity
                    );

                    $guestItem->delete();
                } else {
                    $guestItem->update([
                        'user_id' => $userId,
                        'session_id' => null,
                    ]);
                }
            }
        });
    }

    public function count(
        ?int $userId,
        ?string $sessionId
    ): int {
        $query = CartItem::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id')
                ->where('session_id', $sessionId);
        }

        return (int) $query->sum('quantity');
    }

    protected function resolvePurchasableType(string $type): string
    {
        return match (strtolower($type)) {
            'course' => Course::class,
            'book' => Book::class,
            default => throw new RuntimeException(
                "Unsupported cart item type: {$type}"
            ),
        };
    }

    protected function itemPrice(CartItem $item): float
    {
        $product = $item->purchasable;

        if (!$product) {
            return 0;
        }

        $price = $product->discount_price
            ?? $product->price
            ?? 0;

        return round(
            (float) $price * (int) $item->quantity,
            2
        );
    }
}