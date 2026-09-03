<?php
require "c:/xampp/htdocs/frisucode_ms/system/config/db.php";

$sql = "CREATE TABLE IF NOT EXISTS `system_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `students_base` int(11) DEFAULT 1200,
  `retention_base` int(11) DEFAULT 95,
  `schools_base` int(11) DEFAULT 24,
  `families_base` int(11) DEFAULT 400,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

try {
    $pdo->exec($sql);
    
    // Check if empty
    $count = $pdo->query("SELECT COUNT(*) FROM system_stats")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO `system_stats` (`students_base`, `retention_base`, `schools_base`, `families_base`) VALUES (1200, 95, 24, 400)");
    }
    echo "Migration successful";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage();
}
