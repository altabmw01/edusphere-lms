<?php

namespace App\Repositories\Contracts;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BookRepositoryInterface
{
    public function paginatePublished(array $filters = [], int $perPage = 9): LengthAwarePaginator;

    public function paginateAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Book;

    public function find(int $id): ?Book;

    public function create(array $data): Book;

    public function update(Book $book, array $data): Book;

    public function delete(Book $book): bool;

    public function featured(int $limit = 6): Collection;

    public function related(Book $book, int $limit = 3): Collection;
}
