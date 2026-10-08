# Security Notes

## Implemented protections

- **SQL injection**: all queries go through PDO prepared statements with bound parameters
  (see `core/Model.php` and every `repositories/*.php`). No raw string interpolation of user input into SQL.
- **XSS**: every dynamic value rendered in a view is passed through `e()` (`htmlspecialchars`), including
  in `views/**`. Never echo raw `$_POST`/`$_GET`/DB output without `e()`.
- **CSRF**: every state-changing form includes a token (`csrf_field()`), verified server-side
  (`Controller::requireCsrf()`) before any write happens.
- **Password storage**: `password_hash()` / `password_verify()` (bcrypt), never plaintext or reversible encryption.
- **Session hardening**: `session.use_strict_mode`, `httponly` + `samesite=Lax` cookies, session ID
  regenerated on login, session expiry enforced server-side (`Auth::validateSessionLifetime()`).
- **Account lockout**: configurable max failed attempts (`MAX_LOGIN_ATTEMPTS`) with a timed lockout
  (`LOGIN_LOCKOUT_SECONDS`), tracked per-user in the `users` table.
- **2FA**: optional per-user time-limited verification code on login (`two_factor_enabled` flag).
- **Security headers**: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, a restrictive CSP,
  and HSTS when served over HTTPS — set on every response (`send_security_headers()`).
- **Folder lockdown**: `config/`, `core/`, `repositories/`, `services/`, `controllers/`, `api/`, `database/`,
  and `storage/` each carry a `.htaccess` denying direct web access; only `index.php` is a valid entry point.
- **API auth**: REST endpoints require a bearer token, stored hashed (`sha256`) — never the plaintext token —
  with expiry, plus per-IP rate limiting on all API routes.
- **Audit trail**: every create/update/delete records actor, action, entity, and a before/after JSON diff
  in `activity_logs`, so any change can be traced back to a user and a point in time.

## Known limitations / what to add before real production use

- No email/SMS transport is wired up — 2FA codes are written to `storage/logs/app.log` instead of sent.
  Wire in a real mail/SMS provider before relying on 2FA in production.
- Rate limiting is file-based (fine for a single server); use Redis or similar behind a load balancer.
- No automated dependency/security scanning is configured — add one in CI if you extend this with
  third-party packages.
- `APP_DEBUG=true` will echo stack traces to the browser — never enable it in production.
- The seeded admin password (`ChangeMe123!`) must be changed immediately after setup.
