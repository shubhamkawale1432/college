<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;
use Repositories\UserRepository;
use Repositories\StudentRepository;
use Repositories\ActivityLogRepository;
use Services\MailerService;

final class AuthController extends Controller
{
    private Auth $auth;
    private UserRepository $users;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserRepository();
        $this->logs = new ActivityLogRepository();
        $this->auth = new Auth($this->users, $this->logs);
    }

    public function showLogin(): void
    {
        if (is_authenticated()) {
            redirect('/dashboard');
        }
        $this->view('auth.login', ['title' => 'Sign In']);
    }

    public function login(): void
    {
        $this->requireCsrf();
        $data = $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $result = $this->auth->attempt($data['email'], $data['password']);

        switch ($result['status']) {
            case 'ok':
                flash('success', 'Welcome back, ' . auth()['name'] . '!');
                redirect('/dashboard');
                break;
            case '2fa_required':
                redirect('/login/verify');
                break;
            case 'locked':
                flash('error', 'Account locked due to too many failed attempts. Please try again later.');
                redirect('/login');
                break;
            default:
                flash('error', 'Invalid email or password.');
                redirect('/login');
        }
    }

    public function showRegister(): void
    {
        if (is_authenticated()) {
            redirect('/dashboard');
        }
        $this->view('auth.register', ['title' => 'Create Account']);
    }

    public function register(): void
    {
        $this->requireCsrf();
        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role' => 'required|in:student,faculty,operator',
        ]);

        if ($this->users->findByEmail($data['email'], true)) {
            flash('error', 'An account with this email address already exists.');
            redirect('/register');
        }

        Database::beginTransaction();
        try {
            $userId = $this->users->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => $data['role'],
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // If role is student, auto-generate student record
            if ($data['role'] === 'student') {
                $students = new StudentRepository();
                $admissionNo = 'CMS-' . date('Y') . '-' . str_pad((string)$userId, 4, '0', STR_PAD_LEFT);
                $students->create([
                    'user_id' => $userId,
                    'admission_no' => $admissionNo,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'status' => 'active',
                ]);
            }

            $this->logs->record($userId, 'user_registered', 'users', $userId, null, [
                'name' => $data['name'], 'email' => $data['email'], 'role' => $data['role'],
            ]);

            Database::commit();

            flash('success', 'Registration successful! You may now sign in.');
            redirect('/login');
        } catch (\Throwable $e) {
            Database::rollBack();
            flash('error', 'Registration failed: ' . $e->getMessage());
            redirect('/register');
        }
    }

    public function showForgotPassword(): void
    {
        $this->view('auth.forgot-password', ['title' => 'Reset Password']);
    }

    public function sendResetLink(): void
    {
        $this->requireCsrf();
        $data = $this->validate([
            'email' => 'required|email',
        ]);

        $user = $this->users->findByEmail($data['email']);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);
            $this->users->update($user['id'], [
                'reset_token' => $token,
                'reset_expires_at' => $expires,
            ]);

            $resetUrl = base_url("reset-password?token={$token}");
            MailerService::sendPasswordReset($user['email'], $user['name'], $resetUrl);
            $this->logs->record($user['id'], 'password_reset_requested', 'users', $user['id'], null, null);
        }

        // Always show friendly message to prevent user enumeration
        flash('success', 'If an account exists with that email, a password reset link has been dispatched.');
        redirect('/login');
    }

    public function showResetPassword(): void
    {
        $token = $this->request->input('token', '');
        if (!$token) {
            flash('error', 'Missing password reset token.');
            redirect('/login');
        }

        $user = $this->users->findByResetToken($token);
        if (!$user) {
            flash('error', 'This password reset link is invalid or has expired.');
            redirect('/forgot-password');
        }

        $this->view('auth.reset-password', ['token' => $token, 'title' => 'Set New Password']);
    }

    public function resetPassword(): void
    {
        $this->requireCsrf();
        $data = $this->validate([
            'token' => 'required',
            'password' => 'required|min:8',
        ]);

        $user = $this->users->findByResetToken($data['token']);
        if (!$user) {
            flash('error', 'This password reset link is invalid or has expired.');
            redirect('/forgot-password');
        }

        $this->users->update($user['id'], [
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_expires_at' => null,
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        $this->logs->record($user['id'], 'password_reset_completed', 'users', $user['id'], null, null);

        flash('success', 'Password reset successfully! You can now log in.');
        redirect('/login');
    }

    public function showTwoFactor(): void
    {
        if (empty($_SESSION['2fa_pending'])) {
            redirect('/login');
        }
        $this->view('auth.two-factor', ['title' => 'Two-Factor Verification']);
    }

    public function verifyTwoFactor(): void
    {
        $this->requireCsrf();
        $code = $this->request->input('code', '');
        if ($this->auth->verifyTwoFactor($code)) {
            redirect('/dashboard');
        }
        flash('error', 'Invalid or expired verification code.');
        redirect('/login/verify');
    }

    public function logout(): void
    {
        $this->auth->logout();
        redirect('/login');
    }

    public function showChangePassword(): void
    {
        $this->view('auth.change-password', ['title' => 'Change Password']);
    }

    public function changePassword(): void
    {
        $this->requireCsrf();
        $data = $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
        ]);

        $user = $this->users->find(auth()['id']);

        if (!password_verify($data['current_password'], $user['password'])) {
            flash('error', 'Current password is incorrect.');
            redirect('/change-password');
        }

        $this->users->update($user['id'], ['password' => password_hash($data['new_password'], PASSWORD_DEFAULT)]);
        $this->logs->record($user['id'], 'password_changed', 'users', $user['id'], null, null);

        flash('success', 'Password updated successfully.');
        redirect('/profile');
    }
}
