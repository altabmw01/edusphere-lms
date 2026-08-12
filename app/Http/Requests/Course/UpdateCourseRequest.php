<?php

namespace App\Http\Requests\Course;

class UpdateCourseRequest extends StoreCourseRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('course'));
    }

    public function rules(): array
    {
        $rules = parent::rules();
        $rules['thumbnail'] = ['nullable', 'image', 'max:2048'];
        $rules['banner'] = ['nullable', 'image', 'max:4096'];

        return $rules;
    }
}
