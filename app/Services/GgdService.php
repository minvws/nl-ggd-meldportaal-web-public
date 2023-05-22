<?php

declare(strict_types=1);

namespace App\Services;

use App\Elements;
use App\Models\Test;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

class GgdService
{
    protected Client $client;
    protected string $clientId;
    protected string $clientSecret;

    protected ?string $accessToken = null;

    public function __construct(Client $client, string $clientId, string $clientSecret)
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function upload(Test $test): bool
    {
        // We will clone the test to prevent any changes to the original test
        $test = clone $test;

        $this->convertTestToGGDFormat($test);

        // Retrieve access token if not already done
        if ($this->accessToken === null) {
            $this->accessToken = $this->retrieveAccessToken();
            if ($this->accessToken === null) {
                // Could not retrieve access token
                return false;
            }
        }

        $retry = 5;
        while ($retry > 0) {
            $retry--;
            try {
                $response = $this->client->post('/tests/1.0/tests', [
                    'http_errors' => false,
                    'headers' => [
                        'authorization' => 'Bearer ' . $this->accessToken,
                    ],
                    'json' => [ $this->toJson($test, 'meldportaal') ],
                ]);
            } catch (\Exception $e) {
                sleep(1);
                continue;
            }

            if ($response->getStatusCode() === 202) {
                // Success
                return true;
            }

            if ($response->getStatusCode() === 401) {
                // Access token expired, try to retrieve a new one
                $this->accessToken = $this->retrieveAccessToken();
                if ($this->accessToken === null) {
                    // Could not retrieve access token
                    return false;
                }
            }

            if ($response->getStatusCode() === 429) {
                // Too many requests, wait 1 second and try again
                sleep(1);
            }

            if ($response->getStatusCode() >= 500) {
                // Internal server error, wait 1 second and try again
                sleep(1);
            }
        }

        return false;
    }

    protected function retrieveAccessToken(): ?string
    {
        $retry = 5;
        while ($retry > 0) {
            $retry--;
            try {
                $response = $this->client->post('/token', [
                    'http_errors' => false,
                    'query' => [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'grant_type' => 'client_credentials',
                    ]
                ]);
            } catch (\Exception $e) {
                sleep(1);
                continue;
            }

            if ($response->getStatusCode() === 200) {
                $body = json_decode($response->getBody()->getContents(), true);
                return $body['access_token'];
            }
        }

        return null;
    }

    protected function toJson(Test $test, string $username): array
    {
        return [
            'initials' => $test->initials,
            'insertion' => $test->insertion,
            'surname' => $test->surname,
            'street' => $test->street,
            'house_number' => $test->house_number,
            'house_letter' => $test->house_letter,
            'house_number_addition' => $test->house_number_addition,
            'house_number_designation' => $test->house_number_designation,
            'postcode' => $test->postcode,
            'city' => $test->city,
            'gender' => $test->gender,
            'birthdate' => $test->birthdate,
            'bsn' => $test->bsn,
            'email' => $test->email,
            'phone' => $test->phone,
            'report_permission_gp' => $test->report_permission_gp ? 'JA' : 'NEE',
            'requesting_physician' => $test->requesting_physician,
            'brand_used_test' => $test->brand_used_test,
            'involved_laboratory' => $test->involved_laboratory,
            'category_test_location' => $test->category_test_location,
            'involved_company' => $test->involved_company,
            'test_after_contact_tracing' => $test->test_after_contact_tracing ? 'JA' : 'NEE',
            'bco_number' => $test->bco_number,
            'return_from_trip' => $test->return_from_trip ? 'JA' : 'NEE',
            'country_stay' => $test->country_stay,
            'flight_number' => $test->flight_number,
            'current_symptoms' => implode(', ', $test->current_symptoms),
            'first_day_of_illness_known' => $test->first_day_of_illness_known,
            'first_day_of_illness_date' =>
                $test->first_day_of_illness_date ?
                $test->first_day_of_illness_date->timezone("europe/amsterdam")->format('d-m-Y h:i:s') :
                null,
            'forwarded_by_coronamelder_app' => $test->forwarded_by_coronamelder_app ? 'JA' : 'NEE',
            'date_of_notification_coronamelder_app' =>
                $test->date_of_notification_coronamelder_app ?
                $test->date_of_notification_coronamelder_app->timezone("europe/amsterdam")->format('d-m-Y') :
                null,
            'date_of_contact_coronamelder_app' =>
                $test->date_of_contact_coronamelder_app ?
                $test->date_of_contact_coronamelder_app->timezone("europe/amsterdam")->format('d-m-Y') :
                null,
            'last_two_weeks_worked_as_at_in' => implode(', ', $test->last_two_weeks_worked_as_at_in),
            'caregiver_type' => $test->caregiver_type,
            'contact_profession' => $test->contact_profession,
            'patient_gp_client_vvt_or_risk_group' => $test->patient_gp_client_vvt_or_risk_group,
            'risk_group' => implode(', ', $test->risk_group),
            'date_of_sample_collection' => $test->date_of_sample_collection
                ->timezone("europe/amsterdam")
                ->format('d-m-Y H:i:s'),
            'date_of_test_result' => $test->date_of_test_result->timezone("europe/amsterdam")->format('d-m-Y H:i:s'),
            'test_result' => $test->test_result,

            'user_id' => $username,
        ];
    }

    protected function convertTestToGGDFormat(Test $test): void
    {
        // Convert 2020-XX-XX dates to 2020-01-01, as this is the only format accepted by GGD
        $test->birthdate = str_replace('XX', '01', strtoupper($test->birthdate));
        $test->birthdate = Carbon::parse($test->birthdate)->format('d-m-Y');

        $test->email = strtolower($test->email);

        // Empty BSNs are not allowed, so replace them with a dummy value that GGD accepts
        if (empty($test->bsn)) {
            $test->bsn = '999999999';
        }

        $test->postcode = str_replace(' ', '', strtoupper($test->postcode));

        switch ($test->first_day_of_illness_known) {
            case 'known':
                $test->first_day_of_illness_known = 'Bekend';
                break;
            case 'estimated':
                $test->first_day_of_illness_known = 'Geschat';
                break;
            case 'unknown':
                $test->first_day_of_illness_known = 'Onbekend';
                break;
        }

        switch ($test->test_result) {
            case 'positive':
                $test->test_result = 'POSITIEF';
                break;
            case 'negative':
                $test->test_result = 'NEGATIEF';
                break;
        }

        $list = Elements::getList(Elements::CAREGIVER, locale: 'nl');
        $test->caregiver_type = $list[$test->caregiver_type] ?? '';

        $list = Elements::getList(Elements::TEST_LOCATIONS, locale: 'nl');
        $test->category_test_location = $list[$test->category_test_location]  ?? '';

        $list = Elements::getList(Elements::SYMPTOMS, locale: 'nl');
        $v = [];
        foreach ($test->current_symptoms ?? [] as $key => $symptom) {
            $v[$key] = $list[$symptom]  ?? '';
        }
        $test->current_symptoms = $v;

        $list = Elements::getList(Elements::ENVIRONMENT, locale: 'nl');
        $v = [];
        foreach ($test->last_two_weeks_worked_as_at_in ?? [] as $key => $env) {
            $v[$key] = $list[$env]  ?? '';
        }
        $test->last_two_weeks_worked_as_at_in = $v;

        $list = Elements::getList(Elements::CONTACT_PROFESSION, locale: 'nl');
        $test->contact_profession = $list[$test->contact_profession]  ?? '';

        $list = Elements::getList(Elements::RISK_GROUP, locale: 'nl');
        $v = [];
        foreach ($this->risk_group ?? [] as $key => $risk) {
            $v[$key] = $list[$risk]  ?? '';
        }
        $test->risk_group = $v;

        switch ($test->gender) {
            case 'male':
                $test->gender = 'MAN';
                break;
            case 'female':
                $test->gender = 'VROUW';
                break;
            case 'unspecified':
                $test->gender = 'NIET_GESPECIFICEERD';
                break;
            case 'unknown':
                $test->gender = 'ONBEKEND';
                break;
        }
    }
}
