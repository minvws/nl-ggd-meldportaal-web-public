@extends('layouts.guest')

@section('page-title', __('Login (2FA)'))

@section('content')
<section class="auth">
    <div>
        <h1>@lang('Login with email address')</h1>

        <p>
            @lang('Please confirm access to your account by entering the authentication code provided by your authenticator application.')
        </p>

        <form
            action="{{ route('two-factor.login') }}"
            autocomplete="off"
            class="horizontal-view"
            method="POST"
        >
            @csrf

            @if ($errors->any())
                <div role="group" aria-label="{{ __('Foutmelding')}}">
                    <p class="error" role="alert">
                        <span>@lang('Error:')</span> @lang($errors->first())
                    </p>
                </div>
            @endif

            <div>
                <label for="code">@lang('Code')</label>
                <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" required autofocus autocomplete="off" x-ref="code" />
            </div>

            <x-button>@lang('Login')</x-button>
        </form>
    </div>
</section>
@endsection
