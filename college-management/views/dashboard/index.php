<?php $title = 'Dashboard'; require VIEW_PATH . '/layout/header.php'; ?>

<!-- Top Welcome Banner -->
<div style="background: var(--accent-gradient); border-radius: var(--radius-lg); padding: 28px 32px; color: #fff; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; box-shadow: var(--shadow-glow);">
    <div>
        <div style="font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.85; font-weight: 700; margin-bottom: 4px;">
            Academic Year <?= e(setting('academic_year', '2026-2027')) ?> • <?= date('l, F j, Y') ?>
        </div>
        <h1 style="color: #fff; font-size: 1.8rem; margin: 0 0 6px;">
            Good day, <?= e(auth()['name']) ?>!
        </h1>
        <p style="margin: 0; opacity: 0.9; font-size: 0.95rem;">
            Welcome to the <?= e(setting('institute_name', config('name'))) ?> Academic Portal.
        </p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <?php if (has_role('admin', 'operator')): ?>
            <a href="<?= e(base_url('students/create')) ?>" class="btn" style="background: rgba(255,255,255,0.2); color: #fff; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px);">
                <i class="fa-solid fa-user-plus"></i> Enroll Student
            </a>
            <a href="<?= e(base_url('fees')) ?>" class="btn" style="background: #fff; color: var(--primary); font-weight: 700;">
                <i class="fa-solid fa-credit-card"></i> Collect Fees
            </a>
        <?php elseif (has_role('faculty')): ?>
            <a href="<?= e(base_url('attendance')) ?>" class="btn" style="background: #fff; color: var(--primary); font-weight: 700;">
                <i class="fa-solid fa-clipboard-user"></i> Mark Attendance
            </a>
        <?php else: ?>
            <a href="<?= e(base_url('complaints')) ?>" class="btn" style="background: #fff; color: var(--primary); font-weight: 700;">
                <i class="fa-solid fa-circle-question"></i> Helpdesk Ticket
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($role === 'student'): ?>
    <!-- Student Personalized Dashboard View -->
    <div class="stats-grid">
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fa-solid fa-graduation-cap"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Admission ID</div>
                <div class="stat-val" style="font-size: 1.3rem;"><?= e($student['admission_no'] ?? 'N/A') ?></div>
            </div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Classes Attended</div>
                <div class="stat-val"><?= count($studentAttendance) ?></div>
            </div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Fee Invoices</div>
                <div class="stat-val"><?= count($studentFees) ?></div>
            </div>
        </div>
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fa-solid fa-award"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Exams Taken</div>
                <div class="stat-val"><?= count($studentResults) ?></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-money-check-dollar" style="color: var(--primary);"></i> My Fee Status</h3>
                <a href="<?= e(base_url('fees')) ?>" class="btn btn-outline btn-sm">View Invoices</a>
            </div>
            <div class="card-body">
                <?php if (empty($studentFees)): ?>
                    <p style="color: var(--text-muted);">No fee invoices on record.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($studentFees as $f): ?>
                                    <tr>
                                        <td><?= e($f['fee_type']) ?></td>
                                        <td><?= format_currency($f['amount']) ?></td>
                                        <td><?= format_currency($f['amount_paid']) ?></td>
                                        <td><span class="badge badge-<?= $f['status'] === 'paid' ? 'success' : 'warning' ?>"><?= e($f['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-bullhorn" style="color: var(--primary);"></i> Campus Notices</h3>
                <a href="<?= e(base_url('notices')) ?>" class="btn btn-outline btn-sm">All Notices</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentNotices)): ?>
                    <p style="color: var(--text-muted);">No notices posted recently.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <?php foreach ($recentNotices as $n): ?>
                            <div style="border-left: 3px solid var(--primary); padding-left: 12px;">
                                <div style="font-weight: 700; font-size: 0.92rem;"><?= e($n['title']) ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);"><?= time_ago($n['created_at']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php elseif ($role === 'faculty'): ?>
    <!-- Faculty Personalized Dashboard View -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Faculty Role</div>
                <div class="stat-val" style="font-size: 1.2rem;"><?= e($faculty['designation'] ?? 'Academic Instructor') ?></div>
            </div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Total Students</div>
                <div class="stat-val"><?= (int)($studentCount ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fa-solid fa-book-open"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Offered Courses</div>
                <div class="stat-val"><?= (int)($courseCount ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fa-solid fa-file-signature"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Upcoming Exams</div>
                <div class="stat-val"><?= count($upcomingExams ?? []) ?></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="card mb-0">
            <div class="card-header">
                <h3><i class="fa-solid fa-calendar-days" style="color: var(--primary);"></i> Upcoming Examinations</h3>
                <a href="<?= e(base_url('results')) ?>" class="btn btn-outline btn-sm">Enter Results</a>
            </div>
            <div class="card-body">
                <?php if (empty($upcomingExams)): ?>
                    <p class="text-muted">No exams scheduled at this time.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($upcomingExams as $ex): ?>
                            <div class="p-3 rounded-lg border border-border flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-sm text-text"><?= e($ex['title']) ?></div>
                                    <div class="text-xs text-muted"><?= e($ex['course_name'] ?? 'General') ?></div>
                                </div>
                                <span class="badge badge-primary font-mono text-xs"><?= date('M j, Y', strtotime($ex['exam_date'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-0">
            <div class="card-header">
                <h3><i class="fa-solid fa-bullhorn" style="color: var(--primary);"></i> Campus Announcements</h3>
                <a href="<?= e(base_url('notices')) ?>" class="btn btn-outline btn-sm">All Notices</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentNotices)): ?>
                    <p class="text-muted">No notices posted recently.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recentNotices as $n): ?>
                            <div class="p-3 rounded-lg border-l-4 border-primary bg-primary/5">
                                <div class="font-bold text-sm text-text"><?= e($n['title']) ?></div>
                                <div class="text-xs text-muted"><?= time_ago($n['created_at']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Admin & Operator Dashboard View -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Enrolled Students</div>
                <div class="stat-val"><?= (int)($studentCount ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Faculty Staff</div>
                <div class="stat-val"><?= (int)($facultyCount ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Revenue Collected</div>
                <div class="stat-val"><?= format_currency($totalCollected ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Pending Dues</div>
                <div class="stat-val"><?= format_currency($outstandingFees ?? 0) ?></div>
            </div>
        </div>
        <div class="stat-card stat-danger">
            <div class="stat-icon"><i class="fa-solid fa-headset"></i></div>
            <div class="stat-info-text">
                <div class="stat-label">Open Tickets</div>
                <div class="stat-val"><?= (int)($openComplaints ?? 0) ?></div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-grid">
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-chart-line" style="color: var(--primary);"></i> Revenue & Fee Collections</h3>
                <span class="badge badge-success">Last 6 Months</span>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-chart-pie" style="color: var(--primary);"></i> Students by Department</h3>
                <span class="badge badge-primary">Active Breakdown</span>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Audit Stream -->
    <div class="dashboard-two-col">
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Recent Fee Payments</h3>
                <a href="<?= e(base_url('fees')) ?>" class="btn btn-outline btn-sm">All Payments</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tx Number</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Date</th>
                                <th>Slip</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentTransactions)): ?>
                                <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 24px;">No transactions recorded yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentTransactions as $tx): ?>
                                    <tr>
                                        <td><strong><?= e($tx['transaction_no']) ?></strong></td>
                                        <td><?= e($tx['student_name']) ?></td>
                                        <td><span style="font-weight: 700; color: var(--success);"><?= format_currency($tx['amount']) ?></span></td>
                                        <td><span class="badge badge-primary"><?= e($tx['payment_method']) ?></span></td>
                                        <td><?= e(date('M j, H:i', strtotime($tx['payment_date']))) ?></td>
                                        <td>
                                            <a href="<?= e(base_url("fees/receipt/{$tx['id']}")) ?>" class="btn btn-outline btn-sm" title="Print Receipt">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Security Audit Stream</h3>
                <a href="<?= e(base_url('activity-logs')) ?>" class="btn btn-outline btn-sm">Full Audit</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentActivity)): ?>
                    <p style="color: var(--text-muted);">No activity recorded.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <?php foreach ($recentActivity as $act): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                                <div>
                                    <strong><?= e($act['user_name'] ?? 'System') ?></strong>
                                    <span style="color: var(--text-muted);">• <?= e(str_replace('_', ' ', $act['action'])) ?></span>
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-light);"><?= time_ago($act['created_at']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts Initialization -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Revenue Chart
        const revCtx = document.getElementById('revenueChart');
        if (revCtx) {
            new Chart(revCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chartRevLabels ?? []) ?>,
                    datasets: [{
                        label: 'Fee Collections (<?= e(setting('currency_code', 'USD')) ?>)',
                        data: <?= json_encode($chartRevValues ?? []) ?>,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 3,
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Department Distribution Chart
        const deptCtx = document.getElementById('departmentChart');
        if (deptCtx) {
            new Chart(deptCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($chartDeptLabels ?? ['Computer Science', 'Business']) ?>,
                    datasets: [{
                        data: <?= json_encode($chartDeptValues ?? [1, 1]) ?>,
                        backgroundColor: ['#4f46e5', '#8b5cf6', '#10b981', '#f59e0b', '#0ea5e9'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    });
    </script>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
