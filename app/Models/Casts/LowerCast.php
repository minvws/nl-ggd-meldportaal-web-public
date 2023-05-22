<?php

declare(strict_types=1);

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LowerCast
 *
 * converts given value to lowercase. For instance, for canonicalization of email addresses
 *
 * @template-implements CastsAttributes<string, string>
 *
 * @author jthijssen@noxlogic.nl
 */
class LowerCast implements CastsAttributes
{
    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function get($model, string $key, $value, array $attributes): string
    {
        return strtolower($value ?? "");
    }

    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return strtolower($value ?? "");
    }
}
