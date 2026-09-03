<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director']);
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
    
    header('Location: index.php?deleted=1');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?error=delete_failed');
    exit;
}
