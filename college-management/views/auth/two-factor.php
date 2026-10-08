<?php require VIEW_PATH . '/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <div class="brand-icon" style="margin: 0 auto 12px; width: 56px; height: 56px; font-size: 1.6rem;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h1>Two-Factor Authentication</h1>
            <p>Enter the 6-digit security code sent to your registered channel</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div><?= e($msg) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(base_url('login/verify')) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="code">6-Digit Verification Code</label>
                <input type="text" id="code" name="code" class="form-control" style="font-size: 1.4rem; letter-spacing: 0.3em; text-align: center;" maxlength="6" placeholder="000000" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px;">
                <i class="fa-solid fa-check"></i>
                <span>Verify & Proceed</span>
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 0.85rem; color: var(--text-muted);">
            <a href="<?= e(base_url('login')) ?>">Cancel & Return to Login</a>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
