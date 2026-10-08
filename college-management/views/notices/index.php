<?php
$title = 'Notice Board & Bulletins - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title text-2xl font-bold">Campus Bulletins & Notices</h1>
        <p class="text-muted text-sm">Official announcements, academic calendars, holiday notices, and campus updates.</p>
    </div>
    <div class="flex gap-2">
        <?php if (has_role('admin', 'operator', 'faculty')): ?>
            <button type="button" class="btn btn-primary" onclick="openModal('postNoticeModal')">
                <i class="fas fa-plus-circle mr-1"></i> Post New Notice
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <?php if (empty($notices)): ?>
        <div class="col-span-full card p-12 text-center text-muted">
            <i class="fas fa-clipboard text-5xl mb-3 block opacity-30"></i>
            <h3 class="text-lg font-bold text-text mb-1">No Active Notices</h3>
            <p class="text-xs">There are no administrative bulletins posted right now.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notices as $n): ?>
            <div class="card p-6 flex flex-col justify-between hover:border-primary/50 transition-all shadow-sm">
                <div>
                    <div class="flex justify-between items-start gap-2 mb-3">
                        <span class="badge badge-primary text-2xs uppercase font-bold tracking-wider">Official Circular</span>
                        <div class="text-2xs text-muted flex items-center gap-1">
                            <i class="far fa-calendar-alt"></i>
                            <?= date('M d, Y', strtotime($n['created_at'])) ?>
                        </div>
                    </div>

                    <h3 class="font-bold text-base text-text mb-3 leading-snug"><?= e($n['title']) ?></h3>

                    <div class="text-xs text-muted leading-relaxed whitespace-pre-wrap line-clamp-6">
                        <?= nl2br(e($n['body'])) ?>
                    </div>
                </div>

                <div class="border-t border-border pt-4 mt-6 flex justify-between items-center text-2xs">
                    <span class="text-muted">
                        <i class="fas fa-bullhorn text-primary mr-1"></i> Public Announcement
                    </span>

                    <?php if (has_role('admin', 'operator')): ?>
                        <form method="post" action="<?= e(base_url('notices/' . $n['id'] . '/delete')) ?>" onsubmit="return confirm('Remove this notice bulletin?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Notice">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (has_role('admin', 'operator', 'faculty')): ?>
<!-- Post Notice Modal -->
<div id="postNoticeModal" class="modal-overlay hidden">
    <div class="modal-content max-w-lg">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-bullhorn text-primary mr-2"></i>Post Campus Notice</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('postNoticeModal')">&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('notices')) ?>">
            <?= csrf_field() ?>
            <div class="modal-body space-y-4">
                <div>
                    <label class="form-label text-xs uppercase font-semibold">Notice Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Schedule of Annual Convocation Ceremony" required>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Detailed Body / Circular Text *</label>
                    <textarea name="body" class="form-control" rows="6" placeholder="Provide complete circular details, timing, venue, and instructions..." required></textarea>
                </div>

                <div class="alert alert-info text-2xs">
                    <i class="fas fa-info-circle mr-1"></i>
                    Posting a notice will automatically broadcast a notification banner to all students and staff.
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('postNoticeModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Publish Notice</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
