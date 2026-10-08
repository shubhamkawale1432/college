<?php
$title = "Ticket #{$ticket['ticket_no']} - " . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';

$priClass = match($ticket['priority']) {
    'urgent' => 'badge-danger',
    'high' => 'badge-warning',
    'medium' => 'badge-primary',
    default => 'badge-neutral',
};
$statusClass = match($ticket['status']) {
    'open' => 'badge-danger',
    'in_progress' => 'badge-warning',
    'resolved' => 'badge-success',
    'closed' => 'badge-neutral',
    default => 'badge-neutral',
};
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="page-title text-2xl font-bold font-mono"><?= e($ticket['ticket_no']) ?></h1>
            <span class="badge <?= $statusClass ?> font-bold text-xs uppercase px-2.5 py-1">
                <?= str_replace('_', ' ', e($ticket['status'])) ?>
            </span>
            <span class="badge <?= $priClass ?> font-bold text-xs uppercase px-2 py-0.5">
                <?= e($ticket['priority']) ?> Priority
            </span>
        </div>
        <p class="text-muted text-sm mt-1">Submitted on <?= date('F d, Y h:i A', strtotime($ticket['created_at'])) ?> by <strong class="text-text"><?= e($ticket['user_name']) ?></strong> (<?= e($ticket['user_email']) ?>)</p>
    </div>
    <div class="flex gap-2">
        <a href="<?= e(base_url('complaints')) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Helpdesk
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Ticket Detail Column -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-6">
            <h2 class="text-xl font-bold text-text mb-4"><?= e($ticket['title']) ?></h2>

            <div class="border-t border-border pt-4 text-text leading-relaxed whitespace-pre-wrap text-sm">
                <?= nl2br(e($ticket['description'])) ?>
            </div>

            <?php if (!empty($ticket['attachment'])): ?>
                <div class="mt-6 pt-4 border-t border-border">
                    <span class="text-xs text-muted uppercase font-semibold block mb-2">Supporting Attachment</span>
                    <a href="<?= e(upload_url($ticket['attachment'])) ?>" target="_blank" class="inline-flex items-center gap-2 p-3 rounded-xl bg-background border border-border text-primary hover:bg-card transition-all text-sm font-medium">
                        <i class="fas fa-file-download text-lg"></i>
                        <span>Download Attached Document</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Resolution Notes Section -->
        <?php if (!empty($ticket['admin_notes'])): ?>
            <div class="card p-6 border-l-4 border-emerald-500">
                <div class="flex items-center gap-2 text-emerald-500 font-bold mb-2">
                    <i class="fas fa-check-circle"></i> Official Administration Resolution
                </div>
                <div class="text-sm text-text whitespace-pre-wrap leading-relaxed">
                    <?= nl2br(e($ticket['admin_notes'])) ?>
                </div>
                <?php if (!empty($ticket['resolved_at'])): ?>
                    <div class="text-xs text-muted mt-3">
                        Resolved: <?= date('M d, Y h:i A', strtotime($ticket['resolved_at'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Sidebar: Metadata & Staff Action -->
    <div class="space-y-6">
        <div class="card p-5">
            <h3 class="font-bold text-sm text-text border-b border-border pb-3 mb-4">
                <i class="fas fa-info-circle text-primary mr-1.5"></i> Ticket Information
            </h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between">
                    <span class="text-muted">Ticket ID:</span>
                    <span class="font-mono font-bold text-text">#<?= (int)$ticket['id'] ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted">Category:</span>
                    <span class="badge badge-neutral uppercase font-semibold"><?= e($ticket['category']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted">Requester Role:</span>
                    <span class="badge badge-primary uppercase"><?= e($ticket['user_role']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted">Created:</span>
                    <span class="text-text"><?= date('M d, Y', strtotime($ticket['created_at'])) ?></span>
                </div>
                <?php if (!empty($ticket['resolved_at'])): ?>
                    <div class="flex justify-between">
                        <span class="text-muted">Resolution Date:</span>
                        <span class="text-emerald-500 font-semibold"><?= date('M d, Y', strtotime($ticket['resolved_at'])) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (has_role('admin', 'operator')): ?>
            <div class="card p-5 bg-card">
                <h3 class="font-bold text-sm text-text border-b border-border pb-3 mb-4">
                    <i class="fas fa-user-shield text-primary mr-1.5"></i> Staff Status Control
                </h3>
                <form method="post" action="<?= e(base_url('complaints/' . $ticket['id'] . '/status')) ?>" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Update Ticket Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                            <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="resolved" <?= $ticket['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Resolution Remarks / Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="4" placeholder="Enter findings, repairs initiated, or explanations for the student/staff..."><?= e($ticket['admin_notes'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-save mr-1"></i> Update Ticket Status
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
