<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Course::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'level' => ['required', 'in:beginner,intermediate,advanced,all_levels'],
            'language' => ['required', 'string', 'max:40'],
            'duration_minutes' => ['required', 'integer', 'min:0'],
            'has_certificate' => ['boolean'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'target_audience' => ['nullable', 'string'],
            'what_you_will_learn' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,pending,published'],
            'is_featured' => ['boolean'],
            'is_trending' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
