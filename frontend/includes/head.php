<?php
// Include session configuration
require_once 'includes/session_config.php';

// Check if user is authenticated
requireAuth();

// Get current user data including unit, site, and department information
$currentUser = getCurrentUser();
$defaultUnitId = $currentUser['unit_id'] ?? null;
$defaultUnitName = $currentUser['unit_name'] ?? null;
$defaultSiteId = $currentUser['site_id'] ?? null;
$defaultSiteName = $currentUser['site_name'] ?? null;
$defaultDepartmentId = $currentUser['department_id'] ?? null;
$defaultDepartmentName = $currentUser['department_name'] ?? null;
$defaultRoleStatus = $currentUser['role_status'] ?? null;
?>
<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Language" content="en">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Links Of CSS File -->
    <link rel="stylesheet" href="assets/css/remixicon.css">
    <link rel="stylesheet" href="assets/css/apexcharts.css">
    <link rel="stylesheet" href="assets/css/simplebar.css">
    <link rel="stylesheet" href="assets/css/prism.css">
    <link rel="stylesheet" href="assets/css/jsvectormap.min.css">
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="assets/css/quill.snow.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/logo/icon.webp">

    <!-- Title -->
    <title>Khawaja Traders</title>

    <!-- Font Family -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Toastify JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Slim Select (fallback) -->
    <link href="https://cdn.jsdelivr.net/npm/slim-select@2.7.1/dist/slimselect.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectize/dist/css/selectize.default.css">

</head>