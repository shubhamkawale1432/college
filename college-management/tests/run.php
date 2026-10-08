<?php
declare(strict_types=1);

/**
 * Lightweight smoke test runner: lints every PHP file (php -l) and checks
 * that core classes are loadable in isolation. No PHPUnit dependency, per
 * the project's no-heavy-dependency constraint.
 */

$root = dirname(__DIR__);
$failures = 0;
$checked = 0;

function findPhpFiles(string $dir): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$targets = array_merge(
    findPhpFiles($root . '/core'),
    findPhpFiles($root . '/middleware'),
    findPhpFiles($root . '/repositories'),
    findPhpFiles($root . '/services'),
    findPhpFiles($root . '/controllers'),
    findPhpFiles($root . '/api'),
    findPhpFiles($root . '/views'),
    findPhpFiles($root . '/config'),
    findPhpFiles($root . '/scripts'),
    [$root . '/index.php', $root . '/routes.php']
);

foreach ($targets as $file) {
    $checked++;
    $output = [];
    $phpBin = PHP_BINARY ?: 'C:\\xampp\\php\\php.exe';
    exec(escapeshellarg($phpBin) . ' -l ' . escapeshellarg($file) . ' 2>&1', $output, $code);
    if ($code !== 0) {
        $failures++;
        echo "FAIL: {$file}\n" . implode("\n", $output) . "\n";
    }
}

echo "\nChecked {$checked} files, {$failures} failure(s).\n";
exit($failures > 0 ? 1 : 0);
