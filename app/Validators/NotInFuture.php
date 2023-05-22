<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class NotInFuture implements Rule
{
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
            return $carbon <= Carbon::now();
        } catch (\Exception) {
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
        return strval(__("Date must not be in the future", []));
    }
}
