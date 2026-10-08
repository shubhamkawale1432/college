<?php require VIEW_PATH . '/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <div class="brand-icon" style="margin: 0 auto 12px; width: 56px; height: 56px; font-size: 1.6rem;">
                <i class="fa-solid fa-lock-open"></i>
            </div>
            <h1>Set New Password</h1>
            <p>Enter a secure new password for your account</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div><?= e($msg) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(base_url('reset-password')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= e($token) ?>">

            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <div class="search-input-group">
                    <i class="fa-solid fa-lock search-icon"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Min 8 characters" minlength="8" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px;">
                <i class="fa-solid fa-check"></i>
                <span>Update Password & Sign In</span>
            </button>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
