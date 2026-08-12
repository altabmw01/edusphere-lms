<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MANAGER]);
    }

    public function view(User $user, Book $book): bool
    {
        return $user->isAdmin() || $user->isManager() || $book->added_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, Book $book): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function download(User $user, Book $book): bool
    {
        return $user->hasPurchasedBook($book->id) || $user->isAdmin();
    }
}
