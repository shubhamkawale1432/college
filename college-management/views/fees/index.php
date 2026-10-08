<?php
$title = 'Fee Management & Invoices - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
$items = $pagination['data'] ?? $pagination['items'] ?? [];
$totalPages = (int)($pagination['last_page'] ?? $pagination['totalPages'] ?? 1);
$isStudent = has_role('student');
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title text-2xl font-bold"><?= $isStudent ? 'My Fee Invoices & Payments' : 'Tuition & Fee Billing Management' ?></h1>
        <p class="text-muted text-sm">Monitor student fee obligations, recorded receipts, payment gateways, and outstanding balances.</p>
    </div>
    <div class="flex gap-2">
        <?php if (!$isStudent && has_role('admin', 'operator')): ?>
            <button type="button" class="btn btn-primary" onclick="openModal('issueFeeModal')">
                <i class="fas fa-file-invoice-dollar mr-1"></i> Issue Fee Invoice
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon bg-emerald-500/10 text-emerald-500">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        <div>
            <div class="stat-value text-emerald-500"><?= format_currency((float)$totalCollected) ?></div>
            <div class="stat-label"><?= $isStudent ? 'Total Fees Settled' : 'Total Revenue Collected' ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-rose-500/10 text-rose-500">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div>
            <div class="stat-value text-rose-500"><?= format_currency((float)$outstanding) ?></div>
            <div class="stat-label"><?= $isStudent ? 'My Remaining Balance' : 'Total Outstanding Receivables' ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-primary/10 text-primary">
            <i class="fas fa-receipt"></i>
        </div>
        <div>
            <div class="stat-value"><?= (int)$pagination['total'] ?></div>
            <div class="stat-label">Invoices Registered</div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card p-4 mb-6">
    <form method="get" action="<?= e(base_url('fees')) ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="md:col-span-2">
            <label class="form-label text-xs uppercase font-semibold">Search Invoices</label>
            <input type="text" name="q" value="<?= e($search ?? '') ?>" placeholder="Search by student name or admission number..." class="form-control">
        </div>
        <div>
            <label class="form-label text-xs uppercase font-semibold">Invoice Status</label>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="partial" <?= $currentStatus === 'partial' ? 'selected' : '' ?>>Partially Paid</option>
                <option value="paid" <?= $currentStatus === 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="overdue" <?= $currentStatus === 'overdue' ? 'selected' : '' ?>>Overdue</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary flex-1">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <?php if (!empty($search) || !empty($currentStatus)): ?>
                <a href="<?= e(base_url('fees')) ?>" class="btn btn-secondary" title="Reset Filters">
                    <i class="fas fa-times"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Main Table Card -->
<div class="card p-0 mb-6">
    <div class="card-header p-4 flex justify-between items-center border-b border-border">
        <div class="flex items-center gap-2">
            <span class="badge badge-primary font-bold px-2.5 py-1">
                <?= (int)$pagination['total'] ?> Invoices
            </span>
        </div>
        <div class="text-xs text-muted">
            Page <?= (int)$pagination['page'] ?> of <?= $totalPages ?>
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <?php if (!$isStudent): ?>
                        <th>Student Details</th>
                    <?php endif; ?>
                    <th>Fee Type</th>
                    <th>Invoiced Amount</th>
                    <th>Amount Paid</th>
                    <th>Balance Due</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="<?= $isStudent ? '7' : '8' ?>" class="text-center py-8 text-muted">
                            <i class="fas fa-receipt text-4xl mb-3 block opacity-30"></i>
                            No fee records found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $f): ?>
                        <?php 
                        $total = (float)$f['amount'];
                        $paid = (float)$f['amount_paid'];
                        $due = max(0, $total - $paid);
                        $statusClass = match($f['status']) {
                            'paid' => 'badge-success',
                            'partial' => 'badge-warning',
                            'overdue' => 'badge-danger',
                            default => 'badge-neutral',
                        };
                        ?>
                        <tr>
                            <?php if (!$isStudent): ?>
                                <td>
                                    <div class="font-bold text-text"><?= e($f['student_name'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-muted font-mono"><?= e($f['admission_no'] ?? '') ?></div>
                                </td>
                            <?php endif; ?>
                            <td>
                                <div class="font-semibold text-text"><?= e($f['fee_type'] ?? 'Tuition Fee') ?></div>
                                <div class="text-xs text-muted">Academic Assessment</div>
                            </td>
                            <td class="font-mono font-bold text-text"><?= format_currency($total) ?></td>
                            <td class="font-mono text-emerald-500 font-semibold"><?= format_currency($paid) ?></td>
                            <td class="font-mono font-bold <?= $due > 0 ? 'text-rose-500' : 'text-muted' ?>">
                                <?= format_currency($due) ?>
                            </td>
                            <td class="text-xs <?= (strtotime($f['due_date']) < time() && $f['status'] !== 'paid') ? 'text-rose-500 font-bold' : 'text-muted' ?>">
                                <?= date('M d, Y', strtotime($f['due_date'])) ?>
                            </td>
                            <td>
                                <span class="badge <?= $statusClass ?> uppercase text-2xs font-bold">
                                    <?= e($f['status']) ?>
                                </span>
                            </td>
                            <td class="text-right">
                                <?php if (!$isStudent && has_role('admin', 'operator') && $f['status'] !== 'paid'): ?>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="openPayModal(<?= (int)$f['id'] ?>, '<?= e(addslashes($f['student_name'] ?? 'Student')) ?>', <?= $due ?>)">
                                        <i class="fas fa-cash-register mr-1"></i> Pay
                                    </button>
                                <?php else: ?>
                                    <span class="text-xs text-muted"><?= $f['status'] === 'paid' ? '<i class="fas fa-check-circle text-emerald-500"></i> Cleared' : 'View Only' ?></span>
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
                    <a href="<?= e(base_url('fees?page=' . $p . (!empty($search) ? '&q=' . urlencode($search) : '') . (!empty($currentStatus) ? '&status=' . urlencode($currentStatus) : ''))) ?>" 
                       class="btn btn-sm <?= $p == $pagination['page'] ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Recent Payment Slips Audit -->
<?php if (!empty($recentTransactions)): ?>
<div class="card p-0 mb-6">
    <div class="card-header p-4 border-b border-border flex justify-between items-center">
        <h3 class="font-bold text-sm flex items-center gap-2">
            <i class="fas fa-history text-primary"></i> Recently Logged Payment Transactions
        </h3>
        <span class="text-xs text-muted">Audit trail of cash, card, and electronic settlements</span>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Tx Ref #</th>
                    <th>Student Name</th>
                    <th>Payment Method</th>
                    <th>Amount Settled</th>
                    <th>Date & Time</th>
                    <th>Cashier</th>
                    <th class="text-right">Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentTransactions as $tx): ?>
                    <tr>
                        <td class="font-mono text-xs font-bold text-text"><?= e($tx['transaction_no']) ?></td>
                        <td><?= e($tx['student_name']) ?></td>
                        <td><span class="badge badge-neutral uppercase font-mono text-2xs"><?= str_replace('_', ' ', e($tx['payment_method'])) ?></span></td>
                        <td class="font-mono font-bold text-emerald-500"><?= format_currency((float)$tx['amount']) ?></td>
                        <td class="text-xs text-muted"><?= date('M d, Y h:i A', strtotime($tx['payment_date'])) ?></td>
                        <td class="text-xs text-muted"><?= e($tx['recorder_name'] ?? 'Bursar') ?></td>
                        <td class="text-right">
                            <a href="<?= e(base_url('fees/receipt/' . $tx['id'])) ?>" class="btn btn-sm btn-secondary" title="View & Print Official Slip">
                                <i class="fas fa-receipt mr-1"></i> Slip
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!$isStudent && has_role('admin', 'operator')): ?>
<!-- Issue Fee Modal -->
<div id="issueFeeModal" class="modal-overlay hidden">
    <div class="modal-content max-w-lg">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-file-invoice text-primary mr-2"></i>Issue Fee Invoice</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('issueFeeModal')">&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('fees')) ?>">
            <?= csrf_field() ?>
            <div class="modal-body space-y-4">
                <div>
                    <label class="form-label text-xs uppercase font-semibold">Select Student *</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">-- Choose Student --</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= (int)$s['id'] ?>">
                                <?= e($s['admission_no'] . ' - ' . $s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Fee Classification *</label>
                    <select name="fee_type" class="form-control" required>
                        <option value="Tuition Fee (Semester 1)">Tuition Fee (Semester 1)</option>
                        <option value="Tuition Fee (Semester 2)">Tuition Fee (Semester 2)</option>
                        <option value="Annual Academic Tuition">Annual Academic Tuition</option>
                        <option value="Laboratory & Technology Fee">Laboratory & Technology Fee</option>
                        <option value="Hostel & Accommodation">Hostel & Accommodation</option>
                        <option value="Transportation / Bus Pass">Transportation / Bus Pass</option>
                        <option value="Examination & Registry Fee">Examination & Registry Fee</option>
                        <option value="Library Security Deposit">Library Security Deposit</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Amount ($) *</label>
                        <input type="number" step="0.01" min="1" name="amount" class="form-control font-mono font-bold" placeholder="2500.00" required>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Payment Due Date *</label>
                        <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('issueFeeModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Issue Invoice</button>
            </div>
        </form>
    </div>
</div>

<!-- Record Payment Modal -->
<div id="recordPayModal" class="modal-overlay hidden">
    <div class="modal-content max-w-md">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-cash-register text-primary mr-2"></i>Record Fee Payment</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('recordPayModal')">&times;</button>
        </div>
        <form id="payForm" method="post" action="">
            <?= csrf_field() ?>
            <div class="modal-body space-y-4">
                <div>
                    <label class="form-label text-xs uppercase font-semibold">Paying Student</label>
                    <input type="text" id="pay_student_name" class="form-control bg-background" readonly>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Payment Amount ($) *</label>
                    <input type="number" step="0.01" min="0.01" id="pay_amount" name="amount" class="form-control font-mono font-bold text-lg text-emerald-500" required>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Payment Method *</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="card">Debit / Credit Card</option>
                        <option value="cash">Cash Settlement</option>
                        <option value="upi_online">UPI / Instant Online Gateway</option>
                        <option value="bank_transfer">Wire / Bank Transfer</option>
                        <option value="cheque">Bank Cheque / Draft</option>
                    </select>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Transaction Notes / Reference</label>
                    <input type="text" name="notes" class="form-control" placeholder="Check number or payment gateway reference...">
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('recordPayModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-receipt mr-1"></i> Settle & Print Receipt</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPayModal(feeId, studentName, dueAmount) {
    document.getElementById('payForm').action = '<?= base_url('fees/') ?>' + feeId + '/pay';
    document.getElementById('pay_student_name').value = studentName;
    document.getElementById('pay_amount').value = dueAmount;
    document.getElementById('pay_amount').max = dueAmount;
    openModal('recordPayModal');
}
</script>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
