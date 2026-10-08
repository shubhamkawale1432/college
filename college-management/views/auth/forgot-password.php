<?php require VIEW_PATH . '/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <div class="brand-icon" style="margin: 0 auto 12px; width: 56px; height: 56px; font-size: 1.6rem;">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1>Forgot Password</h1>
            <p>Enter your registered email address to receive reset instructions</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div><?= e($msg) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(base_url('forgot-password')) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="email">Account Email</label>
                <div class="search-input-group">
                    <i class="fa-solid fa-envelope search-icon"></i>
                    <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" placeholder="name@college.local" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px;">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Send Reset Link</span>
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 0.85rem; color: var(--text-muted);">
            Remember your password? <a href="<?= e(base_url('login')) ?>" style="font-weight: 600;">Back to sign in</a>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
