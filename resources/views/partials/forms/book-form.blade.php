@php($book = $book ?? null)

<div class="row g-4">
    <div class="col-lg-8">
        <div class="filter-card">
            <h6 class="fw-bold mb-3">Basic Information</h6>
            <x-form.input name="title" label="Book Title" :value="$book?->title" required />
            <div class="row">
                <div class="col-md-6"><x-form.input name="author" label="Author" :value="$book?->author" required /></div>
                <div class="col-md-6"><x-form.select name="category_id" label="Category" :options="$categories->pluck('name', 'id')" :value="$book?->category_id" required /></div>
            </div>
            <x-form.textarea name="description" label="Description" rows="5" :value="$book?->description" required />
        </div>

        <div class="filter-card">
            <h6 class="fw-bold mb-3">Media &amp; File</h6>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label-custom">Cover Image</label>
                    @if($book?->cover)<img src="{{ $book->cover_url }}" class="rounded-3 mb-2 d-block" width="120" alt="Current cover">@endif
                    <input type="file" name="cover" class="form-control form-control-custom" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">PDF File</label>
                    @if($book?->pdf_path)<p class="small text-success mb-2"><i class="bi bi-file-earmark-pdf"></i> File uploaded</p>@endif
                    <input type="file" name="pdf" class="form-control form-control-custom" accept="application/pdf">
                </div>
            </div>
        </div>

        <div class="filter-card mb-0">
            <h6 class="fw-bold mb-3">SEO</h6>
            <x-form.input name="meta_title" label="Meta Title" :value="$book?->meta_title" />
            <x-form.textarea name="meta_description" label="Meta Description" rows="2" :value="$book?->meta_description" />
        </div>
    </div>

    <div class="col-lg-4">
        <div class="filter-card">
            <h6 class="fw-bold mb-3">Pricing</h6>
            <x-form.input name="price" type="number" step="0.01" label="Price ({{ config('lms.currency_symbol') }})" :value="$book?->price" required />
            <x-form.input name="discount_price" type="number" step="0.01" label="Discount Price ({{ config('lms.currency_symbol') }})" :value="$book?->discount_price" />
        </div>

        <div class="filter-card">
            <h6 class="fw-bold mb-3">Book Details</h6>
            <x-form.input name="pages" type="number" label="Pages" :value="$book?->pages" required />
            <x-form.input name="language" label="Language" :value="$book?->language ?? 'English'" required />
            <x-form.input name="publisher" label="Publisher" :value="$book?->publisher" />
            <x-form.input name="edition" label="Edition" :value="$book?->edition" />
            <x-form.input name="isbn" label="ISBN" :value="$book?->isbn" />
        </div>

        <div class="filter-card">
            <h6 class="fw-bold mb-3">Publishing</h6>
            <x-form.select name="status" label="Status" :options="['draft' => 'Draft', 'published' => 'Published']" :value="$book?->status ?? 'draft'" required />
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked($book?->is_featured)>
                <label class="form-check-label small" for="is_featured">Featured</label>
            </div>
            <button class="btn btn-brand w-100" type="submit">{{ $book ? 'Update Book' : 'Create Book' }}</button>
        </div>
    </div>
</div>
