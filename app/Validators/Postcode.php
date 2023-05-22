<?php

declare(strict_types=1);

namespace App\Validators;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

class Postcode implements DataAwareRule, InvokableRule
{
    protected const NOT_A_VALID_POSTCODE = 'Postcode is invalid.';

    protected array $data = [];
    protected ?string $countryField = null;
    protected string $defaultCountry = "NL";

    public function __construct(?string $countryField = null)
    {
        $this->countryField = $countryField;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail): void
    {
        // If country is not NL, don't validate
        if ($this->getCountryFieldValue() !== 'NL') {
            return;
        }

        if (preg_match('/^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i', $value) !== 1) {
            $fail(self::NOT_A_VALID_POSTCODE)->translate();
        }
    }

    /**
     * Set the data under validation.
     *
     * @param array $data
     * @return $this
     */
    public function setData($data): static
    {
        $this->data = $data;

        return $this;
    }

    protected function getCountryFieldValue(): ?string
    {
        if ($this->countryField === null) {
            return $this->defaultCountry;
        }

        $value = Arr::get($this->data, $this->countryField);
        if (!is_string($value)) {
            return $this->defaultCountry;
        }

        return $value;
    }
}
