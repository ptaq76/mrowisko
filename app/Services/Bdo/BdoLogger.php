<?php

namespace App\Services\Bdo;

use Illuminate\Support\Facades\Log;

class BdoLogger
{
    protected static string $channel = 'bdo_sync';

    public static function info(string $message, array $context = []): void
    {
        Log::channel(static::$channel)->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        Log::channel(static::$channel)->error($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        Log::channel(static::$channel)->warning($message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        Log::channel(static::$channel)->debug($message, $context);
    }
}
