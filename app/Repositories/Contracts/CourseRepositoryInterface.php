<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CourseRepositoryInterface
{
    public function paginatePublished(array $filters = [], int $perPage = 9): LengthAwarePaginator;

    public function paginateForTeacher(int $teacherId, int $perPage = 10): LengthAwarePaginator;

    public function paginateAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Course;

    public function find(int $id): ?Course;

    public function create(array $data): Course;

    public function update(Course $course, array $data): Course;

    public function delete(Course $course): bool;

    public function featured(int $limit = 6): Collection;

    public function related(Course $course, int $limit = 3): Collection;
}
