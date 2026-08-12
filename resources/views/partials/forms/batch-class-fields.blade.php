@php($class = $class ?? null)

<div class="row g-3">
    <div class="col-md-6">
        <x-form.select name="link_type_id" label="Meeting Platform" :options="$linkTypes->pluck('name', 'id')" :value="$class?->link_type_id" required />
    </div>
    <div class="col-md-6">
        <div class="form-check form-switch mt-4 pt-2">
            <input class="form-check-input" type="checkbox" name="status" value="1" id="classStatus{{ $class?->id ?? 'new' }}" @checked($class?->status ?? true)>
            <label class="form-check-label small" for="classStatus{{ $class?->id ?? 'new' }}">Active</label>
        </div>
    </div>
</div>

<x-form.input name="full_link" type="url" label="Full Meeting Link" :value="$class?->full_link" hint="e.g. https://zoom.us/j/1234567890" required />

<div class="row g-3">
    <div class="col-md-6"><x-form.input name="metting_code" label="Meeting Code / ID" :value="$class?->metting_code" /></div>
    <div class="col-md-6"><x-form.input name="metting_pass_code" label="Meeting Passcode" :value="$class?->metting_pass_code" /></div>
</div>

<div class="row g-3">
    <div class="col-md-6"><x-form.input name="class_start_time" type="datetime-local" label="Class Start" :value="$class?->class_start_time?->format('Y-m-d\TH:i')" required /></div>
    <div class="col-md-6"><x-form.input name="class_end_time" type="datetime-local" label="Class End" :value="$class?->class_end_time?->format('Y-m-d\TH:i')" required /></div>
</div>

<x-form.textarea name="class_note" label="Note for Students (optional)" rows="2" :value="$class?->class_note" />
