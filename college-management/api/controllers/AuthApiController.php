<?php
declare(strict_types=1);

namespace Api\Controllers;

use Core\Controller;
use Core\Auth;
use Repositories\UserRepository;
use Repositories\ActivityLogRepository;
use Repositories\ApiTokenRepository;

final class AuthApiController extends Controller
{
    public function login(): void
    {
        $body = $this->request->jsonBody();
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        if (!$email || !$password) {
            $this->json(['error' => 'email and password are required'], 422);
        }

        $auth = new Auth(new UserRepository(), new ActivityLogRepository());
        $result = $auth->attempt($email, $password);

        if ($result['status'] !== 'ok') {
            $this->json(['error' => 'Invalid credentials, account locked, or 2FA required (use the web UI for 2FA)'], 401);
        }

        $tokens = new ApiTokenRepository();
        $token = $tokens->issue($result['user']['id'], 'api-login', 30);

        $this->json([
            'token' => $token,
            'user' => [
                'id' => $result['user']['id'],
                'name' => $result['user']['name'],
                'role' => $result['user']['role'],
            ],
        ]);
    }
}
