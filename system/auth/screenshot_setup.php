<?php
require_once 'c:/xampp/htdocs/frisucode_ms/system/config/db.php';
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->execute(['admin@frisucode.org']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['lang'] = 'en';
    header("Location: /frisucode_ms/system/dashboards/super_admin.php");
    exit;
} else {
    echo "Admin user not found.";
}
