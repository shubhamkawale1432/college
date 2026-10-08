<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\NotificationRepository;
use Repositories\ActivityLogRepository;

final class NotificationController extends Controller
{
    private NotificationRepository $notifications;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->notifications = new NotificationRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $userId = auth()['id'];
        $role = auth_role();
        $items = $this->notifications->forUser($userId, $role, 50);

        $this->view('notifications.index', [
            'notifications' => $items,
            'title' => 'Notifications & Announcements',
        ]);
    }

    public function markAsRead(string $id): void
    {
        $this->requireCsrf();
        $nid = (int)$id;
        $this->notifications->markAsRead($nid, auth()['id']);

        if ($this->request->input('ajax')) {
            $this->json(['success' => true]);
        }

        flash('success', 'Notification marked as read.');
        redirect('/notifications');
    }

    public function markAllAsRead(): void
    {
        $this->requireCsrf();
        $this->notifications->markAllAsRead(auth()['id'], auth_role());

        if ($this->request->input('ajax')) {
            $this->json(['success' => true]);
        }

        flash('success', 'All notifications marked as read.');
        redirect('/notifications');
    }

    public function broadcast(): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();

        $data = $this->validate([
            'title' => 'required|min:3|max:200',
            'message' => 'required',
            'type' => 'required|in:info,success,warning,danger',
        ]);

        $targetRole = $this->request->input('target_role');
        if ($targetRole === 'all' || empty($targetRole)) {
            $targetRole = null;
        }

        $link = $this->request->input('link');

        $this->notifications->broadcast(
            $data['title'],
            $data['message'],
            $data['type'],
            $targetRole,
            $link ?: null
        );

        $this->logs->record(auth()['id'], 'notification_broadcast', 'notifications', null, null, [
            'title' => $data['title'], 'target_role' => $targetRole,
        ]);

        flash('success', 'Announcement broadcasted successfully to all target users.');
        redirect('/notifications');
    }
}
