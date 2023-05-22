@extends('layouts.guest')

@section('page-title', __("Logout"))

@section('content')
    <section class="auth">
        <div>
            <h1>@lang('Logout')</h1>
            @if (Request::user())
                <p>@lang('Here you can log out')</p>

                <div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">@lang('Logout')</button>
                    </form>
                </div>
            @else
                <p>@lang("You are already logged out")</p>
                <div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <p>@lang('Here you can log in')</p>
                        <a href="{{ route('login') }}" class="button">@lang('Log in')</a>
                        <button type="submit">@lang('Logout again')</button>
                    </form>
                </div>
            @endif
        </div>
    </section>
@endsection
