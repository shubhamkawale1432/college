<?php
declare(strict_types=1);

namespace Middleware;

use Core\Request;

final class RoleMiddleware
{
    public function __construct(private array $roles)
    {
    }

    public function __invoke(Request $request): void
    {
        if (!has_role(...$this->roles)) {
            http_response_code(403);
            view('errors.403');
            exit;
        }
    }
}
