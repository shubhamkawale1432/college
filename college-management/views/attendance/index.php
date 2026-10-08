<?php
$title = ($isStudent ? 'My Attendance' : 'Attendance Management') . ' - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
?>

<?php if ($isStudent): ?>
    <!-- Student Self View -->
    <div class="page-header mb-6">
        <h1 class="page-title text-2xl font-bold">My Attendance Record</h1>
        <p class="text-muted text-sm">Review your semester attendance percentage, class sessions, and absence alerts.</p>
    </div>

    <!-- Attendance KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon bg-primary/10 text-primary">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div class="stat-value"><?= (int)$totalClasses ?></div>
                <div class="stat-label">Total Sessions</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-emerald-500/10 text-emerald-500">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <div class="stat-value"><?= (int)$presentClasses ?></div>
                <div class="stat-label">Attended Classes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-rose-500/10 text-rose-500">
                <i class="fas fa-user-times"></i>
            </div>
            <div>
                <div class="stat-value"><?= (int)($totalClasses - $presentClasses) ?></div>
                <div class="stat-label">Missed Classes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon <?= $percentage >= 75 ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' ?>">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <div class="stat-value <?= $percentage >= 75 ? 'text-emerald-500' : 'text-amber-500' ?>"><?= $percentage ?>%</div>
                <div class="stat-label">Overall Rate <?= $percentage < 75 ? '(Below 75%)' : '(Good)' ?></div>
            </div>
        </div>
    </div>

    <!-- Attendance History Table -->
    <div class="card p-0 mb-6">
        <div class="card-header p-4 border-b border-border flex justify-between items-center">
            <h3 class="font-bold text-base flex items-center gap-2">
                <i class="fas fa-history text-primary"></i> Detailed Session Log
            </h3>
            <span class="text-xs text-muted">Mandatory 75% requirement for examinations</span>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Course / Subject</th>
                        <th>Faculty / Recorded By</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-8 text-muted">
                                <i class="fas fa-calendar-times text-4xl mb-3 block opacity-30"></i>
                                No attendance records have been registered for you yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                            <tr>
                                <td class="font-medium text-text">
                                    <i class="far fa-calendar-alt text-muted mr-1.5"></i>
                                    <?= date('D, M d, Y', strtotime($r['date'])) ?>
                                </td>
                                <td><?= e($r['course_name'] ?? 'Core Program') ?></td>
                                <td class="text-xs text-muted"><?= e($r['recorder_name'] ?? 'Faculty Instructor') ?></td>
                                <td>
                                    <?php if ($r['status'] === 'present'): ?>
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Present</span>
                                    <?php elseif ($r['status'] === 'late'): ?>
                                        <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Late</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Absent</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>
    <!-- Faculty / Admin Roll Call View -->
    <div class="page-header flex justify-between items-center mb-6">
        <div>
            <h1 class="page-title text-2xl font-bold">Attendance Roll Call</h1>
            <p class="text-muted text-sm">Select course batch and date to record daily presence and calculate automated metrics.</p>
        </div>
    </div>

    <!-- Course & Date Filter Bar -->
    <div class="card p-4 mb-6">
        <form method="get" action="<?= e(base_url('attendance')) ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="form-label text-xs uppercase font-semibold">Select Course Program *</label>
                <select name="course_id" class="form-control" required>
                    <option value="">-- Choose Academic Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= $courseId === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= e($c['code'] . ' - ' . $c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label text-xs uppercase font-semibold">Date of Session *</label>
                <input type="date" name="date" value="<?= e($date) ?>" class="form-control" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-full">
                    <i class="fas fa-users-class mr-1"></i> Load Roll Call
                </button>
            </div>
        </form>
    </div>

    <?php if ($courseId && !empty($records)): ?>
        <form method="post" action="<?= e(base_url('attendance')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="course_id" value="<?= (int)$courseId ?>">
            <input type="hidden" name="date" value="<?= e($date) ?>">

            <div class="card p-0 mb-6">
                <div class="card-header p-4 border-b border-border flex justify-between items-center bg-card">
                    <div class="flex items-center gap-3">
                        <span class="badge badge-primary font-bold px-2.5 py-1">
                            <?= count($records) ?> Students Enrolled
                        </span>
                        <div class="text-xs text-muted">
                            Recording for: <strong class="text-text"><?= date('F d, Y', strtotime($date)) ?></strong>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('present')">
                            <i class="fas fa-check-double mr-1"></i> Mark All Present
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">
                            <i class="fas fa-times mr-1"></i> Mark All Absent
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">#</th>
                                <th>Admission No</th>
                                <th>Student Name</th>
                                <th class="text-center" style="width: 320px;">Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $idx => $r): ?>
                                <tr>
                                    <td class="text-muted font-mono text-xs"><?= $idx + 1 ?></td>
                                    <td>
                                        <span class="badge badge-neutral font-mono font-bold text-xs">
                                            <?= e($r['admission_no']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-bold text-text"><?= e($r['student_name']) ?></div>
                                        <input type="hidden" name="student_id[]" value="<?= (int)$r['student_id'] ?>">
                                    </td>
                                    <td class="text-center">
                                        <div class="inline-flex rounded-lg border border-border p-1 bg-background gap-1">
                                            <label class="cursor-pointer text-xs font-semibold px-3 py-1.5 rounded flex items-center gap-1 transition-all has-[:checked]:bg-emerald-600 has-[:checked]:text-white">
                                                <input type="radio" name="status[<?= $idx ?>]" value="present" class="hidden status-radio" <?= ($r['status'] ?? 'present') === 'present' ? 'checked' : '' ?>>
                                                <i class="fas fa-check text-xs"></i> Present
                                            </label>
                                            <label class="cursor-pointer text-xs font-semibold px-3 py-1.5 rounded flex items-center gap-1 transition-all has-[:checked]:bg-amber-600 has-[:checked]:text-white">
                                                <input type="radio" name="status[<?= $idx ?>]" value="late" class="hidden status-radio" <?= ($r['status'] ?? '') === 'late' ? 'checked' : '' ?>>
                                                <i class="fas fa-clock text-xs"></i> Late
                                            </label>
                                            <label class="cursor-pointer text-xs font-semibold px-3 py-1.5 rounded flex items-center gap-1 transition-all has-[:checked]:bg-rose-600 has-[:checked]:text-white">
                                                <input type="radio" name="status[<?= $idx ?>]" value="absent" class="hidden status-radio" <?= ($r['status'] ?? '') === 'absent' ? 'checked' : '' ?>>
                                                <i class="fas fa-times text-xs"></i> Absent
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer p-4 border-t border-border flex justify-end">
                    <button type="submit" class="btn btn-primary px-6 py-2 text-base">
                        <i class="fas fa-save mr-2"></i> Save Daily Attendance
                    </button>
                </div>
            </div>
        </form>

        <script>
        function markAll(status) {
            document.querySelectorAll(`input.status-radio[value="${status}"]`).forEach(r => {
                r.checked = true;
                // Dispatch change event to update styled radios
                r.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }
        </script>
    <?php elseif ($courseId): ?>
        <div class="card p-8 text-center text-muted">
            <i class="fas fa-user-slash text-4xl mb-3 block opacity-30"></i>
            <h3 class="text-base font-bold text-text mb-1">No Enrolled Students</h3>
            <p class="text-xs">There are currently no active students assigned to this course program.</p>
            <div class="mt-4">
                <a href="<?= e(base_url('students/create')) ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-plus mr-1"></i> Enroll Students
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card p-8 text-center text-muted">
            <i class="fas fa-clipboard-list text-4xl mb-3 block opacity-30"></i>
            <h3 class="text-base font-bold text-text mb-1">Select Academic Course</h3>
            <p class="text-xs">Please choose a course and date above to load the roster and record daily attendance.</p>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
