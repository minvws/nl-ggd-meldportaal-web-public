<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Ramsey\Uuid\Uuid;

/**
 * Trait HasUuid
 * @package App\Models\Traits
 * @author Pauline Vos <info@pauline-vos.nl>
 */
trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::saving(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
