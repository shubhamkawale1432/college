<?php
$title = 'Faculty & Academic Staff - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
$items = $pagination['data'] ?? $pagination['items'] ?? [];
$totalPages = (int)($pagination['last_page'] ?? $pagination['totalPages'] ?? 1);
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title text-2xl font-bold">Faculty & Professors</h1>
        <p class="text-muted text-sm">Manage teaching staff, academic credentials, department affiliations, and user accounts.</p>
    </div>
    <div class="flex gap-2">
        <?php if ($trash): ?>
            <a href="<?= e(base_url('faculty')) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Active
            </a>
        <?php else: ?>
            <a href="<?= e(base_url('faculty?trash=1')) ?>" class="btn btn-secondary">
                <i class="fas fa-trash-alt mr-1"></i> View Trash
            </a>
            <?php if (has_role('admin', 'operator')): ?>
                <button type="button" class="btn btn-primary" onclick="openModal('addFacultyModal')">
                    <i class="fas fa-user-plus mr-1"></i> Add Faculty Member
                </button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Filters Card -->
<div class="card p-4 mb-6">
    <form method="get" action="<?= e(base_url('faculty')) ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <?php if ($trash): ?><input type="hidden" name="trash" value="1"><?php endif; ?>
        <div class="md:col-span-2">
            <label class="form-label text-xs uppercase font-semibold">Search Faculty</label>
            <div class="relative">
                <input type="text" name="q" value="<?= e($search ?? '') ?>" placeholder="Search by name, email, designation..." class="form-control">
            </div>
        </div>
        <div>
            <label class="form-label text-xs uppercase font-semibold">Department</label>
            <select name="department_id" class="form-control">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= (int)$d['id'] ?>" <?= ($currentDept == $d['id']) ? 'selected' : '' ?>>
                        <?= e($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary flex-1">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            <?php if (!empty($search) || !empty($currentDept)): ?>
                <a href="<?= e(base_url('faculty' . ($trash ? '?trash=1' : ''))) ?>" class="btn btn-secondary" title="Reset Filters">
                    <i class="fas fa-times"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Faculty Table Card -->
<div class="card p-0 mb-6">
    <div class="card-header p-4 flex justify-between items-center border-b border-border">
        <div class="flex items-center gap-2">
            <span class="badge badge-primary font-bold px-2.5 py-1">
                <?= (int)$pagination['total'] ?> Total Faculty
            </span>
            <?php if ($trash): ?>
                <span class="badge badge-danger">Showing Archived / Deleted</span>
            <?php endif; ?>
        </div>
        <div class="text-xs text-muted">
            Page <?= (int)$pagination['page'] ?> of <?= $totalPages ?>
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Faculty Member</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Qualification</th>
                    <th>Contact</th>
                    <th>Joining Date</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-8 text-muted">
                            <i class="fas fa-chalkboard-teacher text-4xl mb-3 block opacity-30"></i>
                            No <?= $trash ? 'deleted' : 'active' ?> faculty members found matching your search.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $f): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar avatar-md">
                                        <?php if (!empty($f['photo'])): ?>
                                            <img src="<?= e(upload_url($f['photo'])) ?>" alt="<?= e($f['name']) ?>" class="rounded-full w-10 h-10 object-cover">
                                        <?php else: ?>
                                            <div class="avatar-fallback w-10 h-10 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-sm">
                                                <?= strtoupper(substr($f['name'] ?? 'F', 0, 2)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-text"><?= e($f['name']) ?></div>
                                        <div class="text-xs text-muted"><?= e($f['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-warning text-xs">
                                    <i class="fas fa-building mr-1"></i> <?= e($f['department_name'] ?? 'General') ?>
                                </span>
                            </td>
                            <td>
                                <span class="font-medium text-text"><?= e($f['designation'] ?? 'Instructor') ?></span>
                            </td>
                            <td>
                                <span class="badge badge-neutral text-xs"><?= e($f['qualification'] ?? 'N/A') ?></span>
                            </td>
                            <td>
                                <div class="text-xs text-text"><?= e($f['phone'] ?? '-') ?></div>
                            </td>
                            <td class="text-xs text-muted">
                                <?= !empty($f['joining_date']) ? date('M d, Y', strtotime($f['joining_date'])) : '-' ?>
                            </td>
                            <td class="text-right">
                                <?php if ($trash): ?>
                                    <form method="post" action="<?= e(base_url('faculty/' . $f['id'] . '/restore')) ?>" class="inline-block">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Restore Faculty">
                                            <i class="fas fa-undo mr-1"></i> Restore
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <?php if (has_role('admin', 'operator')): ?>
                                        <form method="post" action="<?= e(base_url('faculty/' . $f['id'] . '/delete')) ?>" class="inline-block" onsubmit="return confirm('Move this faculty member to trash?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Move to Trash">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted text-xs">Active</span>
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
    <?php if ($totalPages > 1): ?>
        <div class="p-4 border-t border-border flex justify-between items-center">
            <div class="text-xs text-muted">
                Showing page <?= (int)$pagination['page'] ?> of <?= $totalPages ?>
            </div>
            <div class="flex gap-1">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="<?= e(base_url('faculty?page=' . $p . (!empty($search) ? '&q=' . urlencode($search) : '') . (!empty($currentDept) ? '&department_id=' . (int)$currentDept : '') . ($trash ? '&trash=1' : ''))) ?>" 
                       class="btn btn-sm <?= $p == $pagination['page'] ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (has_role('admin', 'operator')): ?>
<!-- Add Faculty Modal -->
<div id="addFacultyModal" class="modal-overlay hidden">
    <div class="modal-content max-w-xl">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-user-plus text-primary mr-2"></i>Add Faculty Member</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('addFacultyModal')">&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('faculty')) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-body space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Full Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="Dr. John Smith" required>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Work Email *</label>
                        <input type="email" name="email" class="form-control" placeholder="john.smith@college.local" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Department *</label>
                        <select name="department_id" class="form-control" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= (int)$d['id'] ?>"><?= e($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Designation / Title *</label>
                        <input type="text" name="designation" class="form-control" placeholder="Professor & HOD, Associate Professor" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Qualification</label>
                        <input type="text" name="qualification" class="form-control" placeholder="Ph.D. in Computer Science">
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="+1 (555) 019-2831">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Profile Photo (JPG/PNG/WEBP)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="alert alert-info text-xs">
                    <i class="fas fa-info-circle mr-1"></i>
                    A faculty user account will be automatically generated with temporary credentials for portal access.
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addFacultyModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Create Faculty Account</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
