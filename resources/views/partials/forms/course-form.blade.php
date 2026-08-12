@php($course = $course ?? null)

<div class="row g-4">
    <div class="col-lg-8">
        <div class="filter-card">
            <h6 class="fw-bold mb-3">Basic Information</h6>
            <x-form.input name="title" label="Course Title" :value="$course?->title" required />
            <div class="row">
                <div class="col-md-6">
                    <x-form.select name="category_id" label="Category" :options="$categories->pluck('name', 'id')" :value="$course?->category_id" required />
                </div>
                @if($showTeacherField ?? false)
                <div class="col-md-6">
                    <x-form.select name="teacher_id" label="Teacher" :options="$teachers->pluck('name', 'id')" :value="$course?->teacher_id" required />
                </div>
                @endif
            </div>
            <x-form.textarea name="description" label="Description" rows="5" :value="$course?->description" required />
            <x-form.textarea name="what_you_will_learn" label="What You Will Learn (one per line)" rows="4" :value="$course?->what_you_will_learn" hint="Each line becomes a checklist item." />
            <x-form.textarea name="requirements" label="Requirements (one per line)" rows="3" :value="$course?->requirements" />
            <x-form.textarea name="target_audience" label="Target Audience" rows="2" :value="$course?->target_audience" />
        </div>

        <div class="filter-card">
            <h6 class="fw-bold mb-3">Media</h6>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label-custom">Thumbnail</label>
                    @if($course?->thumbnail)<img src="{{ $course->thumbnail_url }}" class="rounded-3 mb-2 d-block" width="140" alt="Current thumbnail">@endif
                    <input type="file" name="thumbnail" class="form-control form-control-custom" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Banner</label>
                    @if($course?->banner)<img src="{{ asset('storage/'.$course->banner) }}" class="rounded-3 mb-2 d-block" width="140" alt="Current banner">@endif
                    <input type="file" name="banner" class="form-control form-control-custom" accept="image/*">
                </div>
            </div>
        </div>

        <div class="filter-card">
            <h6 class="fw-bold mb-3">SEO</h6>
            <x-form.input name="meta_title" label="Meta Title" :value="$course?->meta_title" />
            <x-form.textarea name="meta_description" label="Meta Description" rows="2" :value="$course?->meta_description" />
        </div>
    </div>

    <div class="col-lg-4">
        <div class="filter-card">
            <h6 class="fw-bold mb-3">Pricing</h6>
            <x-form.input name="price" type="number" step="0.01" label="Price ({{ config('lms.currency_symbol') }})" :value="$course?->price" required />
            <x-form.input name="discount_price" type="number" step="0.01" label="Discount Price ({{ config('lms.currency_symbol') }})" :value="$course?->discount_price" />
        </div>

        <div class="filter-card">
            <h6 class="fw-bold mb-3">Details</h6>
            <x-form.select name="level" label="Level" :options="['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'all_levels' => 'All Levels']" :value="$course?->level" required />
            <x-form.input name="language" label="Language" :value="$course?->language ?? 'English'" required />
            <x-form.input name="duration_minutes" type="number" label="Duration (minutes)" :value="$course?->duration_minutes" required />
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="has_certificate" value="1" id="has_certificate" @checked($course?->has_certificate ?? true)>
                <label class="form-check-label small" for="has_certificate">Issue Certificate</label>
            </div>
        </div>

        <div class="filter-card">
            <h6 class="fw-bold mb-3">Publishing</h6>
            <x-form.select name="status" label="Status" :options="['draft' => 'Draft', 'pending' => 'Pending Review', 'published' => 'Published']" :value="$course?->status ?? 'draft'" required />
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked($course?->is_featured)>
                <label class="form-check-label small" for="is_featured">Featured</label>
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="is_trending" value="1" id="is_trending" @checked($course?->is_trending)>
                <label class="form-check-label small" for="is_trending">Trending</label>
            </div>
            <button class="btn btn-brand w-100" type="submit">{{ $course ? 'Update Course' : 'Create Course' }}</button>
        </div>
    </div>
</div>
