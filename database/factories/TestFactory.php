<?php

namespace Database\Factories;

use App\Elements;
use App\Models\Test;
use App\Services\MapTestBrandsToEuValuesService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Punic\Territory;

/**
 * @extends Factory<Test>
 */
class TestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'initials' => substr($this->faker->firstName, 0, 10),
            'insertion' => null,
            'surname' => $this->faker->lastName,
            'postcode' => $this->faker->postcode,
            'house_number' => $this->faker->numberBetween(1, 250),
            'house_letter' => null,
            'house_number_addition' => null,
            'house_number_designation' => null,
            'street' => $this->faker->streetName,
            'city' => $this->faker->city,
            'country' => $this->faker->countryCode,
            'gender' => $this->faker->randomElement(['male', 'female', 'unspecified']),
            'bsn' => $this->faker->idNumber,
            'birthdate' => $this->faker->dateTimeBetween('-100 years', 'now')->format('Y-m-d'),
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'report_permission_gp' => $this->faker->randomElement(['yes', 'no']),
            'requesting_physician' => $this->faker->name,
            'brand_used_test' => $this->faker->randomElement(array_keys(Elements::getList(Elements::TEST_BRANDS))),
            'involved_laboratory' => null,
            'category_test_location' => $this->faker->randomElement(array_keys(Elements::getList(Elements::TEST_LOCATIONS))),
            'involved_company' => $this->faker->company,
            'test_after_contact_tracing' => 'no',
            'bco_number' => null,
            'return_from_trip' => 'no',
            'country_stay' => $this->faker->randomNumber(4),
            'flight_number' => null,
            'current_symptoms' => $this->faker->randomElements(array_keys(Elements::getList(Elements::SYMPTOMS)), $this->faker->numberBetween(1, 5)),
            'first_day_of_illness_known' => 'unknown',
            'first_day_of_illness_date' => null,
            'forwarded_by_coronamelder_app' => 'no',
            'last_two_weeks_worked_as_at_in' => null,
            'caregiver_type' => null,
            'date_of_notification_coronamelder_app' => null,
            'date_of_contact_coronamelder_app' => null,
            'contact_profession' => null,
            'patient_gp_client_vvt_or_risk_group' => 'no',
            'risk_group' => null,
            'date_of_sample_collection' => $this->faker->dateTimeBetween('2020-01-01', 'tomorrow')->format('Y-m-d'),
            'date_of_test_result' => $this->faker->dateTimeBetween('2020-01-01', 'tomorrow')->format('Y-m-d'),
            'test_result' => 'positive',
        ];
    }

    public function testAfterContactTracing(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'test_after_contact_tracing' => 'yes',
                'bco_number' => (string) $this->faker->randomNumber(8),
            ];
        });
    }

    public function returnFromTrip(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'return_from_trip' => 'yes',
                'flight_number' => (string) $this->faker->randomNumber(8),
            ];
        });
    }

    public function firstDayOfIllnessKnown(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'first_day_of_illness_known' => 'known',
                'first_day_of_illness_date' => $this->faker->dateTimeBetween('2020-01-01', 'tomorrow')->format('Y-m-d'),
            ];
        });
    }

    public function forwardedByCoronamelderApp(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'forwarded_by_coronamelder_app' => 'yes',
                'date_of_notification_coronamelder_app' => $this->faker->dateTimeBetween('2020-01-01', 'tomorrow')->format('Y-m-d'),
                'date_of_contact_coronamelder_app' => $this->faker->dateTimeBetween('2020-01-01', 'tomorrow')->format('Y-m-d'),
            ];
        });
    }

    public function withEuValues(string $brandUsedTest): self
    {
        $map = new MapTestBrandsToEuValuesService();

        [
            'eu_event_type' => $type,
            'eu_event_manufacturer' => $manufacturer,
            'eu_event_name' => $name,
        ] = $map->getEuEventValuesForTestBrand($brandUsedTest ?? '');

        return $this->state(function (array $attributes) use ($name, $manufacturer, $type, $brandUsedTest) {
            return [
                'brand_used_test' => $brandUsedTest,
                'eu_event_type' => $type,
                'eu_event_manufacturer' => $manufacturer,
                'eu_event_name' => $name,
            ];
        });
    }
}
