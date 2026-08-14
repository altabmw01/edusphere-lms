<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CurriculumController extends Controller
{
    use AuthorizesRequests;
	
    public function edit(Course $course): View
    {
        $this->authorize('manageCurriculum', $course);

        return view('admin.courses.curriculum', ['course' => $course->load('sections.lessons')]);
    }

    public function storeSection(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('manageCurriculum', $course);

        $data = $request->validate(['title' => ['required', 'string', 'max:255']]);
        $data['sort_order'] = $course->sections()->max('sort_order') + 1;

        $course->sections()->create($data);

        return back()->with('status', 'Section added.');
    }

    public function storeLesson(Request $request, Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorize('manageCurriculum', $course);

        $data = $this->validatedLesson($request);

        if ($data['type'] === 'pdf' && ! $request->hasFile('content_file')) {
            return back()->withErrors(['content_file' => 'Please upload a PDF file for this lesson.'])->withInput();
        }

        $data['sort_order'] = $section->lessons()->max('sort_order') + 1;
        $data['course_id'] = $course->id;

        if ($request->hasFile('content_file')) {
            $data['content_path'] = $request->file('content_file')->store('courses/lessons', 'public');
        }

        $section->lessons()->create($data);

        $course->increment('lessons_count');
        $course->update(['duration_minutes' => $course->lessons()->sum('duration_minutes')]);

        return back()->with('status', 'Lesson added.');
    }

    public function updateLesson(Request $request, Course $course, CourseLesson $lesson): RedirectResponse
    {
        $this->authorize('manageCurriculum', $course);

        $data = $this->validatedLesson($request);

        if ($data['type'] === 'pdf' && ! $request->hasFile('content_file') && ! $lesson->content_path) {
            return back()->withErrors(['content_file' => 'Please upload a PDF file for this lesson.'])->withInput();
        }

        if ($request->hasFile('content_file')) {
            if ($lesson->content_path) {
                Storage::disk('public')->delete($lesson->content_path);
            }
            $data['content_path'] = $request->file('content_file')->store('courses/lessons', 'public');
        } elseif ($data['type'] === 'pdf') {
            // Keep the existing file when the type is still PDF and nothing new was uploaded.
            $data['content_path'] = $lesson->content_path;
        } else {
            // Switched away from PDF — the old file no longer applies.
            if ($lesson->content_path) {
                Storage::disk('public')->delete($lesson->content_path);
            }
            $data['content_path'] = null;
        }

        $lesson->update($data);

        $course->update(['duration_minutes' => $course->lessons()->sum('duration_minutes')]);

        return back()->with('status', 'Lesson updated.');
    }

    public function destroyLesson(Course $course, CourseLesson $lesson): RedirectResponse
    {
        $this->authorize('manageCurriculum', $course);

        if ($lesson->content_path) {
            Storage::disk('public')->delete($lesson->content_path);
        }

        $lesson->delete();
        $course->decrement('lessons_count');
        $course->update(['duration_minutes' => $course->lessons()->sum('duration_minutes')]);

        return back()->with('status', 'Lesson removed.');
    }

    public function destroySection(Course $course, CourseSection $section): RedirectResponse
    {
        $this->authorize('manageCurriculum', $course);

        $section->delete();

        return back()->with('status', 'Section removed.');
    }

    /**
     * Lesson fields are conditional on type:
     * - video → a YouTube link/code OR a Vimeo link/code (exactly one platform)
     * - pdf   → an uploaded PDF file (presence is checked by the caller, since
     *           it's only required on create — an update may keep the existing file)
     * - text  → a text body
     * - quiz  → treated like text for now (a description/instructions body)
     */
    protected function validatedLesson(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:video,text,pdf,quiz'],
            'youtube_input' => [
                'nullable', 'string', 'max:500',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('#^https?://#i', $value) && ! preg_match('/(youtube\.com|youtu\.be)/i', $value)) {
                        $fail('This doesn\'t look like a valid YouTube link. Paste the full YouTube link, or just the video code.');
                    }
                },
            ],
            'vimeo_input' => [
                'nullable', 'string', 'max:500',
                function ($attribute, $value, $fail) {
                    if ($value && preg_match('#^https?://#i', $value) && ! preg_match('/vimeo\.com/i', $value)) {
                        $fail('This doesn\'t look like a valid Vimeo link. Paste the full Vimeo link, or just the video code.');
                    }
                },
            ],
            'content_file' => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
            'content_text' => ['nullable', 'string', 'required_if:type,text'],
            'duration_minutes' => ['required', 'integer', 'min:0'],
            'is_preview' => ['boolean'],
        ], [], [
            'youtube_input' => 'YouTube link or code',
            'vimeo_input' => 'Vimeo link or code',
        ]);

        unset($data['content_file']); // never mass-assign the UploadedFile itself; callers handle storage separately

        $data['is_preview'] = $request->boolean('is_preview');
        $data['content_text'] = in_array($data['type'], ['text', 'quiz'], true) ? $data['content_text'] : null;

        $data['video_url'] = $data['type'] === 'video'
            ? $this->resolveVideoUrl($data['youtube_input'] ?? null, $data['vimeo_input'] ?? null)
            : null;

        unset($data['youtube_input'], $data['vimeo_input']);

        return $data;
    }

    /**
     * Builds a canonical video_url from whichever of the two inputs was filled
     * in — each accepts either a full platform link or just the bare video
     * code/ID. YouTube is used if both happen to be filled in.
     *
     * @throws \Illuminate\Validation\ValidationException if neither is provided
     */
    protected function resolveVideoUrl(?string $youtubeInput, ?string $vimeoInput): string
    {
        $youtubeInput = trim((string) $youtubeInput);
        $vimeoInput = trim((string) $vimeoInput);

        if ($youtubeInput !== '') {
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $youtubeInput, $m)) {
                $id = $m[1];
            } else {
                $id = $youtubeInput; // treat as a bare video code
            }

            return "https://www.youtube.com/watch?v={$id}";
        }

        if ($vimeoInput !== '') {
            if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $vimeoInput, $m)) {
                $id = $m[1];
            } else {
                $id = $vimeoInput; // treat as a bare video code
            }

            return "https://vimeo.com/{$id}";
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'youtube_input' => 'Please provide a YouTube link/code or a Vimeo link/code for this video lesson.',
        ]);
    }
}
