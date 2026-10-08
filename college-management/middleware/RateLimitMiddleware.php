<?php
declare(strict_types=1);

namespace Middleware;

use Core\Request;

/**
 * Simple file-based rate limiter keyed by IP + route, to slow down brute force
 * and abusive API usage without adding a Redis dependency (WAMP-friendly).
 */
final class RateLimitMiddleware
{
    public function __construct(private int $maxRequests = 60, private int $windowSeconds = 60)
    {
    }

    public function __invoke(Request $request): void
    {
        $key = md5(($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '|' . $request->uri());
        $file = STORAGE_PATH . '/logs/ratelimit_' . $key . '.json';

        $data = ['count' => 0, 'reset' => time() + $this->windowSeconds];
        if (file_exists($file)) {
            $decoded = json_decode((string) file_get_contents($file), true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        if (time() > $data['reset']) {
            $data = ['count' => 0, 'reset' => time() + $this->windowSeconds];
        }

        $data['count']++;
        file_put_contents($file, json_encode($data));

        if ($data['count'] > $this->maxRequests) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Too many requests. Please slow down.']);
            exit;
        }
    }
}
