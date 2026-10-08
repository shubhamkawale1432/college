<?php
$title = 'Complaints & Helpdesk - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
$items = $pagination['items'] ?? $pagination['data'] ?? [];
$totalPages = (int)($pagination['totalPages'] ?? $pagination['last_page'] ?? 1);
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title text-2xl font-bold">Grievance & Helpdesk Tickets</h1>
        <p class="text-muted text-sm">Submit support requests, report facility issues, and track redressal resolutions.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="openModal('newTicketModal')">
            <i class="fas fa-ticket-alt mr-1"></i> Submit New Ticket
        </button>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="flex gap-2 mb-6 border-b border-border pb-3 overflow-x-auto">
    <a href="<?= e(base_url('complaints')) ?>" class="btn btn-sm <?= empty($currentStatus) ? 'btn-primary' : 'btn-secondary' ?>">
        All Tickets
    </a>
    <a href="<?= e(base_url('complaints?status=open')) ?>" class="btn btn-sm <?= $currentStatus === 'open' ? 'btn-primary' : 'btn-secondary' ?>">
        <i class="fas fa-circle text-rose-500 mr-1 text-2xs"></i> Open
    </a>
    <a href="<?= e(base_url('complaints?status=in_progress')) ?>" class="btn btn-sm <?= $currentStatus === 'in_progress' ? 'btn-primary' : 'btn-secondary' ?>">
        <i class="fas fa-circle text-amber-500 mr-1 text-2xs"></i> In Progress
    </a>
    <a href="<?= e(base_url('complaints?status=resolved')) ?>" class="btn btn-sm <?= $currentStatus === 'resolved' ? 'btn-primary' : 'btn-secondary' ?>">
        <i class="fas fa-circle text-emerald-500 mr-1 text-2xs"></i> Resolved
    </a>
    <a href="<?= e(base_url('complaints?status=closed')) ?>" class="btn btn-sm <?= $currentStatus === 'closed' ? 'btn-primary' : 'btn-secondary' ?>">
        <i class="fas fa-circle text-muted mr-1 text-2xs"></i> Closed
    </a>
</div>

<!-- Tickets Table Card -->
<div class="card p-0 mb-6">
    <div class="card-header p-4 flex justify-between items-center border-b border-border">
        <span class="badge badge-primary font-bold px-2.5 py-1">
            <?= (int)$pagination['total'] ?> Tickets Found
        </span>
        <div class="text-xs text-muted">
            Page <?= (int)$pagination['page'] ?> of <?= $totalPages ?>
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Ticket Ref</th>
                    <th>Requester</th>
                    <th>Category</th>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-8 text-muted">
                            <i class="fas fa-inbox text-4xl mb-3 block opacity-30"></i>
                            No helpdesk tickets found in this view.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $t): ?>
                        <?php 
                        $priClass = match($t['priority']) {
                            'urgent' => 'badge-danger',
                            'high' => 'badge-warning',
                            'medium' => 'badge-primary',
                            default => 'badge-neutral',
                        };
                        $statusClass = match($t['status']) {
                            'open' => 'badge-danger',
                            'in_progress' => 'badge-warning',
                            'resolved' => 'badge-success',
                            'closed' => 'badge-neutral',
                            default => 'badge-neutral',
                        };
                        ?>
                        <tr>
                            <td>
                                <a href="<?= e(base_url('complaints/' . $t['id'])) ?>" class="font-mono font-bold text-primary hover:underline">
                                    <?= e($t['ticket_no']) ?>
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-text"><?= e($t['user_name'] ?? 'User') ?></div>
                                <div class="text-xs text-muted"><?= e($t['user_role'] ?? 'Student') ?></div>
                            </td>
                            <td>
                                <span class="badge badge-neutral uppercase text-2xs font-semibold">
                                    <?= e($t['category']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= e(base_url('complaints/' . $t['id'])) ?>" class="font-semibold text-text hover:text-primary line-clamp-1">
                                    <?= e($t['title']) ?>
                                </a>
                                <?php if (!empty($t['attachment'])): ?>
                                    <span class="text-2xs text-muted"><i class="fas fa-paperclip mr-1"></i> Attachment included</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $priClass ?> uppercase text-2xs font-bold">
                                    <?= e($t['priority']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $statusClass ?> uppercase text-2xs font-bold">
                                    <?= str_replace('_', ' ', e($t['status'])) ?>
                                </span>
                            </td>
                            <td class="text-xs text-muted">
                                <?= date('M d, Y', strtotime($t['created_at'])) ?>
                            </td>
                            <td class="text-right">
                                <a href="<?= e(base_url('complaints/' . $t['id'])) ?>" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="p-4 border-t border-border flex justify-between items-center">
            <div class="text-xs text-muted">
                Showing page <?= (int)$pagination['page'] ?> of <?= $totalPages ?>
            </div>
            <div class="flex gap-1">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="<?= e(base_url('complaints?page=' . $p . (!empty($currentStatus) ? '&status=' . urlencode($currentStatus) : ''))) ?>" 
                       class="btn btn-sm <?= $p == $pagination['page'] ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Submit Ticket Modal -->
<div id="newTicketModal" class="modal-overlay hidden">
    <div class="modal-content max-w-xl">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-ticket-alt text-primary mr-2"></i>Submit Support Request</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('newTicketModal')">&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('complaints')) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-body space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Category *</label>
                        <select name="category" class="form-control" required>
                            <option value="academic">Academic & Examinations</option>
                            <option value="hostel">Hostel & Housing</option>
                            <option value="fees">Fees & Finance</option>
                            <option value="library">Library & Books</option>
                            <option value="infrastructure">Campus Infrastructure & WiFi</option>
                            <option value="other">General Inquiries / Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Urgency / Priority *</label>
                        <select name="priority" class="form-control" required>
                            <option value="low">Low Priority</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High Priority</option>
                            <option value="urgent">Urgent / Critical</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Subject / Brief Summary *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. WiFi outage in Science Block Room 204" required>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Detailed Description of Issue *</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Please describe the issue thoroughly with relevant dates and locations..." required></textarea>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Supporting File / Screenshot (Optional)</label>
                    <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    <span class="text-2xs text-muted">Supports JPG, PNG, PDF up to 10MB</span>
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('newTicketModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Submit Ticket</button>
            </div>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
