<?php
/**
 * POST /api/delete-application.php
 * Body: { "id": 123 }
 * Requires auth.
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();
requirePost();
requireAuth();

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(['error' => 'Valid application ID required'], 400);
}

$db = getDB();

// Fetch before deleting for audit log
$stmt = $db->prepare("SELECT name, email, role FROM applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();

if (!$app) {
    jsonResponse(['error' => 'Application not found'], 404);
}

$db->prepare("DELETE FROM applications WHERE id = ?")->execute([$id]);

auditLog('delete', "id=$id name={$app['name']} email={$app['email']} by=" . ($_SESSION['admin_user'] ?? 'unknown'));
jsonResponse(['ok' => true]);
