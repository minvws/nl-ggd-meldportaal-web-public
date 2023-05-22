@props(['route', 'icon'])
<li>
    <a  {!! Route::currentRouteName() === $route ? ' aria-current="page"' : '' !!}
        @foreach($attributes as $name => $values) {{ $name}}="{{$values}}"
        @endforeach href="{{ route($route) }}"
    >{{ $slot }}</a>
</li>
