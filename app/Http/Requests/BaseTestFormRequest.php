<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Elements;
use App\Validators\NotInFuture;
use App\Validators\Postcode;
use App\Validators\ValidYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BaseTestFormRequest extends FormRequest
{
    public function rules(string $prefix = ''): array
    {
        return [
            $prefix . 'initials' => ['required', 'string', 'min:1', 'max:10'],
            $prefix . 'insertion' => ['nullable', 'string', 'min:1', 'max:10'],
            $prefix . 'surname' => ['required', 'string', 'min:1', 'max:50'],
            $prefix . 'postcode' => ['required', 'string', new Postcode(countryField: $prefix . 'country')],
            $prefix . 'house_number' => ['required', 'integer', 'min:1', 'max:99999'],
            $prefix . 'house_letter' => ['nullable', 'string', 'min:1', 'max:10'],
            $prefix . 'house_number_addition' => ['nullable', 'string', 'min:1', 'max:10'],
            $prefix . 'house_number_designation' => ['nullable', 'string', 'min:1', 'max:10'],
            $prefix . 'street' => ['required', 'string', 'min:1', 'max:200'],
            $prefix . 'city' => ['required', 'string', 'min:1', 'max:200'],
            $prefix . 'country' => [
                'nullable',
                'string',
                Rule::in(array_keys(\Punic\Territory::getCountries('nl'))),
            ],

            $prefix . 'gender' => ['required', 'string', Rule::in(['male', 'female', 'unspecified', 'unknown'])],
            $prefix . 'birthdate' => [
                'required',
                'regex:/^(([0-9]{4})-([0-9]{2}|XX)-([0-9]{2}|XX))$|^([0-9]{4})$/i',
                new NotInFuture(),
                new ValidYear(1890, (int)date('Y')),
            ],
            $prefix . 'bsn' => ['nullable', 'string', 'bsn', 'max:9', 'bsn_lookup'],
            $prefix . 'email' => ['required', 'string', 'email'],
            $prefix . 'phone' => ['required', 'string', 'regex:/[\+?[\d\s]+$/i'],
            $prefix . 'report_permission_gp' => ['required', 'string', Rule::in(['yes', 'no'])],

            $prefix . 'requesting_physician' => ['required', 'string', 'min:1', 'max:200'],
            $prefix . 'brand_used_test' => [
                'required',
                'string',
                Rule::in(array_keys(Elements::getList(Elements::TEST_BRANDS)))
            ],

            $prefix . 'involved_laboratory' => ['nullable', 'string', 'min:1', 'max:200'],
            $prefix . 'category_test_location' => [
                'required',
                'string',
                Rule::in(array_keys(Elements::getList(Elements::TEST_LOCATIONS)))
            ],
            $prefix . 'involved_company' => ['required', 'string', 'min:1', 'max:200'],
            $prefix . 'test_after_contact_tracing' => ['nullable', 'string', Rule::in(['yes', 'no'])],
            $prefix . 'bco_number' => [
                'nullable',
                'required_if:' . $prefix . 'test_after_contact_tracing,yes',
                'string',
                'min:1',
                'max:200'
            ],
            $prefix . 'return_from_trip' => ['required', 'string', Rule::in(['yes', 'no'])],
            $prefix . 'country_stay' => [
                'required_if:' . $prefix . 'return_from_trip,yes',
                'integer',
                'min:0',
                'max:9999',
            ],
            $prefix . 'flight_number' => [
                'nullable',
                'required_if:' . $prefix . 'return_from_trip,yes',
                'string',
                'min:1',
                'max:200'
            ],

            $prefix . 'current_symptoms' => [
                'required',
                'array',
                Rule::in(array_keys(Elements::getList(Elements::SYMPTOMS)))
            ],
            $prefix . 'first_day_of_illness_known' => [
                'required',
                'string',
                Rule::in(['known', 'estimated', 'unknown'])
            ],
            $prefix . 'first_day_of_illness_date' => [
                'nullable',
                'required_if:' . $prefix . 'first_day_of_illness_known:known,estimated',
                'date',
                'after:2020-01-01',
                'before:tomorrow',
            ],

            $prefix . 'forwarded_by_coronamelder_app' => [
                'nullable',
                'string',
                Rule::in(['yes', 'no', 'doesnt_use_the_app'])
            ],
            $prefix . 'date_of_notification_coronamelder_app' => [
                'nullable',
                'required_if:' . $prefix . 'forwarded_by_coronamelder_app,yes',
                'date',
                'after:2020-01-01',
                'before:tomorrow',
            ],
            $prefix . 'date_of_contact_coronamelder_app' => [
                'nullable',
                'required_if:' . $prefix . 'forwarded_by_coronamelder_app,yes',
                'date',
                'after:2020-01-01',
                'before:tomorrow',
            ],

            $prefix . 'last_two_weeks_worked_as_at_in' => [
                'nullable',
                'array',
                Rule::in(array_keys(Elements::getList(Elements::ENVIRONMENT)))
            ],
            $prefix . 'caregiver_type' => [
                'nullable',
                Rule::requiredIf(function () use ($prefix) {
                    $v = $this->input($prefix . 'last_two_weeks_worked_as_at_in') ?? [];
                    return array_search('healthcare_worker_or_paramedic_in_hospital', $v) !== false ||
                        array_search('care_worker_or_paramedic_in_nursing_or_care_home', $v) !== false ||
                        array_search('healthcare_worker_or_paramedic_elsewhere_with_close_contact', $v) !== false
                    ;
                }),
                'string',
                Rule::in(array_keys(Elements::getList(Elements::CAREGIVER)))
            ],
            $prefix . 'contact_profession' => [
                'nullable',
                Rule::requiredIf(function () use ($prefix) {
                    $v = $this->input($prefix . 'last_two_weeks_worked_as_at_in') ?? [];
                    return array_search('other_professions_with_close_contact', $v) !== false;
                }),
                'string',
                Rule::in(array_keys(Elements::getList(Elements::CONTACT_PROFESSION)))
            ],

            $prefix . 'patient_gp_client_vvt_or_risk_group' => [
                'nullable',
                'string',
                Rule::in(['no', 'patient_client', 'risk_group']),
            ],

            $prefix . 'risk_group' => [
                'nullable',
                'required_if:' . $prefix . 'patient_gp_client_vvt_or_risk_group,patient_client,risk_group',
                'array',
                Rule::in(array_keys(Elements::getList(Elements::RISK_GROUP))),
            ],

            $prefix . 'date_of_sample_collection' => ['required', 'date', 'after:2020-01-01', 'before:tomorrow'],
            $prefix . 'date_of_test_result' => ['required', 'date', 'after:2020-01-01', 'before:tomorrow'],
            $prefix . 'test_result' => ['required', 'string', Rule::in(['positive'])],
        ];
    }

    public function attributes(string $prefix = 'data.'): array
    {
        return [
            $prefix . 'initials' => 'Initialen',
            $prefix . 'insertion' => 'Tussenvoegsel',
            $prefix . 'surname' => 'Achternaam',
            $prefix . 'postcode' => 'Postcode',
            $prefix . 'house_number' => 'Huisnummer',
            $prefix . 'house_letter' => 'Huisletter',
            $prefix . 'house_number_addition' => 'Huisnummertoevoeging',
            $prefix . 'house_number_designation' => 'Huisnummeraanduiding',
            $prefix . 'street' => 'Straat',
            $prefix . 'city' => 'Woonplaats',
            $prefix . 'country' => 'Land',

            $prefix . 'gender' => 'Geslacht',
            $prefix . 'birthdate' => 'Geboortedatum',
            $prefix . 'bsn' => 'BSN',
            $prefix . 'email' => 'E-mailadres',
            $prefix . 'phone' => 'Telefoonnummer',
            $prefix . 'report_permission_gp' => 'Toestemming voor het doorgeven van de uitslag aan de huisarts',

            $prefix . 'requesting_physician' => 'Aanvragend arts',
            $prefix . 'brand_used_test' => 'Merk gebruikte test',
            $prefix . 'involved_laboratory' => 'Betrokken laboratorium',
            $prefix . 'category_test_location' => 'Categorie testlocatie',
            $prefix . 'involved_company' => 'Betrokken bedrijf',
            $prefix . 'test_after_contact_tracing' => 'Test na contactonderzoek',
            $prefix . 'bco_number' => 'BCO-nummer',
            $prefix . 'return_from_trip' => 'Terugkeer van reis',
            $prefix . 'country_stay' => 'Land van verblijf',
            $prefix . 'flight_number' => 'Vluchtnummer',

            $prefix . 'current_symptoms' => 'Huidige klachten',
            $prefix . 'first_day_of_illness_known' => 'Eerste dag van ziekte bekend',
            $prefix . 'first_day_of_illness_date' => 'Eerste dag van ziekte',

            $prefix . 'forwarded_by_coronamelder_app' => 'Doorgegeven door CoronaMelder-app',
            $prefix . 'date_of_notification_coronamelder_app' => 'Datum melding CoronaMelder-app',
            $prefix . 'date_of_contact_coronamelder_app' => 'Datum contact CoronaMelder-app',

            $prefix . 'last_two_weeks_worked_as_at_in' => 'In de afgelopen 2 weken gewerkt als',
            $prefix . 'caregiver_type' => 'Zorgverlener',
            $prefix . 'contact_profession' => 'Contact beroep',

            $prefix . 'patient_gp_client_vvt_or_risk_group' => 'Patiënt, cliënt, VVT of risicogroep',
            $prefix . 'risk_group' => 'Risicogroep',

            $prefix . 'date_of_sample_collection' => 'Datum afname test',
            $prefix . 'date_of_test_result' => 'Datum uitslag test',
            $prefix . 'test_result' => 'Uitslag test',
        ];
    }

    public function messages(string $prefix = 'data.'): array
    {
        return [
            $prefix . 'bsn' => 'Het BSN is niet geldig.',
        ];
    }
}
