<?php

namespace App\Repositories\Eloquent;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseRepository implements CourseRepositoryInterface
{
    public function __construct(protected Course $model)
    {
    }

    public function paginatePublished(array $filters = [], int $perPage = 9): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->published()
            ->with(['category'])
            ->search($filters['search'] ?? null);

        if (! empty($filters['category_id'])) {
            $query->ofCategory((int) $filters['category_id']);
        }

        if (! empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (! empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $sort = $filters['sort'] ?? 'popular';
        match ($sort) {
            'newest' => $query->latest('published_at'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating_avg'),
            default => $query->orderByDesc('students_count'),
        };

        return $query->paginate($perPage)->withQueryString();
    }


    public function paginateAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['category'])->search($filters['search'] ?? null);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findBySlug(string $slug): ?Course
    {
        return $this->model->with(['category', 'sections.lessons'])
            ->where('slug', $slug)
            ->first();
    }

    public function find(int $id): ?Course
    {
        return $this->model->find($id);
    }

    public function create(array $data): Course
    {
        return $this->model->create($data);
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);

        return $course->refresh();
    }

    public function delete(Course $course): bool
    {
        return (bool) $course->delete();
    }

    public function featured(int $limit = 6): Collection
    {
        return $this->model->published()->featured()->with('category')->limit($limit)->get();
    }

    public function related(Course $course, int $limit = 3): Collection
    {
        return $this->model->published()
            ->ofCategory($course->category_id)
            ->where('id', '!=', $course->id)
            ->limit($limit)
            ->get();
    }
}
