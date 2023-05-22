<footer class="no-print">
    <div>
        <span lang="nl">@lang('De Rijksoverheid. Voor Nederland')</span>
        <nav aria-label="@lang('Footer Navigation')">
            <ul>
            </ul>
        </nav>

        <div class="meta">
            <p>
                @lang('Version')
                <span id="application_version">{{ App\Http\Kernel::applicationVersion() }}</span>
            </p>
        </div>
    </div>
</footer>
