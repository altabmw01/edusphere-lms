<?php

namespace App\Services;

use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    public function __construct(protected CartRepositoryInterface $cart)
    {
    }

    public function items(): Collection
    {
        return $this->cart->getItems(Auth::id(), $this->sessionId());
    }

    public function add(string $type, int $purchasableId): void
    {
        $this->cart->addItem(Auth::id(), $this->sessionId(), $type, $purchasableId);
    }

    public function remove(int $cartItemId): void
    {
        $this->cart->removeItem(Auth::id(), $this->sessionId(), $cartItemId);
    }

    /** Only ever used for book checkout — courses are always quantity 1. */
    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        $this->cart->updateQuantity(Auth::id(), $this->sessionId(), $cartItemId, $quantity);
    }

    public function clear(): void
    {
        $this->cart->clear(Auth::id(), $this->sessionId());
    }

    public function count(): int
    {
        return $this->cart->count(Auth::id(), $this->sessionId());
    }

    public function subtotal(): float
    {
        return round($this->items()->sum(fn ($item) => $item->line_total), 2);
    }

    public function mergeGuestCartOnLogin(int $userId): void
    {
        $sessionId = $this->sessionId();

        if ($sessionId) {
            $this->cart->mergeGuestCartIntoUser($sessionId, $userId);
        }
    }

    protected function sessionId(): ?string
    {
        if (Auth::check()) {
            return null;
        }

        if (! session()->has('cart_session_id')) {
            session(['cart_session_id' => (string) Str::uuid()]);
        }

        return session('cart_session_id');
    }
}
