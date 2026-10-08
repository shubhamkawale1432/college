<?php
declare(strict_types=1);

/**
 * e() - escape output for HTML context. Use this around every dynamic value
 * echoed into a view to prevent XSS.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function config(string $key, $default = null)
{
    static $app = null;
    if ($app === null) {
        $app = require BASE_PATH . '/config/app.php';
    }
    return $app[$key] ?? $default;
}

function setting(string $key, ?string $default = null): ?string
{
    static $settings = null;
    if ($settings === null) {
        try {
            $db = \Core\Database::connection();
            $rows = $db->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
            $settings = [];
            foreach ($rows as $r) {
                $settings[$r['setting_key']] = $r['setting_value'];
            }
        } catch (\Throwable) {
            $settings = [];
        }
    }
    // Aliases to ensure consistency across views
    if ($key === 'app_name') {
        return $settings['app_name'] ?? $settings['institute_name'] ?? config('name') ?? $default;
    }
    if ($key === 'contact_email') {
        return $settings['contact_email'] ?? $settings['institute_email'] ?? $default;
    }
    if ($key === 'contact_phone') {
        return $settings['contact_phone'] ?? $settings['institute_phone'] ?? $default;
    }
    return $settings[$key] ?? $default;
}

function base_url(string $path = ''): string
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    return rtrim(config('url', ''), '/') . '/' . ltrim($path, '/');
}

function upload_url(?string $path, string $default = ''): string
{
    if (empty($path)) {
        return $default;
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    return base_url('storage/uploads/' . ltrim($path, '/'));
}

function avatar_url(?string $avatar, string $name = 'User'): string
{
    if (!empty($avatar)) {
        return upload_url($avatar);
    }
    // High-resolution initials SVG via UI Avatars
    $encodedName = urlencode($name);
    return "https://ui-avatars.com/api/?name={$encodedName}&background=4f46e5&color=fff&bold=true&size=128";
}

function format_currency(float|int|string|null $amount): string
{
    $num = (float) ($amount ?? 0);
    $sym = setting('currency_symbol', '$');
    return $sym . number_format($num, 2);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    return !empty($_SESSION['csrf_token']) && !empty($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

function redirect(string $path): never
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header('Location: ' . $path);
        exit;
    }
    header('Location: ' . base_url($path));
    exit;
}

function view(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $path = VIEW_PATH . '/' . str_replace('.', '/', $name) . '.php';
    if (!file_exists($path)) {
        throw new \RuntimeException("View not found: {$name}");
    }
    require $path;
}

function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_role(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}

function is_authenticated(): bool
{
    return isset($_SESSION['user']);
}

function has_role(string ...$roles): bool
{
    $userRole = auth_role();
    if (!$userRole) {
        return false;
    }
    if ($userRole === 'admin') {
        return true;
    }
    return in_array($userRole, $roles, true);
}

function can(string $permission): bool
{
    $role = auth_role();
    if (!$role) {
        return false;
    }
    if ($role === 'admin') {
        return true;
    }
    static $userPermissions = null;
    if ($userPermissions === null) {
        try {
            $db = \Core\Database::connection();
            $stmt = $db->prepare("
                SELECT p.name FROM permissions p
                JOIN role_permissions rp ON rp.permission_id = p.id
                JOIN roles r ON r.id = rp.role_id
                WHERE r.name = ?
            ");
            $stmt->execute([$role]);
            $userPermissions = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
        } catch (\Throwable) {
            $userPermissions = [];
        }
    }
    return in_array($permission, $userPermissions, true);
}

function unread_notifications_count(): int
{
    if (!is_authenticated()) {
        return 0;
    }
    static $count = null;
    if ($count === null) {
        try {
            $db = \Core\Database::connection();
            $userId = auth()['id'];
            $role = auth_role();
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM notifications
                WHERE is_read = 0 AND (user_id = ? OR (user_id IS NULL AND (target_role IS NULL OR target_role = ?)))
            ");
            $stmt->execute([$userId, $role]);
            $count = (int) $stmt->fetchColumn();
        } catch (\Throwable) {
            $count = 0;
        }
    }
    return $count;
}

function time_ago(?string $datetime): string
{
    if (!$datetime) {
        return 'Never';
    }
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) {
        return 'just now';
    }
    $minutes = round($diff / 60);
    if ($minutes < 60) {
        return $minutes . 'm ago';
    }
    $hours = round($diff / 3600);
    if ($hours < 24) {
        return $hours . 'h ago';
    }
    $days = round($diff / 86400);
    if ($days < 30) {
        return $days . 'd ago';
    }
    return date('M j, Y', $time);
}

function active_nav(string $path): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    $cleanPath = trim($path, '/');
    if ($cleanPath === 'dashboard') {
        return (str_ends_with($uri, '/dashboard') || str_ends_with($uri, '/dashboard/')) ? 'active' : '';
    }
    if ($cleanPath !== '' && str_contains($uri, '/' . $cleanPath)) {
        return 'active';
    }
    return '';
}

/** Apply security-relevant HTTP headers on every response. */
function send_security_headers(): void
{
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; img-src 'self' data: https: blob:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;");
    if (!empty($_SERVER['HTTPS'])) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
