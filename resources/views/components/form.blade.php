<form action="{{ $action }}" method="{{ $method !== 'GET' ? 'POST' : 'GET' }}" {{ $attributes }}>
    @csrf
    @if ($method !== 'POST' && $method !== 'GET')
        @method($method)
    @endif
    {{ $slot }}
</form>
