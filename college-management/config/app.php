<?php
declare(strict_types=1);

/**
 * Loads .env (simple parser, no Composer dependency) and exposes config as an array.
 */
if (!function_exists('loadEnv')) {
    function loadEnv(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, "\"'");
            if ($key !== '' && getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

loadEnv(dirname(__DIR__) . '/.env');

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return match (strtolower($value)) {
            'true' => true,
            'false' => false,
            'null' => null,
            default => $value,
        };
    }
}

return [
    'name' => env('APP_NAME', 'College Management System'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'key' => env('APP_KEY', ''),
    'session_lifetime' => (int) env('SESSION_LIFETIME', 3600),
    'max_login_attempts' => (int) env('MAX_LOGIN_ATTEMPTS', 5),
    'lockout_seconds' => (int) env('LOGIN_LOCKOUT_SECONDS', 300),
];
