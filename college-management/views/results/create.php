<?php
$title = 'Enter Examination Marks - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title text-2xl font-bold">Grade Entry: <?= e($exam['title'] ?? 'Examination') ?></h1>
        <p class="text-muted text-sm">Exam Date: <span class="text-text font-medium"><?= date('M d, Y', strtotime($exam['exam_date'] ?? date('Y-m-d'))) ?></span> | Enter student marks to compute official letter grades.</p>
    </div>
    <div>
        <a href="<?= e(base_url('results?exam_id=' . ($exam['id'] ?? 0))) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Grade Book
        </a>
    </div>
</div>

<?php if (empty($students)): ?>
    <div class="card p-8 text-center text-muted">
        <i class="fas fa-user-slash text-4xl mb-3 block opacity-30"></i>
        <h3 class="text-base font-bold text-text mb-1">No Students Found</h3>
        <p class="text-xs">There are no active students enrolled in this course to grade.</p>
    </div>
<?php else: ?>
    <form method="post" action="<?= e(base_url('results')) ?>" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="exam_id" value="<?= (int)($exam['id'] ?? 0) ?>">

        <div class="card p-4">
            <div class="flex items-center gap-4">
                <div class="w-48">
                    <label class="form-label text-xs uppercase font-semibold">Maximum Marks *</label>
                    <input type="number" id="max_marks" name="max_marks" class="form-control font-mono font-bold" value="100" min="10" max="500" required>
                </div>
                <div class="text-xs text-muted pt-5">
                    <i class="fas fa-info-circle mr-1 text-primary"></i>
                    All student percentages and grades (A+, A, B, C, D, F) will be computed dynamically against this ceiling.
                </div>
            </div>
        </div>

        <div class="card p-0">
            <div class="card-header p-4 border-b border-border flex justify-between items-center">
                <span class="badge badge-primary font-bold px-2.5 py-1">
                    <?= count($students) ?> Students in Roster
                </span>
                <span class="text-xs text-muted">Leave empty to omit student from this publish cycle</span>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>Admission No</th>
                            <th>Student Name</th>
                            <th style="width: 250px;">Marks Scored</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $idx => $s): ?>
                            <tr>
                                <td class="text-muted font-mono text-xs"><?= $idx + 1 ?></td>
                                <td>
                                    <span class="badge badge-neutral font-mono font-bold text-xs">
                                        <?= e($s['admission_no']) ?>
                                    </span>
                                    <input type="hidden" name="student_id[]" value="<?= (int)$s['id'] ?>">
                                </td>
                                <td>
                                    <div class="font-bold text-text"><?= e($s['name']) ?></div>
                                    <div class="text-xs text-muted"><?= e($s['email']) ?></div>
                                </td>
                                <td>
                                    <div class="relative">
                                        <input type="number" step="0.5" min="0" max="500" name="marks_obtained[]" class="form-control font-mono text-base font-bold" placeholder="e.g. 85">
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer p-4 border-t border-border flex justify-end gap-3">
                <a href="<?= e(base_url('results?exam_id=' . ($exam['id'] ?? 0))) ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-6">
                    <i class="fas fa-check-circle mr-1"></i> Save & Publish Results
                </button>
            </div>
        </div>
    </form>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
