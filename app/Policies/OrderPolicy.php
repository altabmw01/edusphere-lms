<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_STUDENT]);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isManager() || $order->user_id === $user->id;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function cancel(User $user, Order $order): bool
    {
        return ($order->user_id === $user->id || $user->isAdmin() || $user->isManager())
            && $order->canBeCancelled();
    }
}
