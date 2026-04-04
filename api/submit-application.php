<?php
/**
 * POST /api/submit-application.php
 * Receives join form data, validates, stores in MySQL, sends email notification.
 * No auth required (public endpoint).
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();
requirePost();

// Rate limit: 3 submissions per 10 minutes per IP
if (!checkRateLimit('submit', FORM_MAX_ATTEMPTS, FORM_WINDOW_SECONDS)) {
    auditLog('submit_rate_limited');
    jsonResponse(['error' => 'Too many submissions. Please try again later.'], 429);
}

// Accept both JSON and form-encoded data
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
} else {
    $data = $_POST;
}

// Validate required fields
$name  = sanitize($data['name'] ?? '', 200);
$email = sanitizeEmail($data['email'] ?? '');
$role  = sanitize($data['role'] ?? '', 100);

if ($name === '' || !$email || $role === '') {
    jsonResponse(['error' => 'Name, valid email, and role are required'], 400);
}

// Sanitize optional fields
$phone        = sanitize($data['phone'] ?? '', 30);
$location     = sanitize($data['location'] ?? '', 200);
$experience   = sanitize($data['experience'] ?? '', 100);
$availability = sanitize($data['availability'] ?? '', 100);
$portfolio    = sanitizeUrl($data['portfolio'] ?? '');
$tools        = sanitize($data['tools'] ?? '', 2000);
$niches       = sanitize($data['niches'] ?? '', 2000);
$why_vak      = sanitize($data['why_vak'] ?? '', 5000);
$other_info   = sanitize($data['other_info'] ?? '', 5000);

// Insert into database
$db = getDB();
$stmt = $db->prepare(
    "INSERT INTO applications
        (name, email, phone, location, role, experience, availability, portfolio, tools, niches, why_vak, other_info, status)
     VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')"
);
$stmt->execute([
    $name, $email, $phone, $location, $role, $experience,
    $availability, $portfolio, $tools, $niches, $why_vak, $other_info
]);

$appId = $db->lastInsertId();
auditLog('application_submitted', "id=$appId email=$email role=$role");

// Send email notification
$subject = "New Application — $role — $name";
$body  = "New application received on Vāk Media\n";
$body .= "=========================================\n\n";
$body .= "Name:         $name\n";
$body .= "Email:        $email\n";
$body .= "Phone:        $phone\n";
$body .= "Location:     $location\n";
$body .= "Role:         $role\n";
$body .= "Experience:   $experience\n";
$body .= "Availability: $availability\n";
$body .= "Portfolio:    $portfolio\n";
$body .= "Tools:        $tools\n";
$body .= "Niches:       $niches\n\n";
$body .= "Why Vāk:\n$why_vak\n\n";
$body .= "Other info:\n$other_info\n\n";
$body .= "=========================================\n";
$body .= "View in dashboard: https://vakmedia.in/dashboard.html\n";

$headers  = "From: " . NOTIFY_FROM . "\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

@mail(NOTIFY_EMAIL, $subject, $body, $headers);

jsonResponse(['ok' => true, 'id' => $appId], 201);
