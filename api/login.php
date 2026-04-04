<?php
/**
 * POST /api/login.php
 * Body: { "username": "...", "password": "..." }
 * Returns: { "ok": true } + sets httpOnly session cookie
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();
requirePost();

// Rate limit: 5 attempts per 15 minutes
if (!checkRateLimit('login', LOGIN_MAX_ATTEMPTS, LOGIN_WINDOW_SECONDS)) {
    auditLog('login_rate_limited');
    jsonResponse(['error' => 'Too many login attempts. Try again in 15 minutes.'], 429);
}

// Parse input
$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    jsonResponse(['error' => 'Username and password are required'], 400);
}

// Look up user
$db = getDB();
$stmt = $db->prepare("SELECT id, password FROM admin_users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    auditLog('login_fail', 'username=' . sanitize($username));
    jsonResponse(['error' => 'Invalid credentials'], 401);
}

// Success — start session
resetRateLimit('login');
startSecureSession();
session_regenerate_id(true);
$_SESSION['admin_id'] = $user['id'];
$_SESSION['admin_user'] = $username;
$_SESSION['login_time'] = time();

auditLog('login_success', 'username=' . sanitize($username));
jsonResponse(['ok' => true, 'username' => $username]);
