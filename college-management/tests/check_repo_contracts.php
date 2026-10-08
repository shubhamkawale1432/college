<?php
declare(strict_types=1);

require __DIR__ . '/../config/app.php';
require __DIR__ . '/../config/constants.php';
require __DIR__ . '/../core/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Core\\' => __DIR__ . '/../core/',
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

$controllerFiles = glob(__DIR__ . '/../controllers/*.php');
$repos = [
    'attendance' => \Repositories\AttendanceRepository::class,
    'students' => \Repositories\StudentRepository::class,
    'courses' => \Repositories\CourseRepository::class,
    'departments' => \Repositories\DepartmentRepository::class,
    'faculty' => \Repositories\FacultyRepository::class,
    'fees' => \Repositories\FeeRepository::class,
    'transactions' => \Repositories\TransactionRepository::class,
    'complaints' => \Repositories\ComplaintRepository::class,
    'notices' => \Repositories\NoticeRepository::class,
    'notifications' => \Repositories\NotificationRepository::class,
    'library' => \Repositories\LibraryRepository::class,
    'exams' => \Repositories\ExamRepository::class,
    'results' => \Repositories\ResultRepository::class,
    'logs' => \Repositories\ActivityLogRepository::class,
    'users' => \Repositories\UserRepository::class,
    'settings' => \Repositories\SettingRepository::class,
    'roles' => \Repositories\RoleRepository::class,
];

echo "Checking repository methods...\n";
$issues = 0;

foreach ($controllerFiles as $cf) {
    $code = file_get_contents($cf);
    $cName = basename($cf, '.php');
    
    // Look for $this->repo->method or $repo->method
    preg_match_all('/\$([a-zA-Z0-9_]+)->([a-zA-Z0-9_]+)\s*\(/', $code, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        $var = strtolower($m[1]);
        $method = $m[2];
        
        // Find if $var maps to a repo
        $targetRepoClass = null;
        if (isset($repos[$var])) {
            $targetRepoClass = $repos[$var];
        } else {
            foreach ($repos as $rKey => $rClass) {
                if (str_contains($var, $rKey)) {
                    $targetRepoClass = $rClass;
                    break;
                }
            }
        }
        
        if ($targetRepoClass) {
            if (!method_exists($targetRepoClass, $method)) {
                echo "MISSING METHOD in {$targetRepoClass}: {$method}() called in {$cName}\n";
                $issues++;
            }
        }
    }
}

if ($issues === 0) {
    echo "ALL repository method calls exist and are valid!\n";
} else {
    echo "Found {$issues} missing method(s)!\n";
}
exit($issues > 0 ? 1 : 0);
