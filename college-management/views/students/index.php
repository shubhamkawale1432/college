<?php $title = 'Student Directory'; require VIEW_PATH . '/layout/header.php'; ?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 14px;">
            <h2><i class="fa-solid fa-user-graduate" style="color: var(--primary); margin-right: 8px;"></i> Student Directory</h2>
            <div style="display: flex; gap: 6px;">
                <a href="<?= e(base_url('students')) ?>" class="btn btn-sm <?= !$trash ? 'btn-primary' : 'btn-outline' ?>">Active Students</a>
                <a href="<?= e(base_url('students?trash=1')) ?>" class="btn btn-sm <?= $trash ? 'btn-danger' : 'btn-outline' ?>">
                    <i class="fa-solid fa-trash-can"></i> Trashed
                </a>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= e(base_url('reports/export?type=students' . ($currentDept ? '&dept_id='.$currentDept : '') . ($currentCourse ? '&course_id='.$currentCourse : ''))) ?>" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-file-excel"></i> Export CSV
            </a>
            <button type="button" class="btn btn-outline btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print
            </button>
            <?php if (!$trash && has_role('admin', 'operator')): ?>
                <a href="<?= e(base_url('students/create')) ?>" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Enroll Student
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body">
        <!-- Filter Toolbar -->
        <form method="get" action="<?= e(base_url('students')) ?>" class="filter-bar no-print">
            <?php if ($trash): ?><input type="hidden" name="trash" value="1"><?php endif; ?>
            <div class="search-input-group">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="q" class="form-control" placeholder="Search by name, admission no, email, or phone..." value="<?= e($search ?? '') ?>">
            </div>
            <select name="department_id" class="form-control" style="width: auto;">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($currentDept ?? null) === (int)$d['id'] ? 'selected' : '' ?>>
                        <?= e($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="course_id" class="form-control" style="width: auto;">
                <option value="">All Courses</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($currentCourse ?? null) === (int)$c['id'] ? 'selected' : '' ?>>
                        <?= e($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-outline"><i class="fa-solid fa-filter"></i> Apply</button>
            <?php if (!empty($search) || !empty($currentDept) || !empty($currentCourse)): ?>
                <a href="<?= e(base_url('students' . ($trash ? '?trash=1' : ''))) ?>" class="btn btn-outline">Clear</a>
            <?php endif; ?>
        </form>

        <!-- Students Data Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Admission No</th>
                        <th>Student Details</th>
                        <th>Department</th>
                        <th>Course / Program</th>
                        <th>Sem</th>
                        <th>Status</th>
                        <th style="text-align: right;" class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pagination['data'])): ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px;">No student records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pagination['data'] as $s): ?>
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700; color: var(--primary);">
                                        <?= e($s['admission_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <img src="<?= e(avatar_url($s['photo'], $s['name'])) ?>" alt="Photo" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border);">
                                        <div>
                                            <a href="<?= e(base_url("students/{$s['id']}")) ?>" style="font-weight: 700; color: var(--text-main);">
                                                <?= e($s['name']) ?>
                                            </a>
                                            <div style="font-size: 0.76rem; color: var(--text-muted);"><?= e($s['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= e($s['department_name'] ?? '—') ?></td>
                                <td><?= e($s['course_name'] ?? '—') ?></td>
                                <td><span class="badge badge-primary">S<?= (int)($s['semester'] ?? 1) ?></span></td>
                                <td>
                                    <span class="badge badge-<?= $s['status'] === 'active' ? 'success' : ($s['status'] === 'graduated' ? 'info' : 'warning') ?>">
                                        <?= e($s['status']) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;" class="no-print">
                                    <?php if ($trash): ?>
                                        <form method="post" action="<?= e(base_url("students/{$s['id']}/restore")) ?>" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fa-solid fa-rotate-left"></i> Restore
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?= e(base_url("students/{$s['id']}")) ?>" class="btn btn-outline btn-sm" title="View Profile">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <?php if (has_role('admin', 'operator')): ?>
                                            <a href="<?= e(base_url("students/{$s['id']}/edit")) ?>" class="btn btn-outline btn-sm" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <form method="post" action="<?= e(base_url("students/{$s['id']}/delete")) ?>" style="display:inline;" onsubmit="return confirm('Move this student to trash?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger);" title="Trash Student">
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
            <div class="pagination no-print">
                <div>Showing page <?= $pagination['page'] ?> of <?= $pagination['last_page'] ?> (<?= $pagination['total'] ?> students)</div>
                <div class="page-numbers">
                    <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                        <a href="<?= e(base_url("students?page={$p}&q=" . urlencode($search ?? '') . '&department_id=' . ($currentDept ?? '') . '&course_id=' . ($currentCourse ?? '') . ($trash ? '&trash=1' : ''))) ?>" 
                           class="page-link <?= $p === $pagination['page'] ? 'active' : '' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
