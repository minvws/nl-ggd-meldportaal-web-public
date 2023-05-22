@foreach($notifications as $notification)
<section
    id="general-notification-{{ $notification->id }}"
    role="alert"
    aria-live="polite"
    class="{{ $notification->notification_class }} no-print"
    role="group"
    aria-label="@lang($notification->notification_class)"
>
    <div>
        <p>
            <span>{{ $notification->readable_notification_class }}: {{ $notification->translated_title }}</span>
        </p>
        <p>{!! $notification->translated_description !!}</p>

        @if ($notification->needs_confirmation)
        <form method="POST" class="line-form general-notification-form" action="{{ route('general-notifications.markRead') }}">
            @csrf
            @error('resolvedIds')
            <p class="error">{{ $message }}</p>
            @enderror

            <div class="required">
                <x-required />
                <input class="field_resolved" id="resolved-checkbox-notification-{{ $notification->id }}" type="checkbox" name="{{ $notification->id }}">
                <label class="field_resolved_label" for="resolved-checkbox-notification-{{ $notification->id }}">@lang('I have read this message')</label>
            </div>

            <x-button disabled>@lang('Mark as read')</x-button>
            <input type="hidden" name="resolvedIds" class="resolvedIds" value="">
        </form>
        @endif
    </div>
</section>
@endforeach
