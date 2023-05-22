@extends('layouts.guest')

@section('content')

    <section class="auth">
        <div>
            <div class="warning">
                <h1>@lang($message ?? '')</h1>

                <p>@lang("There was an error while trying to complete the last action. Use your back button to try again, or contact our support desk at :helpdesk or :phone", [ 'helpdesk' => config('app.helpdesk_email'), 'phone' => config('app.helpdesk_phone') ])</p>

                <p class="de-emphazised">({{now()}})</p>
            </div>
        </div>
    </section>
@endsection
