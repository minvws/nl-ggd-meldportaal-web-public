@extends('layouts.app')

@section('content')
    <section class="form-view" id="declaration-section">
        <div>
            <h1>@lang('Questionnaire GGD Reporting Portal Test Results')</h1>
            <p>@lang('Enter the details and situation of the person who tested positive for COVID-19 below. Not all questions are mandatory, but we ask you to fill them in as completely as possible. This is necessary for the source and contact investigation, so that together we can get the pandemic under control. It is important to request explicit consent from the citizen before sending non-mandatory personal data.')</p>

            @if (Auth::user()->hasRole(\App\Role::SPECIMEN))
            <div class="explanation" role="group" aria-label="{{ __('Remark')}}">
                <span>@lang('Notice:')</span> @lang('You can only enter specimen tests and will not generate valid results')
            </div>
            @endif

            @if ($errors->any())
                <div role="group" aria-label="{{ __('Foutmelding')}}">
                    <p class="error" role="alert">
                        <span>@lang('Foutmelding:')</span> @lang('There are one or more problems found when validating the data. Please correct these fields and try again.')
                    </p>
                </div>
            @endif

            <p>@lang('With a * marked fields are required')</p>

            <form class="horizontal-view" id="contact_form" method="post" action="{{route('store.encrypted')}}">
                @csrf
                <input type="hidden" name="backend_public_key" value="{{ $backend_public_key }}">

                <fieldset>
                    <legend>@lang('Data test person')</legend>

                    <x-form-input :required=true id="initials" name="initials" />
                    <x-form-input :required=false id="insertion" name="insertion" />
                    <x-form-input :required=true id="surname" name="surname" />
                    <div>
                        <br/>
                        <br/>
                    </div>

                    <x-form-input :required=true id="postcode" name="postcode" />
                    <x-form-input :required=true id="house_number" name="house_number" />
                    <x-form-input :required=false id="house_letter" name="house_letter" />
                    <x-form-input :required=false id="house_number_addition" name="house_number_addition" />
                    <x-form-input :required=false id="house_number_designation" name="house_number_designation" />

                    <div id="postcode_notification_message" class="hidden" role="group" aria-label="waarschuwing">
                        <p class="warning" role="alert"><span>@lang('Warning:')</span> @lang('This postcode/house number combination does not yield a valid address. Check or manually enter the street/city.')</p>
                    </div>

                    <x-form-input :required=true id="street" name="street" />
                    <x-form-input :required=true id="city" name="city" />
                    <?php $list = json_encode(\Punic\Territory::getCountries(Config::get('app.locale'))); ?>
                    <x-form-select :required=true id="country" name="country" default="NL" :options=$list :notranslate=true/>


                    <div>
                        <br/>
                        <br/>
                    </div>

                    <?php $list = json_encode(['male' => 'gender_male', 'female' => 'gender_female', 'unspecified' => 'gender_unspecified', 'unknown' => 'gender_unknown']); ?>
                    <x-form-select :required=true id="gender" name="gender" :empty=true :options=$list/>

                    <x-form-input :required=false id="bsn" name="bsn" type="text"/>

                    <x-form-input :required=true id="birthdate" name="birthdate" type="text" placeholder="JJJJ-MM-DD" />
                    <div id="birthdate_notification_message" class="hidden" role="group" aria-label="waarschuwing">
                         <p class="warning" role="alert"><span>@lang('Warning:')</span> @lang('The entered date seems to be a vaccination date instead of a persons birthdate. Make sure this date is correct.')</p>
                     </div>

                    <div>
                        <br/>
                        <br/>
                    </div>

                    <x-form-input :required=true id="email" name="email" type="email"/>
                    <x-form-input :required=true id="phone" name="phone" type="phone"/>

                    <div>
                        <br/>
                        <br/>
                    </div>

                    <?php $list = json_encode(['yes' => 'option_yes', 'no' => 'option_no']); ?>
                    <x-form-select :required=true id="report_permission_gp" name="report_permission_gp" :empty=true :options=$list/>
                </fieldset>


                <div>
                    <br/>
                    <br/>
                </div>

                <fieldset>
                    <legend>@lang('Data applicant')</legend>

                    <x-form-input :autocomplete=true :required=true id="requesting_physician" name="requesting_physician" />
                    <div>
                        <br/>
                        <br/>
                    </div>

                    <x-form-select :required=true id="brand_used_test" name="brand_used_test"  :empty=true
                        :options=\App\Elements::asJson(\App\Elements::TEST_BRANDS) :notranslate=true />

                    <x-form-input :required=false id="involved_laboratory" name="involved_laboratory" />

                    <?php $list = json_encode(['yes' => 'option_yes', 'no' => 'option_no']); ?>
                    <x-form-select :required=true id="category_test_location" name="category_test_location"
                       :empty=true :options=\App\Elements::asJson(\App\Elements::TEST_LOCATIONS)/>

                    <x-form-input :required=true id="involved_company" name="involved_company" />

                    <div>
                        <br/>
                        <br/>
                    </div>

                    <?php $list = json_encode(['yes' => 'option_yes', 'no' => 'option_no']); ?>
                    <x-form-select :required=false id="test_after_contact_tracing" name="test_after_contact_tracing" :empty=true :options=$list/>
                    <x-form-input :required=true :hidden=true id="bco_number" name="bco_number" />
                    <div>
                        <br/>
                        <br/>
                    </div>

                    <?php $list = json_encode(['yes' => 'option_yes', 'no' => 'option_no']); ?>
                    <x-form-select :required=false id="return_from_trip" name="return_from_trip" :empty=true :options=$list/>
                    <?php $list = json_encode(\Punic\Territory::getCountries(Config::get('app.locale'))); ?>
                    <x-form-select :required=true :hidden=true id="country_stay" name="country_stay"  :empty=true
                        :options=\App\Elements::asJson(\App\Elements::COUNTRIES) :notranslate=true />
                    <x-form-input :required=true :hidden=true id="flight_number" name="flight_number" />
                    <div>
                        <br/>
                        <br/>
                    </div>

                    <x-form-checkbox :required=true id="current_symptoms" name="current_symptoms" :options=\App\Elements::asJson(\App\Elements::SYMPTOMS) :notranslate=true/>
                    <div>
                        <br/>
                        <br/>
                    </div>

                    <?php $list = json_encode(['known' => 'option_known', 'estimated' => 'option_estimated', 'unknown' => 'option_unknown']); ?>
                    <x-form-select :required=true id="first_day_of_illness_known" name="first_day_of_illness_known" :empty=true :options=$list/>
                    <x-form-input :required=true :hidden=true id="first_day_of_illness_date" name="first_day_of_illness_date" type="date"/>
                    <div>
                        <br/>
                        <br/>
                    </div>

                    <input type="hidden" id="forwarded_by_coronamelder_app" name="forwarded_by_coronamelder_app" value="doesnt_use_the_app" />
                    <input type="hidden" id="date_of_notification_coronamelder_app" name="date_of_notification_coronamelder_app"  value="" />
                    <input type="hidden" id="date_of_contact_coronamelder_app" name="date_of_contact_coronamelder_app" value="" />

                    <x-form-checkbox :required=false id="last_two_weeks_worked_as_at_in" name="last_two_weeks_worked_as_at_in"
                      :options=\App\Elements::asJson(\App\Elements::ENVIRONMENT) :notranslate=true/>
                    <x-form-select :required=true :hidden=true id="caregiver_type" name="caregiver_type"
                        :empty=true :options=\App\Elements::asJson(\App\Elements::CAREGIVER) :notranslate=true />
                    <x-form-select :required=true :hidden=true id="contact_profession" name="contact_profession"
                        :empty=true :options=\App\Elements::asJson(\App\Elements::CONTACT_PROFESSION)/>
                    <div>
                        <br/>
                        <br/>
                    </div>

                    <?php $list = json_encode(['patient_client' => 'patient_gp_client_vvt_or_risk_group_patient_client', 'risk_group' => 'patient_gp_client_vvt_or_risk_group_risk_group', 'no' => 'option_no']); ?>
                    <x-form-select :required=true id="patient_gp_client_vvt_or_risk_group" name="patient_gp_client_vvt_or_risk_group" :empty=true :options=$list/>
                    <x-form-checkbox :required=true :hidden=true id="risk_group" name="risk_group" :options=\App\Elements::asJson(\App\Elements::RISK_GROUP)/>
                    <div>
                        <br/>
                        <br/>
                    </div>

                    <x-form-input :required=true id="date_of_sample_collection" name="date_of_sample_collection" type="date"/>
                    <x-form-input :required=false id="date_of_sample_collection_time" name="date_of_sample_collection_time" type="time" value="12:00"/>
                    <div id="date_of_sample_collection_notification_message" class="hidden" role="group" aria-label="waarschuwing">
                         <p class="warning" role="alert"><span>@lang('Warning:')</span> @lang("The entered date is too long ago. It looks like it's a birthdate. Make sure this date is correct.")</p>
                     </div>

                    <x-form-input :required=true id="date_of_test_result" name="date_of_test_result" type="date"/>
                    <div id="date_of_test_result_notification_message" class="hidden" role="group" aria-label="waarschuwing">
                         <p class="warning" role="alert"><span>@lang('Warning:')</span> @lang("The entered date is too long ago. It looks like it's a birthdate. Make sure this date is correct.")</p>
                     </div>

                    <?php $list = json_encode(['positive' => 'test_result_positive']); ?>
                    <x-form-select :required=true id="test_result" name="test_result" :empty=true :options=$list/>
                </fieldset>

                <fieldset>
                    <div>
                        <button type="submit" id="submitForm">@lang('Save report data')</button>
                    </div>
                </fieldset>

            </form>
        </div>
    </section>
@endsection
