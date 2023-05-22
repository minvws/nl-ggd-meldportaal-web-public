<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules()
    {
        return [
            'required',     // A password is required
            'string',       // A password must be a string
            'min:14',       // A password must be at least 14 chars
            'caps:1',       // A password must at least one capital letter
            'commonlist',   // A password must not be on our common word list
            'not_numeric',  // A password must not contain only numeric values
            'similarity',   // A password must not be similar to our name or email
            'confirmed'     // A password must be confirmed
        ];
    }
}
