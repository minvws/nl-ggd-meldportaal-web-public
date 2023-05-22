@extends('layouts.app')

@section('content')
    <section class="form-view" id="declaration-section">
        <div id="confirmed_page">

            <h1>@lang('Questionnaire GGD Reporting Portal Test Results')</h1>

            <div class="confirmation" role="group" aria-label="Bevestiging">
                <span>Bevestiging:</span>
                <h1>@lang('Test report is successfully submitted.')</h1>
                <p>@lang('success_message')</p>
            </div>

            <a class="button" href="{{ route('home') }}">@lang('Submit new report')</a>
        </div>
    </section>
@endsection
