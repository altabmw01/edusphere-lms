<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Book::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'cover' => ['nullable', 'image', 'max:2048'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'pages' => ['required', 'integer', 'min:1'],
            'language' => ['required', 'string', 'max:40'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'edition' => ['nullable', 'string', 'max:100'],
            'isbn' => ['nullable', 'string', 'max:40'],
            'is_featured' => ['boolean'],
            'status' => ['required', 'in:draft,published'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
