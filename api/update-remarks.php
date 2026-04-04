<?php
/**
 * POST /api/update-remarks.php
 * Body: { "id": 123, "remarks": "Some notes about this applicant" }
 * Requires auth.
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();
requirePost();
requireAuth();

$input = json_decode(file_get_contents('php://input'), true);
$id      = intval($input['id'] ?? 0);
$remarks = sanitize($input['remarks'] ?? '', 5000);

if ($id <= 0) {
    jsonResponse(['error' => 'Valid application ID required'], 400);
}

$db = getDB();
$stmt = $db->prepare("UPDATE applications SET remarks = ? WHERE id = ?");
$stmt->execute([$remarks, $id]);

if ($stmt->rowCount() === 0) {
    // Row exists but value unchanged, or row not found — check existence
    $check = $db->prepare("SELECT id FROM applications WHERE id = ?");
    $check->execute([$id]);
    if (!$check->fetch()) {
        jsonResponse(['error' => 'Application not found'], 404);
    }
}

auditLog('remarks_update', "id=$id by=" . ($_SESSION['admin_user'] ?? 'unknown'));
jsonResponse(['ok' => true]);
