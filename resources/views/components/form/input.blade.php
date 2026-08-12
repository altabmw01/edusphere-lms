@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label-custom">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'form-control form-control-custom' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        @if($required) required @endif
    >
    @if($hint)<div class="form-text">{{ $hint }}</div>@endif
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
