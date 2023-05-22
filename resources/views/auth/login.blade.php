@extends('layouts.guest')

@section('page-title', __("Login"))

@section('content')
    <section class="login">
        <div>
            <h1>@lang('Welcome to the GGD Reporting Portal Test Results')</h1>

            <p>@lang('The Reporting Portal Test Results is for all parties that perform corona tests outside the GGD test chain. In this portal, positive COVID-19 test results can be reported directly to the GGD.')</p>

            <x-flash />

            <div class="column-2">
@if (env('FEATURE_AUTH_UZI'))
                <div class="instructions">
                    <h2>@lang('Login with UZI card')</h2>
                    <p>@lang('Here you can login with your UZI card')</p>
                    <ol>
                        <li>@lang('Place your UZI card in the card reader and wait until the green light is lit.')</li>
                        <li>@lang('Click on the ":buttonText" button.', ['buttonText' => __('Login with UZI card')])</li>
                        <li>@lang('Select your certificate and press OK.')</li>
                        <li>@lang('Enter the PIN code for the selected certificate and press OK.')</li>
                    </ol>
                    <a href="{{ config('uzi.login_url') }}" class="button uzi-button">
                        <img src="{{ asset('img/uzipas.png') }}" alt="">
                        @lang('Login with UZI card')
                    </a>
                </div>
@endif

@if (env('FEATURE_AUTH_USERPASS'))
                <div class="instructions">
                    <h2>@lang('Login with email address')</h2>

                    @if (env('FEATURE_AUTH_UZI'))
                    <p>@lang('You can login using your email if you currently do not own a UZI-card')</p>
                    @endif

                    @include('auth.login-form')
                </div>
@endif

@if (env('FEATURE_AUTH_UZI'))
            <div class="explanation" role="group" aria-label="@lang('Explanation')">
                <span>@lang("Explanation"):</span>
                <p>
                    @lang('Are you seeing the message “Certificaat niet gevonden” or “Cannot establish a secure connection to server”?')
                </p>
                <p>
                    @lang('Check if you use the correct software for your UZI card.')
                </p>
                <p>
                    @lang('Download the') <a href="https://www.uziregister.nl/uzi-pas/activeer-en-installeer-uw-uzi-pas" target="_blank" rel="external">UZI card software</a>
                </p>

            </div>
@endif
        </div>
    </section>

@endsection
