<?php
declare(strict_types=1);

require __DIR__ . '/config/app.php';
require __DIR__ . '/config/constants.php';
require __DIR__ . '/core/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Core\\' => __DIR__ . '/core/',
        'Middleware\\' => __DIR__ . '/middleware/',
        'Repositories\\' => __DIR__ . '/repositories/',
        'Services\\' => __DIR__ . '/services/',
        'Controllers\\' => __DIR__ . '/controllers/',
        'Api\\Controllers\\' => __DIR__ . '/api/controllers/',
    ];
    foreach ($prefixes as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (file_exists($file)) {
                require $file;
            }
            return;
        }
    }
});

// Session hardening
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if (!empty($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', '1');
}
session_start();

send_security_headers();

if (config('debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

set_exception_handler(function (\Throwable $e): void {
    \Core\Logger::error($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    if (config('debug')) {
        echo '<pre>' . e($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        echo 'Something went wrong. Please try again later.';
    }
});

$router = new \Core\Router();
require __DIR__ . '/routes.php';

$router->dispatch(new \Core\Request());
