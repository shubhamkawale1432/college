<?php $title = 'My Profile'; require VIEW_PATH . '/layout/header.php'; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <!-- Profile Card Summary -->
    <div class="card lg:col-span-1">
        <div class="card-body" style="text-align: center; padding: 36px 24px;">
            <div style="position: relative; display: inline-block; margin-bottom: 16px;">
                <img src="<?= e(avatar_url($user['avatar'], $user['name'])) ?>" alt="Avatar" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-light); box-shadow: var(--shadow-md);">
            </div>
            <h2 style="font-size: 1.3rem; margin-bottom: 4px;"><?= e($user['name']) ?></h2>
            <div class="badge badge-primary" style="margin-bottom: 16px;"><?= e($user['role']) ?></div>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;"><?= e($user['email']) ?></p>

            <div style="border-top: 1px solid var(--border); padding-top: 18px; text-align: left; font-size: 0.85rem; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Account Status:</span>
                    <span class="badge badge-success"><?= e($user['status'] ?? 'active') ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">2FA Protection:</span>
                    <span class="badge <?= !empty($user['two_factor_enabled']) ? 'badge-success' : 'badge-warning' ?>">
                        <?= !empty($user['two_factor_enabled']) ? 'Enabled' : 'Disabled' ?>
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Last Sign In:</span>
                    <span><?= e(time_ago($user['last_login_at'])) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Member Since:</span>
                    <span><?= date('M Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <a href="<?= e(base_url('change-password')) ?>" class="btn btn-outline btn-sm" style="width: 100%;">
                    <i class="fa-solid fa-key"></i> Change Password
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="card lg:col-span-2">
        <div class="card-header">
            <h2><i class="fa-solid fa-user-pen" style="color: var(--primary); margin-right: 8px;"></i> Edit Personal Information</h2>
        </div>
        <div class="card-body">
            <form method="post" action="<?= e(base_url('profile')) ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address (Read-only)</label>
                        <input type="email" id="email" class="form-control" value="<?= e($user['email']) ?>" readonly style="background: var(--border-subtle); cursor: not-allowed;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>" placeholder="+1 (555) 000-0000">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="avatar">Profile Avatar Photo</label>
                        <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Max 5MB (JPG, PNG, WebP)</span>
                    </div>
                </div>

                <!-- Academic Role Linked Data -->
                <?php if ($roleData): ?>
                    <div style="background: var(--border-subtle); padding: 18px; border-radius: var(--radius-sm); margin: 18px 0; border: 1px solid var(--border);">
                        <h4 style="font-size: 0.95rem; margin-bottom: 12px; color: var(--primary);">
                            <i class="fa-solid fa-id-card"></i> Academic Affiliation Details
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; font-size: 0.85rem;">
                            <?php if (!empty($roleData['admission_no'])): ?>
                                <div><strong>Admission No:</strong> <?= e($roleData['admission_no']) ?></div>
                                <div><strong>Semester:</strong> <?= e((string)($roleData['semester'] ?? 1)) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($roleData['designation'])): ?>
                                <div><strong>Designation:</strong> <?= e($roleData['designation']) ?></div>
                                <div><strong>Qualification:</strong> <?= e($roleData['qualification'] ?? 'N/A') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group" style="margin-top: 10px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="two_factor_enabled" value="1" <?= !empty($user['two_factor_enabled']) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                        <span>Enable Two-Factor Authentication (2FA) verification code on login</span>
                    </label>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
