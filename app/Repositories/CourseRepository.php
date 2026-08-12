<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseRepository implements CourseRepositoryInterface
{
    public function paginatePublished(
        array $filters = [],
        int $perPage = 9
    ): LengthAwarePaginator {
        $query = Course::query()
            ->where('status', 'published')
            ->with(['category', 'teacher']);

        $this->applyFilters($query, $filters);

        return $query
            ->latest('published_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForTeacher(
        int $teacherId,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Course::query()
            ->where('teacher_id', $teacherId)
            ->with('category')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Course::query()
            ->with(['category', 'teacher']);

        $this->applyFilters($query, $filters);

        return $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findBySlug(string $slug): ?Course
    {
        return Course::query()
            ->with(['category', 'teacher'])
            ->where('slug', $slug)
            ->first();
    }

    public function find(int $id): ?Course
    {
        return Course::find($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);

        return $course->fresh();
    }

    public function delete(Course $course): bool
    {
        return (bool) $course->delete();
    }

    public function featured(int $limit = 6): Collection
    {
        return Course::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->with(['category', 'teacher'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function related(
        Course $course,
        int $limit = 3
    ): Collection {
        return Course::query()
            ->where('status', 'published')
            ->where('id', '!=', $course->id)
            ->where('category_id', $course->category_id)
            ->with(['category', 'teacher'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_featured'])) {
            $query->where(
                'is_featured',
                (bool) $filters['is_featured']
            );
        }

        if (isset($filters['is_trending'])) {
            $query->where(
                'is_trending',
                (bool) $filters['is_trending']
            );
        }
    }
}