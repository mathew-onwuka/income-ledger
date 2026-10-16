# EARN-NOW Income Ledger

EARN-NOW is a PHP and MySQL web application for recording and reviewing personal income. It includes user accounts, an earnings dashboard, and an administrator area for managing users and monitoring earnings.

## Features

- User registration and sign-in with hashed passwords
- User profile details, profile photo upload, and password changes
- Create, edit, delete, search, and review earnings records
- Earnings summaries and chart data on the user dashboard
- Date-based earnings lookup and reporting
- Administrator sign-in and an admin area with dashboard, user and earnings management, reports, settings, activity log, login history, and backup pages
- User account statuses, including active, inactive, suspended, and deleted

## Technology

- PHP with PDO
- MySQL or MariaDB
- Apache (for example, through XAMPP)
- Bootstrap, jQuery, and Chartist assets are included in `assets/`

## Requirements

- PHP 7.4 or later, with the PDO MySQL extension enabled
- MySQL or MariaDB
- Apache with PHP support

The application uses PHP sessions and stores profile images under `uploads/profile/`. The web-server process needs write access to that directory for profile photo uploads.

## Local setup with XAMPP

1. Place or clone the project into XAMPP's `htdocs` directory. For example:

   ```text
   C:\xampp\htdocs\IncomeLedgerMvc
   ```

2. Start Apache and MySQL from the XAMPP Control Panel.

3. Create a MySQL database named `earnnowdb` and import or create a schema compatible with the application. The database schema and seed data are **not included** in this repository. The code expects at least the `users`, `earnings`, and `admins` tables, with columns used by the application code.

4. Configure the database connection in [`classes/dbh.classes.php`](classes/dbh.classes.php). The current development defaults are MySQL on `localhost`, database `earnnowdb`, username `root`, and a blank password. Change these values to match your local database; do not use development defaults in a deployed environment.

5. Create an administrator record in the `admins` table before using the administrator sign-in page. The project does not include a default administrator account or a database seeding script. Store the administrator password as a PHP password hash, compatible with `password_hash()` and `password_verify()`.

6. Open the application in your browser. If the project directory is `IncomeLedgerMvc`, use:

   ```text
   http://localhost/IncomeLedgerMvc/
   ```

   Register a user through the sign-up page, then sign in. The administrator sign-in page is at `/admin-login.php`.

## Project layout

```text
.
├── admin-user/   Administrator pages
├── assets/       Stylesheets, scripts, fonts, and images
├── classes/      Database access, models, controllers, and view classes
├── includes/     Form handlers and authentication checks
├── uploads/      Profile image uploads
└── *.php         User-facing pages
```

## Important setup notes

- Database credentials are currently defined directly in `classes/dbh.classes.php`; there is no separate environment configuration file.
- No schema migration, SQL dump, default admin account, automated test suite, or dependency manifest is currently provided.
- For deployment, use a dedicated database account with a strong password, serve the site over HTTPS, and ensure uploaded files and database configuration are appropriately protected.
