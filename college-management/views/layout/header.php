<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e(($title ?? '') ? $title . ' - ' . setting('institute_name', config('name')) : setting('institute_name', config('name'))) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= e(base_url('assets/css/app.css')) ?>">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php if (is_authenticated()): ?>
<div class="app-wrapper">
    <!-- Collapsible Modern Sidebar -->
    <aside class="app-sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <?php if ($logo = setting('institute_logo')): ?>
                    <img src="<?= e(upload_url($logo)) ?>" alt="Logo" style="width: 100%; height: 100%; border-radius: var(--radius-sm); object-fit: cover;">
                <?php else: ?>
                    <i class="fa-solid fa-graduation-cap"></i>
                <?php endif; ?>
            </div>
            <div class="brand-text">
                <h2><?= e(setting('institute_name', config('name'))) ?></h2>
                <span>Academic Portal</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <!-- Main Section -->
            <div class="nav-group">
                <div class="nav-group-title">Main</div>
                <ul class="nav-links">
                    <li>
                        <a href="<?= e(base_url('dashboard')) ?>" class="nav-link <?= active_nav('dashboard') ?>">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(base_url('/')) ?>" class="nav-link" target="_blank">
                            <i class="fa-solid fa-globe"></i>
                            <span>Campus Website</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(base_url('notifications')) ?>" class="nav-link <?= active_nav('notifications') ?>">
                            <i class="fa-solid fa-bell"></i>
                            <span>Notifications</span>
                            <?php if ($unread = unread_notifications_count()): ?>
                                <span class="nav-badge"><?= $unread ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Academics Section -->
            <div class="nav-group">
                <div class="nav-group-title">Academics</div>
                <ul class="nav-links">
                    <?php if (has_role('admin', 'operator', 'faculty')): ?>
                        <li>
                            <a href="<?= e(base_url('students')) ?>" class="nav-link <?= active_nav('students') ?>">
                                <i class="fa-solid fa-user-graduate"></i>
                                <span>Students</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?= e(base_url('courses')) ?>" class="nav-link <?= active_nav('courses') ?>">
                            <i class="fa-solid fa-book-open"></i>
                            <span>Courses</span>
                        </a>
                    </li>
                    <?php if (has_role('admin', 'operator', 'faculty')): ?>
                        <li>
                            <a href="<?= e(base_url('departments')) ?>" class="nav-link <?= active_nav('departments') ?>">
                                <i class="fa-solid fa-building-columns"></i>
                                <span>Departments</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= e(base_url('faculty')) ?>" class="nav-link <?= active_nav('faculty') ?>">
                                <i class="fa-solid fa-chalkboard-user"></i>
                                <span>Faculty</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?= e(base_url('attendance')) ?>" class="nav-link <?= active_nav('attendance') ?>">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Attendance</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(base_url('results')) ?>" class="nav-link <?= active_nav('results') ?>">
                            <i class="fa-solid fa-square-poll-vertical"></i>
                            <span>Exams & Results</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(base_url('notices')) ?>" class="nav-link <?= active_nav('notices') ?>">
                            <i class="fa-solid fa-bullhorn"></i>
                            <span>Notices</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Management & Finance Section -->
            <div class="nav-group">
                <div class="nav-group-title">Operations</div>
                <ul class="nav-links">
                    <li>
                        <a href="<?= e(base_url('fees')) ?>" class="nav-link <?= active_nav('fees') ?>">
                            <i class="fa-solid fa-receipt"></i>
                            <span>Fees & Payments</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(base_url('library')) ?>" class="nav-link <?= active_nav('library') ?>">
                            <i class="fa-solid fa-book-bookmark"></i>
                            <span>Library</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(base_url('complaints')) ?>" class="nav-link <?= active_nav('complaints') ?>">
                            <i class="fa-solid fa-headset"></i>
                            <span>Helpdesk</span>
                        </a>
                    </li>
                    <?php if (has_role('admin', 'operator', 'faculty')): ?>
                        <li>
                            <a href="<?= e(base_url('reports')) ?>" class="nav-link <?= active_nav('reports') ?>">
                                <i class="fa-solid fa-chart-pie"></i>
                                <span>Reports Center</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Administration Section (Admin only) -->
            <?php if (has_role('admin')): ?>
            <div class="nav-group">
                <div class="nav-group-title">Administration</div>
                <ul class="nav-links">
                    <li>
                        <a href="<?= e(base_url('users')) ?>" class="nav-link <?= active_nav('users') ?>">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(base_url('roles')) ?>" class="nav-link <?= active_nav('roles') ?>">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Roles & Perms</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(base_url('activity-logs')) ?>" class="nav-link <?= active_nav('activity-logs') ?>">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>Audit Trail</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= e(base_url('settings')) ?>" class="nav-link <?= active_nav('settings') ?>">
                            <i class="fa-solid fa-sliders"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <img src="<?= e(avatar_url(auth()['avatar'] ?? null, auth()['name'])) ?>" alt="Avatar" class="user-avatar-sm">
                <div class="user-meta" style="min-width: 0; flex: 1;">
                    <div class="user-meta-name"><?= e(auth()['name']) ?></div>
                    <div class="user-meta-role"><?= e(auth_role()) ?></div>
                </div>
                <a href="<?= e(base_url('profile')) ?>" title="Profile" style="color: var(--sidebar-text);"><i class="fa-solid fa-gear"></i></a>
            </div>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <!-- Main Content Area -->
    <div class="app-main">
        <header class="app-topbar no-print">
            <div class="topbar-left">
                <button type="button" class="icon-btn" id="sidebar-toggle-btn" title="Toggle Sidebar" aria-label="Toggle Navigation Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-title">
                    <h1><?= e($title ?? 'Dashboard') ?></h1>
                </div>
            </div>

            <div class="topbar-right">
                <!-- Theme Switcher -->
                <button type="button" class="icon-btn" id="theme-toggle-btn" title="Toggle Theme" aria-label="Toggle Theme Mode">
                    <i class="fa-solid fa-moon"></i>
                </button>

                <!-- Notifications -->
                <a href="<?= e(base_url('notifications')) ?>" class="icon-btn" title="Notifications" aria-label="Notifications">
                    <i class="fa-regular fa-bell"></i>
                    <?php if ($unread = unread_notifications_count()): ?>
                        <span class="icon-badge"><?= $unread ?></span>
                    <?php endif; ?>
                </a>

                <!-- User Profile Pill -->
                <a href="<?= e(base_url('profile')) ?>" class="user-profile-pill flex items-center gap-2" title="User Profile" style="color: var(--text-main); font-size: 0.88rem; font-weight: 600;">
                    <img src="<?= e(avatar_url(auth()['avatar'] ?? null, auth()['name'])) ?>" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                    <span class="topbar-username"><?= e(auth()['name']) ?></span>
                </a>

                <!-- Logout Form -->
                <form method="post" action="<?= e(base_url('logout')) ?>" style="display:inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline btn-sm" title="Log Out" aria-label="Log Out">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Exit</span>
                    </button>
                </form>
            </div>
        </header>

        <div class="content-body">
            <!-- Flash Message Alerts -->
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <div><?= e($msg) ?></div>
                </div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div><?= e($msg) ?></div>
                </div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['_errors'])): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <div>
                        <ul style="margin-left: 18px; margin-top: 4px;">
                            <?php foreach ($_SESSION['_errors'] as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php unset($_SESSION['_errors']); ?>
            <?php endif; ?>
<?php else: ?>
    <!-- Public / Non-authenticated Wrapper -->
    <main class="auth-page">
<?php endif; ?>
