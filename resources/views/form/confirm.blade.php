@extends('layouts.app')

@section('content')
    <section class="form-view" id="declaration-section" data-locale="{{ app()->getLocale() }}">
        <div >

            <h1>@lang('Questionnaire GGD Reporting Portal Test Results')</h1>

            <h1>@lang('Confirm report data')</h1>

            <dl id="confirm_table">
                <div><dt>@lang('label_initials')</dt><dd><span id="initials"></span></dd></div>
                <div><dt>@lang('label_insertion')</dt><dd><span id="insertion"></span></dd></div>
                <div><dt>@lang('label_surname')</dt><dd><span id="surname"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_postcode')</dt><dd><span id="postcode"></span></dd></div>
                <div><dt>@lang('label_house_number')</dt><dd><span id="house_number"></span></dd></div>
                <div><dt>@lang('label_house_letter')</dt><dd><span id="house_letter"></span></dd></div>
                <div><dt>@lang('label_house_number_addition')</dt><dd><span id="house_number_addition"></span></dd></div>
                <div><dt>@lang('label_house_number_designation')</dt><dd><span id="house_number_designation"></span></dd></div>
                <div><dt>@lang('label_street')</dt><dd><span id="street"></span></dd></div>
                <div><dt>@lang('label_city')</dt><dd><span id="city"></span></dd></div>
                <div><dt>@lang('label_country')</dt><dd><span id="country"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_gender')</dt><dd><span id="gender"></span></dd></div>
                <div><dt>@lang('label_bsn')</dt><dd><span id="bsn"></span></dd></div>
                <div><dt>@lang('label_birthdate')</dt><dd><span id="birthdate"></span></dd></div>
                <div><dt>@lang('label_email')</dt><dd><span id="email"></span></dd></div>
                <div><dt>@lang('label_phone')</dt><dd><span id="phone"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_report_permission_gp')</dt><dd><span id="report_permission_gp"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_requesting_physician')</dt><dd><span id="requesting_physician"></span></dd></div>
                <div><dt>@lang('label_brand_used_test')</dt><dd><span id="brand_used_test"></span></dd></div>
                <div><dt>@lang('label_involved_laboratory')</dt><dd><span id="involved_laboratory"></span></dd></div>
                <div><dt>@lang('label_category_test_location')</dt><dd><span id="category_test_location"></span></dd></div>
                <div><dt>@lang('label_involved_company')</dt><dd><span id="involved_company"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_test_after_contact_tracing')</dt><dd><span id="test_after_contact_tracing"></span></dd></div>
                <div><dt>@lang('label_bco_number')</dt><dd><span id="bco_number"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_return_from_trip')</dt><dd><span id="return_from_trip"></span></dd></div>
                <div><dt>@lang('label_country_stay')</dt><dd><span id="country_stay"></span></dd></div>
                <div><dt>@lang('label_flight_number')</dt><dd><span id="flight_number"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_current_symptoms')</dt><dd><span id="current_symptoms"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_first_day_of_illness_known')</dt><dd><span id="first_day_of_illness_known"></span></dd></div>
                <div><dt>@lang('label_first_day_of_illness_date')</dt><dd><span id="first_day_of_illness_date"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_forwarded_by_coronamelder_app')</dt><dd><span id="forwarded_by_coronamelder_app"></span></dd></div>
                <div><dt>@lang('label_date_of_notification_coronamelder_app')</dt><dd><span id="date_of_notification_coronamelder_app"></span></dd></div>
                <div><dt>@lang('label_date_of_contact_coronamelder_app')</dt><dd><span id="date_of_contact_coronamelder_app"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_last_two_weeks_worked_as_at_in')</dt><dd><span id="last_two_weeks_worked_as_at_in"></span></dd></div>
                <div><dt>@lang('label_caregiver_type')</dt><dd><span id="caregiver_type"></span></dd></div>
                <div><dt>@lang('label_contact_profession')</dt><dd><span id="contact_profession"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_patient_gp_client_vvt_or_risk_group')</dt><dd><span id="patient_gp_client_vvt_or_risk_group"></span></dd></div>
                <div><dt>@lang('label_risk_group')</dt><dd><span id="risk_group"></span></dd></div>

                <div>&nbsp;</div>
                <div><dt>@lang('label_date_of_sample_collection')</dt><dd><span id="date_of_sample_collection"></span> <span id="date_of_sample_collection_time"></span></dd></div>
                <div><dt>@lang('label_date_of_test_result')</dt><dd><span id="date_of_test_result"></span></dd></div>
                <div><dt>@lang('label_test_result')</dt><dd><span id="test_result"></span></dd></div>
            </dl>



            <form method="post" action="{{route('store.encrypted')}}" class="line-form">
                @csrf
                <input type="hidden" name="backend_public_key" value="{{ $backend_public_key }}">
                <input type="hidden" name="confirmed" value="1">
                <input type="hidden" name="formdata" value="{{ $formdata }}">

                <div class="button-container">
                    <a type="submit" id="backbutton" class="button secondary" href="{{route('home')}}">@lang('Back')</a>
                    <button type="submit" id="submitForm">@lang('Confirm report data')</button>
                </div>
            </form>

        </div>
    </section>
@endsection
