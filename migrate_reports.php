<?php
/**
 * Migration: Create student_reports table
 * Run this file once to create the table.
 */
require_once __DIR__ . '/system/config/db.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `student_reports` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `beneficiary_id` int(11) NOT NULL,
            `title` varchar(255) NOT NULL,
            `report_text` text DEFAULT NULL,
            `file_url` varchar(500) DEFAULT NULL,
            `file_name` varchar(255) DEFAULT NULL,
            `report_date` date NOT NULL,
            `created_by` int(11) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `beneficiary_id` (`beneficiary_id`),
            KEY `created_by` (`created_by`),
            CONSTRAINT `student_reports_ibfk_1` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`) ON DELETE CASCADE,
            CONSTRAINT `student_reports_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "✅ student_reports table created successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
