<?php
$title = 'Courses & Programs - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title text-2xl font-bold">Academic Courses & Programs</h1>
        <p class="text-muted text-sm">Manage curriculum programs, academic degrees, duration, and course fees.</p>
    </div>
    <div class="flex gap-2">
        <?php if ($trash): ?>
            <a href="<?= e(base_url('courses')) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Active
            </a>
        <?php else: ?>
            <a href="<?= e(base_url('courses?trash=1')) ?>" class="btn btn-secondary">
                <i class="fas fa-trash-alt mr-1"></i> View Trash
            </a>
            <?php if (has_role('admin', 'operator')): ?>
                <button type="button" class="btn btn-primary" onclick="openModal('addCourseModal')">
                    <i class="fas fa-plus-circle mr-1"></i> Add New Course
                </button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="card p-0 mb-6">
    <div class="card-header p-4 flex justify-between items-center border-b border-border">
        <div class="flex items-center gap-3">
            <span class="badge badge-primary text-xs px-2.5 py-1">
                <?= count($courses) ?> <?= $trash ? 'Archived' : 'Active' ?> Programs
            </span>
            <input type="text" id="courseSearch" placeholder="Search course by title or code..." class="form-control text-sm w-72" onkeyup="filterTable('courseSearch', 'courseTable')">
        </div>
        <div class="text-sm text-muted">
            <i class="fas fa-graduation-cap mr-1 text-primary"></i> Degree & Diploma Programs
        </div>
    </div>

    <div class="table-container">
        <table class="table" id="courseTable">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Program Title</th>
                    <th>Department</th>
                    <th>Duration</th>
                    <th>Credits</th>
                    <th>Annual Fee</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-8 text-muted">
                            <i class="fas fa-book-open text-4xl mb-3 block opacity-30"></i>
                            No <?= $trash ? 'deleted' : 'active' ?> courses found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($courses as $c): ?>
                        <tr>
                            <td>
                                <span class="badge badge-info font-mono text-xs px-2 py-1 font-bold">
                                    <?= e($c['code']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="font-semibold text-text"><?= e($c['name']) ?></div>
                                <?php if (!empty($c['description'])): ?>
                                    <div class="text-xs text-muted line-clamp-1 max-w-xs"><?= e($c['description']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-warning text-xs">
                                    <i class="fas fa-building mr-1"></i> <?= e($c['department_name'] ?? 'General') ?>
                                </span>
                            </td>
                            <td><?= (int)$c['duration_years'] ?> Year<?= (int)$c['duration_years'] > 1 ? 's' : '' ?></td>
                            <td><span class="badge badge-neutral"><?= (int)($c['credits'] ?? 3) ?> Credits</span></td>
                            <td class="font-medium text-emerald-500 font-mono"><?= format_currency((float)($c['fee_per_year'] ?? 2500)) ?>/yr</td>
                            <td class="text-right">
                                <?php if ($trash): ?>
                                    <?php if (has_role('admin', 'operator')): ?>
                                        <form method="post" action="<?= e(base_url('courses/' . $c['id'] . '/restore')) ?>" class="inline-block">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restore Course">
                                                <i class="fas fa-undo mr-1"></i> Restore
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="flex justify-end gap-1.5">
                                        <?php if (has_role('admin', 'operator')): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editCourse(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)" title="Edit Course">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="post" action="<?= e(base_url('courses/' . $c['id'] . '/delete')) ?>" class="inline-block" onsubmit="return confirm('Move this course to trash?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Move to Trash">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted text-xs">View Only</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (has_role('admin', 'operator')): ?>
<!-- Add Course Modal -->
<div id="addCourseModal" class="modal-overlay hidden">
    <div class="modal-content max-w-lg">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-plus-circle text-primary mr-2"></i>Add Academic Course</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('addCourseModal')">&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('courses')) ?>">
            <?= csrf_field() ?>
            <div class="modal-body space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Course Code *</label>
                        <input type="text" name="code" class="form-control font-mono uppercase" placeholder="e.g. CS101, MBA" required>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Department *</label>
                        <select name="department_id" class="form-control" required>
                            <option value="">Select Dept</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= (int)$d['id'] ?>"><?= e($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Course Title / Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Bachelor of Computer Applications" required>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Duration (Yrs) *</label>
                        <input type="number" name="duration_years" class="form-control" value="3" min="1" max="10" required>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Total Credits</label>
                        <input type="number" name="credits" class="form-control" value="120" min="1">
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Fee/Year ($)</label>
                        <input type="number" step="0.01" name="fee_per_year" class="form-control" value="2500.00" min="0">
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Description / Syllabus Overview</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Brief outline of syllabus and degree specifications..."></textarea>
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addCourseModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Save Course</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Course Modal -->
<div id="editCourseModal" class="modal-overlay hidden">
    <div class="modal-content max-w-lg">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-edit text-primary mr-2"></i>Edit Course</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('editCourseModal')">&times;</button>
        </div>
        <form id="editCourseForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="modal-body space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Course Code *</label>
                        <input type="text" id="edit_code" name="code" class="form-control font-mono uppercase" required>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Department *</label>
                        <select id="edit_department_id" name="department_id" class="form-control" required>
                            <option value="">Select Dept</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= (int)$d['id'] ?>"><?= e($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Course Title / Name *</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Duration (Yrs) *</label>
                        <input type="number" id="edit_duration_years" name="duration_years" class="form-control" min="1" max="10" required>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Total Credits</label>
                        <input type="number" id="edit_credits" name="credits" class="form-control" min="1">
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Fee/Year ($)</label>
                        <input type="number" step="0.01" id="edit_fee_per_year" name="fee_per_year" class="form-control" min="0">
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Description / Syllabus Overview</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editCourseModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Update Course</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCourse(course) {
    document.getElementById('editCourseForm').action = '<?= base_url('courses/') ?>' + course.id;
    document.getElementById('edit_code').value = course.code || '';
    document.getElementById('edit_name').value = course.name || '';
    document.getElementById('edit_department_id').value = course.department_id || '';
    document.getElementById('edit_duration_years').value = course.duration_years || '3';
    document.getElementById('edit_credits').value = course.credits || '120';
    document.getElementById('edit_fee_per_year').value = course.fee_per_year || '2500';
    document.getElementById('edit_description').value = course.description || '';
    openModal('editCourseModal');
}
function filterTable(inputId, tableId) {
    const query = document.getElementById(inputId).value.toLowerCase();
    const rows = document.getElementById(tableId).querySelectorAll('tbody tr');
    rows.forEach(r => {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(query) ? '' : 'none';
    });
}
</script>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
