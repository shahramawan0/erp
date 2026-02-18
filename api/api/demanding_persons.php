<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/config/Session.php';
session_start();
$companyId = $_SESSION['company_id'] ?? 1;
$db = (new Database())->getConnection();

$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$parts = $path ? explode('/', $path) : [];
$id = null; foreach ($parts as $p) { if (ctype_digit($p)) { $id = (int)$p; break; } }
$method = $_SERVER['REQUEST_METHOD'];
$cols = "id, company_id, unit_id, first_name, last_name, name_in_urdu, cell, address, ptcl, is_deleted";

if ($method === 'GET' && $id) {
    $stmt = $db->prepare("SELECT $cols FROM demanding_persons WHERE id = ? AND company_id = ? AND is_deleted = 0");
    $stmt->execute([$id, $companyId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) { $row['name'] = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); }
    echo $row ? json_encode(['success' => true, 'data' => $row]) : json_encode(['success' => false, 'error' => 'Not found']);
    exit;
}

if ($method === 'GET') {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(100, max(1, (int)($_GET['limit'] ?? 10)));
    $search = trim($_GET['search'] ?? '');
    $unitId = $_GET['unit_id'] ?? null;
    $offset = ($page - 1) * $limit;
    $sql = "SELECT $cols FROM demanding_persons WHERE company_id = ? AND is_deleted = 0";
    $params = [$companyId];
    if ($unitId) { $sql .= " AND unit_id = ?"; $params[] = $unitId; }
    if ($search !== '') { $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR name_in_urdu LIKE ?)"; $params[] = '%'.$search.'%'; $params[] = '%'.$search.'%'; $params[] = '%'.$search.'%'; }
    $stmt = $db->prepare(preg_replace('/SELECT .+ FROM/', 'SELECT COUNT(*) FROM', $sql)); $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();
    $sql .= " ORDER BY first_name, last_name LIMIT ? OFFSET ?"; $params[] = $limit; $params[] = $offset;
    $stmt = $db->prepare($sql); $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($records as &$r) { $r['name'] = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')); }
    echo json_encode(['success' => true, 'data' => ['records' => $records, 'demandingPersons' => $records, 'total' => $total, 'page' => $page, 'limit' => $limit, 'total_pages' => (int)ceil($total / $limit)]]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $firstName = trim($input['first_name'] ?? ''); if ($firstName === '') { http_response_code(400); echo json_encode(['success' => false, 'error' => 'First name required']); exit; }
    $unitId = !empty($input['unit_id']) ? (int)$input['unit_id'] : null;
    $stmt = $db->prepare("INSERT INTO demanding_persons (company_id, unit_id, first_name, last_name, name_in_urdu, cell, address, ptcl, is_deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->execute([$companyId, $unitId, $firstName, trim($input['last_name'] ?? ''), trim($input['name_in_urdu'] ?? ''), trim($input['cell'] ?? ''), trim($input['address'] ?? ''), trim($input['ptcl'] ?? '')]);
    echo json_encode(['success' => true, 'data' => ['id' => (int)$db->lastInsertId()]]);
    exit;
}

if ($method === 'PUT' && $id) {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $firstName = trim($input['first_name'] ?? ''); if ($firstName === '') { http_response_code(400); echo json_encode(['success' => false, 'error' => 'First name required']); exit; }
    $unitId = array_key_exists('unit_id', $input) ? ($input['unit_id'] ? (int)$input['unit_id'] : null) : null;
    $stmt = $db->prepare("UPDATE demanding_persons SET unit_id = ?, first_name = ?, last_name = ?, name_in_urdu = ?, cell = ?, address = ?, ptcl = ? WHERE id = ? AND company_id = ? AND is_deleted = 0");
    $stmt->execute([$unitId, $firstName, trim($input['last_name'] ?? ''), trim($input['name_in_urdu'] ?? ''), trim($input['cell'] ?? ''), trim($input['address'] ?? ''), trim($input['ptcl'] ?? ''), $id, $companyId]);
    echo $stmt->rowCount() ? json_encode(['success' => true, 'data' => ['id' => $id]]) : json_encode(['success' => false, 'error' => 'Not found']);
    exit;
}

if ($method === 'DELETE' && $id) {
    $stmt = $db->prepare("UPDATE demanding_persons SET is_deleted = 1 WHERE id = ? AND company_id = ?");
    $stmt->execute([$id, $companyId]);
    echo $stmt->rowCount() ? json_encode(['success' => true]) : json_encode(['success' => false, 'error' => 'Not found']);
    exit;
}
http_response_code(405); echo json_encode(['success' => false, 'error' => 'Method not allowed']);
