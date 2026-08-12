@php($batch = $batch ?? null)
@php($currentType = old('batchable_type', $batch?->batchable_type === \App\Models\Book::class ? 'book' : 'course'))
@php($selectedDays = old('batch_days', $batch?->batch_days ?? []))

<div class="row g-4">
    <div class="col-lg-8">
        <div class="filter-card">
            <h6 class="fw-bold mb-3">Course or Book</h6>
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label-custom">Type</label>
                    <select name="batchable_type" id="batchableType" class="form-select form-control-custom" required onchange="toggleBatchableOptions()">
                        <option value="course" @selected($currentType === 'course')>Course</option>
                        <option value="book" @selected($currentType === 'book')>Book</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label-custom">Select Course</label>
                    <select name="batchable_id" id="courseSelect" class="form-select form-control-custom">
                        <option value="">Choose a course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected($currentType === 'course' && (string) old('batchable_id', $batch?->batchable_id) === (string) $course->id)>{{ $course->title }}</option>
                        @endforeach
                    </select>
                    <label class="form-label-custom mt-2 d-none" id="bookSelectLabel">Select Book</label>
                    <select name="batchable_id_book" id="bookSelect" class="form-select form-control-custom d-none">
                        <option value="">Choose a book</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" @selected($currentType === 'book' && (string) old('batchable_id', $batch?->batchable_id) === (string) $book->id)>{{ $book->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <h6 class="fw-bold mb-3">Batch Details</h6>
            <div class="row">
                <div class="col-md-6"><x-form.input name="batch_number" label="Batch Number" :value="$batch?->batch_number" required /></div>
                <div class="col-md-6"><x-form.input name="batch_name" label="Batch Name" :value="$batch?->batch_name" required /></div>
            </div>
            <div class="row">
                <div class="col-md-6"><x-form.select name="teacher_id" label="Assign Teacher" :options="$teachers->pluck('name', 'id')" :value="$batch?->teacher_id" required /></div>
                <div class="col-md-6"><x-form.select name="batch_level_id" label="Batch Level" :options="$batchLevels->pluck('name', 'id')" :value="$batch?->batch_level_id" placeholder="No level" /></div>
            </div>
        </div>

        <div class="filter-card mb-0">
            <h6 class="fw-bold mb-3">Schedule</h6>
            <div class="row">
                <div class="col-md-6"><x-form.input name="class_start_time" type="time" label="Daily Class Start Time" :value="$batch?->class_start_time?->format('H:i')" required /></div>
                <div class="col-md-6"><x-form.input name="class_end_time" type="time" label="Daily Class End Time" :value="$batch?->class_end_time?->format('H:i')" required /></div>
            </div>
            <label class="form-label-custom">Class Days</label>
            <div class="d-flex flex-wrap gap-3 mb-3">
                @foreach($days as $day)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="batch_days[]" value="{{ $day }}" id="day{{ $day }}" @checked(in_array($day, $selectedDays))>
                        <label class="form-check-label small" for="day{{ $day }}">{{ $day }}</label>
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-md-6"><x-form.input name="batch_started_date" type="date" label="Batch Start Date" :value="$batch?->batch_started_date?->format('Y-m-d')" required /></div>
                <div class="col-md-6"><x-form.input name="batch_end_date" type="date" label="Batch End Date" :value="$batch?->batch_end_date?->format('Y-m-d')" /></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="filter-card">
            <h6 class="fw-bold mb-3">Capacity &amp; Type</h6>
            <x-form.input name="student_limit" type="number" label="Student Limit" :value="$batch?->student_limit ?? 30" required />
            <x-form.select name="free_or_paid" label="Batch Type" :options="[1 => 'Paid', 0 => 'Free']" :value="$batch?->free_or_paid ?? 1" />
        </div>

        <div class="filter-card">
            <h6 class="fw-bold mb-3">Visibility</h6>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="upcoming_status" value="1" id="upcoming_status" @checked($batch?->upcoming_status)>
                <label class="form-check-label small" for="upcoming_status">Mark as Upcoming</label>
            </div>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="hide_batch" value="1" id="hide_batch" @checked($batch?->hide_batch)>
                <label class="form-check-label small" for="hide_batch">Hide Batch (internal only)</label>
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="status" value="1" id="status" @checked($batch?->status ?? true)>
                <label class="form-check-label small" for="status">Active</label>
            </div>
            <button class="btn btn-brand w-100" type="submit">{{ $batch ? 'Update Batch' : 'Create Batch' }}</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleBatchableOptions() {
    var type = document.getElementById('batchableType').value;
    var courseSelect = document.getElementById('courseSelect');
    var bookSelect = document.getElementById('bookSelect');
    var bookLabel = document.getElementById('bookSelectLabel');

    if (type === 'book') {
        courseSelect.classList.add('d-none');
        courseSelect.name = '';
        bookSelect.classList.remove('d-none');
        bookLabel.classList.remove('d-none');
        bookSelect.name = 'batchable_id';
    } else {
        bookSelect.classList.add('d-none');
        bookLabel.classList.add('d-none');
        bookSelect.name = '';
        courseSelect.classList.remove('d-none');
        courseSelect.name = 'batchable_id';
    }
}
document.addEventListener('DOMContentLoaded', toggleBatchableOptions);
</script>
@endpush
