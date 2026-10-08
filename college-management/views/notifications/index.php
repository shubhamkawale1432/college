<?php
$title = 'Notifications & Announcements - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title text-2xl font-bold">Notifications & Broadcasts</h1>
        <p class="text-muted text-sm">Stay informed with campus alerts, examination schedules, fee deadlines, and administrative circulars.</p>
    </div>
    <div class="flex gap-2">
        <?php if (!empty($notifications)): ?>
            <form method="post" action="<?= e(base_url('notifications/read-all')) ?>" class="inline-block">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-check-double mr-1"></i> Mark All as Read
                </button>
            </form>
        <?php endif; ?>

        <?php if (has_role('admin', 'operator')): ?>
            <button type="button" class="btn btn-primary" onclick="openModal('broadcastModal')">
                <i class="fas fa-bullhorn mr-1"></i> Broadcast Announcement
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="card p-0 mb-6">
    <div class="card-header p-4 border-b border-border flex justify-between items-center">
        <span class="badge badge-primary font-bold px-2.5 py-1">
            <?= count($notifications) ?> Total Notifications
        </span>
        <span class="text-xs text-muted">Retained for 30 days</span>
    </div>

    <div class="divide-y divide-border">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-12 text-muted">
                <i class="far fa-bell-slash text-4xl mb-3 block opacity-30"></i>
                <h3 class="text-base font-bold text-text mb-1">You're All Caught Up!</h3>
                <p class="text-xs">There are no unread notifications or active broadcasts for your account.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <?php 
                $isRead = (bool)$n['is_read'];
                $type = $n['type'] ?? 'info';
                $iconClass = match($type) {
                    'success' => 'fas fa-check-circle text-emerald-500 bg-emerald-500/10',
                    'warning' => 'fas fa-exclamation-triangle text-amber-500 bg-amber-500/10',
                    'danger' => 'fas fa-exclamation-circle text-rose-500 bg-rose-500/10',
                    default => 'fas fa-info-circle text-primary bg-primary/10',
                };
                ?>
                <div class="p-4 flex items-start gap-4 transition-all hover:bg-card/50 <?= !$isRead ? 'bg-primary/5' : '' ?>">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0 <?= $iconClass ?>"></div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-sm text-text"><?= e($n['title']) ?></h4>
                            <?php if (!$isRead): ?>
                                <span class="badge badge-primary text-2xs px-2 py-0.5 font-bold uppercase">New</span>
                            <?php endif; ?>
                            <?php if (!empty($n['target_role'])): ?>
                                <span class="badge badge-neutral text-2xs uppercase"><?= e($n['target_role']) ?></span>
                            <?php endif; ?>
                        </div>

                        <p class="text-xs text-text mt-1 leading-relaxed"><?= e($n['message']) ?></p>

                        <div class="flex items-center gap-4 mt-2 text-2xs text-muted">
                            <span><i class="far fa-clock mr-1"></i> <?= date('M d, Y h:i A', strtotime($n['created_at'])) ?></span>
                            <?php if (!empty($n['link'])): ?>
                                <a href="<?= e(base_url(ltrim($n['link'], '/'))) ?>" class="text-primary font-bold hover:underline">
                                    <i class="fas fa-external-link-alt mr-1"></i> View Related Page
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!$isRead): ?>
                        <div class="flex-shrink-0">
                            <form method="post" action="<?= e(base_url('notifications/' . $n['id'] . '/read')) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Mark as Read">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (has_role('admin', 'operator')): ?>
<!-- Broadcast Announcement Modal -->
<div id="broadcastModal" class="modal-overlay hidden">
    <div class="modal-content max-w-lg">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-bullhorn text-primary mr-2"></i>Broadcast Campus Announcement</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('broadcastModal')">&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('notifications/broadcast')) ?>">
            <?= csrf_field() ?>
            <div class="modal-body space-y-4">
                <div>
                    <label class="form-label text-xs uppercase font-semibold">Announcement Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Campus Holiday / Midterm Examination Schedule" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Target Audience</label>
                        <select name="target_role" class="form-control">
                            <option value="all">All Registered Users</option>
                            <option value="student">Students Only</option>
                            <option value="faculty">Faculty & Professors</option>
                            <option value="operator">Operators & Staff</option>
                            <option value="admin">Administrators</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Alert Severity *</label>
                        <select name="type" class="form-control" required>
                            <option value="info">Informational (Blue)</option>
                            <option value="success">Success / Milestone (Green)</option>
                            <option value="warning">Warning / Deadline (Amber)</option>
                            <option value="danger">Urgent / Critical (Red)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Announcement Body *</label>
                    <textarea name="message" class="form-control" rows="4" placeholder="Provide full details of the notice or announcement..." required></textarea>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Optional Action Link</label>
                    <input type="text" name="link" class="form-control" placeholder="e.g. /results or /fees">
                    <span class="text-2xs text-muted">Relative URL to redirect user upon clicking</span>
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('broadcastModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Dispatch Announcement</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
