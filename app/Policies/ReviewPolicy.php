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

    public function reply(User $user, Review $review): bool
    {
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        return $user->isTeacher() && $review->reviewable_type === \App\Models\Course::class
            && $review->reviewable?->teacher_id === $user->id;
    }
}
