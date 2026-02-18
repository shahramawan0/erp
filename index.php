<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$pathParts = array_values(array_filter(explode('/', trim($path, '/'))));

if (isset($pathParts[0]) && $pathParts[0] === 'khawaja_traders') array_shift($pathParts);

if (empty($pathParts)) {
    header('Location: frontend/sign-in.php');
    exit;
}

$apiEndpoints = [
    'auth' => 'api/api/auth.php',
    'session' => 'api/api/session.php',
    'units' => 'api/api/units.php',
    'sites' => 'api/api/sites.php',
    'departments' => 'api/api/departments.php',
    'sections' => 'api/api/sections.php',
    'sub-sections' => 'api/api/sub_sections.php',
    'demanding-persons' => 'api/api/demanding_persons.php',
    'suppliers' => 'api/api/suppliers.php',
    'items' => 'api/api/items.php',
    'main-heads' => 'api/api/main-heads.php',
    'control-heads' => 'api/api/control-heads.php',
    'unit-types' => 'api/api/unit-types.php',
    'item-types' => 'api/api/item_types.php',
    'racks' => 'api/api/racks.php',
    'unit-racks' => 'api/api/unit-racks.php',
    'demand-types' => 'api/api/demand_types.php',
    'cities' => 'api/api/cities.php',
    'production-quality' => 'api/api/production_quality.php',
    'sizes' => 'api/api/sizes.php',
    'categories' => 'api/api/categories.php',
    'sub-categories' => 'api/api/sub_categories.php',
    'banks' => 'api/api/banks.php',
    'company-types' => 'api/api/company_types.php',
    'payment-terms' => 'api/api/payment_terms.php',
    'brands' => 'api/api/brands.php',
    'shifts' => 'api/api/shifts.php',
    'store-opening-stock' => 'api/api/store_opening_stock.php',
];

$endpoint = $pathParts[0] ?? '';
if ($endpoint === 'api' && isset($pathParts[1])) {
    $endpoint = $pathParts[1];
    array_shift($pathParts);
}

if (!isset($apiEndpoints[$endpoint])) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Endpoint not found', 'endpoints' => array_keys($apiEndpoints)]);
    exit;
}

$apiFile = $apiEndpoints[$endpoint];
if (!file_exists($apiFile)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'API file not found']);
    exit;
}

$_SERVER['REQUEST_URI'] = '/' . implode('/', $pathParts);
require_once $apiFile;
?>
