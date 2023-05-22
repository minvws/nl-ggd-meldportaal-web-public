<?php

declare(strict_types=1);

namespace App\Validators;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class MinYearsAgo
 *
 * Checks that the given date is before specific year.
 *
 * @package App\Validators
 */
class MinYearsAgo implements Rule
{
    public function __construct(public int $minYears = 12)
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
        // Possible receiving date 2022-02-XX
        if (!preg_match("/^(([0-9]{4})-([0-9]{2}|XX)-([0-9]{2}|XX))$|^([0-9]{4})$/i", $value)) {
            return false;
        }

        $date = strval(str_ireplace(["-00","-XX"], ["-01","-01"], $value));
        if (strlen($value) === 4) {
            $date .= '-01-01';
        } elseif (strlen($value) === 7) {
            $date .= '-01';
        }

        try {
            $carbon = Carbon::parse($date);
            return Carbon::now()->diffInYears($carbon) >= $this->minYears;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): array|string
    {
        return strval(__("Person must be at least :years old", ['years' => $this->minYears]));
    }
}
