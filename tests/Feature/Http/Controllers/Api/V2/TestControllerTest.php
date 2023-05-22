<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\V2;

use App\Http\Middleware\Authenticate;
use App\Models\Test;
use Tests\Feature\TestCase;

class TestControllerTest extends TestCase
{
    public function testStore(): void
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

        $response = $this->postJson(route('api.v2.tests'), $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Test created',
        ]);
    }

    public function testStoreWithDifferentCountry(): void
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
            'country' => 'BE',
            'postcode' => '1234',
        ])->raw();

        $response = $this->postJson(route('api.v2.tests'), $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Test created',
        ]);
    }


    public function testValidationWhenFieldsMissing(): void
    {
        $this->withoutMiddleware(Authenticate::class);

        $this->setupUser([
            'id' => 1,
            'email' => 'phpunit@example.org',
            'name' => 'phpunit test',
        ], true);

        $response = $this->postJson(route('api.v2.tests'), [
            'initials' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'initials',
            'surname',
            'postcode',
            'house_number',
            'street',
            'city',
            'gender',
            'birthdate',
            'email',
            'phone',
            'report_permission_gp',
            'requesting_physician',
            'brand_used_test',
            'category_test_location',
            'involved_company',
            'return_from_trip',
            'current_symptoms',
            'first_day_of_illness_known',
            'date_of_sample_collection',
            'date_of_test_result',
            'test_result',
        ]);
    }
}
