<?php

declare(strict_types=1);

namespace App\Models\Traits;

use RuntimeException;

/**
 * Trait HasServerSidePublicKey
 * @package App\Models\Traits
 * @author Pauline Vos <info@pauline-vos.nl>
 */
trait HasServerSidePublicKey
{
    protected static function bootHasServerSidePublicKey(): void
    {
        static::saving(function ($model) {
            $property = $model->getServerPublicKeyProperty();
            $model->$property = self::getKeyId();
        });
    }

    protected function getServerPublicKeyProperty(): string
    {
        return 'keyid_server';
    }

    private static function getKeyId(): string
    {
        $certificates = config('crypto.certificates_recipients');
        if (!$certificates) {
            throw new RuntimeException("No certificates_recipients defined");
        }

        $contents = [];
        foreach ($certificates as $certificate) {
            $content = file_get_contents($certificate);
            if ($content === false) {
                throw new RuntimeException("CMS Certificate not found");
            }
            /** @var array $info */
            $info = openssl_x509_parse($content);
            $contents[] = $info['serialNumberHex'];
        }

        return collect($contents)->join(",");
    }
}
