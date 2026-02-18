<?php
/**
 * Change current unit for the logged-in user (SUA).
 * Expects POST JSON: { "unit_id": <int> }
 * Returns JSON: { "success": true } or { "success": false, "error": "..." }
 */
header('Content-Type: application/json');

require_once __DIR__ . '/includes/session_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isAuthenticated()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$companyId = (int)($_SESSION['company_id'] ?? 0);
if ($companyId < 1) {
    echo json_encode(['success' => false, 'error' => 'Invalid session']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$unitId = isset($input['unit_id']) ? (int)$input['unit_id'] : 0;
if ($unitId < 1) {
    echo json_encode(['success' => false, 'error' => 'Invalid unit_id']);
    exit;
}

require_once __DIR__ . '/config.php';
try {
    $db = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser, $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
    exit;
}

$stmt = $db->prepare("SELECT id, name, name_in_urdu, short_name FROM units WHERE id = ? AND company_id = ? AND is_deleted = 0");
$stmt->execute([$unitId, $companyId]);
$unit = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$unit) {
    echo json_encode(['success' => false, 'error' => 'Unit not found or access denied']);
    exit;
}

$_SESSION['unit_id'] = (int)$unit['id'];
$_SESSION['unit_name'] = $unit['name'];
$_SESSION['unit_name_urdu'] = $unit['name_in_urdu'] ?? '';
$_SESSION['unit_short_name'] = $unit['short_name'] ?? '';

echo json_encode(['success' => true]);
