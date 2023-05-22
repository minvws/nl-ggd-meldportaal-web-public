@props(['element' => 'div', 'only' => null])

@foreach($only ?? [
    /* Messages from the Application */
    \App\Models\FlashMessage::ERROR,
    \App\Models\FlashMessage::INFO,
    \App\Models\FlashMessage::PRIMARY,
    \App\Models\FlashMessage::SUCCESS,
    \App\Models\FlashMessage::WARNING,
    /* Messages from Laravel or Packages */
    'errors',
    'status',
    'status-confirmation',
    'status-error',
] as $messageType)
    @if ($messages = Session::get($messageType))
        <{{ $element }}
            class="{{ \App\Models\FlashMessage::getCssClassFor($messageType) }}"
            data-message-type="{{ $messageType }}"
            role="status"
        >
        {{-- @TODO: Add logic to close a message ?
            <button type="button" class="ro-icon ro-icon-close action-button" data-dismiss="error">@lang('Close')</button>
        --}}
            @if (is_scalar($messages))
                @lang($messages)
            @elseif(is_array($messages))
                <ul>
                    @foreach ($messages as $message)
                        <li>@lang($message)</li>
                    @endforeach
                </ul>
            @elseif ($messages->any())
                <ul>
                    @foreach ($messages->all() as $message)
                        <li>@lang($message)</li>
                    @endforeach
                </ul>
            @else
                {{--
                    @FIXME: What else is there that we want to deal with?
                    {{ dd($messages) }}
                --}}
            @endif
        </{{ $element }}>
    @endif
@endforeach
@if ($messages = Session::get('debug'))
    <div class="debug" style="background-color: #DDD;border: 1px solid #999;color: black;">
        <h2 style="margin-top: 0;">Debug Info</h2>
        @if (is_array($messages))
            <ul>
                @foreach ($messages as $message)
                    <li>@lang($message)</li>
                @endforeach
            </ul>
        @else
            @lang($messages)
        @endif
    </div>
@endif
