<?php
declare(strict_types=1);

use Controllers\AuthController;
use Controllers\HomeController;
use Controllers\DashboardController;
use Controllers\ProfileController;
use Controllers\UserController;
use Controllers\RoleController;
use Controllers\StudentController;
use Controllers\DepartmentController;
use Controllers\CourseController;
use Controllers\FacultyController;
use Controllers\AttendanceController;
use Controllers\NoticeController;
use Controllers\ResultController;
use Controllers\FeeController;
use Controllers\ComplaintController;
use Controllers\LibraryController;
use Controllers\SettingController;
use Controllers\ActivityLogController;
use Controllers\NotificationController;
use Controllers\ReportController;

use Api\Controllers\AuthApiController;
use Api\Controllers\StudentApiController;
use Middleware\AuthMiddleware;
use Middleware\GuestMiddleware;
use Middleware\RateLimitMiddleware;
use Middleware\TokenAuthMiddleware;

/** @var \Core\Router $router */

$authMw = new AuthMiddleware();
$guestMw = new GuestMiddleware();

// ---- Public & Home routes ----
$router->get('/', [HomeController::class, 'index']);
$router->get('/home', [HomeController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index'], [$authMw]);

// ---- Auth routes ----
$router->get('/login', [AuthController::class, 'showLogin'], [$guestMw]);
$router->post('/login', [AuthController::class, 'login'], [$guestMw]);
$router->get('/register', [AuthController::class, 'showRegister'], [$guestMw]);
$router->post('/register', [AuthController::class, 'register'], [$guestMw]);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword'], [$guestMw]);
$router->post('/forgot-password', [AuthController::class, 'sendResetLink'], [$guestMw]);
$router->get('/reset-password', [AuthController::class, 'showResetPassword'], [$guestMw]);
$router->post('/reset-password', [AuthController::class, 'resetPassword'], [$guestMw]);
$router->get('/login/verify', [AuthController::class, 'showTwoFactor']);
$router->post('/login/verify', [AuthController::class, 'verifyTwoFactor']);
$router->post('/logout', [AuthController::class, 'logout'], [$authMw]);
$router->get('/change-password', [AuthController::class, 'showChangePassword'], [$authMw]);
$router->post('/change-password', [AuthController::class, 'changePassword'], [$authMw]);

// ---- Dashboard & Profile ----
$router->get('/dashboard', [DashboardController::class, 'index'], [$authMw]);
$router->get('/profile', [ProfileController::class, 'index'], [$authMw]);
$router->post('/profile', [ProfileController::class, 'update'], [$authMw]);

// ---- User Management ----
$router->get('/users', [UserController::class, 'index'], [$authMw]);
$router->post('/users', [UserController::class, 'store'], [$authMw]);
$router->post('/users/{id}', [UserController::class, 'update'], [$authMw]);
$router->post('/users/{id}/unlock', [UserController::class, 'unlock'], [$authMw]);
$router->post('/users/{id}/delete', [UserController::class, 'destroy'], [$authMw]);
$router->post('/users/{id}/restore', [UserController::class, 'restore'], [$authMw]);

// ---- Role & Permission Management ----
$router->get('/roles', [RoleController::class, 'index'], [$authMw]);
$router->post('/roles', [RoleController::class, 'store'], [$authMw]);
$router->post('/roles/{id}/permissions', [RoleController::class, 'updatePermissions'], [$authMw]);

// ---- Students Module ----
$router->get('/students', [StudentController::class, 'index'], [$authMw]);
$router->get('/students/create', [StudentController::class, 'create'], [$authMw]);
$router->post('/students', [StudentController::class, 'store'], [$authMw]);
$router->get('/students/{id}', [StudentController::class, 'show'], [$authMw]);
$router->get('/students/{id}/edit', [StudentController::class, 'edit'], [$authMw]);
$router->post('/students/{id}', [StudentController::class, 'update'], [$authMw]);
$router->post('/students/{id}/delete', [StudentController::class, 'destroy'], [$authMw]);
$router->post('/students/{id}/restore', [StudentController::class, 'restore'], [$authMw]);

// ---- Departments Module ----
$router->get('/departments', [DepartmentController::class, 'index'], [$authMw]);
$router->post('/departments', [DepartmentController::class, 'store'], [$authMw]);
$router->post('/departments/{id}', [DepartmentController::class, 'update'], [$authMw]);
$router->post('/departments/{id}/delete', [DepartmentController::class, 'destroy'], [$authMw]);
$router->post('/departments/{id}/restore', [DepartmentController::class, 'restore'], [$authMw]);

// ---- Courses Module ----
$router->get('/courses', [CourseController::class, 'index'], [$authMw]);
$router->post('/courses', [CourseController::class, 'store'], [$authMw]);
$router->post('/courses/{id}', [CourseController::class, 'update'], [$authMw]);
$router->post('/courses/{id}/delete', [CourseController::class, 'destroy'], [$authMw]);
$router->post('/courses/{id}/restore', [CourseController::class, 'restore'], [$authMw]);

// ---- Faculty Module ----
$router->get('/faculty', [FacultyController::class, 'index'], [$authMw]);
$router->post('/faculty', [FacultyController::class, 'store'], [$authMw]);
$router->post('/faculty/{id}/delete', [FacultyController::class, 'destroy'], [$authMw]);
$router->post('/faculty/{id}/restore', [FacultyController::class, 'restore'], [$authMw]);

// ---- Attendance Module ----
$router->get('/attendance', [AttendanceController::class, 'index'], [$authMw]);
$router->post('/attendance', [AttendanceController::class, 'store'], [$authMw]);

// ---- Notices & Bulletins ----
$router->get('/notices', [NoticeController::class, 'index'], [$authMw]);
$router->post('/notices', [NoticeController::class, 'store'], [$authMw]);
$router->post('/notices/{id}/delete', [NoticeController::class, 'destroy'], [$authMw]);

// ---- Exams & Results ----
$router->get('/results', [ResultController::class, 'index'], [$authMw]);
$router->get('/results/create', [ResultController::class, 'create'], [$authMw]);
$router->post('/results', [ResultController::class, 'store'], [$authMw]);
$router->post('/exams', [ResultController::class, 'storeExam'], [$authMw]);

// ---- Fees & Invoices & Receipts ----
$router->get('/fees', [FeeController::class, 'index'], [$authMw]);
$router->post('/fees', [FeeController::class, 'store'], [$authMw]);
$router->post('/fees/{id}/pay', [FeeController::class, 'pay'], [$authMw]);
$router->get('/fees/receipt/{id}', [FeeController::class, 'receipt'], [$authMw]);

// ---- Complaints & Grievances ----
$router->get('/complaints', [ComplaintController::class, 'index'], [$authMw]);
$router->post('/complaints', [ComplaintController::class, 'store'], [$authMw]);
$router->get('/complaints/{id}', [ComplaintController::class, 'show'], [$authMw]);
$router->post('/complaints/{id}/status', [ComplaintController::class, 'updateStatus'], [$authMw]);

// ---- Library / Inventory ----
$router->get('/library', [LibraryController::class, 'index'], [$authMw]);
$router->post('/library/books', [LibraryController::class, 'storeBook'], [$authMw]);
$router->post('/library/issue', [LibraryController::class, 'issueBook'], [$authMw]);
$router->post('/library/return/{id}', [LibraryController::class, 'returnBook'], [$authMw]);

// ---- Settings ----
$router->get('/settings', [SettingController::class, 'index'], [$authMw]);
$router->post('/settings', [SettingController::class, 'update'], [$authMw]);

// ---- Activity Logs ----
$router->get('/activity-logs', [ActivityLogController::class, 'index'], [$authMw]);

// ---- Notifications & Announcements ----
$router->get('/notifications', [NotificationController::class, 'index'], [$authMw]);
$router->post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'], [$authMw]);
$router->post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'], [$authMw]);
$router->post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'], [$authMw]);
$router->post('/notifications/broadcast', [NotificationController::class, 'broadcast'], [$authMw]);

// ---- Reports & Exports ----
$router->get('/reports', [ReportController::class, 'index'], [$authMw]);
$router->get('/reports/export', [ReportController::class, 'exportCsv'], [$authMw]);

// ---- REST API v1 (token auth + rate limiting) ----
$apiRateLimit = new RateLimitMiddleware(120, 60);
$tokenAuth = new TokenAuthMiddleware();

$router->post('/api/v1/auth/login', [AuthApiController::class, 'login'], [$apiRateLimit]);
$router->get('/api/v1/students', [StudentApiController::class, 'index'], [$apiRateLimit, $tokenAuth]);
$router->get('/api/v1/students/{id}', [StudentApiController::class, 'show'], [$apiRateLimit, $tokenAuth]);
$router->post('/api/v1/students', [StudentApiController::class, 'store'], [$apiRateLimit, $tokenAuth]);
$router->post('/api/v1/students/{id}', [StudentApiController::class, 'update'], [$apiRateLimit, $tokenAuth]);
$router->post('/api/v1/students/{id}/delete', [StudentApiController::class, 'destroy'], [$apiRateLimit, $tokenAuth]);
