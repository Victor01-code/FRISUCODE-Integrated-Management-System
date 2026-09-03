<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'admin']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../mail/mailer.php'; // Required for sendSystemEmail if we want to send, but bulk import might skip emails to avoid spam.

header('Content-Type: application/json');

// Get raw POST data
$jsonData = file_get_contents('php://input');
$records = json_decode($jsonData, true);

if (!is_array($records) || empty($records)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or empty data received.']);
    exit;
}

$count = 0;
$errors = [];

// Mapping array keys to standardize variations from Excel headers
function getFieldValue($record, $possibleKeys, $default = '') {
    foreach ($possibleKeys as $key) {
        foreach ($record as $k => $v) {
            if (strtolower(trim($k)) === strtolower(trim($key))) {
                return trim($v) !== '' ? trim($v) : $default;
            }
        }
    }
    return $default;
}

$defaultPassword = password_hash("Welcome123!", PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES (:full_name, :email, :password, :role, :status)");
$sponsorStmt = $pdo->prepare("INSERT INTO sponsors (user_id, organization_name, sponsor_type) VALUES (?, ?, 'individual')");
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");

$seenEmails = [];

foreach ($records as $index => $row) {
    $fullName = getFieldValue($row, ['full_name', 'fullname', 'name', 'personnel name', 'Name', 'Full Name']);
    $email = getFieldValue($row, ['email', 'electronic mail', 'Email Address', 'Email']);
    
    // Skip empty rows
    if (empty($fullName) || empty($email)) {
        continue;
    }

    if (in_array(strtolower($email), $seenEmails)) {
        $errors[] = "Row " . ($index + 1) . " ($email): Duplicate email in the uploaded file.";
        continue;
    }
    $seenEmails[] = strtolower($email);

    // Check if email already exists in DB
    $checkStmt->execute([$email]);
    if ($checkStmt->fetchColumn() > 0) {
        $errors[] = "Row " . ($index + 1) . " ($email): Email already exists in the system.";
        continue;
    }

    $role = strtolower(getFieldValue($row, ['role', 'access privilege', 'Role', 'Role Assignment'], 'staff'));
    
    // Validate role
    $validRoles = ['staff', 'project_manager', 'finance', 'director', 'super_admin', 'donor', 'admin'];
    if (!in_array($role, $validRoles)) {
        $role = 'staff';
    }

    $status = strtolower(getFieldValue($row, ['status', 'Status', 'Account Status'], 'active'));
    if (!in_array($status, ['active', 'inactive'])) {
        $status = 'active';
    }

    try {
        $pdo->beginTransaction();
        
        $stmt->execute([
            ':full_name' => $fullName,
            ':email' => $email,
            ':password' => $defaultPassword,
            ':role' => $role,
            ':status' => $status
        ]);
        
        $newUserId = $pdo->lastInsertId();

        if ($role === 'donor') {
            $sponsorStmt->execute([$newUserId, $fullName]);
        }
        
        $pdo->commit();
        $count++;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // If it's a duplicate entry, $e->getCode() might be 23000
        $errors[] = "Row " . ($index + 1) . " ($email): " . $e->getMessage();
    }
}

if ($count > 0) {
    echo json_encode([
        'success' => true,
        'count' => $count,
        'errors' => $errors
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No valid records were imported. Please check your file headers and data format.',
        'errors' => $errors
    ]);
}
