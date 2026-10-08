<?php $title = 'Change Password'; require VIEW_PATH . '/layout/header.php'; ?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <div class="card-header">
        <h2><i class="fa-solid fa-lock" style="color: var(--primary); margin-right: 8px;"></i> Change Password</h2>
    </div>
    <div class="card-body">
        <form method="post" action="<?= e(base_url('change-password')) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="current_password">Current Password</label>
                <div class="search-input-group">
                    <i class="fa-solid fa-key search-icon"></i>
                    <input type="password" id="current_password" name="current_password" class="form-control" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password">New Password</label>
                <div class="search-input-group">
                    <i class="fa-solid fa-shield-halved search-icon"></i>
                    <input type="password" id="new_password" name="new_password" class="form-control" minlength="8" placeholder="Min 8 characters" required>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Password</span>
                </button>
                <a href="<?= e(base_url('profile')) ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
