@extends('layouts.guest')

@section('content')
    <section class="auth">
        <div>
            <div class="warning">
                <span>@lang("Warning"):</span>
                <h1>@lang($title)</h1>
                <p>@lang("Something went wrong. Please contact our support desk at :helpdesk or :phone", [ 'helpdesk' => config('app.helpdesk_email'), 'phone' => config('app.helpdesk_phone') ])</p>

                <p class="de-emphazised">({{now()}})</p>
            </div>
        </div>
    </section>
@endsection
