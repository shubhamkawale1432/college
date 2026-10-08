<?php $title = 'Enroll New Student'; require VIEW_PATH . '/layout/header.php'; ?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h2><i class="fa-solid fa-user-plus" style="color: var(--primary); margin-right: 8px;"></i> Student Admission & Enrollment</h2>
        <a href="<?= e(base_url('students')) ?>" class="btn btn-outline btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to Directory
        </a>
    </div>

    <div class="card-body">
        <form method="post" action="<?= e(base_url('students')) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--primary); font-weight: 700; margin-bottom: 12px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 6px;">
                1. Personal Details & Photo
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= old('name') ?>" placeholder="e.g. Alexander Hamilton" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" placeholder="student@college.local" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone">Student Mobile Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= old('phone') ?>" placeholder="+1 (555) 123-4567">
                </div>
                <div class="form-group">
                    <label class="form-label" for="gender">Gender *</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?= old('date_of_birth') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="photo">Student Photo Identification</label>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                <span style="font-size: 0.75rem; color: var(--text-muted);">Used on student ID card & profile. Max 5MB (JPG, PNG, WebP).</span>
            </div>

            <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--primary); font-weight: 700; margin: 24px 0 12px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 6px;">
                2. Academic Program Assignment
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="department_id">Department</label>
                    <select id="department_id" name="department_id" class="form-control">
                        <option value="">-- Select Department --</option>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= (int) $d['id'] ?>"><?= e($d['name']) ?> (<?= e($d['code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="course_id">Course / Degree Program</label>
                    <select id="course_id" name="course_id" class="form-control">
                        <option value="">-- Select Course --</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?> (<?= e($c['code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="semester">Entry Semester</label>
                    <select id="semester" name="semester" class="form-control">
                        <?php for ($s = 1; $s <= 8; $s++): ?>
                            <option value="<?= $s ?>">Semester <?= $s ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--primary); font-weight: 700; margin: 24px 0 12px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 6px;">
                3. Guardian Information & Residential Address
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="guardian_name">Guardian / Parent Name</label>
                    <input type="text" id="guardian_name" name="guardian_name" class="form-control" placeholder="Guardian Full Name">
                </div>
                <div class="form-group">
                    <label class="form-label" for="guardian_phone">Guardian Emergency Phone</label>
                    <input type="text" id="guardian_phone" name="guardian_phone" class="form-control" placeholder="Guardian Phone">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Residential Address</label>
                <textarea id="address" name="address" class="form-control" rows="2" placeholder="Street, City, Postal Code, State"></textarea>
            </div>

            <div style="margin-top: 28px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                    <i class="fa-solid fa-user-check"></i> Complete Enrollment & Generate Invoice
                </button>
                <a href="<?= e(base_url('students')) ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
