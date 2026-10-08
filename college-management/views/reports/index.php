<?php
$title = 'Reports & Analytics Center - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
?>

<div class="page-header mb-6">
    <h1 class="page-title text-2xl font-bold">Reports & Data Analytics Center</h1>
    <p class="text-muted text-sm">Generate executive reports, download spreadsheet exports (CSV / Excel compatible), and print certified institutional summaries.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Student Roster Report Card -->
    <div class="card p-6 flex flex-col justify-between hover:border-primary/50 transition-all">
        <div>
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-xl mb-4">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3 class="font-bold text-lg text-text mb-1">Student Enrollment Census</h3>
            <p class="text-xs text-muted leading-relaxed mb-4">
                Comprehensive directory of enrolled students with admission details, semester standing, contacts, and degree programs.
            </p>

            <form method="get" action="<?= e(base_url('reports/export')) ?>" class="space-y-3">
                <input type="hidden" name="type" value="students">
                <div>
                    <label class="form-label text-2xs uppercase font-semibold">Filter Department</label>
                    <select name="dept_id" class="form-control text-xs">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= (int)$d['id'] ?>"><?= e($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label text-2xs uppercase font-semibold">Filter Course Program</label>
                    <select name="course_id" class="form-control text-xs">
                        <option value="">All Courses</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= (int)$c['id'] ?>"><?= e($c['code'] . ' - ' . $c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-full mt-2">
                    <i class="fas fa-file-excel mr-1"></i> Download Students CSV
                </button>
            </form>
        </div>
    </div>

    <!-- Financial Transactions Report Card -->
    <div class="card p-6 flex flex-col justify-between hover:border-emerald-500/50 transition-all">
        <div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xl mb-4">
                <i class="fas fa-receipt"></i>
            </div>
            <h3 class="font-bold text-lg text-text mb-1">Bursar Collections & Cash Flow</h3>
            <p class="text-xs text-muted leading-relaxed mb-4">
                Chronological ledger of all student fee collections, payment methods, transaction reference numbers, and cashier accounts.
            </p>
        </div>
        <div class="space-y-3">
            <div class="p-3 rounded-lg bg-emerald-500/5 border border-emerald-500/20 text-xs text-emerald-600 dark:text-emerald-400">
                <i class="fas fa-check-circle mr-1"></i> Formatted for direct import into Excel, SAP, or QuickBooks.
            </div>
            <a href="<?= e(base_url('reports/export?type=transactions')) ?>" class="btn btn-primary w-full bg-emerald-600 hover:bg-emerald-700 border-none">
                <i class="fas fa-file-csv mr-1"></i> Export Transactions CSV
            </a>
        </div>
    </div>

    <!-- Outstanding & Receivables Report Card -->
    <div class="card p-6 flex flex-col justify-between hover:border-rose-500/50 transition-all">
        <div>
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center text-xl mb-4">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="font-bold text-lg text-text mb-1">Overdue & Aging Receivables</h3>
            <p class="text-xs text-muted leading-relaxed mb-4">
                Listing of all unpaid student invoices sorted by outstanding balance, overdue days, and semester due dates.
            </p>
        </div>
        <div class="space-y-3">
            <div class="p-3 rounded-lg bg-rose-500/5 border border-rose-500/20 text-xs text-rose-600 dark:text-rose-400">
                <i class="fas fa-clock mr-1"></i> Used for financial recovery, hall ticket clearance, and follow-ups.
            </div>
            <a href="<?= e(base_url('reports/export?type=fees_outstanding')) ?>" class="btn btn-primary w-full bg-rose-600 hover:bg-rose-700 border-none">
                <i class="fas fa-file-invoice mr-1"></i> Export Overdue Fees CSV
            </a>
        </div>
    </div>
</div>

<!-- Quick Print Direct Report Sheets -->
<div class="card p-6 mb-6">
    <div class="card-header p-0 pb-4 mb-4 border-b border-border flex justify-between items-center">
        <div>
            <h3 class="font-bold text-base text-text">Instant Print Summaries</h3>
            <p class="text-2xs text-muted">Generate clean, header-branded printable summaries for physical board meetings and accreditation files.</p>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Print Current Sheet
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl border border-border bg-background flex items-center justify-between">
            <div>
                <h4 class="font-bold text-sm text-text">Department Programs Matrix</h4>
                <p class="text-2xs text-muted">Degrees, duration, capacity & HODs</p>
            </div>
            <a href="<?= e(base_url('departments')) ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </div>
        <div class="p-4 rounded-xl border border-border bg-background flex items-center justify-between">
            <div>
                <h4 class="font-bold text-sm text-text">Faculty & Teaching Directory</h4>
                <p class="text-2xs text-muted">Academic staff credentials & emails</p>
            </div>
            <a href="<?= e(base_url('faculty')) ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </div>
        <div class="p-4 rounded-xl border border-border bg-background flex items-center justify-between">
            <div>
                <h4 class="font-bold text-sm text-text">Library Holdings Catalog</h4>
                <p class="text-2xs text-muted">Book inventory & circulation ledger</p>
            </div>
            <a href="<?= e(base_url('library')) ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-external-link-alt"></i>
            </a>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
