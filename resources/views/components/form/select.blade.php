@props(['name', 'label' => null, 'options' => [], 'value' => null, 'required' => false, 'placeholder' => 'Select an option'])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label-custom">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'form-select form-control-custom' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        @if($required) required @endif
    >
        @if($placeholder)<option value="">{{ $placeholder }}</option>@endif
        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected((string) old($name, $value) === (string) $optValue)>{{ $optLabel }}</option>
        @endforeach
    </select>
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
