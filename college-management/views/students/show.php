<?php $title = "Student Profile - {$student['name']}"; require VIEW_PATH . '/layout/header.php'; ?>

<?php if (($printMode ?? '') === 'id_card'): ?>
    <!-- Printable Student ID Card View -->
    <div style="max-width: 420px; margin: 30px auto; padding: 24px; border: 2px solid #1e293b; border-radius: 14px; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); font-family: var(--font-body); text-align: center; position: relative;">
        <div style="border-bottom: 2px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px;">
            <h3 style="margin: 0; color: #4f46e5; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.05em; font-family: var(--font-heading);">
                <?= e(setting('institute_name', config('name'))) ?>
            </h3>
            <span style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">Official Student ID Card</span>
        </div>

        <img src="<?= e(avatar_url($student['photo'], $student['name'])) ?>" alt="Photo" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid #4f46e5; margin: 0 auto 12px; display: block;">

        <h2 style="font-size: 1.25rem; margin: 0 0 4px; color: #0f172a; font-family: var(--font-heading);"><?= e($student['name']) ?></h2>
        <div style="display: inline-block; background: #eef2ff; color: #4338ca; font-weight: 800; font-family: monospace; font-size: 0.95rem; padding: 3px 12px; border-radius: 9999px; margin-bottom: 16px;">
            <?= e($student['admission_no']) ?>
        </div>

        <div style="text-align: left; font-size: 0.82rem; border-top: 1px dashed #cbd5e1; border-bottom: 1px dashed #cbd5e1; padding: 12px 0; margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px;">
            <div><strong>Department:</strong> <?= e($student['department_name'] ?? 'General') ?></div>
            <div><strong>Program:</strong> <?= e($student['course_name'] ?? 'Undergraduate') ?></div>
            <div><strong>Semester:</strong> Semester <?= (int)($student['semester'] ?? 1) ?></div>
            <div><strong>Emergency Contact:</strong> <?= e($student['guardian_phone'] ?? $student['phone'] ?? 'Campus Security') ?></div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div style="font-family: monospace; font-size: 0.68rem; color: #64748b; text-align: left;">
                VALID: 2026-2027<br>CMS-VERIFIED
            </div>
            <div style="text-align: center;">
                <div style="border-bottom: 1px solid #000; width: 100px; margin-bottom: 2px;"></div>
                <span style="font-size: 0.65rem; color: #64748b;">Registrar Signature</span>
            </div>
        </div>
    </div>
    <div style="text-align: center; margin: 20px 0;" class="no-print">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Print ID Card
        </button>
        <a href="<?= e(base_url("students/{$student['id']}")) ?>" class="btn btn-outline">Back to Profile</a>
    </div>

<?php else: ?>
    <!-- Full 360-degree Student Profile Overview -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;">
            <div style="display: flex; align-items: center; gap: 24px; flex-wrap: wrap;">
                <img src="<?= e(avatar_url($student['photo'], $student['name'])) ?>" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-light); box-shadow: var(--shadow-md);">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                        <h1 style="font-size: 1.6rem; margin: 0;"><?= e($student['name']) ?></h1>
                        <span class="badge badge-<?= $student['status'] === 'active' ? 'success' : 'info' ?>"><?= e($student['status']) ?></span>
                    </div>
                    <div style="font-family: monospace; font-weight: 700; color: var(--primary); font-size: 0.95rem; margin-bottom: 6px;">
                        <?= e($student['admission_no']) ?>
                    </div>
                    <div style="font-size: 0.88rem; color: var(--text-muted); display: flex; gap: 16px; flex-wrap: wrap;">
                        <span><i class="fa-solid fa-envelope"></i> <?= e($student['email']) ?></span>
                        <?php if ($student['phone']): ?><span><i class="fa-solid fa-phone"></i> <?= e($student['phone']) ?></span><?php endif; ?>
                        <span><i class="fa-solid fa-graduation-cap"></i> <?= e($student['course_name'] ?? 'No Course') ?></span>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px;" class="no-print">
                <a href="<?= e(base_url("students/{$student['id']}?print=id_card")) ?>" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-id-card"></i> Student ID Card
                </a>
                <?php if (has_role('admin', 'operator')): ?>
                    <a href="<?= e(base_url("students/{$student['id']}/edit")) ?>" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Student
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Personal & Academic Details -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-user" style="color: var(--primary);"></i> Academic & Personal Records</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);">Department</span>
                        <strong><?= e($student['department_name'] ?? 'N/A') ?> (<?= e($student['department_code'] ?? '') ?>)</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);">Course / Major</span>
                        <strong><?= e($student['course_name'] ?? 'N/A') ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);">Current Semester</span>
                        <span class="badge badge-primary">Semester <?= (int)($student['semester'] ?? 1) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);">Gender</span>
                        <span style="text-transform: capitalize;"><?= e($student['gender'] ?? 'Not specified') ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);">Date of Birth</span>
                        <span><?= e($student['date_of_birth'] ? date('M j, Y', strtotime($student['date_of_birth'])) : 'N/A') ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);">Guardian Contact</span>
                        <span><?= e($student['guardian_name'] ?? 'N/A') ?> (<?= e($student['guardian_phone'] ?? 'N/A') ?>)</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Address</span>
                        <span style="max-width: 250px; text-align: right;"><?= e($student['address'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Stats -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-calendar-check" style="color: var(--primary);"></i> Attendance Summary</h3>
                <a href="<?= e(base_url('attendance')) ?>" class="btn btn-outline btn-sm">Full Attendance</a>
            </div>
            <div class="card-body">
                <?php 
                    $totalAtt = count($attendanceRecords);
                    $presentAtt = count(array_filter($attendanceRecords, fn($a) => $a['status'] === 'present'));
                    $attRate = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 0.0;
                ?>
                <div style="text-align: center; padding: 16px 0; border-bottom: 1px solid var(--border-subtle); margin-bottom: 16px;">
                    <div style="font-family: var(--font-heading); font-size: 2.2rem; font-weight: 800; color: <?= $attRate >= 75 ? 'var(--success)' : 'var(--danger)' ?>;">
                        <?= $attRate ?>%
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Overall Attendance Rate (<?= $presentAtt ?> / <?= $totalAtt ?> Sessions)</div>
                </div>

                <div style="max-height: 180px; overflow-y: auto;">
                    <?php if (empty($attendanceRecords)): ?>
                        <p style="color: var(--text-muted); text-align: center;">No attendance marked yet.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($attendanceRecords, 0, 5) as $att): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; padding: 6px 0; border-bottom: 1px solid var(--border-subtle);">
                                <span><?= e(date('M j, Y', strtotime($att['attendance_date']))) ?></span>
                                <span class="badge badge-<?= $att['status'] === 'present' ? 'success' : 'danger' ?>"><?= e($att['status']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Invoices & Results Rows -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Invoices -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Fee Invoices</h3>
                <a href="<?= e(base_url('fees')) ?>" class="btn btn-outline btn-sm">All Invoices</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($feeInvoices)): ?>
                                <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">No invoices on record.</td></tr>
                            <?php else: ?>
                                <?php foreach ($feeInvoices as $f): ?>
                                    <tr>
                                        <td><?= e($f['fee_type']) ?></td>
                                        <td><?= format_currency($f['amount']) ?></td>
                                        <td><strong><?= format_currency($f['amount'] - $f['amount_paid']) ?></strong></td>
                                        <td><span class="badge badge-<?= $f['status'] === 'paid' ? 'success' : 'warning' ?>"><?= e($f['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Exam Results -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-square-poll-vertical" style="color: var(--primary);"></i> Exam Results</h3>
                <a href="<?= e(base_url('results')) ?>" class="btn btn-outline btn-sm">All Results</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Examination</th>
                                <th>Marks</th>
                                <th>Max</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($examResults)): ?>
                                <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">No exam results published yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($examResults as $res): ?>
                                    <tr>
                                        <td><strong><?= e($res['exam_title']) ?></strong></td>
                                        <td><?= (float)$res['marks_obtained'] ?></td>
                                        <td><?= (float)$res['max_marks'] ?></td>
                                        <td><span class="badge badge-primary"><?= e($res['grade'] ?? '—') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
