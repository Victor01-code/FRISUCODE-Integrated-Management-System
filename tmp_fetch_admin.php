<?php
require_once 'c:/xampp/htdocs/frisucode_ms/system/config/db.php';
$stmt = $pdo->query("SELECT email, password FROM users WHERE role='super_admin' LIMIT 1");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Email: " . $user['email'] . "\n";
echo "Hashed Password: " . $user['password'] . "\n";
