<?php $title = 'User Management'; require VIEW_PATH . '/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 14px;">
            <h2><i class="fa-solid fa-users-gear" style="color: var(--primary); margin-right: 8px;"></i> User Accounts</h2>
            <div style="display: flex; gap: 6px;">
                <a href="<?= e(base_url('users')) ?>" class="btn btn-sm <?= !$trash ? 'btn-primary' : 'btn-outline' ?>">Active Users</a>
                <a href="<?= e(base_url('users?trash=1')) ?>" class="btn btn-sm <?= $trash ? 'btn-danger' : 'btn-outline' ?>">
                    <i class="fa-solid fa-trash-can"></i> Trashed
                </a>
            </div>
        </div>
        <div>
            <?php if (!$trash): ?>
                <button type="button" class="btn btn-primary" data-modal-target="create-user-modal">
                    <i class="fa-solid fa-user-plus"></i> Add New User
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body">
        <!-- Search & Filter Bar -->
        <form method="get" action="<?= e(base_url('users')) ?>" class="filter-bar">
            <?php if ($trash): ?><input type="hidden" name="trash" value="1"><?php endif; ?>
            <div class="search-input-group">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="q" class="form-control" placeholder="Search by name, email, or phone..." value="<?= e($search ?? '') ?>">
            </div>
            <select name="role" class="form-control" style="width: auto;">
                <option value="">All Roles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= e($r['name']) ?>" <?= ($currentRole ?? '') === $r['name'] ? 'selected' : '' ?>>
                        <?= e($r['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-outline"><i class="fa-solid fa-filter"></i> Filter</button>
            <?php if (!empty($search) || !empty($currentRole)): ?>
                <a href="<?= e(base_url('users' . ($trash ? '?trash=1' : ''))) ?>" class="btn btn-outline">Clear</a>
            <?php endif; ?>
        </form>

        <!-- User Accounts Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>2FA</th>
                        <th>Joined</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagination['data'])): ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px;">No user accounts found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pagination['data'] as $u): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <img src="<?= e(avatar_url($u['avatar'], $u['name'])) ?>" alt="Avatar" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                                        <div>
                                            <div style="font-weight: 700;"><?= e($u['name']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= e($u['phone'] ?? 'No phone') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= e($u['email']) ?></td>
                                <td><span class="badge badge-primary"><?= e($u['role']) ?></span></td>
                                <td>
                                    <?php if ($u['locked_until'] && strtotime($u['locked_until']) > time()): ?>
                                        <span class="badge badge-danger">Locked</span>
                                    <?php else: ?>
                                        <span class="badge badge-<?= $u['status'] === 'active' ? 'success' : ($u['status'] === 'suspended' ? 'danger' : 'warning') ?>">
                                            <?= e($u['status']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= !empty($u['two_factor_enabled']) ? 'badge-success' : 'badge-warning' ?>">
                                        <?= !empty($u['two_factor_enabled']) ? 'Enabled' : 'Disabled' ?>
                                    </span>
                                </td>
                                <td><?= e(date('M j, Y', strtotime($u['created_at']))) ?></td>
                                <td style="text-align: right;">
                                    <?php if ($trash): ?>
                                        <form method="post" action="<?= e(base_url("users/{$u['id']}/restore")) ?>" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fa-solid fa-rotate-left"></i> Restore
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <?php if ($u['locked_until'] && strtotime($u['locked_until']) > time()): ?>
                                            <form method="post" action="<?= e(base_url("users/{$u['id']}/unlock")) ?>" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline btn-sm" title="Unlock Account">
                                                    <i class="fa-solid fa-lock-open"></i> Unlock
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <button type="button" class="btn btn-outline btn-sm" onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>)">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <?php if ($u['id'] !== auth()['id']): ?>
                                            <form method="post" action="<?= e(base_url("users/{$u['id']}/delete")) ?>" style="display:inline;" onsubmit="return confirm('Move this user to trash?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);" title="Delete User">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['last_page'] > 1): ?>
            <div class="pagination">
                <div>Showing page <?= $pagination['page'] ?> of <?= $pagination['last_page'] ?> (<?= $pagination['total'] ?> users)</div>
                <div class="page-numbers">
                    <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                        <a href="<?= e(base_url("users?page={$p}&q=" . urlencode($search ?? '') . '&role=' . urlencode($currentRole ?? '') . ($trash ? '&trash=1' : ''))) ?>" 
                           class="page-link <?= $p === $pagination['page'] ? 'active' : '' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Add New User -->
<div class="modal-overlay" id="create-user-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Add New System User</h3>
            <button type="button" class="modal-close" data-modal-close>&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('users')) ?>">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="new_name">Full Name</label>
                    <input type="text" id="new_name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="new_email">Email Address</label>
                    <input type="email" id="new_email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="new_phone">Phone Number</label>
                    <input type="text" id="new_phone" name="phone" class="form-control">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="new_role">System Role</label>
                        <select id="new_role" name="role" class="form-control" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= e($r['name']) ?>"><?= e($r['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="new_status">Status</label>
                        <select id="new_status" name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="new_pass">Initial Password</label>
                    <input type="password" id="new_pass" name="password" class="form-control" minlength="8" required>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit User -->
<div class="modal-overlay" id="edit-user-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Edit User Account</h3>
            <button type="button" class="modal-close" data-modal-close>&times;</button>
        </div>
        <form method="post" id="edit-user-form" action="">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit_name">Full Name</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_email">Email Address</label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_phone">Phone Number</label>
                    <input type="text" id="edit_phone" name="phone" class="form-control">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="edit_role">System Role</label>
                        <select id="edit_role" name="role" class="form-control" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= e($r['name']) ?>"><?= e($r['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit_status">Status</label>
                        <select id="edit_status" name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_pass">Reset Password (Leave blank to keep unchanged)</label>
                    <input type="password" id="edit_pass" name="password" class="form-control" minlength="8" placeholder="••••••••">
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit-user-form').action = '<?= base_url('users/') ?>' + user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_phone').value = user.phone || '';
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_status').value = user.status;
    document.getElementById('edit_pass').value = '';
    document.getElementById('edit-user-modal').classList.add('open');
}
</script>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
