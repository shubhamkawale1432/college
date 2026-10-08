<?php $title = "Edit Student - {$student['name']}"; require VIEW_PATH . '/layout/header.php'; ?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <div>
            <h2><i class="fa-solid fa-user-pen" style="color: var(--primary); margin-right: 8px;"></i> Edit Student Profile</h2>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Admission No: <strong><?= e($student['admission_no']) ?></strong></div>
        </div>
        <a href="<?= e(base_url("students/{$student['id']}")) ?>" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> View Student
        </a>
    </div>

    <div class="card-body">
        <form method="post" action="<?= e(base_url("students/{$student['id']}")) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="display: flex; gap: 24px; align-items: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                <img src="<?= e(avatar_url($student['photo'], $student['name'])) ?>" alt="Photo" style="width: 76px; height: 76px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-light);">
                <div style="flex: 1;">
                    <label class="form-label" for="photo">Change Student Photo</label>
                    <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Leave empty to retain existing photo. Max 5MB.</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= e($student['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= e($student['email']) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone">Mobile Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= e($student['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="gender">Gender *</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="male" <?= ($student['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= ($student['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= ($student['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Enrollment Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?= $student['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $student['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="graduated" <?= $student['status'] === 'graduated' ? 'selected' : '' ?>>Graduated</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="department_id">Department</label>
                    <select id="department_id" name="department_id" class="form-control">
                        <option value="">-- None --</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= (int) $d['id'] ?>" <?= (int) $d['id'] === (int) $student['department_id'] ? 'selected' : '' ?>>
                                <?= e($d['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="course_id">Course / Degree Program</label>
                    <select id="course_id" name="course_id" class="form-control">
                        <option value="">-- None --</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (int) $c['id'] === (int) $student['course_id'] ? 'selected' : '' ?>>
                                <?= e($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="semester">Semester</label>
                    <select id="semester" name="semester" class="form-control">
                        <?php for ($s = 1; $s <= 8; $s++): ?>
                            <option value="<?= $s ?>" <?= (int)($student['semester'] ?? 1) === $s ? 'selected' : '' ?>>Semester <?= $s ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="guardian_name">Guardian Name</label>
                    <input type="text" id="guardian_name" name="guardian_name" class="form-control" value="<?= e($student['guardian_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="guardian_phone">Guardian Phone</label>
                    <input type="text" id="guardian_phone" name="guardian_phone" class="form-control" value="<?= e($student['guardian_phone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Residential Address</label>
                <textarea id="address" name="address" class="form-control" rows="2"><?= e($student['address'] ?? '') ?></textarea>
            </div>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Save Student Changes
                </button>
                <a href="<?= e(base_url("students/{$student['id']}")) ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
