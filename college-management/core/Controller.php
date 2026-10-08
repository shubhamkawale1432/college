<?php
declare(strict_types=1);

namespace Core;

abstract class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    protected function view(string $name, array $data = []): void
    {
        view($name, $data);
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function requireCsrf(): void
    {
        if (!verify_csrf($this->request->input('csrf_token'))) {
            http_response_code(419);
            flash('error', 'Your session expired, please try again.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    protected function validate(array $rules): array
    {
        $validator = new Validator($this->request->all(), $rules);
        if ($validator->fails()) {
            $_SESSION['_old'] = $this->request->all();
            $_SESSION['_errors'] = $validator->firstErrors();
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
        return $this->request->all();
    }

    protected function authorize(string ...$roles): void
    {
        if (!has_role(...$roles)) {
            http_response_code(403);
            $this->view('errors.403');
            exit;
        }
    }
}
