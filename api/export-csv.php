<?php
/**
 * GET /api/export-csv.php
 * Downloads all applications as CSV. Requires auth.
 */

require_once __DIR__ . '/helpers.php';
setApiHeaders();
requireGet();
requireAuth();

$db = getDB();
$stmt = $db->query(
    "SELECT id, name, email, phone, location, role, experience, availability,
            portfolio, tools, niches, why_vak, other_info, status, submitted_at
     FROM applications ORDER BY submitted_at DESC"
);
$apps = $stmt->fetchAll();

auditLog('export_csv', 'count=' . count($apps) . ' by=' . ($_SESSION['admin_user'] ?? 'unknown'));

// Output CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="vak-applications-' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-cache, no-store, must-revalidate');

$out = fopen('php://output', 'w');
// BOM for Excel UTF-8 compatibility
fwrite($out, "\xEF\xBB\xBF");

// Header row
fputcsv($out, [
    'ID', 'Name', 'Email', 'Phone', 'Location', 'Role', 'Experience',
    'Availability', 'Portfolio', 'Tools', 'Niches', 'Why Vāk',
    'Other Info', 'Status', 'Submitted At'
]);

foreach ($apps as $a) {
    fputcsv($out, [
        $a['id'], $a['name'], $a['email'], $a['phone'], $a['location'],
        $a['role'], $a['experience'], $a['availability'], $a['portfolio'],
        $a['tools'], $a['niches'], $a['why_vak'], $a['other_info'],
        $a['status'], $a['submitted_at']
    ]);
}

fclose($out);
exit;
