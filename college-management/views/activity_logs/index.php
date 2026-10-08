<?php
$title = 'Audit & Activity Logs - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';

$items = $pagination['data'] ?? $pagination['items'] ?? [];
$totalPages = (int)($pagination['last_page'] ?? $pagination['totalPages'] ?? 1);
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title text-2xl font-bold">System Security & Audit Trail</h1>
        <p class="text-muted text-sm">Comprehensive forensic ledger capturing all authentication events, user modifications, and entity lifecycle changes.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card p-4 mb-6">
    <form method="get" action="<?= e(base_url('activity-logs')) ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label class="form-label text-xs uppercase font-semibold">Filter by User</label>
            <select name="user_id" class="form-control">
                <option value="">All Users</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= (int)$u['id'] ?>" <?= $currentUserId === (int)$u['id'] ? 'selected' : '' ?>>
                        <?= e($u['name']) ?> (<?= e($u['role']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label text-xs uppercase font-semibold">Filter by Action Event</label>
            <input type="text" name="action" value="<?= e($currentAction ?? '') ?>" placeholder="e.g. login, student_created, course_updated..." class="form-control">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary flex-1">
                <i class="fas fa-filter mr-1"></i> Filter Logs
            </button>
            <?php if (!empty($currentAction) || !empty($currentUserId)): ?>
                <a href="<?= e(base_url('activity-logs')) ?>" class="btn btn-secondary" title="Reset Filters">
                    <i class="fas fa-times"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Logs Table Card -->
<div class="card p-0 mb-6">
    <div class="card-header p-4 flex justify-between items-center border-b border-border">
        <span class="badge badge-primary font-bold px-2.5 py-1">
            <?= (int)$pagination['total'] ?> Logged Events
        </span>
        <div class="text-xs text-muted">
            Page <?= (int)$pagination['page'] ?> of <?= $totalPages ?>
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>User / Actor</th>
                    <th>Action</th>
                    <th>Entity Scope</th>
                    <th>IP Address & Host</th>
                    <th>Timestamp</th>
                    <th class="text-right">Payload</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-8 text-muted">
                            <i class="fas fa-shield-alt text-4xl mb-3 block opacity-30"></i>
                            No activity logs found matching current parameters.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $log): ?>
                        <?php 
                        $actionClass = 'badge-neutral';
                        if (str_contains($log['action'], 'delete')) $actionClass = 'badge-danger';
                        elseif (str_contains($log['action'], 'create') || str_contains($log['action'], 'added')) $actionClass = 'badge-success';
                        elseif (str_contains($log['action'], 'update') || str_contains($log['action'], 'edit')) $actionClass = 'badge-warning';
                        elseif (str_contains($log['action'], 'login')) $actionClass = 'badge-primary';
                        ?>
                        <tr>
                            <td class="font-mono text-muted text-xs">#<?= (int)$log['id'] ?></td>
                            <td>
                                <?php if (!empty($log['user_name'])): ?>
                                    <div class="font-bold text-text"><?= e($log['user_name']) ?></div>
                                    <div class="text-2xs text-muted"><?= e($log['user_role'] ?? 'user') ?></div>
                                <?php else: ?>
                                    <span class="text-muted italic text-xs">System / Anonymous</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $actionClass ?> font-mono uppercase text-2xs font-bold">
                                    <?= e($log['action']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="text-xs font-mono font-medium text-text">
                                    <?= e($log['entity_type'] ?? 'system') ?>
                                    <?php if (!empty($log['entity_id'])): ?>
                                        <span class="text-muted">#<?= (int)$log['entity_id'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="font-mono text-xs text-text"><?= e($log['ip_address'] ?? '127.0.0.1') ?></div>
                                <?php if (!empty($log['user_agent'])): ?>
                                    <div class="text-2xs text-muted line-clamp-1 max-w-xs" title="<?= e($log['user_agent']) ?>">
                                        <?= e(substr($log['user_agent'], 0, 45)) ?>...
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-xs text-muted whitespace-nowrap">
                                <?= date('M d, Y h:i:s A', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="text-right">
                                <?php if (!empty($log['before_data']) || !empty($log['after_data'])): ?>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="viewLogPayload(<?= htmlspecialchars(json_encode([
                                        'id' => $log['id'],
                                        'action' => $log['action'],
                                        'before' => json_decode($log['before_data'] ?? 'null'),
                                        'after' => json_decode($log['after_data'] ?? 'null')
                                    ]), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fas fa-code mr-1"></i> Diff
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted text-2xs">&mdash;</span>
                                <?php endif; ?>
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
                    <a href="<?= e(base_url('activity-logs?page=' . $p . (!empty($currentAction) ? '&action=' . urlencode($currentAction) : '') . (!empty($currentUserId) ? '&user_id=' . (int)$currentUserId : ''))) ?>" 
                       class="btn btn-sm <?= $p == $pagination['page'] ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Payload JSON Diff Modal -->
<div id="payloadModal" class="modal-overlay hidden">
    <div class="modal-content max-w-2xl">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-code text-primary mr-2"></i>Event Payload & State Changes</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('payloadModal')">&times;</button>
        </div>
        <div class="modal-body space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label text-xs uppercase font-semibold text-rose-500">Before State (Previous)</label>
                    <pre id="payloadBefore" class="p-3 rounded-lg bg-background border border-border text-2xs font-mono overflow-auto max-h-60 text-muted"></pre>
                </div>
                <div>
                    <label class="form-label text-xs uppercase font-semibold text-emerald-500">After State (New)</label>
                    <pre id="payloadAfter" class="p-3 rounded-lg bg-background border border-border text-2xs font-mono overflow-auto max-h-60 text-text"></pre>
                </div>
            </div>
        </div>
        <div class="modal-footer flex justify-end border-t border-border pt-3 mt-4">
            <button type="button" class="btn btn-secondary" onclick="closeModal('payloadModal')">Close</button>
        </div>
    </div>
</div>

<script>
function viewLogPayload(log) {
    document.getElementById('payloadBefore').textContent = log.before ? JSON.stringify(log.before, null, 2) : 'None / Null';
    document.getElementById('payloadAfter').textContent = log.after ? JSON.stringify(log.after, null, 2) : 'None / Null';
    openModal('payloadModal');
}
</script>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
