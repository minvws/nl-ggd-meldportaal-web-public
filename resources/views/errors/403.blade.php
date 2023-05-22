@extends('layouts.guest')

@section('content')

    <section class="auth">
        <div>
            <div class="warning">
                <h1>@lang($message)</h1>

                <p>@lang("It's not possible to login with the given credentials. Please contact our support desk at :helpdesk or :phone", [ 'helpdesk' => config('app.helpdesk_email'), 'phone' => config('app.helpdesk_phone') ])</p>

@if (env('FEATURE_AUTH_UZI'))
    @php $uziInfo = App::get(\MinVWS\PUZI\UziReader::class)->getDataFromRequest($request); @endphp
            <table>
                <tr><td>@lang('UZI card detected:')</td><td>{{ $uziInfo ? __("Yes") : __("No") }}</td></tr>
@if ($uziInfo)
                <tr><td>@lang('Card number:')</td><td>{{ $uziInfo->getUziNumber() }} ({{ $uziInfo->getCardType() }})</td></tr>
                <tr><td>@lang('Subscription number:')</td><td>{{ $uziInfo->getSubscriberNumber() }}</td></tr>
                <tr><td>@lang('Name:')</td><td>{{ $uziInfo->getGivenName() }} {{ $uziInfo->getSurname() }}</td></tr>
                <tr><td>@lang('Role:')</td><td>{{ $uziInfo->getRole() }}</td></tr>
@endif
            </table>
@endif

                <p class="de-emphazised">({{now()}})</p>
            </div>
        </div>
    </section>
@endsection
