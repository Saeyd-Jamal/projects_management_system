@props([
    'type' => 'text',
    'value' => '',
    'name',
    'id' => null,
    'label'=>'',
])
@if ($label)
    <label class="form-label" for="{{$name}}">
        {{ $label }}
    </label>
@endif

<input
    type="{{$type}}"
    id="{{$id ?? $name}}"
    name="{{$name}}"
    value="{{old($name, $value)}}"
    {{$attributes->class([
        'form-control',
        'is-invalid' => $errors->has($name)
    ])}}
/>

{{-- Validation --}}
@error($name)
    <div class="invalid-feedback">
        {{$message}}
    </div>
@enderror
