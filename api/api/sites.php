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

$path = $_SERVER['REQUEST_URI'] ?? '';
$path = trim(parse_url($path, PHP_URL_PATH), '/');
$parts = $path ? explode('/', $path) : [];
// path can be "sites" or "sites/1" - id is the numeric part
$id = null;
foreach ($parts as $p) {
    if (ctype_digit($p)) { $id = (int)$p; break; }
}

$method = $_SERVER['REQUEST_METHOD'];

// GET single
if ($method === 'GET' && $id) {
    $stmt = $db->prepare("SELECT id, company_id, name, is_deleted FROM sites WHERE id = ? AND company_id = ? AND is_deleted = 0");
    $stmt->execute([$id, $companyId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Site not found']);
    }
    exit;
}

// GET list (paginated)
if ($method === 'GET') {
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(100, max(1, (int)($_GET['limit'] ?? 10)));
    $search = trim($_GET['search'] ?? '');
    $offset = ($page - 1) * $limit;

    $sql = "SELECT id, company_id, name, is_deleted FROM sites WHERE company_id = ? AND is_deleted = 0";
    $params = [$companyId];
    if ($search !== '') {
        $sql .= " AND (name LIKE ?)";
        $params[] = '%' . $search . '%';
    }
    $countSql = preg_replace('/SELECT .+ FROM/', 'SELECT COUNT(*) FROM', $sql);
    $stmt = $db->prepare($countSql);
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    $sql .= " ORDER BY name LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'records' => $records,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => (int)ceil($total / $limit),
        ]
    ]);
    exit;
}

// POST create
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $name = trim($input['name'] ?? '');
    if ($name === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name is required']);
        exit;
    }
    $stmt = $db->prepare("INSERT INTO sites (company_id, name, is_deleted) VALUES (?, ?, 0)");
    $stmt->execute([$companyId, $name]);
    $newId = (int)$db->lastInsertId();
    echo json_encode(['success' => true, 'data' => ['id' => $newId, 'name' => $name]]);
    exit;
}

// PUT update
if ($method === 'PUT' && $id) {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $name = trim($input['name'] ?? '');
    if ($name === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name is required']);
        exit;
    }
    $stmt = $db->prepare("UPDATE sites SET name = ? WHERE id = ? AND company_id = ? AND is_deleted = 0");
    $stmt->execute([$name, $id, $companyId]);
    if ($stmt->rowCount()) {
        echo json_encode(['success' => true, 'data' => ['id' => $id, 'name' => $name]]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Site not found']);
    }
    exit;
}

// DELETE (soft)
if ($method === 'DELETE' && $id) {
    $stmt = $db->prepare("UPDATE sites SET is_deleted = 1 WHERE id = ? AND company_id = ?");
    $stmt->execute([$id, $companyId]);
    if ($stmt->rowCount()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Site not found']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
?>
