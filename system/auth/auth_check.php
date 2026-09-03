<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Language logic
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'sw', 'fr', 'de', 'es'])) {
    $_SESSION['lang'] = $_GET['lang'];
    // redirect to same page without lang param to avoid stuck urls (optional, but let's just let it be)
    $url = preg_replace('/([?&])lang=[^&]+(&|$)/', '$1', $_SERVER['REQUEST_URI']);
    $url = rtrim($url, '?&');
    header("Location: $url");
    exit;
}

$lang = $_SESSION['lang'] ?? 'en';
$lang_file = __DIR__ . '/../lang/' . $lang . '.php';

if (file_exists($lang_file)) {
    $GLOBALS['SYS_LANG'] = require $lang_file;
} else {
    $GLOBALS['SYS_LANG'] = [];
}

if (!function_exists('__')) {
    function __($key, $default = null) {
        if ($default === null) $default = $key;
        return $GLOBALS['SYS_LANG'][$key] ?? $default;
    }
}


if (!isset($_SESSION['user_id'])) {
    header("Location: /frisucode_ms/system/auth/login.php");
    exit;
}

// Ensure role is never empty
if (empty($_SESSION['role'])) {
    $_SESSION['role'] = 'staff';
}

function getSysDashboardUrl($role) {
    if (empty($role)) return '/frisucode_ms/system/dashboards/staff.php';
    switch (strtolower(trim($role))) {
        case 'super_admin':
        case 'admin':
        case 'me_officer':
            return '/frisucode_ms/system/dashboards/super_admin.php';
        case 'project_manager':
            return '/frisucode_ms/system/dashboards/project_manager.php';
        case 'director':
            return '/frisucode_ms/system/dashboards/super_admin.php';
        case 'finance':
            return '/frisucode_ms/system/dashboards/finance.php';
        case 'donor':
            return '/frisucode_ms/system/dashboards/donor.php';
        case 'staff':
        case 'field_officer':
        default:
            return '/frisucode_ms/system/dashboards/staff.php';
    }
}

function requireRole($role) {
    if (!isset($_SESSION['role'])) {
        header("Location: /frisucode_ms/system/auth/login.php?error=unauthorized");
        exit;
    }

    $sessRole = strtolower(trim($_SESSION['role']));
    $allowed = false;

    if (is_array($role)) {
        $allowed = in_array($sessRole, array_map('strtolower', $role));
    } else {
        $allowed = ($sessRole === strtolower(trim($role)));
    }

    if (!$allowed) {
        $dest = getSysDashboardUrl($sessRole);
        header("Location: " . $dest);
        exit;
    }
}

/**
 * Centrally manages which sidebar to display based on the session role
 */
function renderSidebar() {
    $role = $_SESSION['role'] ?? 'staff';
    $partialsPath = __DIR__ . '/../partials/';

    switch (strtolower(trim($role))) {
        case 'super_admin':
        case 'admin':
        case 'director':
            include $partialsPath . 'admin_sidebar.php';
            break;
        case 'project_manager':
            include $partialsPath . 'pm_sidebar.php';
            break;
        case 'finance':
            include $partialsPath . 'finance_sidebar.php';
            break;
        case 'donor':
            include $partialsPath . 'donor_sidebar.php';
            break;
        case 'staff':
        case 'field_officer':
        default:
            include $partialsPath . 'staff_sidebar.php';
            break;
    }
}
