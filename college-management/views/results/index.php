<?php
$title = ($isStudent ? 'Academic Transcript' : 'Examinations & Results') . ' - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
?>

<?php if ($isStudent): ?>
    <!-- Student Transcript View -->
    <div class="page-header flex justify-between items-center mb-6">
        <div>
            <h1 class="page-title text-2xl font-bold">Academic Transcript & Results</h1>
            <p class="text-muted text-sm">Official semester grade sheet, exam evaluation, and earned credit performance.</p>
        </div>
        <div>
            <button type="button" class="btn btn-secondary" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Print Transcript
            </button>
        </div>
    </div>

    <!-- Printable Official Grade Card -->
    <div class="card p-6 mb-6 border border-border shadow-sm print:shadow-none print:border-none">
        <div class="border-b border-border pb-4 mb-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-xl">
                    <i class="fas fa-university"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-text"><?= setting('app_name', 'EduCore College of Technology') ?></h2>
                    <p class="text-xs text-muted">Office of the Controller of Examinations & Academic Records</p>
                </div>
            </div>
            <div class="text-right">
                <span class="badge badge-success px-3 py-1 font-bold text-xs uppercase tracking-wider">
                    Official Student Transcript
                </span>
                <div class="text-xs text-muted mt-1">Generated: <?= date('M d, Y') ?></div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-xl bg-card border border-border mb-6">
            <div>
                <span class="text-xs text-muted block uppercase">Student Name</span>
                <strong class="text-text text-sm"><?= e($student['name'] ?? auth()['name']) ?></strong>
            </div>
            <div>
                <span class="text-xs text-muted block uppercase">Admission No</span>
                <strong class="text-text text-sm font-mono"><?= e($student['admission_no'] ?? '-') ?></strong>
            </div>
            <div>
                <span class="text-xs text-muted block uppercase">Enrolled Course</span>
                <strong class="text-text text-sm"><?= e($student['course_name'] ?? 'Degree Program') ?></strong>
            </div>
            <div>
                <span class="text-xs text-muted block uppercase">Academic Status</span>
                <span class="badge badge-success">Good Standing</span>
            </div>
        </div>

        <div class="table-container mb-6">
            <table class="table">
                <thead>
                    <tr>
                        <th>Examination Title</th>
                        <th>Exam Date</th>
                        <th class="text-center">Max Marks</th>
                        <th class="text-center">Marks Scored</th>
                        <th class="text-center">Score %</th>
                        <th class="text-center">Grade</th>
                        <th class="text-right">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-muted">
                                <i class="fas fa-file-alt text-4xl mb-3 block opacity-30"></i>
                                No published examination records found for your account yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $totalMax = 0;
                        $totalEarned = 0;
                        foreach ($results as $r): 
                            $max = (float)($r['max_marks'] ?? 100);
                            $earned = (float)($r['marks_obtained'] ?? 0);
                            $totalMax += $max;
                            $totalEarned += $earned;
                            $pct = $max > 0 ? round(($earned / $max) * 100, 1) : 0;
                        ?>
                            <tr>
                                <td class="font-bold text-text"><?= e($r['exam_title']) ?></td>
                                <td class="text-xs text-muted"><?= date('M d, Y', strtotime($r['exam_date'])) ?></td>
                                <td class="text-center font-mono"><?= $max ?></td>
                                <td class="text-center font-mono font-bold text-text"><?= $earned ?></td>
                                <td class="text-center font-mono font-semibold"><?= $pct ?>%</td>
                                <td class="text-center">
                                    <span class="badge <?= in_array($r['grade'], ['A+', 'A']) ? 'badge-success' : ($r['grade'] === 'F' ? 'badge-danger' : 'badge-primary') ?> font-bold font-mono">
                                        <?= e($r['grade']) ?>
                                    </span>
                                </td>
                                <td class="text-right text-xs">
                                    <?= $r['grade'] === 'F' ? '<span class="text-rose-500 font-semibold">Needs Improvement</span>' : '<span class="text-emerald-500 font-semibold">Passed</span>' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($results) && $totalMax > 0): ?>
            <?php $cumPct = round(($totalEarned / $totalMax) * 100, 1); ?>
            <div class="p-4 rounded-xl bg-primary/5 border border-primary/20 flex justify-between items-center">
                <div>
                    <h4 class="font-bold text-text text-sm">Cumulative Performance Summary</h4>
                    <p class="text-xs text-muted">Aggregated marks scored across all verified examinations.</p>
                </div>
                <div class="flex items-center gap-6 text-right">
                    <div>
                        <div class="text-xs text-muted">Total Marks</div>
                        <div class="font-mono font-bold text-base"><?= $totalEarned ?> / <?= $totalMax ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-muted">Aggregate Percentage</div>
                        <div class="font-mono font-bold text-xl text-primary"><?= $cumPct ?>%</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <!-- Faculty / Admin Grading View -->
    <div class="page-header flex justify-between items-center mb-6">
        <div>
            <h1 class="page-title text-2xl font-bold">Examinations & Grade Book</h1>
            <p class="text-muted text-sm">Schedule course exams, enter marks scored by students, and calculate automated grading.</p>
        </div>
        <div class="flex gap-2">
            <?php if (has_role('admin', 'operator', 'faculty')): ?>
                <button type="button" class="btn btn-primary" onclick="openModal('scheduleExamModal')">
                    <i class="fas fa-calendar-plus mr-1"></i> Schedule Exam
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Exam Selector Card -->
    <div class="card p-4 mb-6">
        <form method="get" action="<?= e(base_url('results')) ?>" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="form-label text-xs uppercase font-semibold">Select Scheduled Examination</label>
                <select name="exam_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Choose an examination session --</option>
                    <?php foreach ($exams as $ex): ?>
                        <option value="<?= (int)$ex['id'] ?>" <?= $examId === (int)$ex['id'] ? 'selected' : '' ?>>
                            <?= e($ex['title']) ?> &mdash; <?= date('M d, Y', strtotime($ex['exam_date'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search mr-1"></i> Load Roster
                </button>
                <?php if ($examId): ?>
                    <a href="<?= e(base_url('results/create?exam_id=' . $examId)) ?>" class="btn btn-secondary">
                        <i class="fas fa-pen-alt mr-1"></i> Enter / Edit Marks
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if ($examId): ?>
        <div class="card p-0 mb-6">
            <div class="card-header p-4 border-b border-border flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="badge badge-primary font-bold px-2.5 py-1">
                        <?= count($results) ?> Evaluated Students
                    </span>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()">
                        <i class="fas fa-print mr-1"></i> Print Grade Sheet
                    </button>
                </div>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Admission No</th>
                            <th>Student Name</th>
                            <th class="text-center">Marks Obtained</th>
                            <th class="text-center">Maximum Marks</th>
                            <th class="text-center">Percentage</th>
                            <th class="text-center">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-8 text-muted">
                                    <i class="fas fa-clipboard-check text-4xl mb-3 block opacity-30"></i>
                                    No marks recorded for this exam yet.
                                    <div class="mt-3">
                                        <a href="<?= e(base_url('results/create?exam_id=' . $examId)) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus mr-1"></i> Enter Marks Now
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($results as $r): ?>
                                <?php 
                                $max = (float)($r['max_marks'] ?? 100);
                                $earned = (float)($r['marks_obtained'] ?? 0);
                                $pct = $max > 0 ? round(($earned / $max) * 100, 1) : 0;
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge badge-neutral font-mono font-bold text-xs">
                                            <?= e($r['admission_no']) ?>
                                        </span>
                                    </td>
                                    <td class="font-bold text-text"><?= e($r['student_name']) ?></td>
                                    <td class="text-center font-mono font-bold text-text"><?= $earned ?></td>
                                    <td class="text-center font-mono text-muted"><?= $max ?></td>
                                    <td class="text-center font-mono font-semibold"><?= $pct ?>%</td>
                                    <td class="text-center">
                                        <span class="badge <?= in_array($r['grade'], ['A+', 'A']) ? 'badge-success' : ($r['grade'] === 'F' ? 'badge-danger' : 'badge-primary') ?> font-bold font-mono">
                                            <?= e($r['grade']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="card p-8 text-center text-muted">
            <i class="fas fa-award text-4xl mb-3 block opacity-30"></i>
            <h3 class="text-base font-bold text-text mb-1">Select an Examination</h3>
            <p class="text-xs">Choose an exam from the dropdown above or schedule a new one to evaluate grades.</p>
        </div>
    <?php endif; ?>

    <!-- Schedule Exam Modal -->
    <div id="scheduleExamModal" class="modal-overlay hidden">
        <div class="modal-content max-w-lg">
            <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
                <h3 class="text-lg font-bold"><i class="fas fa-calendar-plus text-primary mr-2"></i>Schedule New Exam</h3>
                <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('scheduleExamModal')">&times;</button>
            </div>
            <form method="post" action="<?= e(base_url('results/exams')) ?>">
                <?= csrf_field() ?>
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Exam Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Mid-Term Examination Fall 2026" required>
                    </div>

                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Course Program *</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">Select Course</option>
                            <?php foreach ($courses as $c): ?>
                                <option value="<?= (int)$c['id'] ?>"><?= e($c['code'] . ' - ' . $c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Exam Date *</label>
                        <input type="date" name="exam_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('scheduleExamModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Create Exam & Enter Marks</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
