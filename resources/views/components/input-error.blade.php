@props(['for'])

@if ($errors->has($for))
    <p {{ $attributes->merge(['class' => 'error']) }}>
        <span>@lang('Error'):</span> @lang($errors->first($for))
    </p>
@else
    <p {{ $attributes->merge(['class' => 'error hidden']) }}></p>
@endif
