<?php
declare(strict_types=1);

namespace Api\Controllers;

use Core\Controller;
use Repositories\StudentRepository;
use Repositories\UserRepository;
use Services\StudentService;

/**
 * Versioned REST API (v1) for students. Every request must carry a valid
 * bearer token (see TokenAuthMiddleware) and is subject to rate limiting.
 */
final class StudentApiController extends Controller
{
    public function index(): void
    {
        $repo = new StudentRepository();
        $page = (int) $this->request->input('page', 1);
        $result = $repo->paginate($page, 20, 'created_at DESC');
        $this->json($result);
    }

    public function show(string $id): void
    {
        $repo = new StudentRepository();
        $student = $repo->findWithRelations((int) $id);
        if (!$student) {
            $this->json(['error' => 'Not found'], 404);
        }
        $this->json($student);
    }

    public function store(): void
    {
        $this->requireApiRole('admin');
        $body = $this->request->jsonBody();
        $required = ['name', 'email'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                $this->json(['error' => "{$field} is required"], 422);
            }
        }
        try {
            $result = (new StudentService())->enroll($body, $this->apiUserId());
            $this->json($result, 201);
        } catch (\Throwable $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(string $id): void
    {
        $this->requireApiRole('admin');
        $body = $this->request->jsonBody();
        (new StudentService())->update((int) $id, $body, $this->apiUserId());
        $this->json(['status' => 'updated']);
    }

    public function destroy(string $id): void
    {
        $this->requireApiRole('admin');
        (new StudentService())->delete((int) $id, $this->apiUserId());
        $this->json(['status' => 'deleted']);
    }

    private function apiUserId(): ?int
    {
        return $GLOBALS['api_user']['user_id'] ?? null;
    }

    private function requireApiRole(string ...$roles): void
    {
        $userId = $this->apiUserId();
        $user = $userId ? (new UserRepository())->find($userId) : null;
        if (!$user || !in_array($user['role'], $roles, true)) {
            $this->json(['error' => 'Forbidden'], 403);
        }
    }
}
