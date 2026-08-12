<?php

namespace App\Policies;

use App\Models\Batch;
use App\Models\User;

class BatchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, Batch $batch): bool
    {
        return $user->isAdmin() || $batch->teacher_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Batch $batch): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Batch $batch): bool
    {
        return $user->isAdmin();
    }

    /** Only the assigned teacher (or admin) may manage class links for a batch. */
    public function manageClasses(User $user, Batch $batch): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $batch->teacher_id === $user->id);
    }
}
