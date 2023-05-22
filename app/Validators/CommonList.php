<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Contracts\Validation\Validator;

/**
 * Class CommonList
 *
 * Checks if the given password is found in a list of common passwords
 *
 * @author jthijssen@noxlogic.nl
 */
class CommonList
{
    /** @var array */
    protected $commons = [];

    protected const CANNOT_BE_COMMON = 'Password cannot be a common password.';

    /**
     * CommonList constructor.
     */
    public function __construct()
    {
        $f = file(base_path('resources/upgraded-common-passwords.txt'), FILE_IGNORE_NEW_LINES);
        $this->commons =  $f !== false ? $f : [];
    }


    /**
     * @param mixed $field
     * @param mixed $value
     * @param mixed $param
     * @param Validator $validator
     * @return bool
     */
    public function check($field, $value, $param, Validator $validator)
    {
        if (in_array($value, $this->commons)) {
            $msg = strval(__(self::CANNOT_BE_COMMON));

            $validator->errors()->add($field, $msg);
            return false;
        }

        return true;
    }
}
