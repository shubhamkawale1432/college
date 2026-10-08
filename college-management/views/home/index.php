<?php
$title = setting('institute_name', config('name')) . ' - Academic Excellence & Campus Portal';
$isAuth = is_authenticated();
$user = auth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= e(base_url('assets/css/app.css')) ?>">
    <style>
        /* Home Screen Custom Polish */
        .home-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }
        .home-hero {
            position: relative;
            background: radial-gradient(circle at 15% 20%, rgba(79, 70, 229, 0.15) 0%, rgba(15, 23, 42, 0.02) 70%), var(--bg-app);
            padding: 80px 24px 60px;
            overflow: hidden;
        }
        .home-hero::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -100px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
            border: 1px solid rgba(79, 70, 229, 0.2);
            border-radius: var(--radius-full);
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .home-stat-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }
        .home-stat-box:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: rgba(79, 70, 229, 0.4);
        }
        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(79, 70, 229, 0.35);
        }
        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 18px;
        }
        .portal-tile {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px;
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        .portal-tile:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }
        .home-footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border);
            padding: 60px 24px 30px;
        }
        .mobile-nav-menu {
            display: none;
        }
        @media (max-width: 768px) {
            .home-hero {
                padding: 40px 16px;
            }
            .desktop-nav-links {
                display: none;
            }
            .mobile-nav-toggle {
                display: block !important;
            }
            .mobile-nav-menu.open {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 16px;
                border-top: 1px solid var(--border);
            }
        }
    </style>
</head>
<body>

<!-- Public Top Navigation Bar -->
<header class="home-nav">
    <div style="max-width: 1200px; margin: 0 auto; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between;">
        <!-- Brand & Emblem -->
        <a href="<?= e(base_url('/')) ?>" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit;">
            <div style="width: 42px; height: 42px; background: var(--accent-gradient); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.3rem; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
                <?php if ($logo = setting('institute_logo')): ?>
                    <img src="<?= e(upload_url($logo)) ?>" alt="Logo" style="width: 100%; height: 100%; border-radius: 10px; object-fit: cover;">
                <?php else: ?>
                    <i class="fa-solid fa-graduation-cap"></i>
                <?php endif; ?>
            </div>
            <div>
                <span style="display: block; font-weight: 800; font-size: 1.15rem; color: var(--text-main); font-family: var(--font-heading); line-height: 1.2;">
                    <?= e(setting('institute_name', config('name'))) ?>
                </span>
                <span style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">
                    Academic Portal
                </span>
            </div>
        </a>

        <!-- Desktop Links -->
        <nav class="desktop-nav-links" style="display: flex; align-items: center; gap: 24px; font-size: 0.9rem; font-weight: 600;">
            <a href="#about" style="color: var(--text-muted); text-decoration: none; transition: var(--transition);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">About</a>
            <a href="#programs" style="color: var(--text-muted); text-decoration: none; transition: var(--transition);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Programs</a>
            <a href="#features" style="color: var(--text-muted); text-decoration: none; transition: var(--transition);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Campus Features</a>
            <a href="#notices" style="color: var(--text-muted); text-decoration: none; transition: var(--transition);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Notices</a>
            <a href="#contact" style="color: var(--text-muted); text-decoration: none; transition: var(--transition);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Contact</a>
        </nav>

        <!-- Right Action Controls -->
        <div style="display: flex; align-items: center; gap: 12px;">
            <!-- Theme Mode Switcher -->
            <button type="button" id="theme-toggle-btn" class="topbar-btn" title="Toggle Theme" aria-label="Toggle Dark/Light Mode" style="border: 1px solid var(--border); border-radius: 8px;">
                <i class="fa-solid fa-moon"></i>
            </button>

            <?php if ($isAuth): ?>
                <a href="<?= e(base_url('dashboard')) ?>" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Dashboard</span>
                </a>
            <?php else: ?>
                <a href="<?= e(base_url('login')) ?>" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Portal Sign In</span>
                </a>
            <?php endif; ?>

            <!-- Mobile Hamburger Toggle -->
            <button type="button" class="mobile-nav-toggle btn-secondary btn" style="display: none; padding: 6px 12px;" onclick="document.getElementById('mobile-nav').classList.toggle('open')">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Collapsible Mobile Menu -->
    <div id="mobile-nav" class="mobile-nav-menu">
        <a href="#about" class="btn btn-secondary btn-sm text-left" onclick="document.getElementById('mobile-nav').classList.remove('open')">About Campus</a>
        <a href="#programs" class="btn btn-secondary btn-sm text-left" onclick="document.getElementById('mobile-nav').classList.remove('open')">Academic Programs</a>
        <a href="#features" class="btn btn-secondary btn-sm text-left" onclick="document.getElementById('mobile-nav').classList.remove('open')">Campus Features</a>
        <a href="#notices" class="btn btn-secondary btn-sm text-left" onclick="document.getElementById('mobile-nav').classList.remove('open')">Notice Board</a>
        <a href="#contact" class="btn btn-secondary btn-sm text-left" onclick="document.getElementById('mobile-nav').classList.remove('open')">Contact & Location</a>
    </div>
</header>

<!-- Main Hero Section -->
<section class="home-hero">
    <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
        <div class="hero-badge">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Accredited Campus • Academic Session <?= e(setting('academic_year', '2026-2027')) ?></span>
        </div>

        <h1 style="font-size: clamp(2.2rem, 5vw, 3.8rem); font-weight: 800; color: var(--text-main); line-height: 1.15; max-width: 900px; margin: 0 auto 20px; font-family: var(--font-heading); letter-spacing: -0.02em;">
            Empowering Modern Education with Institutional Excellence
        </h1>

        <p style="font-size: clamp(1rem, 2vw, 1.25rem); color: var(--text-muted); max-width: 760px; margin: 0 auto 36px; line-height: 1.6;">
            Welcome to <?= e(setting('institute_name', config('name'))) ?>'s integrated academic management gateway. Connect students, instructors, and campus administrators through a secure and unified digital platform.
        </p>

        <div style="display: flex; justify-content: center; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 60px;">
            <?php if ($isAuth): ?>
                <a href="<?= e(base_url('dashboard')) ?>" class="btn btn-primary" style="padding: 14px 28px; font-size: 1rem; border-radius: var(--radius-md);">
                    <i class="fa-solid fa-gauge-high mr-1"></i> Access Your Dashboard
                </a>
            <?php else: ?>
                <a href="<?= e(base_url('login')) ?>" class="btn btn-primary" style="padding: 14px 28px; font-size: 1rem; border-radius: var(--radius-md);">
                    <i class="fa-solid fa-user-lock mr-1"></i> Access Student / Staff Portal
                </a>
            <?php endif; ?>
            <a href="#programs" class="btn btn-secondary" style="padding: 14px 24px; font-size: 1rem; border-radius: var(--radius-md);">
                <i class="fa-solid fa-graduation-cap mr-1"></i> Explore Academic Programs
            </a>
            <a href="#notices" class="btn btn-outline" style="padding: 14px 24px; font-size: 1rem; border-radius: var(--radius-md);">
                <i class="fa-solid fa-bullhorn mr-1"></i> Latest Announcements
            </a>
        </div>

        <!-- Key Stats Counter Strip -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" style="max-width: 1000px; margin: 0 auto;">
            <div class="home-stat-box">
                <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary); line-height: 1; margin-bottom: 6px;">
                    <?= (int)($stats['students'] ?? 0) ?>+
                </div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                    Active Students
                </div>
            </div>
            <div class="home-stat-box">
                <div style="font-size: 2.2rem; font-weight: 800; color: #10b981; line-height: 1; margin-bottom: 6px;">
                    <?= (int)($stats['faculty'] ?? 0) ?>+
                </div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                    Faculty Mentors
                </div>
            </div>
            <div class="home-stat-box">
                <div style="font-size: 2.2rem; font-weight: 800; color: #f59e0b; line-height: 1; margin-bottom: 6px;">
                    <?= (int)($stats['courses'] ?? 0) ?>
                </div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                    Degree Programs
                </div>
            </div>
            <div class="home-stat-box">
                <div style="font-size: 2.2rem; font-weight: 800; color: #0ea5e9; line-height: 1; margin-bottom: 6px;">
                    <?= (int)($stats['departments'] ?? 0) ?>
                </div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                    Academic Depts
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" style="padding: 70px 24px; max-width: 1200px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; align-items: center;">
        <div>
            <div class="badge badge-primary font-bold mb-3">About The Institution</div>
            <h2 style="font-size: 2.2rem; font-weight: 800; color: var(--text-main); line-height: 1.2; margin-bottom: 16px;">
                Committed to Scholastic Excellence and Technological Innovation
            </h2>
            <p style="color: var(--text-muted); font-size: 1rem; line-height: 1.6; margin-bottom: 20px;">
                At <?= e(setting('institute_name', config('name'))) ?>, we foster an enriching academic environment that combines rigorous theoretical study with real-world application. Our campus infrastructure leverages state-of-the-art information systems to ensure seamless administrative transparency, student support, and academic success.
            </p>
            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span style="font-size: 0.95rem; font-weight: 600;">Government & Higher Education Commission Accredited</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span style="font-size: 0.95rem; font-weight: 600;">100% Digitalized Records, Attendance, & Grading Systems</span>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span style="font-size: 0.95rem; font-weight: 600;">Modern Labs, Digital Library, and Student Grievance Redressal</span>
                </div>
            </div>
            <a href="<?= e(base_url('login')) ?>" class="btn btn-primary">
                <i class="fa-solid fa-id-card-clip mr-1"></i> Sign In to Student Portal
            </a>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 32px; box-shadow: var(--shadow-md);">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-landmark text-primary"></i> Campus Core Pillars
            </h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="padding: 16px; background: var(--border-subtle); border-radius: var(--radius-md); border-left: 4px solid var(--primary);">
                    <div style="font-weight: 700; font-size: 0.95rem;">Rigorous Curriculum</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Industry-aligned courses reviewed annually by academic councils.</div>
                </div>
                <div style="padding: 16px; background: var(--border-subtle); border-radius: var(--radius-md); border-left: 4px solid #10b981;">
                    <div style="font-weight: 700; font-size: 0.95rem;">Empowered Faculty</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Dedicated instructors with extensive domain expertise and research credentials.</div>
                </div>
                <div style="padding: 16px; background: var(--border-subtle); border-radius: var(--radius-md); border-left: 4px solid #f59e0b;">
                    <div style="font-weight: 700; font-size: 0.95rem;">Holistic Student Growth</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Active support for extracurricular activities, campus library research, and workshops.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Academic Programs Section -->
<section id="programs" style="background: var(--border-subtle); padding: 70px 24px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 48px;">
            <div class="badge badge-primary font-bold mb-2">Curriculum Offerings</div>
            <h2 style="font-size: 2.2rem; font-weight: 800; color: var(--text-main); margin-bottom: 10px;">
                Degree Programs & Departments
            </h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto; font-size: 1rem;">
                Explore undergraduate and postgraduate programs crafted to prepare graduates for impactful careers.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($courses)): ?>
                <div class="card p-8 text-center col-span-3">
                    <i class="fa-solid fa-graduation-cap text-4xl text-muted mb-3"></i>
                    <p class="text-muted">Academic curriculum catalog is being updated for the upcoming term.</p>
                </div>
            <?php else: ?>
                <?php foreach ($courses as $c): ?>
                    <div class="feature-card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <span class="badge badge-info font-bold"><?= e($c['code'] ?? 'DEGREE') ?></span>
                            <span class="badge badge-neutral"><?= (int)($c['duration_years'] ?? 3) ?> Years</span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">
                            <?= e($c['name']) ?>
                        </h3>
                        <p style="color: var(--text-muted); font-size: 0.88rem; line-height: 1.5; margin-bottom: 20px; flex: 1;">
                            <?= e($c['description'] ?? 'Comprehensive professional degree curriculum offering foundational theory, practical laboratory work, and professional development.') ?>
                        </p>
                        <div style="border-top: 1px solid var(--border); padding-top: 14px; display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem;">
                            <span style="color: var(--text-muted);">Credits: <strong><?= (int)($c['credits'] ?? 120) ?></strong></span>
                            <?php if (!empty($c['fee_per_year']) && (float)$c['fee_per_year'] > 0): ?>
                                <span style="font-weight: 700; color: var(--primary);">
                                    <?= format_currency((float)$c['fee_per_year']) ?> / yr
                                </span>
                            <?php else: ?>
                                <span class="badge badge-success">Admissions Open</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Campus Features Section -->
<section id="features" style="padding: 70px 24px; max-width: 1200px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 48px;">
        <div class="badge badge-primary font-bold mb-2">Integrated Platform</div>
        <h2 style="font-size: 2.2rem; font-weight: 800; color: var(--text-main); margin-bottom: 10px;">
            Comprehensive Campus Management Systems
        </h2>
        <p style="color: var(--text-muted); max-width: 640px; margin: 0 auto; font-size: 1rem;">
            A unified suite of cloud-ready modules powering daily operations across all institutional facets.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="feature-card">
            <div class="feature-icon bg-primary/10 text-primary">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Digital Attendance Management</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">
                Instant session recording with faculty authorization, automatic 75% examination eligibility alerts, and transparent student logs.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon bg-emerald-500/10 text-emerald-500">
                <i class="fa-solid fa-award"></i>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Examinations & Grade Reports</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">
                Centralized exam scheduling, marks entry with grade calculation, and downloadable printable student report cards.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon bg-amber-500/10 text-amber-500">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Fee Collection & Invoicing</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">
                Complete ledger tracking, installment management, receipt generation with official signatures, and monthly revenue analytics.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon bg-sky-500/10 text-sky-500">
                <i class="fa-solid fa-book"></i>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Library Inventory & Circulation</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">
                Book cataloging, ISBN lookup, automated checkout due dates, physical copy tracking, and automated overdue fines.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon bg-rose-500/10 text-rose-500">
                <i class="fa-solid fa-headset"></i>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Helpdesk & Grievance Redressal</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">
                Confidential support ticketing for academic inquiries, hostel maintenance, and facility issues with priority redressal.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon bg-primary/10 text-primary">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Audit Trails & Security</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5;">
                Granular role-based access control, CSRF defenses, session timeouts, and audit logging of all sensitive administrative modifications.
            </p>
        </div>
    </div>
</section>

<!-- Notice Board Section -->
<section id="notices" style="background: var(--border-subtle); padding: 70px 24px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 36px; flex-wrap: wrap; gap: 16px;">
            <div>
                <div class="badge badge-primary font-bold mb-2">Campus Bulletin</div>
                <h2 style="font-size: 2.2rem; font-weight: 800; color: var(--text-main); margin: 0;">
                    Official Announcements & Notices
                </h2>
            </div>
            <?php if ($isAuth): ?>
                <a href="<?= e(base_url('notices')) ?>" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-list mr-1"></i> View All Notices
                </a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if (empty($notices)): ?>
                <div class="card p-8 text-center col-span-2">
                    <i class="fa-solid fa-bullhorn text-4xl text-muted mb-3"></i>
                    <p class="text-muted">No public announcements are active right now.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notices as $n): ?>
                    <div class="card p-6" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span class="badge badge-primary"><i class="fa-solid fa-bullhorn mr-1"></i> Notice</span>
                                <span style="font-size: 0.78rem; color: var(--text-muted);"><i class="far fa-clock mr-1"></i> <?= time_ago($n['created_at']) ?></span>
                            </div>
                            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">
                                <?= e($n['title']) ?>
                            </h3>
                            <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">
                                <?= e(mb_strimwidth($n['body'], 0, 180, '...')) ?>
                            </p>
                        </div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); border-top: 1px solid var(--border); padding-top: 12px; display: flex; justify-content: space-between;">
                            <span>Issued by: <strong><?= e($n['poster_name'] ?? 'Office of Academic Affairs') ?></strong></span>
                            <span>Date: <?= date('M d, Y', strtotime($n['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Direct Portal Gateway Callout -->
<section style="padding: 70px 24px; max-width: 1200px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 40px;">
        <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 10px;">
            Campus Portal Gateways
        </h2>
        <p style="color: var(--text-muted); font-size: 1rem;">
            Direct access channels tailored for students, faculty, and administrative staff.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="portal-tile">
            <div style="width: 60px; height: 60px; background: rgba(79, 70, 229, 0.1); color: var(--primary); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 18px;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 8px;">Student Portal</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px;">
                Check personal attendance, download semester grade sheets, review fee invoices, and issue helpdesk tickets.
            </p>
            <a href="<?= e(base_url('login')) ?>" class="btn btn-outline btn-sm w-full">Student Access</a>
        </div>

        <div class="portal-tile">
            <div style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 18px;">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 8px;">Faculty Hub</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px;">
                Record daily attendance, evaluate and publish exam scores, manage course syllabi, and communicate with batches.
            </p>
            <a href="<?= e(base_url('login')) ?>" class="btn btn-outline btn-sm w-full">Faculty Access</a>
        </div>

        <div class="portal-tile">
            <div style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 18px;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 8px;">Admin Gateway</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px;">
                Full institutional governance: admissions, fee reconciliation, role permissions, activity logs, and system settings.
            </p>
            <a href="<?= e(base_url('login')) ?>" class="btn btn-outline btn-sm w-full">Admin Access</a>
        </div>
    </div>
</section>

<!-- Footer Section -->
<footer id="contact" class="home-footer">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div class="col-span-1 md:col-span-2">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                    <div style="width: 36px; height: 36px; background: var(--accent-gradient); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.1rem;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span style="font-weight: 800; font-size: 1.2rem; color: var(--text-main); font-family: var(--font-heading);">
                        <?= e(setting('institute_name', config('name'))) ?>
                    </span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; max-width: 440px; margin-bottom: 18px;">
                    Providing high-quality higher education, cutting-edge technology, and modern learning experiences to cultivate leaders for tomorrow.
                </p>
                <div style="font-size: 0.88rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 8px;">
                    <div><i class="fa-solid fa-location-dot text-primary mr-2"></i> <?= e(setting('institute_address', 'Main Campus Avenue, University District')) ?></div>
                    <div><i class="fa-solid fa-envelope text-primary mr-2"></i> <?= e(setting('contact_email', 'info@college.edu')) ?></div>
                    <div><i class="fa-solid fa-phone text-primary mr-2"></i> <?= e(setting('contact_phone', '+1 (555) 019-2834')) ?></div>
                </div>
            </div>

            <div>
                <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 14px;">Quick Links</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.88rem;">
                    <li><a href="#about" style="color: var(--text-muted); text-decoration: none;">About Institution</a></li>
                    <li><a href="#programs" style="color: var(--text-muted); text-decoration: none;">Academic Programs</a></li>
                    <li><a href="#features" style="color: var(--text-muted); text-decoration: none;">Campus Facilities</a></li>
                    <li><a href="#notices" style="color: var(--text-muted); text-decoration: none;">Campus Notices</a></li>
                    <li><a href="<?= e(base_url('login')) ?>" style="color: var(--text-muted); text-decoration: none;">Portal Sign In</a></li>
                </ul>
            </div>

            <div>
                <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 14px;">Academic Governance</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.88rem;">
                    <li><span style="color: var(--text-muted);">Session: <?= e(setting('academic_year', '2026-2027')) ?></span></li>
                    <li><span style="color: var(--text-muted);">System: Advanced CMS v2.0</span></li>
                    <li><span style="color: var(--text-muted);">Status: Operational & Secured</span></li>
                    <li><span style="color: var(--text-muted);">Database: MySQL Enterprise</span></li>
                </ul>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 0.82rem; color: var(--text-muted);">
            <div>
                &copy; <?= date('Y') ?> <?= e(setting('institute_name', config('name'))) ?>. All rights reserved.
            </div>
            <div style="display: flex; gap: 16px;">
                <a href="<?= e(base_url('login')) ?>" style="color: var(--text-muted); text-decoration: none;">Admin Login</a>
                <span>•</span>
                <a href="#top" style="color: var(--text-muted); text-decoration: none;">Back to top &uarr;</a>
            </div>
        </div>
    </div>
</footer>

<script src="<?= e(base_url('assets/js/app.js')) ?>"></script>
</body>
</html>
