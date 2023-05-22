<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class ValidYear
 *
 * Checks that the given date has a valid year. Year is ALWAYS the first 4 chars of the string.
 *
 * @package App\Validators
 */
class ValidYear implements Rule
{
    public function __construct(public int $minYear = 1900, public ?int $maxYear = null)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (substr($value, 0, 4) < $this->minYear) {
            return false;
        }

        if ($this->maxYear && substr($value, 0, 4) > $this->maxYear) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): array|string
    {
        if ($this->maxYear) {
            return strval(__("Year must be between :minYear and :maxYear", [
                'minYear' => $this->minYear,
                'maxYear' => $this->maxYear
            ]));
        }

        return strval(__("Year must be at least :minYear", [ 'minYear' => $this->minYear]));
    }
}
