<?php
require "c:/xampp/htdocs/frisucode_ms/system/config/db.php";

$stats = [];

// 1. Students Supported
$stats['students'] = $pdo->query("SELECT COUNT(*) FROM beneficiaries")->fetchColumn();

// 2. Retention Rate (Active + Graduated) / Total
$active = $pdo->query("SELECT COUNT(*) FROM beneficiaries WHERE status='active'")->fetchColumn();
$graduated = $pdo->query("SELECT COUNT(*) FROM beneficiaries WHERE status='graduated'")->fetchColumn();
$total = $stats['students'] ?: 1;
$stats['retention'] = round((($active + $graduated) / $total) * 100);

// 3. Partner Schools
$stats['schools'] = $pdo->query("SELECT COUNT(DISTINCT school_name) FROM beneficiaries WHERE school_name IS NOT NULL AND school_name != ''")->fetchColumn();

// 4. Families Empowered
// If there's no families table, let's look at public_donations or maybe multiplier
$stats['families'] = $pdo->query("SELECT COUNT(*) FROM public_donations WHERE status='completed'")->fetchColumn();

header('Content-Type: application/json');
echo json_encode($stats);
