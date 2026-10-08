<?php
declare(strict_types=1);

namespace Middleware;

use Core\Request;

final class GuestMiddleware
{
    public function __invoke(Request $request): void
    {
        if (is_authenticated()) {
            redirect('/dashboard');
        }
    }
}
