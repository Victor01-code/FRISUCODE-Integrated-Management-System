<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'admin', 'director']);
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    // Delete sponsor assignments first
    $pdo->prepare("DELETE FROM beneficiary_sponsors WHERE beneficiary_id = ?")->execute([$id]);
    // Delete the beneficiary
    $pdo->prepare("DELETE FROM beneficiaries WHERE id = ?")->execute([$id]);
    
    header('Location: index.php?deleted=1');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?error=delete_failed');
    exit;
}
