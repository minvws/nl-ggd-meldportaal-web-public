<?php

declare(strict_types=1);

namespace App\Validators;

class Bsn
{
    /**
     * @param mixed $field
     * @param mixed $value
     * @param mixed $param
     * @param mixed $validator
     * @return bool
     */
    public function check($field, $value, $param, $validator)
    {
        if (!is_string($value) && !is_int($value)) {
            return false;
        }

        $value = (string)$value;
        if (strlen($value) != 8 && strlen($value) != 9) {
            return false;
        }

        $sum = 9 * (int)$value[0] +
            8 * (int)$value[1] +
            7 * (int)$value[2] +
            6 * (int)$value[3] +
            5 * (int)$value[4] +
            4 * (int)$value[5] +
            3 * (int)$value[6] +
            2 * (int)$value[7] +
           -1 * ((int)($value[8] ?? 0));

        return $sum % 11 == 0;
    }
}
