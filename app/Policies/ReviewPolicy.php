<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function moderate(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Only Admin/Manager can reply. Teachers have no course ownership to check
     * against anymore — their relationship to a course is only through Batches.
     */
    public function reply(User $user, Review $review): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}
