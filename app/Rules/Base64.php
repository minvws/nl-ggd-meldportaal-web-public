<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class Base64
 *
 * @package App\Rules
 * @author annejan@noprotocol.nl
 */
class Base64 implements Rule
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
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $value)) {
            if (base64_decode($value, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return array|string|null
     */
    public function message()
    {
        return __('validation.base64');
    }
}
