<?php
declare(strict_types=1);

namespace Middleware;

use Core\Request;

final class AuthMiddleware
{
    public function __invoke(Request $request): void
    {
        if (!is_authenticated()) {
            flash('error', 'Please log in to continue.');
            redirect('/login');
        }

        $sessionLifetime = (int) config('session_lifetime', 3600);
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $sessionLifetime)) {
            unset($_SESSION['user'], $_SESSION['login_time']);
            flash('error', 'Your session expired. Please log in again.');
            redirect('/login');
        }
    }
}
