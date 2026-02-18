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
$cols = "id, company_id, name, name_in_urdu, is_deleted";

if ($method === 'GET' && $id) {
    $stmt = $db->prepare("SELECT $cols FROM brands WHERE id = ? AND company_id = ? AND is_deleted = 0");
    $stmt->execute([$id, $companyId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row ? json_encode(['success' => true, 'data' => $row]) : json_encode(['success' => false, 'error' => 'Not found']);
    exit;
}

if ($method === 'GET') {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(100, max(1, (int)($_GET['limit'] ?? 10)));
    $search = trim($_GET['search'] ?? '');
    $offset = ($page - 1) * $limit;
    $sql = "SELECT $cols FROM brands WHERE company_id = ? AND is_deleted = 0";
    $params = [$companyId];
    if ($search !== '') { $sql .= " AND (name LIKE ? OR name_in_urdu LIKE ?)"; $params[] = '%'.$search.'%'; $params[] = '%'.$search.'%'; }
    $stmt = $db->prepare(preg_replace('/SELECT .+ FROM/', 'SELECT COUNT(*) FROM', $sql)); $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();
    $limit = (int)$limit;
    $offset = (int)$offset;
    $sql .= " ORDER BY name LIMIT $limit OFFSET $offset";
    $stmt = $db->prepare($sql); $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => ['records' => $records, 'total' => $total, 'page' => $page, 'limit' => $limit, 'total_pages' => (int)ceil($total / $limit)]]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $name = trim($input['name'] ?? ''); if ($name === '') { http_response_code(400); echo json_encode(['success' => false, 'error' => 'Name required']); exit; }
    $stmt = $db->prepare("INSERT INTO brands (company_id, name, name_in_urdu, is_deleted) VALUES (?, ?, ?, 0)");
    $stmt->execute([$companyId, $name, trim($input['name_in_urdu'] ?? '')]);
    echo json_encode(['success' => true, 'data' => ['id' => (int)$db->lastInsertId(), 'name' => $name]]);
    exit;
}

if ($method === 'PUT' && $id) {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $name = trim($input['name'] ?? ''); if ($name === '') { http_response_code(400); echo json_encode(['success' => false, 'error' => 'Name required']); exit; }
    $stmt = $db->prepare("UPDATE brands SET name = ?, name_in_urdu = ? WHERE id = ? AND company_id = ? AND is_deleted = 0");
    $stmt->execute([$name, trim($input['name_in_urdu'] ?? ''), $id, $companyId]);
    echo $stmt->rowCount() ? json_encode(['success' => true, 'data' => ['id' => $id]]) : json_encode(['success' => false, 'error' => 'Not found']);
    exit;
}

if ($method === 'DELETE' && $id) {
    $stmt = $db->prepare("UPDATE brands SET is_deleted = 1 WHERE id = ? AND company_id = ?");
    $stmt->execute([$id, $companyId]);
    echo $stmt->rowCount() ? json_encode(['success' => true]) : json_encode(['success' => false, 'error' => 'Not found']);
    exit;
}
http_response_code(405); echo json_encode(['success' => false, 'error' => 'Method not allowed']);
