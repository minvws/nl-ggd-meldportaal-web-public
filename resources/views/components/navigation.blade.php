<section>
    <div>
        <nav data-open-label="Menu" data-close-label="Sluit menu" data-media="(min-width: 42rem)" aria-label="@lang('Main Navigation')">
            <ul>
                <x-nav-item :route="'home'">@lang('Homepage') </x-nav-item>
            </ul>
        </nav>

        @if (Request::user() && ! Request::user()->isUzi())
            <div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">@lang('Logout')</button>
                </form>
            </div>
        @endif
    </div>
</section>
