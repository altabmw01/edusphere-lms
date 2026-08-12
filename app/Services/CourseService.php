<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseService
{
    public function __construct(protected CourseRepositoryInterface $courses)
    {
    }

    public function browse(array $filters): LengthAwarePaginator
    {
        return $this->courses->paginatePublished($filters);
    }

    public function forTeacher(User $teacher): LengthAwarePaginator
    {
        return $this->courses->paginateForTeacher($teacher->id);
    }

    public function forAdmin(array $filters): LengthAwarePaginator
    {
        return $this->courses->paginateAll($filters);
    }

    public function show(string $slug): ?Course
    {
        return $this->courses->findBySlug($slug);
    }

    public function create(array $data, User $teacher, ?UploadedFile $thumbnail = null, ?UploadedFile $banner = null): Course
    {
        $data['teacher_id'] = $teacher->id;

        if ($thumbnail) {
            $data['thumbnail'] = $thumbnail->store('courses/thumbnails', 'public');
        }

        if ($banner) {
            $data['banner'] = $banner->store('courses/banners', 'public');
        }

        if (($data['status'] ?? null) === 'published') {
            $data['published_at'] = now();
        }

        return $this->courses->create($data);
    }

    public function update(Course $course, array $data, ?UploadedFile $thumbnail = null, ?UploadedFile $banner = null): Course
    {
        if ($thumbnail) {
            $this->deleteFile($course->thumbnail);
            $data['thumbnail'] = $thumbnail->store('courses/thumbnails', 'public');
        }

        if ($banner) {
            $this->deleteFile($course->banner);
            $data['banner'] = $banner->store('courses/banners', 'public');
        }

        if (($data['status'] ?? null) === 'published' && $course->status !== 'published') {
            $data['published_at'] = now();
        }

        return $this->courses->update($course, $data);
    }

    public function delete(Course $course): bool
    {
        $this->deleteFile($course->thumbnail);
        $this->deleteFile($course->banner);

        return $this->courses->delete($course);
    }

    public function featured(int $limit = 6)
    {
        return $this->courses->featured($limit);
    }

    public function related(Course $course, int $limit = 3)
    {
        return $this->courses->related($course, $limit);
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
