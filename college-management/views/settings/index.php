<?php
$title = 'System Configuration & Settings - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';
?>

<div class="page-header mb-6">
    <h1 class="page-title text-2xl font-bold">System Configuration & Settings</h1>
    <p class="text-muted text-sm">Configure institutional metadata, currency formats, public student enrollment, security policies, and branding.</p>
</div>

<form method="post" action="<?= e(base_url('settings')) ?>" enctype="multipart/form-data" class="space-y-6">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: General Institutional Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Organization Profile -->
            <div class="card p-6">
                <div class="card-header p-0 pb-4 mb-4 border-b border-border flex items-center gap-2">
                    <i class="fas fa-university text-primary text-lg"></i>
                    <div>
                        <h3 class="font-bold text-base text-text">Institutional Identity</h3>
                        <p class="text-2xs text-muted">Legal institution title and official contact coordinates</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Institute / University Name *</label>
                        <input type="text" name="institute_name" class="form-control" value="<?= e($settings['institute_name'] ?? 'EduCore College of Technology') ?>" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label text-xs uppercase font-semibold">Official Contact Email *</label>
                            <input type="email" name="institute_email" class="form-control" value="<?= e($settings['institute_email'] ?? 'admin@educore.edu') ?>" required>
                        </div>
                        <div>
                            <label class="form-label text-xs uppercase font-semibold">Contact Phone Number *</label>
                            <input type="text" name="institute_phone" class="form-control" value="<?= e($settings['institute_phone'] ?? '+1 (555) 019-2831') ?>" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Campus Physical Address</label>
                        <textarea name="institute_address" class="form-control" rows="2"><?= e($settings['institute_address'] ?? '450 University Ave, Cambridge, MA 02138') ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label text-xs uppercase font-semibold">Current Academic Year</label>
                            <input type="text" name="academic_year" class="form-control" value="<?= e($settings['academic_year'] ?? '2026-2027') ?>" placeholder="2026-2027">
                        </div>
                        <div>
                            <label class="form-label text-xs uppercase font-semibold">Default System UI Theme</label>
                            <select name="theme_mode" class="form-control">
                                <option value="light" <?= ($settings['theme_mode'] ?? 'light') === 'light' ? 'selected' : '' ?>>Light Mode</option>
                                <option value="dark" <?= ($settings['theme_mode'] ?? '') === 'dark' ? 'selected' : '' ?>>Dark Mode</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currency & Financial Settings -->
            <div class="card p-6">
                <div class="card-header p-0 pb-4 mb-4 border-b border-border flex items-center gap-2">
                    <i class="fas fa-coins text-emerald-500 text-lg"></i>
                    <div>
                        <h3 class="font-bold text-base text-text">Currency & Financial Formatting</h3>
                        <p class="text-2xs text-muted">Bursar ledger currency symbols and invoicing codes</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Currency Symbol *</label>
                        <input type="text" name="currency_symbol" class="form-control font-mono font-bold" value="<?= e($settings['currency_symbol'] ?? '$') ?>" required>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Currency Code (ISO) *</label>
                        <input type="text" name="currency_code" class="form-control font-mono uppercase" value="<?= e($settings['currency_code'] ?? 'USD') ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Branding & Feature Toggles -->
        <div class="space-y-6">
            <!-- Branding & Logo -->
            <div class="card p-6">
                <div class="card-header p-0 pb-4 mb-4 border-b border-border flex items-center gap-2">
                    <i class="fas fa-image text-primary text-lg"></i>
                    <div>
                        <h3 class="font-bold text-base text-text">Branding & Logo</h3>
                        <p class="text-2xs text-muted">Displayed on navigation and official receipts</p>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <?php if (!empty($settings['institute_logo'])): ?>
                        <div class="p-4 rounded-xl bg-background border border-border inline-block mb-3">
                            <img src="<?= e(upload_url($settings['institute_logo'])) ?>" alt="College Logo" class="h-16 max-w-full object-contain mx-auto">
                        </div>
                    <?php else: ?>
                        <div class="w-20 h-20 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-3xl mx-auto mb-3">
                            <i class="fas fa-university"></i>
                        </div>
                    <?php endif; ?>
                    <p class="text-xs text-muted">Upload high-res PNG or SVG logo</p>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Choose New Logo File</label>
                    <input type="file" name="institute_logo" class="form-control" accept="image/*">
                </div>
            </div>

            <!-- Portal Feature Toggles -->
            <div class="card p-6">
                <div class="card-header p-0 pb-4 mb-4 border-b border-border flex items-center gap-2">
                    <i class="fas fa-toggle-on text-primary text-lg"></i>
                    <div>
                        <h3 class="font-bold text-base text-text">Security & Policy Toggles</h3>
                        <p class="text-2xs text-muted">Enable or restrict student onboarding</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="allow_registration" value="1" class="form-checkbox h-5 w-5 text-primary rounded mt-0.5" <?= ($settings['allow_registration'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <div>
                            <div class="font-semibold text-sm text-text">Public Student Registration</div>
                            <div class="text-xs text-muted">Permit prospective students to register accounts online</div>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="email_notifications" value="1" class="form-checkbox h-5 w-5 text-primary rounded mt-0.5" <?= ($settings['email_notifications'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <div>
                            <div class="font-semibold text-sm text-text">Email Notifications & Alerts</div>
                            <div class="text-xs text-muted">Dispatch email alerts for credentials and invoices</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Save Action Button -->
            <div class="card p-4 bg-primary/5 border border-primary/20">
                <button type="submit" class="btn btn-primary w-full py-2.5 text-base font-bold">
                    <i class="fas fa-save mr-2"></i> Save All Settings
                </button>
            </div>
        </div>
    </div>
</form>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
