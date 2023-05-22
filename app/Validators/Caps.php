<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * Class Similarity
 *
 * Check if the password has at least X uppercase letters
 *
 * @package App\Rules
 * @author jthijssen@noxlogic.nl
 */
class Caps
{
    protected const NOT_ENOUGH_UPPERCASE_LETTERS = 'Password must have at least :len uppercase letters';

    /**
     * @param mixed $field
     * @param mixed $value
     * @param mixed $param
     * @param Validator $validator
     * @return bool
     */
    public function check($field, $value, $param, Validator $validator)
    {
        $user = Auth::User();
        if (is_null($user)) {
            return true;
        }

        $uppercases = preg_replace("/[^A-Z]/", "", $value);
        if (!is_string($uppercases)) {
            $uppercases = "";
        }

        $len = $param[0];

        if (strlen($uppercases) < $len) {
            $msg = strval(__(self::NOT_ENOUGH_UPPERCASE_LETTERS, [ 'len' => $len ]));

            $validator->errors()->add($field, $msg);
            return false;
        }

        return true;
    }
}
