<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\ActivityLogRepository;
use Repositories\UserRepository;

final class ActivityLogController extends Controller
{
    private ActivityLogRepository $logs;
    private UserRepository $users;

    public function __construct()
    {
        parent::__construct();
        $this->logs = new ActivityLogRepository();
        $this->users = new UserRepository();
    }

    public function index(): void
    {
        $this->authorize('admin', 'operator');
        $page = (int)$this->request->input('page', 1);
        $action = $this->request->input('action');
        $userId = $this->request->input('user_id') ? (int)$this->request->input('user_id') : null;

        $pagination = $this->logs->paginateLogs($page, 20, $action, $userId);
        $users = $this->users->all('name');

        $this->view('activity_logs.index', [
            'pagination' => $pagination,
            'users' => $users,
            'currentAction' => $action,
            'currentUserId' => $userId,
            'title' => 'Audit & Activity Logs',
        ]);
    }
}
