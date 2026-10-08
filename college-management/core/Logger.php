<?php
declare(strict_types=1);

namespace Core;

final class Logger
{
    private static function write(string $level, string $message): void
    {
        $line = sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), strtoupper($level), $message);
        $file = STORAGE_PATH . '/logs/app.log';
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message): void
    {
        self::write('info', $message);
    }

    public static function error(string $message): void
    {
        self::write('error', $message);
    }

    public static function warning(string $message): void
    {
        self::write('warning', $message);
    }
}
