<?php
declare(strict_types=1);

namespace Core;

use Repositories\UserRepository;
use Repositories\ActivityLogRepository;

final class Auth
{
    public function __construct(
        private UserRepository $users,
        private ActivityLogRepository $logs
    ) {
    }

    /**
     * Attempt to authenticate. Returns:
     *  ['status' => 'ok', 'user' => array]
     *  ['status' => 'locked']
     *  ['status' => 'invalid']
     *  ['status' => '2fa_required', 'user_id' => int]
     */
    public function attempt(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);
        $maxAttempts = config('max_login_attempts', 5);
        $lockoutSeconds = config('lockout_seconds', 300);

        if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return ['status' => 'locked'];
        }

        if (!$user || !password_verify($password, $user['password'])) {
            if ($user) {
                $attempts = (int) $user['failed_attempts'] + 1;
                $lockUntil = null;
                if ($attempts >= $maxAttempts) {
                    $lockUntil = date('Y-m-d H:i:s', time() + $lockoutSeconds);
                }
                $this->users->update($user['id'], [
                    'failed_attempts' => $attempts,
                    'locked_until' => $lockUntil,
                ]);
                $this->logs->record($user['id'], 'login_failed', 'users', $user['id'], null, null);
            }
            return ['status' => 'invalid'];
        }

        // Reset failed attempts on success
        $this->users->update($user['id'], ['failed_attempts' => 0, 'locked_until' => null]);

        if ((bool) $user['two_factor_enabled']) {
            $code = (string) random_int(100000, 999999);
            $_SESSION['2fa_pending'] = [
                'user_id' => $user['id'],
                'code_hash' => password_hash($code, PASSWORD_DEFAULT),
                'expires' => time() + 300,
            ];
            // In production this would be emailed/SMS'd; logged here since no mail transport is configured.
            Logger::info("2FA code for user {$user['id']}: {$code}");
            return ['status' => '2fa_required', 'user_id' => $user['id']];
        }

        $this->login($user);
        return ['status' => 'ok', 'user' => $user];
    }

    public function verifyTwoFactor(string $code): bool
    {
        $pending = $_SESSION['2fa_pending'] ?? null;
        if (!$pending || $pending['expires'] < time()) {
            return false;
        }
        if (!password_verify($code, $pending['code_hash'])) {
            return false;
        }
        $user = $this->users->find($pending['user_id']);
        unset($_SESSION['2fa_pending']);
        if ($user) {
            $this->login($user);
            return true;
        }
        return false;
    }

    public function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'avatar' => $user['avatar'] ?? null,
        ];
        $_SESSION['login_time'] = time();
        $this->users->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        $this->logs->record($user['id'], 'login_success', 'users', $user['id'], null, null);
    }

    public function logout(): void
    {
        $userId = $_SESSION['user']['id'] ?? null;
        if ($userId) {
            $this->logs->record($userId, 'logout', 'users', $userId, null, null);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function validateSessionLifetime(): void
    {
        if (!isset($_SESSION['user'], $_SESSION['login_time'])) {
            return;
        }
        if (time() - $_SESSION['login_time'] > config('session_lifetime', 3600)) {
            $this->logout();
            flash('error', 'Your session expired. Please log in again.');
            redirect('/login');
        }
    }
}
