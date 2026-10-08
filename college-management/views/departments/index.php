<?php $title = 'Departments Directory'; require VIEW_PATH . '/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 14px;">
            <h2><i class="fa-solid fa-building-columns" style="color: var(--primary); margin-right: 8px;"></i> Academic Departments</h2>
            <div style="display: flex; gap: 6px;">
                <a href="<?= e(base_url('departments')) ?>" class="btn btn-sm <?= !$trash ? 'btn-primary' : 'btn-outline' ?>">Active</a>
                <a href="<?= e(base_url('departments?trash=1')) ?>" class="btn btn-sm <?= $trash ? 'btn-danger' : 'btn-outline' ?>">
                    <i class="fa-solid fa-trash-can"></i> Trashed
                </a>
            </div>
        </div>
        <div>
            <?php if (!$trash && has_role('admin', 'operator')): ?>
                <button type="button" class="btn btn-primary" data-modal-target="create-dept-modal">
                    <i class="fa-solid fa-plus"></i> Add Department
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Department Name</th>
                        <th>Head of Department (HOD)</th>
                        <th>Courses</th>
                        <th>Students</th>
                        <th>Faculty</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($departments)): ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px;">No departments found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($departments as $d): ?>
                            <tr>
                                <td><span style="font-family: monospace; font-weight: 800; color: var(--primary);"><?= e($d['code']) ?></span></td>
                                <td>
                                    <strong><?= e($d['name']) ?></strong>
                                    <?php if (!empty($d['description'])): ?>
                                        <div style="font-size: 0.76rem; color: var(--text-muted);"><?= e($d['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($d['head_of_department'] ?? '—') ?></td>
                                <td><span class="badge badge-primary"><?= (int)$d['course_count'] ?> courses</span></td>
                                <td><span class="badge badge-success"><?= (int)$d['student_count'] ?> enrolled</span></td>
                                <td><span class="badge badge-info"><?= (int)$d['faculty_count'] ?> faculty</span></td>
                                <td style="text-align: right;">
                                    <?php if ($trash): ?>
                                        <form method="post" action="<?= e(base_url("departments/{$d['id']}/restore")) ?>" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fa-solid fa-rotate-left"></i> Restore
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <?php if (has_role('admin', 'operator')): ?>
                                            <button type="button" class="btn btn-outline btn-sm" onclick="editDept(<?= htmlspecialchars(json_encode($d), ENT_QUOTES) ?>)">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <form method="post" action="<?= e(base_url("departments/{$d['id']}/delete")) ?>" style="display:inline;" onsubmit="return confirm('Move department to trash?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);">
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
    </div>
</div>

<!-- Modal: Add Department -->
<div class="modal-overlay" id="create-dept-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Add Academic Department</h3>
            <button type="button" class="modal-close" data-modal-close>&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('departments')) ?>">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="dept_name">Department Name *</label>
                    <input type="text" id="dept_name" name="name" class="form-control" placeholder="e.g. Electrical Engineering" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="dept_code">Code (Abbreviation) *</label>
                    <input type="text" id="dept_code" name="code" class="form-control" placeholder="e.g. EE" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="dept_hod">Head of Department (HOD)</label>
                    <input type="text" id="dept_hod" name="head_of_department" class="form-control" placeholder="e.g. Dr. Nikola Tesla">
                </div>
                <div class="form-group">
                    <label class="form-label" for="dept_desc">Description / Overview</label>
                    <textarea id="dept_desc" name="description" class="form-control" rows="2" placeholder="Brief outline of curriculum and facilities..."></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Create Department</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Department -->
<div class="modal-overlay" id="edit-dept-modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3>Edit Academic Department</h3>
            <button type="button" class="modal-close" data-modal-close>&times;</button>
        </div>
        <form method="post" id="edit-dept-form" action="">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit_dept_name">Department Name *</label>
                    <input type="text" id="edit_dept_name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_dept_code">Code *</label>
                    <input type="text" id="edit_dept_code" name="code" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_dept_hod">Head of Department (HOD)</label>
                    <input type="text" id="edit_dept_hod" name="head_of_department" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_dept_desc">Description</label>
                    <textarea id="edit_dept_desc" name="description" class="form-control" rows="2"></textarea>
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
function editDept(d) {
    document.getElementById('edit-dept-form').action = '<?= base_url('departments/') ?>' + d.id;
    document.getElementById('edit_dept_name').value = d.name;
    document.getElementById('edit_dept_code').value = d.code;
    document.getElementById('edit_dept_hod').value = d.head_of_department || '';
    document.getElementById('edit_dept_desc').value = d.description || '';
    document.getElementById('edit-dept-modal').classList.add('open');
}
</script>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
