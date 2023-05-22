<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

abstract class AbstractException extends \Exception
{
    /** @var string */
    protected static $endpoint = '';

    /**
     * @param Throwable $previous
     *
     * @return static
     */
    public static function serverFailure(Throwable $previous)
    {
        return self::create('Server failure on ' . static::$endpoint . ' endpoint', $previous);
    }

    /**
     * @param Throwable $previous
     *
     * @return static
     */
    public static function clientFailure(Throwable $previous)
    {
        return self::create('Invalid request to ' . static::$endpoint . ' endpoint', $previous);
    }

    /**
     * @param Throwable $previous
     *
     * @return static
     */
    public static function resultFailure(Throwable $previous)
    {
        return self::create('Invalid response received from ' . static::$endpoint . ' endpoint', $previous);
    }

    /**
     * @param string $message
     * @param Throwable $previous
     *
     * @return static
     */
    protected static function create(string $message, Throwable $previous)
    {
        $class = static::class;

        return new $class($message, 0, $previous);
    }
}
