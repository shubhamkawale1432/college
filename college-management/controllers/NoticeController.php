<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\NoticeRepository;
use Repositories\NotificationRepository;
use Repositories\ActivityLogRepository;

final class NoticeController extends Controller
{
    private NoticeRepository $notices;
    private NotificationRepository $notifications;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->notices = new NoticeRepository();
        $this->notifications = new NotificationRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $this->view('notices.index', [
            'notices' => $this->notices->all('created_at DESC'),
            'title' => 'Notice Board & Bulletins',
        ]);
    }

    public function store(): void
    {
        $this->authorize('admin', 'operator', 'faculty');
        $this->requireCsrf();

        $data = $this->validate([
            'title' => 'required|min:3|max:150',
            'body' => 'required',
        ]);

        $id = $this->notices->create([
            'title' => trim($data['title']),
            'body' => trim($data['body']),
            'posted_by' => auth()['id'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Auto broadcast in notifications
        $this->notifications->broadcast(
            "New Notice: " . $data['title'],
            substr(strip_tags($data['body']), 0, 150) . '...',
            'info',
            null,
            'notices'
        );

        $this->logs->record(auth()['id'], 'notice_posted', 'notices', $id, null, $data);
        flash('success', 'Notice posted and announced to all users.');
        redirect('/notices');
    }

    public function destroy(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $nid = (int)$id;

        $before = $this->notices->find($nid);
        $this->notices->delete($nid);
        $this->logs->record(auth()['id'], 'notice_deleted', 'notices', $nid, $before, null);

        flash('success', 'Notice removed.');
        redirect('/notices');
    }
}
