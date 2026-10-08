# Installation (WAMP / XAMPP / LAMP)

1. **Copy the project** into your web root, e.g. `C:\wamp64\www\college-management` or `/var/www/html/college-management`.

2. **Create the database**

   ```sql
   CREATE DATABASE college_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   Then import the schema:

   ```
   mysql -u root -p college_management < database/schema.sql
   ```

   (Or use phpMyAdmin: create the DB, then Import → select `database/schema.sql`.)

3. **Configure environment**

   Copy `.env.example` to `.env` and fill in your DB credentials:

   ```
   cp .env.example .env
   ```

   Edit `.env`:
   ```
   DB_HOST=127.0.0.1
   DB_NAME=college_management
   DB_USER=root
   DB_PASS=your_password
   APP_URL=http://localhost/college-management
   APP_ENV=production
   APP_DEBUG=false
   ```

4. **Enable mod_rewrite** (Apache)

   WAMP: click the WAMP tray icon → Apache → Apache Modules → check `rewrite_module`.
   Confirm `AllowOverride All` is set for your web root in Apache config so `.htaccess` is honored.

5. **Set folder permissions**

   The app writes to `storage/logs/`. Make sure the web server user can write there:
   ```
   chmod -R 775 storage/
   ```

6. **Visit the app**

   `http://localhost/college-management/login`

   Log in with the seeded admin account (see README.md), then change the password immediately.

7. **(Optional) Generate an API token** for REST API use by logging in at
   `POST /api/v1/auth/login` with `{"email": "...", "password": "..."}` — returns a bearer token
   valid 30 days.

## Troubleshooting

- **500 error, blank page**: set `APP_DEBUG=true` temporarily in `.env` to see the error, then check `storage/logs/app.log`.
- **404 on every page except /login**: `mod_rewrite` isn't enabled, or `.htaccess` isn't being read — check `AllowOverride`.
- **"Database connection failed"**: check `.env` credentials and that MySQL is running.
