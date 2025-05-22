<input 
    type="{{ $type }}"
    name="{{ $name }}"
    value="{{ $value }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
>
@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror

{{-- resources/views/components/button.blade.php --}}
<button type="{{ $type }}" {{ $attributes }}>
    {{ $slot }}
</button>
