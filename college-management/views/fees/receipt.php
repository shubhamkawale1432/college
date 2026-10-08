<?php
$title = 'Payment Receipt #' . e($tx['transaction_no']) . ' - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
?>

<div class="page-header flex justify-between items-center mb-6 print:hidden">
    <div>
        <h1 class="page-title text-2xl font-bold">Official Payment Receipt</h1>
        <p class="text-muted text-sm">Transaction Ref: <span class="font-mono font-bold text-text"><?= e($tx['transaction_no']) ?></span></p>
    </div>
    <div class="flex gap-2">
        <a href="<?= e(base_url('fees')) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Invoices
        </a>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Print Receipt
        </button>
    </div>
</div>

<!-- Printable Slip -->
<div class="max-w-2xl mx-auto card p-8 bg-card border border-border shadow-md print:shadow-none print:border-none print:max-w-full print:p-0">
    <!-- Header -->
    <div class="flex justify-between items-start border-b border-border pb-6 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-2xl">
                <i class="fas fa-university"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-text"><?= setting('app_name', 'EduCore College of Technology') ?></h2>
                <p class="text-xs text-muted"><?= setting('contact_email', 'accounts@educore.edu') ?> &bull; <?= setting('contact_phone', '+1 (555) 019-2831') ?></p>
                <p class="text-xs text-muted">Bursar & Treasury Accounts Division</p>
            </div>
        </div>
        <div class="text-right">
            <div class="badge badge-success px-3 py-1 font-bold text-xs uppercase tracking-wider mb-1">
                <i class="fas fa-check-circle mr-1"></i> Payment Verified
            </div>
            <div class="font-mono text-xs text-muted">Receipt No:</div>
            <div class="font-mono font-bold text-sm text-text"><?= e($tx['transaction_no']) ?></div>
            <div class="text-xs text-muted"><?= date('M d, Y h:i A', strtotime($tx['payment_date'])) ?></div>
        </div>
    </div>

    <!-- Student & Account Details -->
    <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-background border border-border mb-6 text-sm">
        <div>
            <span class="text-xs text-muted uppercase block font-semibold mb-1">Received From (Student)</span>
            <div class="font-bold text-text text-base"><?= e($tx['student_name']) ?></div>
            <div class="text-xs text-muted">Admission No: <span class="font-mono font-bold text-text"><?= e($tx['admission_no']) ?></span></div>
            <div class="text-xs text-muted">Program: <?= e($tx['course_name'] ?? 'Degree Program') ?></div>
            <div class="text-xs text-muted">Dept: <?= e($tx['department_name'] ?? 'General') ?></div>
        </div>
        <div>
            <span class="text-xs text-muted uppercase block font-semibold mb-1">Payment Specification</span>
            <div class="text-xs text-muted">Fee Classification: <strong class="text-text"><?= e($tx['fee_type']) ?></strong></div>
            <div class="text-xs text-muted">Payment Channel: <strong class="text-text uppercase font-mono"><?= str_replace('_', ' ', e($tx['payment_method'])) ?></strong></div>
            <div class="text-xs text-muted">Issued By: <?= e($tx['cashier_name'] ?? 'Finance Officer') ?></div>
            <?php if (!empty($tx['notes'])): ?>
                <div class="text-xs text-muted mt-1 italic">Remarks: "<?= e($tx['notes']) ?>"</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Line Items Table -->
    <div class="table-container mb-6">
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Total Invoiced</th>
                    <th class="text-right">Total Settled</th>
                    <th class="text-right">Amount Paid Now</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="font-bold text-text"><?= e($tx['fee_type']) ?></div>
                        <div class="text-xs text-muted">Tuition & Institutional Assessment</div>
                    </td>
                    <td class="text-right font-mono"><?= format_currency((float)$tx['total_fee']) ?></td>
                    <td class="text-right font-mono text-emerald-500 font-semibold"><?= format_currency((float)$tx['total_paid']) ?></td>
                    <td class="text-right font-mono font-bold text-lg text-primary"><?= format_currency((float)$tx['amount']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Outstanding Balance & Signatures -->
    <?php 
    $remainingBalance = max(0, (float)$tx['total_fee'] - (float)$tx['total_paid']);
    ?>
    <div class="border-t border-border pt-4 mb-8">
        <div class="flex justify-between items-center text-sm">
            <span class="font-semibold text-muted">Remaining Balance After This Transaction:</span>
            <span class="font-mono font-bold <?= $remainingBalance > 0 ? 'text-amber-500' : 'text-emerald-500' ?>">
                <?= $remainingBalance > 0 ? format_currency($remainingBalance) : 'Fully Cleared ($0.00)' ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-8 pt-8 border-t border-dashed border-border mt-8 text-center text-xs text-muted">
        <div>
            <div class="h-10"></div>
            <div class="border-t border-border pt-1 font-semibold">Student / Payee Signature</div>
        </div>
        <div>
            <div class="h-10 flex items-center justify-center font-script text-primary font-bold text-sm">
                <?= e($tx['cashier_name'] ?? 'Authorized Bursar') ?>
            </div>
            <div class="border-t border-border pt-1 font-semibold">Authorized Signatory / Seal</div>
        </div>
    </div>

    <div class="text-center text-2xs text-muted mt-8 border-t border-border pt-3">
        This is a computer-generated official receipt issued by <?= setting('app_name', 'EduCore College') ?> ERP.
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
