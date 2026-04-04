<?php
/**
 * Database Configuration — Vāk Media Backend
 *
 * IMPORTANT: On Hostinger, move this file OUTSIDE public_html:
 *   /home/u<YOUR_ID>/config/db_config.php
 * Then update the require path in each API file accordingly.
 *
 * For development/repo, this file lives at /api/db_config.php
 * with placeholder values. Update with real credentials on the server.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u123456789_vakmedia');   // Change to your Hostinger DB name
define('DB_USER', 'u123456789_vakadmin');   // Change to your Hostinger DB user
define('DB_PASS', 'CHANGE_ME_ON_SERVER');   // Change to your Hostinger DB password

define('APP_SECRET', 'CHANGE_ME_RANDOM_64_CHAR_STRING'); // Used for CSRF tokens

// Session configuration
define('SESSION_LIFETIME', 3600);    // 1 hour
define('SESSION_NAME', 'vak_dash');

// Rate limiting
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_WINDOW_SECONDS', 900);        // 15 minutes
define('FORM_MAX_ATTEMPTS', 3);
define('FORM_WINDOW_SECONDS', 600);         // 10 minutes

// Email notification
define('NOTIFY_EMAIL', 'hello@vakmedia.in');
define('NOTIFY_FROM', 'noreply@vakmedia.in');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES    => false,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
    }
    return $pdo;
}
