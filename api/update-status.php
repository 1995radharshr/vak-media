<?php
/**
 * POST /api/update-status.php
 * Body: { "id": 123, "status": "reviewed" }
 * Requires auth.
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();
requirePost();
requireAuth();

$input = json_decode(file_get_contents('php://input'), true);
$id     = intval($input['id'] ?? 0);
$status = $input['status'] ?? '';

if ($id <= 0) {
    jsonResponse(['error' => 'Valid application ID required'], 400);
}

$allowedStatuses = ['new', 'reviewed', 'shortlisted', 'rejected'];
if (!in_array($status, $allowedStatuses)) {
    jsonResponse(['error' => 'Invalid status. Allowed: ' . implode(', ', $allowedStatuses)], 400);
}

$db = getDB();
$stmt = $db->prepare("UPDATE applications SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

if ($stmt->rowCount() === 0) {
    jsonResponse(['error' => 'Application not found'], 404);
}

auditLog('status_change', "id=$id status=$status by=" . ($_SESSION['admin_user'] ?? 'unknown'));
jsonResponse(['ok' => true]);
