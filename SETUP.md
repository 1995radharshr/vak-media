# Hostinger Deployment — Backend Setup Guide

## Prerequisites

- Hostinger Premium or Business hosting plan (PHP + MySQL included)
- Domain pointing to Hostinger (e.g., vakmedia.in)
- SSL enabled (free Let's Encrypt — enable in hPanel > Security > SSL)

---

## Step 1: Create MySQL Database

1. Log in to **hPanel** (hpanel.hostinger.com)
2. Go to **Databases > MySQL Databases**
3. Fill in:
   - **Database name**: `vakmedia` (Hostinger will prefix it, e.g., `u123456789_vakmedia`)
   - **Username**: `vakadmin` (becomes `u123456789_vakadmin`)
   - **Password**: Choose a strong password (16+ chars, mixed case, numbers, symbols)
4. Click **Create**
5. Note down the full database name, username, and password

---

## Step 2: Import Database Schema

1. In hPanel, go to **Databases > phpMyAdmin**
2. Click **Enter phpMyAdmin** next to your new database
3. Click the **Import** tab
4. Click **Choose File** and select `setup.sql` from this repo
5. Click **Go** to run the SQL
6. Verify 4 tables were created: `admin_users`, `applications`, `audit_log`, `rate_limits`

---

## Step 3: Upload Files

1. In hPanel, go to **Files > File Manager**
2. Navigate to `public_html/`
3. Upload ALL files from this repo:

```
public_html/
├── .htaccess              (root security headers — OVERWRITE if one exists)
├── index.html
├── services.html
├── process.html
├── join.html
├── dashboard.html
├── assets/
│   ├── vak-pipeline.mp4
│   └── vak-pipeline.webm
└── api/
    ├── .htaccess
    ├── db_config.php
    ├── helpers.php
    ├── login.php
    ├── logout.php
    ├── submit-application.php
    ├── applications.php
    ├── update-status.php
    ├── delete-application.php
    ├── export-csv.php
    └── setup.php           (temporary — delete after Step 5)
```

---

## Step 4: Configure Database Credentials

1. In File Manager, open `public_html/api/db_config.php`
2. Update these lines with your actual Hostinger credentials:

```php
define('DB_HOST', 'localhost');                        // Keep as localhost
define('DB_NAME', 'u123456789_vakmedia');              // Your full DB name from Step 1
define('DB_USER', 'u123456789_vakadmin');              // Your full DB username from Step 1
define('DB_PASS', 'YOUR_ACTUAL_DB_PASSWORD_HERE');     // Your DB password from Step 1
define('APP_SECRET', 'CHANGE_ME_RANDOM_64_CHAR_STRING'); // Generate at: https://randomkeygen.com
```

3. Save the file

### (Recommended) Move config outside public_html

For extra security, move `db_config.php` outside the web root:

1. Create folder: `/home/u123456789/config/`
2. Move `db_config.php` there
3. In `helpers.php`, change line 7:
   ```php
   require_once '/home/u123456789/config/db_config.php';
   ```
4. In `setup.php`, change line 13:
   ```php
   require_once '/home/u123456789/config/db_config.php';
   ```

---

## Step 5: Create Admin User

1. Visit `https://vakmedia.in/api/setup.php` in your browser
2. You should see:
   ```
   === Vāk Media — Initial Setup ===
   Admin user created/updated successfully!
   Username: Batman
   Password: [hidden — you know it]
   Hash: $2y$12$...
   ```
3. **IMMEDIATELY delete setup.php from the server:**
   - File Manager > `public_html/api/` > right-click `setup.php` > Delete

> **Admin credentials:**
> - Username: `Batman`
> - Password: `24Jumpstreet`

---

## Step 6: Verify Everything Works

### Test 1: Dashboard login
1. Visit `https://vakmedia.in/dashboard.html`
2. Enter Username: `Batman`, Password: `24Jumpstreet`
3. You should see the dashboard (empty — no applications yet)

### Test 2: Join form submission
1. Visit `https://vakmedia.in/join.html`
2. Fill in a test application and submit
3. Check your email (`hello@vakmedia.in`) for the notification
4. Refresh the dashboard — the application should appear

### Test 3: Dashboard actions
1. Click an application row to view details
2. Change status (e.g., "Reviewed")
3. Export CSV
4. Delete the test application

---

## Step 7: PHP Version Check

1. In hPanel, go to **Advanced > PHP Configuration**
2. Ensure PHP version is **8.0 or higher** (8.1/8.2 recommended)
3. Under PHP Options, ensure these extensions are enabled:
   - `pdo_mysql`
   - `mbstring`
   - `json`

---

## Troubleshooting

### "Database connection failed"
- Double-check credentials in `db_config.php`
- Verify the database exists in phpMyAdmin
- Ensure DB_HOST is `localhost` (not an IP)

### Forms return 500 error
- Check PHP error log: hPanel > Files > Error Logs
- Most likely: wrong DB credentials or missing PHP extensions

### Email notifications not arriving
- Hostinger's PHP `mail()` may require a verified sender domain
- In hPanel, go to **Emails > Email Accounts** and ensure the domain is set up
- Check spam folder
- Alternative: set up SMTP via Hostinger's email settings

### Dashboard shows "Unauthorized" after login
- Ensure the browser accepts cookies (not blocked by extensions)
- Check that SSL is enabled (session cookies are set with `secure: true`)
- Try clearing browser cookies and logging in again

### .htaccess causing 500 error
- Some Hostinger servers use LiteSpeed which supports most Apache directives
- If 500 error occurs, temporarily rename `.htaccess` to `.htaccess.bak`
- Remove directives one by one to find the incompatible one

---

## Security Notes

- **Never commit real credentials** to this repo — `db_config.php` contains placeholders only
- **Delete `setup.php`** after initial setup — it creates admin accounts
- **Delete `setup.sql`** from the server — it's only needed once
- The `.htaccess` blocks access to `.sql`, `.md`, `.env`, and `.git` files
- All API endpoints use prepared statements (SQL injection safe)
- All user input is sanitized with `htmlspecialchars()` (XSS safe)
- Login is rate-limited to 5 attempts per 15 minutes per IP
- Form submissions are rate-limited to 3 per 10 minutes per IP
- Session cookies are `httpOnly`, `secure`, `SameSite=Strict`
- Passwords are hashed with bcrypt (cost 12)

---

## Changing the Admin Password

To change the admin password after initial setup:

1. Open phpMyAdmin from hPanel
2. Select your database
3. Run this SQL (replace `NEW_PASSWORD_HERE`):

```sql
UPDATE admin_users
SET password = '$2y$12$...'
WHERE username = 'Batman';
```

To generate the hash, create a temporary PHP file:
```php
<?php echo password_hash('NEW_PASSWORD_HERE', PASSWORD_BCRYPT, ['cost' => 12]); ?>
```
Visit it in browser, copy the hash, paste into the SQL above, then delete the temp file.

---

## Adding More Admin Users

```sql
INSERT INTO admin_users (username, password) VALUES (
    'NewAdmin',
    '$2y$12$...'  -- bcrypt hash from password_hash()
);
```
