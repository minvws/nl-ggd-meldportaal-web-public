<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * Class Similarity
 *
 * Check if the password is not similar to the users email or name (ie: johndoe1)
 *
 * @package App\Rules
 * @author jthijssen@noxlogic.nl
 */
class Similarity
{
    public const THRESHOLD = 5;

    protected const CANNOT_BE_SIMILAR_EMAIL = 'Password cannot be similar to your email address.';
    protected const CANNOT_BE_SIMILAR_NAME = 'Password cannot be similar to your name.';

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

        if (levenshtein($user->email, $value) <= self::THRESHOLD) {
            $msg = strval(__(self::CANNOT_BE_SIMILAR_EMAIL));

            $validator->errors()->add($field, $msg);
            return false;
        }

        if (levenshtein($user->name, $value) <= self::THRESHOLD) {
            $msg = strval(__(self::CANNOT_BE_SIMILAR_NAME));

            $validator->errors()->add($field, $msg);
            return false;
        }

        return true;
    }
}
