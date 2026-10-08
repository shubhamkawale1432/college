<?php require VIEW_PATH . '/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <div class="brand-icon" style="margin: 0 auto 12px; width: 56px; height: 56px; font-size: 1.6rem;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1>Create Account</h1>
            <p>Join the college academic portal</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div><?= e($msg) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(base_url('register')) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <div class="search-input-group">
                    <i class="fa-solid fa-user search-icon"></i>
                    <input type="text" id="name" name="name" class="form-control" value="<?= old('name') ?>" placeholder="John Doe" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="search-input-group">
                    <i class="fa-solid fa-envelope search-icon"></i>
                    <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" placeholder="john.doe@example.com" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="role">Account Role</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="student">Student / Applicant</option>
                    <option value="faculty">Faculty / Instructor</option>
                    <option value="operator">Academic Operator / Manager</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Create Password</label>
                <div class="search-input-group">
                    <i class="fa-solid fa-lock search-icon"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Min 8 characters" minlength="8" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 8px;">
                <i class="fa-solid fa-check"></i>
                <span>Register Account</span>
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 0.85rem; color: var(--text-muted);">
            Already have an account? <a href="<?= e(base_url('login')) ?>" style="font-weight: 600;">Sign in here</a>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
