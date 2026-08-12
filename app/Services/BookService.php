<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function __construct(protected BookRepositoryInterface $books)
    {
    }

    public function browse(array $filters): LengthAwarePaginator
    {
        return $this->books->paginatePublished($filters);
    }

    public function forAdmin(array $filters): LengthAwarePaginator
    {
        return $this->books->paginateAll($filters);
    }

    public function show(string $slug): ?Book
    {
        return $this->books->findBySlug($slug);
    }

    public function create(array $data, User $addedBy, ?UploadedFile $cover = null, ?UploadedFile $pdf = null): Book
    {
        $data['added_by'] = $addedBy->id;

        if ($cover) {
            $data['cover'] = $cover->store('books/covers', 'public');
        }

        if ($pdf) {
            $data['pdf_path'] = $pdf->store('books/files', 'public');
        }

        return $this->books->create($data);
    }

    public function update(Book $book, array $data, ?UploadedFile $cover = null, ?UploadedFile $pdf = null): Book
    {
        if ($cover) {
            $this->deleteFile($book->cover, 'public');
            $data['cover'] = $cover->store('books/covers', 'public');
        }

        if ($pdf) {
            $this->deleteFile($book->pdf_path, 'public');
            $data['pdf_path'] = $pdf->store('books/files', 'public');
        }

        return $this->books->update($book, $data);
    }

    public function delete(Book $book): bool
    {
        $this->deleteFile($book->cover, 'public');
        $this->deleteFile($book->pdf_path, 'public');

        return $this->books->delete($book);
    }

    public function featured(int $limit = 6)
    {
        return $this->books->featured($limit);
    }

    public function related(Book $book, int $limit = 3)
    {
        return $this->books->related($book, $limit);
    }

    protected function deleteFile(?string $path, string $disk): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
