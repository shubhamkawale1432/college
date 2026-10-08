<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\ComplaintRepository;
use Repositories\ActivityLogRepository;
use Services\UploadService;

final class ComplaintController extends Controller
{
    private ComplaintRepository $complaints;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->complaints = new ComplaintRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $page = (int)$this->request->input('page', 1);
        $status = $this->request->input('status');
        $userFilter = null;

        // If student or faculty, only see their own tickets unless admin/operator
        if (!has_role('admin', 'operator')) {
            $userFilter = auth()['id'];
        }

        $pagination = $this->complaints->allWithUsers($page, 15, $status, $userFilter);

        $this->view('complaints.index', [
            'pagination' => $pagination,
            'currentStatus' => $status,
            'title' => 'Complaints & Helpdesk',
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();
        $data = $this->validate([
            'title' => 'required|min:4|max:200',
            'category' => 'required|in:academic,hostel,fees,library,infrastructure,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|min:10',
        ]);

        $attachmentPath = null;
        if (!empty($_FILES['attachment']['name'])) {
            try {
                $attachmentPath = UploadService::saveDocument($_FILES['attachment'], 'complaints');
            } catch (\Throwable $e) {
                flash('error', $e->getMessage());
                redirect('/complaints');
            }
        }

        $ticketId = $this->complaints->createTicket([
            'user_id' => auth()['id'],
            'title' => $data['title'],
            'category' => $data['category'],
            'priority' => $data['priority'],
            'description' => $data['description'],
            'attachment' => $attachmentPath,
            'status' => 'open',
        ]);

        $this->logs->record(auth()['id'], 'complaint_submitted', 'complaints', $ticketId, null, [
            'title' => $data['title'], 'category' => $data['category'],
        ]);

        flash('success', 'Your grievance ticket has been submitted. Academic staff will respond shortly.');
        redirect('/complaints');
    }

    public function show(string $id): void
    {
        $cid = (int)$id;
        $ticket = $this->complaints->findWithUser($cid);
        if (!$ticket) {
            flash('error', 'Ticket not found.');
            redirect('/complaints');
        }

        if (!has_role('admin', 'operator') && (int)$ticket['user_id'] !== auth()['id']) {
            http_response_code(403);
            $this->view('errors.403');
            return;
        }

        $this->view('complaints.show', [
            'ticket' => $ticket,
            'title' => "Ticket #{$ticket['ticket_no']}",
        ]);
    }

    public function updateStatus(string $id): void
    {
        $this->authorize('admin', 'operator');
        $this->requireCsrf();
        $cid = (int)$id;
        $ticket = $this->complaints->find($cid);
        if (!$ticket) {
            flash('error', 'Ticket not found.');
            redirect('/complaints');
        }

        $data = $this->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $adminNotes = $this->request->input('admin_notes');
        $resolvedAt = ($data['status'] === 'resolved' || $data['status'] === 'closed') ? date('Y-m-d H:i:s') : null;

        $updateData = [
            'status' => $data['status'],
            'admin_notes' => $adminNotes,
            'resolved_at' => $resolvedAt,
        ];

        $this->complaints->update($cid, $updateData);
        $this->logs->record(auth()['id'], 'complaint_status_updated', 'complaints', $cid, $ticket, $updateData);

        flash('success', "Ticket status updated to '{$data['status']}'.");
        redirect("/complaints/{$cid}");
    }
}
