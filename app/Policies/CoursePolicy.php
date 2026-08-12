<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

/**
 * Courses are entirely admin-managed. Teachers have no ownership or editing
 * relationship to courses — their only connection to a course is indirect,
 * through being assigned to teach a Batch of it.
 */
class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function view(User $user, Course $course): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user, Course $course): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function manageCurriculum(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }
}
