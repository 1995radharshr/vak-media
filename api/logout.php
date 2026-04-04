<?php
/**
 * POST /api/logout.php
 * Destroys session and clears cookie
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();
requirePost();

startSecureSession();
auditLog('logout', 'user=' . ($_SESSION['admin_user'] ?? 'unknown'));

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

jsonResponse(['ok' => true]);
