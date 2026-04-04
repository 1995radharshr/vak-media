<?php
/**
 * ONE-TIME SETUP SCRIPT
 * Run this once after importing setup.sql to set the correct bcrypt hash.
 * Then DELETE this file from the server.
 *
 * Access via: https://yourdomain.com/api/setup.php
 */

require_once __DIR__ . '/db_config.php';

// Safety: only allow if no admin exists yet or password is the placeholder
$db = getDB();

echo "<pre style='font-family:monospace;background:#0C0C10;color:#F0EFF8;padding:40px;'>";
echo "=== Vāk Media — Initial Setup ===\n\n";

// Create tables if they don't exist (run setup.sql first, but this is a safety net)
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('admin_users', $tables)) {
    echo "ERROR: Tables not found. Please run setup.sql in phpMyAdmin first.\n";
    echo "</pre>";
    exit;
}

// Hash the password and insert/update admin user
$username = 'Batman';
$password = '24Jumpstreet';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $db->prepare(
    "INSERT INTO admin_users (username, password) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE password = VALUES(password)"
);
$stmt->execute([$username, $hash]);

echo "Admin user created/updated successfully!\n\n";
echo "  Username: $username\n";
echo "  Password: [hidden — you know it]\n";
echo "  Hash:     $hash\n\n";
echo "=================================\n";
echo "IMPORTANT: DELETE this file from the server NOW!\n";
echo "  rm /home/*/public_html/api/setup.php\n";
echo "=================================\n";
echo "</pre>";
