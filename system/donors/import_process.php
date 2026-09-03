<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'finance', 'admin']);
require_once __DIR__ . '/../config/db.php';

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

$defaultPassword = password_hash("Donor@123", PASSWORD_DEFAULT);

$stmtUser = $pdo->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, 'donor', 'active')");
$stmtSponsor = $pdo->prepare("INSERT INTO sponsors (user_id, organization_name, phone, sponsor_type) VALUES (?, ?, ?, ?)");
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");

$seenEmails = [];

foreach ($records as $index => $row) {
    $fullName = getFieldValue($row, ['contact person name', 'full_name', 'fullname', 'name', 'Name', 'Full Name']);
    $email = getFieldValue($row, ['email address', 'email', 'electronic mail', 'Email Address', 'Email']);
    
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

    $phone = getFieldValue($row, ['phone', 'phone number', 'contact', 'Phone'], '');
    $orgName = getFieldValue($row, ['organization name', 'organization_name', 'organization', 'company', 'Org Name'], '');
    $type = strtolower(getFieldValue($row, ['donor type', 'sponsor_type', 'type', 'partnership type'], 'individual'));

    if (!in_array($type, ['individual', 'organization', 'government'])) {
        $type = 'individual';
    }

    try {
        $pdo->beginTransaction();
        
        $stmtUser->execute([$fullName, $email, $defaultPassword]);
        $userId = $pdo->lastInsertId();

        $stmtSponsor->execute([$userId, $orgName, $phone, $type]);
        
        $pdo->commit();
        $count++;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
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
