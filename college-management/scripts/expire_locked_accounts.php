<?php
declare(strict_types=1);

/**
 * Cron helper: clears expired account lockouts so users aren't stuck locked
 * out past their lockout window even if they never retry to trigger the
 * inline check in Auth::attempt(). Suggested schedule: every 5 minutes.
 *
 *   CRON schedule example: every 5 minutes:
 *   * /5 * * * * php /path/to/college-management/scripts/expire_locked_accounts.php
 */

require dirname(__DIR__) . '/config/app.php';
require dirname(__DIR__) . '/config/constants.php';
require dirname(__DIR__) . '/core/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefixes = ['Core\\' => __DIR__ . '/../core/', 'Repositories\\' => __DIR__ . '/../repositories/'];
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

$db = \Core\Database::connection();
$stmt = $db->prepare("UPDATE users SET locked_until = NULL, failed_attempts = 0 WHERE locked_until IS NOT NULL AND locked_until <= NOW()");
$stmt->execute();
echo "Cleared lockouts for {$stmt->rowCount()} account(s).\n";
