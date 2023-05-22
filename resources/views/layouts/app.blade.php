<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex,nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @hasSection('page-title')
        <title>@yield('page-title') - {{ config('app.name', '') }}</title>
        @else
        <title>{{ config('app.name', '') }}</title>
        @endif
        <link rel="preload" media="screen and (min-width: 768px)" href="/huisstijl/img/ggdghor-logo.svg" as="image">
        <link rel="preload" href="/huisstijl/fonts/RO-SansWebText-Regular.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/huisstijl/fonts/RO-SansWebText-Bold.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="{{ url('/huisstijl/css/manon-ruby-red.css') }}">
        <link rel="stylesheet" href="{{ url('/css/app.css') }}">
        <link rel="shortcut icon" href="/huisstijl/img/favicon.ico">
        <link rel="icon" type="image/png" sizes="32x32" href="/huisstijl/img/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/huisstijl/img/favicon-16x16.png">
        <script defer src="{{ url('js/app.js') }}" nonce="{{ csp_nonce() }}"></script>
        @yield('head')
    </head>
    <body>
        <x-old-browser-error />
        <x-header>
            @includeFirst(['components.navigation-' . strtolower(config('app.mode')), 'components.navigation'])
        </x-header>

        <main id="main-content" tabindex="-1">
            @yield('content')
        </main>
        <x-footer></x-footer>
    </body>
</html>
