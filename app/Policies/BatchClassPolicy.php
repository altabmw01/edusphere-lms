<?php

namespace App\Policies;

use App\Models\BatchClass;
use App\Models\User;

class BatchClassPolicy
{
    public function update(User $user, BatchClass $batchClass): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $batchClass->teacher_id === $user->id);
    }

    public function delete(User $user, BatchClass $batchClass): bool
    {
        return $this->update($user, $batchClass);
    }
}
