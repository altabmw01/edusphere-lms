<?php

namespace App\Repositories\Eloquent;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BookRepository implements BookRepositoryInterface
{
    public function __construct(protected Book $model)
    {
    }

    public function paginatePublished(array $filters = [], int $perPage = 9): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->published()
            ->with('category')
            ->search($filters['search'] ?? null);

        if (! empty($filters['category_id'])) {
            $query->ofCategory((int) $filters['category_id']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        $sort = $filters['sort'] ?? 'popular';
        match ($sort) {
            'newest' => $query->latest(),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default => $query->orderByDesc('sales_count'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function paginateAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('category')->search($filters['search'] ?? null);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findBySlug(string $slug): ?Book
    {
        return $this->model->with('category', 'addedBy')->where('slug', $slug)->first();
    }

    public function find(int $id): ?Book
    {
        return $this->model->find($id);
    }

    public function create(array $data): Book
    {
        return $this->model->create($data);
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        return $book->refresh();
    }

    public function delete(Book $book): bool
    {
        return (bool) $book->delete();
    }

    public function featured(int $limit = 6): Collection
    {
        return $this->model->published()->featured()->with('category')->limit($limit)->get();
    }

    public function related(Book $book, int $limit = 3): Collection
    {
        return $this->model->published()
            ->ofCategory($book->category_id)
            ->where('id', '!=', $book->id)
            ->limit($limit)
            ->get();
    }
}
