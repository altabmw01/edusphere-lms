@props(['name', 'label' => null, 'value' => null, 'required' => false, 'rows' => 4, 'hint' => null])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label-custom">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'form-control form-control-custom' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        @if($required) required @endif
    >{{ old($name, $value) }}</textarea>
    @if($hint)<div class="form-text">{{ $hint }}</div>@endif
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
