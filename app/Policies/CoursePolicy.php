<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_TEACHER, User::ROLE_MANAGER]);
    }

    public function view(User $user, Course $course): bool
    {
        return $user->isAdmin() || $user->isManager() || $course->teacher_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher() && $course->teacher_id === $user->id;
    }

    public function delete(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isTeacher() && $course->teacher_id === $user->id;
    }

    public function publish(User $user, Course $course): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function manageCurriculum(User $user, Course $course): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $course->teacher_id === $user->id);
    }
}
