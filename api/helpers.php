<?php
/**
 * Shared helpers for Vāk Media API
 */

require_once __DIR__ . '/db_config.php';

/* ========== SESSION ========== */

function startSecureSession() {
    if (session_status() === PHP_SESSION_ACTIVE) return;

    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'domain'   => '',
        'secure'   => true,
        'httponly'  => true,
        'samesite'  => 'Strict',
    ]);
    session_start();
}

function requireAuth() {
    startSecureSession();
    if (empty($_SESSION['admin_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

/* ========== JSON RESPONSE ========== */

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/* ========== INPUT SANITIZATION ========== */

function sanitize($value, $maxLen = 500) {
    if ($value === null) return '';
    $value = trim($value);
    $value = mb_substr($value, 0, $maxLen, 'UTF-8');
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitizeEmail($email) {
    $email = trim($email);
    $email = mb_substr($email, 0, 254, 'UTF-8');
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
}

function sanitizeUrl($url) {
    $url = trim($url);
    if ($url === '') return '';
    $url = mb_substr($url, 0, 500, 'UTF-8');
    // Only allow http(s) URLs
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    return filter_var($url, FILTER_VALIDATE_URL) ? htmlspecialchars($url, ENT_QUOTES, 'UTF-8') : '';
}

/* ========== RATE LIMITING ========== */

function checkRateLimit($endpoint, $maxAttempts, $windowSeconds) {
    $db = getDB();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    // Clean old entries
    $db->prepare("DELETE FROM rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL ? SECOND)")
       ->execute([$windowSeconds]);

    // Check current count
    $stmt = $db->prepare(
        "SELECT attempts, window_start FROM rate_limits WHERE ip_address = ? AND endpoint = ?"
    );
    $stmt->execute([$ip, $endpoint]);
    $row = $stmt->fetch();

    if ($row) {
        if ($row['attempts'] >= $maxAttempts) {
            return false; // Rate limited
        }
        $db->prepare("UPDATE rate_limits SET attempts = attempts + 1 WHERE ip_address = ? AND endpoint = ?")
           ->execute([$ip, $endpoint]);
    } else {
        $db->prepare("INSERT INTO rate_limits (ip_address, endpoint, attempts) VALUES (?, ?, 1)")
           ->execute([$ip, $endpoint]);
    }
    return true;
}

function resetRateLimit($endpoint) {
    $db = getDB();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $db->prepare("DELETE FROM rate_limits WHERE ip_address = ? AND endpoint = ?")
       ->execute([$ip, $endpoint]);
}

/* ========== AUDIT LOG ========== */

function auditLog($action, $detail = '') {
    $db = getDB();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $db->prepare("INSERT INTO audit_log (action, detail, ip_address) VALUES (?, ?, ?)")
       ->execute([$action, $detail, $ip]);
}

/* ========== CORS & HEADERS ========== */

function setApiHeaders() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = ['https://vakmedia.in', 'https://www.vakmedia.in'];

    // In development, also allow localhost
    if (in_array($origin, $allowed) || strpos($origin, 'http://localhost') === 0) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');

    // Handle preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

function requirePost() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
}

function requireGet() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
}
