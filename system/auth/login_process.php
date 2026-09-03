<?php
session_start();
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header("Location: login.php?error=invalid");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php?error=invalid");
    exit;
}

if (!password_verify($password, $user['password'])) {
    header("Location: login.php?error=invalid");
    exit;
}

/* ✅ SET SESSION */
$_SESSION['auth'] = true;
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = strtolower(trim($user['role']));
$_SESSION['user_name'] = $user['full_name'] ?? $user['name'] ?? 'User';
$_SESSION['lang'] = $user['preferred_language'] ?? 'en';

// If role is empty, default to staff
if (empty($_SESSION['role'])) {
    $_SESSION['role'] = 'staff';
}

/* ✅ ABSOLUTE REDIRECT BASED ON ROLE */
switch ($_SESSION['role']) {
    case 'super_admin':
    case 'admin':
    case 'me_officer':
        header("Location: /frisucode_ms/system/dashboards/super_admin.php");
        break;
    case 'director':
        header("Location: /frisucode_ms/system/dashboards/super_admin.php");
        break;
    case 'finance':
        header("Location: /frisucode_ms/system/dashboards/finance.php");
        break;
    case 'project_manager':
        header("Location: /frisucode_ms/system/dashboards/project_manager.php");
        break;
    case 'staff':
    case 'field_officer':
        header("Location: /frisucode_ms/system/dashboards/staff.php");
        break;
    case 'donor':
        header("Location: /frisucode_ms/system/dashboards/donor.php");
        break;
    default:
        // Fallback: staff dashboard for unknown roles
        $_SESSION['role'] = 'staff';
        header("Location: /frisucode_ms/system/dashboards/staff.php"); 
        break;
}
exit;
