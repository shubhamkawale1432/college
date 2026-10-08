<?php require VIEW_PATH . '/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <div class="brand-icon" style="margin: 0 auto 12px; width: 56px; height: 56px; font-size: 1.6rem;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h1>Welcome Back</h1>
            <p>Sign in to your academic account</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div><?= e($msg) ?></div>
            </div>
        <?php endif; ?>
        <?php if ($msg = flash('success')): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <div><?= e($msg) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(base_url('login')) ?>">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="search-input-group">
                    <i class="fa-solid fa-envelope search-icon"></i>
                    <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" placeholder="name@college.local" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                    <a href="<?= e(base_url('forgot-password')) ?>" style="font-size: 0.8rem; color: var(--primary);">Forgot?</a>
                </div>
                <div class="search-input-group">
                    <i class="fa-solid fa-lock search-icon"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px;">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Sign In</span>
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border);">
            <div style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); font-weight: 700; margin-bottom: 10px; text-align: center;">
                Demo Accounts (Click to Fill)
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                <button type="button" class="btn btn-outline btn-sm" onclick="fillLogin('admin@college.local', 'ChangeMe123!')">
                    <i class="fa-solid fa-user-shield"></i> Admin
                </button>
                <button type="button" class="btn btn-outline btn-sm" onclick="fillLogin('operator@college.local', 'ChangeMe123!')">
                    <i class="fa-solid fa-user-tie"></i> Operator
                </button>
                <button type="button" class="btn btn-outline btn-sm" onclick="fillLogin('faculty@college.local', 'ChangeMe123!')">
                    <i class="fa-solid fa-chalkboard-user"></i> Faculty
                </button>
                <button type="button" class="btn btn-outline btn-sm" onclick="fillLogin('student@college.local', 'ChangeMe123!')">
                    <i class="fa-solid fa-user-graduate"></i> Student
                </button>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 0.85rem; color: var(--text-muted);">
            Don't have an account? <a href="<?= e(base_url('register')) ?>" style="font-weight: 600;">Register here</a>
        </div>
    </div>
</div>

<script>
function fillLogin(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
}
</script>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
