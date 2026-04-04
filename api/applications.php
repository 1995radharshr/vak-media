<?php
/**
 * GET /api/applications.php
 * Returns all applications as JSON. Requires auth.
 * Optional query params: ?status=new&sort=submitted_at&dir=desc
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();
requireGet();
requireAuth();

$db = getDB();

// Build query with optional status filter
$where = '';
$params = [];
$status = $_GET['status'] ?? '';
if (in_array($status, ['new', 'reviewed', 'shortlisted', 'rejected'])) {
    $where = 'WHERE status = ?';
    $params[] = $status;
}

// Sorting
$allowedSort = ['submitted_at', 'name', 'role', 'status'];
$sort = in_array($_GET['sort'] ?? '', $allowedSort) ? $_GET['sort'] : 'submitted_at';
$dir = (strtolower($_GET['dir'] ?? '') === 'asc') ? 'ASC' : 'DESC';

$sql = "SELECT id, name, email, phone, location, role, experience, availability,
               portfolio, tools, niches, why_vak, other_info, status,
               submitted_at, updated_at
        FROM applications $where ORDER BY $sort $dir";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$apps = $stmt->fetchAll();

jsonResponse(['ok' => true, 'applications' => $apps, 'count' => count($apps)]);
