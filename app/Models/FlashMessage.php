<?php

declare(strict_types=1);

namespace App\Models;

class FlashMessage
{
    /*/ Manon message types /*/
    public const ERROR = 'error';
    public const INFO = 'explanation';
    public const PRIMARY = 'primary';
    public const SUCCESS = 'confirmation';
    public const WARNING = 'warning';

    private const DEFAULT = self::PRIMARY;

    public static function getCssClassFor(string $type): string
    {
        $cssClass = self::DEFAULT;

        $cssClasses = [
            /* Messages from the Application */
            self::SUCCESS => 'confirmation',
            self::ERROR => 'error',
            self::INFO => 'explanation',
            self::PRIMARY => 'primary',
            self::WARNING => 'warning',
            /* Messages from Laravel or Packages */
            'errors' => self::ERROR,
            'status' => self::INFO,
            'status-confirmation' => self::INFO,
            'status-error' => self::ERROR,
        ];

        if (array_key_exists($type, $cssClasses)) {
            $cssClass = $cssClasses[$type];
        }

        return $cssClass;
    }
}
