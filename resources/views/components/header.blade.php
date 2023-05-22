<header class="no-print">
    <a href="#main-content" class="button focus-only">@lang('Skip to content')</a>

    <div class="page-meta no-print">
        @auth
        <div class="login-meta">
            <p>@lang("Logged in as"):
                @if (Auth::user()->canChangePassword())
                <a href="{{route('profile.show')}}">{{ Auth::user()->name }}</a>
                @else
                {{ Auth::user()->name }}
                @endif
            </p>
        </div>
        @endauth
        <div class="language">
            <a lang="nl" hreflang="nl" href="{{route('changelang', ['locale' => 'nl'])}}">Nederlands</a>
            <a lang="en" hreflang="en" href="{{route('changelang', ['locale' => 'en'])}}">English</a>
        </div>
    </div>

    @if(config('app.env') == "production")
        <a href="/" class="ro-logo" aria-label="@lang('Logo GGD-GHOR, go to the homepage')">
            <img src="/img/ggdghor-logo.svg" alt="">
        </a>
    @elseif(!Session::get('logo') )
        <a
            href="/?logo=1"
            class="ro-logo"
            aria-label="@lang('Logo GGD-GHOR, go to the homepage')">
            <img src="/img/staging.gif" alt="" />
            @lang('Staging environment')
        </a>
    @else
        <a
            href="/?logo=0"
            class="ro-logo"
            aria-label="@lang('Logo GGD-GHOR, go to the homepage')">
            <img src="/img/ggdghor-logo.svg" alt="" />
        </a>
    @endif

    {{ $slot }}
</header>
