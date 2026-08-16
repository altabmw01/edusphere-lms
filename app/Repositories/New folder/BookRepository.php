<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BookRepository implements BookRepositoryInterface
{
    public function paginatePublished(
        array $filters = [],
        int $perPage = 9
    ): LengthAwarePaginator {
        $query = Book::query()
            ->where('status', 'published');

        $this->applyFilters($query, $filters);

        return $query
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Book::query();

        $this->applyFilters($query, $filters);

        return $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findBySlug(string $slug): ?Book
    {
        return Book::query()
            ->where('slug', $slug)
            ->first();
    }

    public function find(int $id): ?Book
    {
        return Book::find($id);
    }

    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        return $book->fresh();
    }

    public function delete(Book $book): bool
    {
        return (bool) $book->delete();
    }

    public function featured(int $limit = 6): Collection
    {
        return Book::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function related(Book $book, int $limit = 3): Collection
    {
        return Book::query()
            ->where('status', 'published')
            ->where('id', '!=', $book->id)
            ->where('category_id', $book->category_id)
            ->latest('created_at')
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

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_featured'])) {
            $query->where(
                'is_featured',
                (bool) $filters['is_featured']
            );
        }
    }
}