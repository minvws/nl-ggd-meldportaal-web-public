<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Contracts\Validation\Validator;

/**
 * Class NotNumeric
 *
 * Checks if the password given is not completely numerical
 *
 * @package App\Rules
 * @author jthijssen@noxlogic.nl
 */
class NotNumeric
{
    protected const CANNOT_BE_NUMERIC = 'Password cannot be all numerical.';

    /**
     * @param mixed $field
     * @param mixed $value
     * @param mixed $param
     * @param Validator $validator
     * @return bool
     */
    public function check($field, $value, $param, Validator $validator)
    {
        if (preg_match('/^[0-9]+$/', $value)) {
            $msg = strval(__(self::CANNOT_BE_NUMERIC));

            $validator->errors()->add($field, $msg);
            return false;
        }

        return true;
    }
}
