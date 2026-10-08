<?php
declare(strict_types=1);

namespace Middleware;

use Core\Request;
use Repositories\ApiTokenRepository;

/**
 * Authenticates REST API requests via a bearer token (hashed at rest,
 * looked up by prefix for efficiency).
 */
final class TokenAuthMiddleware
{
    public function __invoke(Request $request): void
    {
        $token = $request->bearerToken();
        if (!$token) {
            $this->reject();
        }

        $repo = new ApiTokenRepository();
        $record = $repo->findValidByPlainToken($token);
        if (!$record) {
            $this->reject();
        }

        $_SESSION['api_user_id'] = $record['user_id'];
        $GLOBALS['api_user'] = $record;
    }

    private function reject(): void
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}
