@if ($paginator->hasPages())
    <br>
    <br>
    <div class="pagination">
        <p class="">
            {!! __('Showing') !!}
            <span class="">{{ $paginator->firstItem() }}</span>
            {!! __('to') !!}
            <span class="">{{ $paginator->lastItem() }}</span>
            {!! __('of') !!}
            <span class="">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        <ul>
            @if ($paginator->onFirstPage())
                <li><a href="#" class="disabled">&laquo;</a></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">&laquo;</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>{{ $element }}</li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><a href="#" aria-current="page" class="active">{{ $page }}</a></li>
                        @else
                            <li><a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">&raquo;</a></li>
            @else
                <li><a aria-disabled="true" aria-label="{{ __('pagination.next') }}" href="#" class="disabled">&raquo;</a></li>
            @endif
        </ul>
    </div>
@endif
