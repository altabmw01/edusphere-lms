<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CartRepositoryInterface
{
    public function getItems(?int $userId, ?string $sessionId): Collection;

    public function addItem(?int $userId, ?string $sessionId, string $type, int $purchasableId): void;

    public function removeItem(?int $userId, ?string $sessionId, int $cartItemId): void;

    public function updateQuantity(?int $userId, ?string $sessionId, int $cartItemId, int $quantity): void;

    public function clear(?int $userId, ?string $sessionId): void;

    public function mergeGuestCartIntoUser(string $sessionId, int $userId): void;

    public function count(?int $userId, ?string $sessionId): int;
}
