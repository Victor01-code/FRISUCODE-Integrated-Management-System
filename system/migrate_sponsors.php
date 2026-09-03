<?php
require_once __DIR__ . '/config/db.php';

try {
    // 1. Create the new junction table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS beneficiary_sponsors (
            beneficiary_id INT NOT NULL,
            sponsor_id INT NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (beneficiary_id, sponsor_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Table beneficiary_sponsors created or already exists.\n";

    // 2. Migrate existing data from beneficiaries.sponsor_id
    // First check if sponsor_id column still exists
    $stmt = $pdo->query("SHOW COLUMNS FROM beneficiaries LIKE 'sponsor_id'");
    if ($stmt->rowCount() > 0) {
        $migrated = 0;
        
        // Fetch all beneficiaries that have a sponsor_id
        $beneficiaries = $pdo->query("SELECT id, sponsor_id FROM beneficiaries WHERE sponsor_id IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
        
        $insertStmt = $pdo->prepare("INSERT IGNORE INTO beneficiary_sponsors (beneficiary_id, sponsor_id) VALUES (?, ?)");
        
        foreach ($beneficiaries as $b) {
            $insertStmt->execute([$b['id'], $b['sponsor_id']]);
            $migrated++;
        }
        
        echo "Successfully migrated $migrated sponsor links to the new table.\n";
        
        // We will keep the old sponsor_id column for now to prevent breaking existing code during the transition,
        // but it will no longer be the source of truth once we update the files.
    } else {
        echo "The sponsor_id column does not exist in beneficiaries table.\n";
    }

} catch (PDOException $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
}
