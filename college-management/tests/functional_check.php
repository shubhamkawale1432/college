<?php
declare(strict_types=1);

/**
 * Functional smoke & integration tester for Core modules
 */

require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/constants.php';
require __DIR__ . '/../core/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Core\\' => __DIR__ . '/../core/',
        'Middleware\\' => __DIR__ . '/../middleware/',
        'Repositories\\' => __DIR__ . '/../repositories/',
        'Services\\' => __DIR__ . '/../services/',
        'Controllers\\' => __DIR__ . '/../controllers/',
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

session_start();

$passed = 0;
$failed = 0;

function assertTest(string $desc, bool $condition): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "PASS: {$desc}\n";
    } else {
        $failed++;
        echo "FAIL: {$desc}\n";
    }
}

// 1. Test Base URL and Redirect URL Safety
assertTest('base_url relative path', base_url('login') === 'http://localhost/college-management/login');
assertTest('base_url absolute path preserves URL', base_url('http://example.com/test') === 'http://example.com/test');

// 2. Test CSRF Generation and Verification
$token = csrf_token();
assertTest('csrf_token generated non-empty', !empty($token) && strlen($token) === 64);
assertTest('verify_csrf valid token', verify_csrf($token) === true);
assertTest('verify_csrf invalid token', verify_csrf('invalid_token') === false);

// 3. Test Validator
$v1 = new \Core\Validator(['email' => 'admin@college.local', 'age' => '25', 'optional_phone' => ''], [
    'email' => 'required|email',
    'age' => 'required|numeric',
    'optional_phone' => 'min:10',
]);
assertTest('Validator passes valid data and ignores empty optional min field', !$v1->fails());

$v2 = new \Core\Validator(['email' => 'invalid-email', 'age' => 'abc'], [
    'email' => 'required|email',
    'age' => 'required|numeric',
]);
assertTest('Validator catches invalid email and non-numeric', $v2->fails());
assertTest('Validator reports two errors', count($v2->firstErrors()) === 2);

// 4. Test Helper Formatters
assertTest('format_currency handles string and float', format_currency(1250.50) === '$1,250.50');
assertTest('time_ago handles null and recent date', time_ago(null) === 'Never');
assertTest('time_ago recent seconds', time_ago(date('Y-m-d H:i:s')) === 'just now');
assertTest('avatar_url generates UI Avatars default', str_contains(avatar_url(null, 'Jane Smith'), 'ui-avatars.com'));

// 5. Test Settings Aliases
assertTest('setting app_name falls back gracefully', setting('app_name') !== null);

// 6. Test Repository Contract Consistency
assertTest('AttendanceRepository has forStudent method', method_exists(\Repositories\AttendanceRepository::class, 'forStudent'));
assertTest('FeeRepository has forStudent method', method_exists(\Repositories\FeeRepository::class, 'forStudent'));
assertTest('ResultRepository has forStudent method', method_exists(\Repositories\ResultRepository::class, 'forStudent'));
assertTest('StudentRepository has findByUserId method', method_exists(\Repositories\StudentRepository::class, 'findByUserId'));

echo "\nSummary: {$passed} passed, {$failed} failed.\n";
exit($failed > 0 ? 1 : 0);
