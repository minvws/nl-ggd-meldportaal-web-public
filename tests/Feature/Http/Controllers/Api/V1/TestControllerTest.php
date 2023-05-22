<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Http\Middleware\Authenticate;
use App\Models\Test;
use Tests\Feature\TestCase;

class TestControllerTest extends TestCase
{
    public function testValidationWhenFieldsMissing(): void
    {
        $this->withoutMiddleware(Authenticate::class);

        $this->setupUser([
            'id' => 1,
            'email' => 'phpunit@example.org',
            'name' => 'phpunit test',
        ], true);

        $data = Test::factory([
            'initials' => 'f',
            'surname' => 'last-name',
        ])->raw();

        $response = $this->postJson(route('api.v1.tests'), [$data]);

        $response->assertStatus(422);
        $json = json_decode($response->getContent(), true);
        $this->assertIsArray($json);
        $this->assertCount(1, $json);
        $json = $json[0];
        $this->assertFalse($json['validated']);
        $this->assertArrayHasKey('errors', $json);
        $this->assertArrayHasKey('birthdate', $json['errors']);
        $this->assertArrayHasKey('category_test_location', $json['errors']);
        $this->assertArrayHasKey('current_symptoms', $json['errors']);
        $this->assertArrayHasKey('test_result', $json['errors']);
    }

    public function testStore(): void
    {
        $this->withoutMiddleware(Authenticate::class);

        $this->setupUser([
            'id' => 1,
            'email' => 'phpunit@example.org',
            'name' => 'phpunit test',
        ], true);

        $response = $this->postJson(route('api.v1.tests'), $this->getV1Data());
        $response->assertStatus(202);

        $this->assertSame(1, Test::count());

        $test = Test::first();
        $this->assertSame('Voornaam', $test->initials);
        $this->assertSame('tus', $test->insertion);
        $this->assertSame('last-name', $test->surname); // Surname overwritten via Monster
        $this->assertSame('1234AB', $test->postcode);
        $this->assertSame('66', $test->house_number);
        $this->assertSame('a', $test->house_letter);
        $this->assertSame('blastreet', $test->street);
        $this->assertSame('Amsterdam', $test->city);
        $this->assertSame('NL', $test->country);
        $this->assertSame('male', $test->gender);
        $this->assertSame('1991-11-02', $test->birthdate);
        $this->assertSame('999995844', $test->bsn);
        $this->assertSame('email@example.org', $test->email);
        $this->assertSame('0612345678', $test->phone);
        $this->assertSame('no', $test->report_permission_gp);
        $this->assertSame('cupi', $test->requesting_physician);
        $this->assertSame('A', $test->brand_used_test);
        $this->assertSame('aute sint', $test->involved_laboratory);
        $this->assertSame('commercial_test_location', $test->category_test_location);
        $this->assertSame('laborum non anim', $test->involved_company);
        $this->assertSame('yes', $test->test_after_contact_tracing);
        $this->assertSame('fugiat velit cillum', $test->bco_number);
        $this->assertSame('no', $test->return_from_trip);
        $this->assertSame('5742', $test->country_stay);
        $this->assertSame('dolor proident Lorem voluptate', $test->flight_number);
        $this->assertSame(['none_of_these'], $test->current_symptoms);
        $this->assertSame('estimated', $test->first_day_of_illness_known);
        $this->assertSame('2022-01-01', $test->first_day_of_illness_date->format('Y-m-d'));
        $this->assertSame('yes', $test->forwarded_by_coronamelder_app);
        $this->assertSame('2022-01-01', $test->date_of_notification_coronamelder_app->format('Y-m-d'));
        $this->assertSame('2022-01-01', $test->date_of_contact_coronamelder_app->format('Y-m-d'));
        $this->assertSame(
            ['secondary_education_incl_mbo', 'informal_caregiver'],
            $test->last_two_weeks_worked_as_at_in
        );
        $this->assertSame('dietitian', $test->caregiver_type);
        $this->assertSame('beauty_therapist', $test->contact_profession);
        $this->assertSame('patient_client', $test->patient_gp_client_vvt_or_risk_group);
        $this->assertSame(['heart_patient', 'diabetes_mellitus'], $test->risk_group);
        $this->assertSame('2022-01-01', $test->date_of_sample_collection->format('Y-m-d'));
        $this->assertSame('2022-01-01', $test->date_of_test_result->format('Y-m-d'));
        $this->assertSame('positive', $test->test_result);
    }

    public function testStoreValidWithDifferentCountry(): void
    {
        $this->withoutMiddleware(Authenticate::class);

        $this->setupUser([
            'id' => 1,
            'email' => 'phpunit@example.org',
            'name' => 'phpunit test',
        ], true);

        $data = $this->getV1Data();
        $data[0]['country'] = 'BE';
        $data[0]['postcode'] = '1234';

        $response = $this->postJson(route('api.v1.tests'), $data);
        $response->assertStatus(202);
    }

    protected function getV1Data(): array
    {
        return [
            [
              'initials' => 'Voornaam',
              'insertion' => 'tus',
              'surname' => 'last_name',
              'postcode' => '1234AB',
              'house_number' => '66',
              'house_letter' => 'a',
              'street' => 'blastreet',
              'city' => 'Amsterdam',
              'country' => 'NL',
              'gender' => 'MAN',
              'birthdate' => '02-11-1991',
              'bsn' => '999995844',
              'email' => 'email@example.org',
              'phone' => '0612345678',
              'report_permission_gp' => 'NEE',
              'requesting_physician' => 'cupi',
              'brand_used_test' => 'A',
              'involved_laboratory' => 'aute sint',
              'category_test_location' => 'Commerciële testlocatie',
              'involved_company' => 'laborum non anim',
              'test_after_contact_tracing' => 'JA',
              'bco_number' => 'fugiat velit cillum',
              'return_from_trip' => 'NEE',
              'country_stay' => '5742',
              'flight_number' => 'dolor proident Lorem voluptate',
              'current_symptoms' => 'Geen van deze',
              'first_day_of_illness_known' => 'Geschat',
              'first_day_of_illness_date' => '01-01-2022',
              'forwarded_by_coronamelder_app' => 'JA',
              'date_of_notification_coronamelder_app' => '01-01-2022',
              'date_of_contact_coronamelder_app' => '01-01-2022',
              'last_two_weeks_worked_as_at_in' => 'Middelbaar onderwijs, inclusief MBO (12+), Mantelzorger',
              'caregiver_type' => 'Diëtist',
              'contact_profession' => 'Schoonheidsspecialist',
              'patient_gp_client_vvt_or_risk_group' => 'Patiënt/cliënt',
              'risk_group' => 'Hartpatiënt,Suikerziekte',
              'date_of_sample_collection' => '01-01-2022',
              'date_of_test_result' => '01-01-2022',
              'test_result' => 'POSITIEF',
            ]
        ];
    }
}
