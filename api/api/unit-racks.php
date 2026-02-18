<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/config/Session.php';

session_start();
$unitId = $_GET['unit_id'] ?? null;
if (!$unitId) {
    echo json_encode(['success' => true, 'data' => [], 'records' => []]);
    exit;
}
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT r.id, r.name, r.unit_id FROM racks r WHERE r.unit_id = ? AND r.is_deleted = 0 ORDER BY r.name");
$stmt->execute([$unitId]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['success' => true, 'data' => $records, 'records' => $records]);
?>
