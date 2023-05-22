<button
    @if ($attributes->has('cypress-tag'))
        {{ cypress($attributes->get('cypress-tag')) }}
        @php
          unset($attributes['cypress-tag']);
        @endphp
    @endif
    {{ $attributes }}
>
    {{ $slot }}
</button>
