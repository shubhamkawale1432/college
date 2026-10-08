# College Management System (Advanced Edition)

A layered PHP 8 college management application: controllers → services → repositories,
running on plain PDO/MySQL with no Composer dependencies, built to run on a stock WAMP/XAMPP stack.

## Features

- **Role-based access** (Admin / Faculty / Student) enforced per-route and per-record
- **2FA on login** (time-limited verification code, logged server-side)
- **Account lockout** after repeated failed logins, with automatic expiry
- **CSRF protection** on every state-changing form
- **Full audit trail**: every create/update/delete writes a before/after JSON diff to `activity_logs`
- **Transactional business logic**: student enrollment, bulk attendance, bulk result publishing,
  and fee payments all wrap multiple writes in a single DB transaction
- **Versioned REST API** (`/api/v1/...`) with bearer-token auth and rate limiting, alongside the web UI
- Modules: Students, Faculty, Departments, Courses, Attendance, Notices, Exams/Results, Fees, Roles

## Requirements

- PHP 8.1+ with `pdo_mysql` extension
- MySQL 5.7+ / MariaDB 10.3+
- Apache with `mod_rewrite` (WAMP/XAMPP/LAMP all work)

## Installation

See [INSTALLATION.md](INSTALLATION.md).

## Security

See [SECURITY.md](SECURITY.md) for the security model and hardening notes.

## Architecture

```
config/        env loading, DB config, constants
core/          framework: Database, Router, Request, Controller, Model, Auth, Validator, Logger, helpers
middleware/    Auth, Guest, Role, RateLimit, TokenAuth
repositories/  one class per table, PDO prepared statements only
services/      transactional business logic (enrollment, attendance, results, fees)
controllers/   web request handlers, thin — delegate to services/repositories
api/           versioned REST API controllers
views/         plain PHP templates, all output escaped via e()
database/      schema.sql (tables, FKs, seed data)
assets/        CSS/JS, no external framework dependency
```

## Default login

After importing the schema, log in with:

- Email: `admin@college.local`
- Password: `ChangeMe123!`

**Change this password immediately** via "Change Password" after first login.

## Running tests

```
php tests/run.php
```

Runs a lightweight syntax/smoke check over every PHP file (no PHPUnit dependency, per the
no-heavy-dependency constraint). For real unit coverage, install PHPUnit via Composer separately.
