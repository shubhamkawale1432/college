<?php $title = 'Role & Permission Management'; require VIEW_PATH . '/layout/header.php'; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <!-- Roles List Navigation -->
    <div class="card lg:col-span-1">
        <div class="card-header">
            <h3><i class="fa-solid fa-shield-halved" style="color: var(--primary);"></i> Roles</h3>
            <button type="button" class="btn btn-outline btn-sm" data-modal-target="create-role-modal" title="Add Role">
                <i class="fa-solid fa-plus"></i> New
            </button>
        </div>
        <div class="card-body" style="padding: 12px;">
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <?php foreach ($roles as $r): ?>
                    <a href="<?= e(base_url("roles?role_id={$r['id']}")) ?>" 
                       style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-radius: var(--radius-sm); border: 1px solid <?= $r['id'] === $selectedRoleId ? 'var(--primary)' : 'var(--border)' ?>; background: <?= $r['id'] === $selectedRoleId ? 'var(--primary-light)' : 'var(--bg-card)' ?>; color: var(--text-main);">
                        <div>
                            <div style="font-weight: 700; font-size: 0.92rem;"><?= e($r['label']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;"><?= e($r['name']) ?></div>
                        </div>
                        <span class="badge badge-primary"><?= (int)$r['user_count'] ?> users</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Permissions Matrix -->
    <div class="card lg:col-span-2">
        <div class="card-header">
            <div>
                <?php 
                    $activeRole = null;
                    foreach ($roles as $r) { if ($r['id'] === $selectedRoleId) { $activeRole = $r; break; } }
                ?>
                <h3>Permissions for <?= e($activeRole['label'] ?? 'Role') ?></h3>
                <p style="margin: 4px 0 0; font-size: 0.82rem; color: var(--text-muted);">
                    <?= e($activeRole['description'] ?? 'Configure module privileges and route access control') ?>
                </p>
            </div>
        </div>
        <div class="card-body">
            <?php if (($activeRole['name'] ?? '') === 'admin'): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-shield-check"></i>
                    <div>The <strong>Administrator</strong> role automatically has all system capabilities and privileges by design.</div>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(base_url("roles/{$selectedRoleId}/permissions")) ?>">
                <?= csrf_field() ?>

                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <?php foreach ($groupedPermissions as $module => $perms): ?>
                        <div style="border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden;">
                            <div style="background: var(--border-subtle); padding: 10px 16px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); display: flex; justify-content: space-between; align-items: center;">
                                <span><?= e($module) ?></span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);"><?= count($perms) ?> permissions</span>
                            </div>
                            <div style="padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px;">
                                <?php foreach ($perms as $p): ?>
                                    <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-size: 0.88rem;">
                                        <input type="checkbox" name="permissions[]" value="<?= $p['id'] ?>" 
                                               <?= !empty($p['assigned']) || ($activeRole['name'] ?? '') === 'admin' ? 'checked' : '' ?>
                                               <?= ($activeRole['name'] ?? '') === 'admin' ? 'disabled' : '' ?>
                                               style="margin-top: 3px; width: 16px; height: 16px;">
                                        <div>
                                            <div style="font-weight: 600;"><?= e($p['name']) ?></div>
                                            <div style="font-size: 0.76rem; color: var(--text-muted);"><?= e($p['description']) ?></div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (($activeRole['name'] ?? '') !== 'admin'): ?>
                    <div style="margin-top: 24px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Save Permissions Matrix
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add New Role -->
<div class="modal-overlay" id="create-role-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Add New System Role</h3>
            <button type="button" class="modal-close" data-modal-close>&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('roles')) ?>">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="role_name">Role Identifier (Slug)</label>
                    <input type="text" id="role_name" name="name" class="form-control" placeholder="e.g. librarian" required>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Lowercase letters, no spaces</span>
                </div>
                <div class="form-group">
                    <label class="form-label" for="role_label">Display Label</label>
                    <input type="text" id="role_label" name="label" class="form-control" placeholder="e.g. Chief Librarian" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="role_desc">Description</label>
                    <textarea id="role_desc" name="description" class="form-control" rows="3" placeholder="Brief summary of duties and access permissions..."></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Create Role</button>
            </div>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
