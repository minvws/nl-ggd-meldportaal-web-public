<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\Postcode;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Validator;
use Tests\Unit\TestCase;

class PostcodeRuleTest extends TestCase
{
    /**
     * @dataProvider provideValidPostcodes
     */
    public function testPostcodeIsValid(string $postcode): void
    {
        $validator = $this->getValidator(
            ['postcode' => $postcode],
            ['postcode' => ['required', new Postcode()]]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * @dataProvider provideValidPostcodes
     */
    public function testDefaultCountryIsUsedWhenCountryFieldDoesNotExists(string $postcode): void
    {
        $validator = $this->getValidator(
            ['postcode' => $postcode],
            ['postcode' => ['required', new Postcode('not-existing-country-field')]]
        );

        $this->assertTrue($validator->passes());
    }

    public function provideValidPostcodes(): array
    {
        return [
            ['1234 AB'],
            ['1234ab'],
            ['1234AB'],
        ];
    }

    /**
     * @dataProvider provideInvalidPostcodes
     */
    public function testPostcodeIsInvalid(string $postcode): void
    {
        $validator = $this->getValidator(
            ['postcode' => $postcode],
            ['postcode' => ['required', new Postcode()]]
        );

        $this->assertFalse($validator->passes());
        $this->assertEquals(
            'Postcode is invalid.',
            $validator->errors()->first('postcode')
        );
    }

    public function provideInvalidPostcodes(): array
    {
        return [
            ['50000'],
            ['1234'],
            ['1234  AB'],
        ];
    }

    /**
     * @dataProvider providePostcodeAndCountry
     */
    public function testPostcodeIsValidWhenCountryIsNotNL(string $postcode, string $country): void
    {
        $validator = $this->getValidator(
            [
                'postcode' => $postcode,
                'country' => $country,
            ],
            ['postcode' => ['required', new Postcode('country')]]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * @dataProvider providePostcodeAndCountry
     */
    public function testPostcodeInDataIsValidWhenCountryIsNotNL(string $postcode, string $country): void
    {
        $validator = $this->getValidator(
            [
                'data' => [
                    'postcode' => $postcode,
                    'country' => $country,
                ],
            ],
            ['data.postcode' => ['required', new Postcode('data.country')]]
        );

        $this->assertTrue($validator->passes());
    }

    public function providePostcodeAndCountry(): array
    {
        return [
            ['1234AB', 'NL'],
            ['12345', 'DE'],
            ['1234', 'BE'],
        ];
    }

    protected function getValidator(array $data, array $rules): Validator
    {
        $trans = new Translator(
            new ArrayLoader(),
            'en'
        );

        return new Validator(
            $trans,
            $data,
            $rules
        );
    }
}
