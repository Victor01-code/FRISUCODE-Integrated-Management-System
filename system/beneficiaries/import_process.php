<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'admin', 'staff', 'director', 'project_manager']);
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

// Prepare the insert statement
$stmt = $pdo->prepare("INSERT INTO beneficiaries (
    full_name, student_id, dob, gender, education_level, class_level, school_name, 
    location_name, google_map_link, guardian_name, guardian_phone, guardian_relation, status, bio,
    dropout_reason, dropout_date, dropout_recorded_by, graduation_date, graduation_notes, registered_at
) VALUES (
    :full_name, :student_id, :dob, :gender, :education_level, :class_level, :school_name, 
    :location_name, :google_map_link, :guardian_name, :guardian_phone, :guardian_relation, :status, :bio,
    :dropout_reason, :dropout_date, :dropout_recorded_by, :graduation_date, :graduation_notes, NOW()
)");

// Mapping array keys to standardize variations from Excel headers
function getFieldValue($record, $possibleKeys, $default = '') {
    foreach ($possibleKeys as $key) {
        // Check exact match or case-insensitive match
        foreach ($record as $k => $v) {
            if (strtolower(trim($k)) === strtolower(trim($key))) {
                return trim($v) !== '' ? trim($v) : $default;
            }
        }
    }
    return $default;
}

$currentYear = date('Y');
$nextSeq = 1;
try {
    $lastIdStmt = $pdo->prepare("SELECT student_id FROM beneficiaries WHERE student_id LIKE ? ORDER BY id DESC LIMIT 1");
    $lastIdStmt->execute(["FSC-01-%-$currentYear"]);
    $lastId = $lastIdStmt->fetchColumn();
    if ($lastId) {
        $parts = explode('-', $lastId);
        if (isset($parts[2])) {
            $nextSeq = (int)$parts[2] + 1;
        }
    }
} catch (\PDOException $e) {}

$checkIdStmt = $pdo->prepare("SELECT COUNT(*) FROM beneficiaries WHERE student_id = ?");
$checkNameStmt = $pdo->prepare("SELECT COUNT(*) FROM beneficiaries WHERE LOWER(full_name) = ?");

$seenNames = [];
$seenIds = [];

foreach ($records as $index => $row) {
    // Extract data using flexible key matching
    $fullName = getFieldValue($row, ['full_name', 'fullname', 'name', 'student_name', 'Student Name', 'Full Name']);
    
    // Skip empty rows (must have at least a name)
    if (empty($fullName)) {
        continue;
    }

    $studentId = getFieldValue($row, ['student_id', 'student id', 'student_no', 'student no', 'Student ID', 'Student Number'], '');
    if (empty($studentId)) {
        $studentId = sprintf("FSC-01-%04d-%s", $nextSeq, $currentYear);
        $nextSeq++;
    }

    if (in_array(strtolower($fullName), $seenNames)) {
        $errors[] = "Row " . ($index + 1) . " ($fullName): Duplicate student name in the uploaded file.";
        continue;
    }
    if (!empty($studentId) && in_array(strtolower($studentId), $seenIds)) {
        $errors[] = "Row " . ($index + 1) . " ($fullName): Duplicate student ID ($studentId) in the uploaded file.";
        continue;
    }

    $seenNames[] = strtolower($fullName);
    if (!empty($studentId)) {
        $seenIds[] = strtolower($studentId);
    }

    // Check DB
    if (!empty($studentId)) {
        $checkIdStmt->execute([$studentId]);
        if ($checkIdStmt->fetchColumn() > 0) {
            $errors[] = "Row " . ($index + 1) . " ($fullName): Student ID ($studentId) already exists in the system.";
            continue;
        }
    }

    $checkNameStmt->execute([strtolower($fullName)]);
    if ($checkNameStmt->fetchColumn() > 0) {
        $errors[] = "Row " . ($index + 1) . " ($fullName): A student with this name is already registered.";
        continue;
    }

    $dob = getFieldValue($row, ['dob', 'date_of_birth', 'Date of Birth'], null);
    // basic date validation/formatting could go here, fallback to null if empty
    if (empty($dob)) $dob = null; 

    $gender = getFieldValue($row, ['gender', 'Gender', 'sex'], 'Other');
    $educationLevel = getFieldValue($row, ['education_level', 'Education Level', 'level'], 'Primary');
    $classLevel = getFieldValue($row, ['class_level', 'Class Level', 'class', 'grade', 'Grade'], '');
    $schoolName = getFieldValue($row, ['school_name', 'School Name', 'school', 'School'], '');
    
    $locationName = getFieldValue($row, ['location_name', 'Location Name', 'location', 'Location', 'address'], '');
    $googleMapLink = getFieldValue($row, ['google_map_link', 'map_link', 'google map link', 'Google Maps Link'], '');
    
    $guardianName = getFieldValue($row, ['guardian_name', 'Guardian Name', 'guardian', 'parent'], '');
    $guardianPhone = getFieldValue($row, ['guardian_phone', 'Guardian Phone', 'phone', 'contact'], '');
    $guardianRelation = getFieldValue($row, ['guardian_relation', 'Guardian Relation', 'relation', 'relationship'], '');
    
    $status = strtolower(trim(getFieldValue($row, ['status', 'Status', 'current_status'], 'active')));
    if (!in_array($status, ['active', 'graduated', 'dropped_out'])) {
        $errors[] = "Row " . ($index + 1) . " ($fullName): Invalid status '$status'. Allowed values: active, graduated, dropped_out.";
        continue;
    }
    
    $bio = getFieldValue($row, ['bio', 'biography', 'notes', 'description'], '');

    // Dropout & Graduation validation
    $dropoutReason = getFieldValue($row, ['dropout_reason', 'Dropout Reason', 'removal_reason'], null);
    $dropoutDate = getFieldValue($row, ['dropout_date', 'Dropout Date', 'date_of_removal'], null);
    $dropoutRecordedBy = getFieldValue($row, ['dropout_recorded_by', 'Dropout Recorded By'], null);
    $graduationDate = getFieldValue($row, ['graduation_date', 'Graduation Date', 'date_of_graduation'], null);
    $graduationNotes = getFieldValue($row, ['graduation_notes', 'Graduation Notes', 'profession_practiced', 'Profession Practiced'], null);

    if ($status === 'dropped_out') {
        if (empty($dropoutReason)) {
            $errors[] = "Row " . ($index + 1) . " ($fullName): Dropout reason is required for status 'dropped_out'.";
            continue;
        }
        if (empty($dropoutDate)) {
            $dropoutDate = date('Y-m-d');
        }
        if (empty($dropoutRecordedBy)) {
            $dropoutRecordedBy = $_SESSION['user_id'] ?? null;
        }
    } else {
        $dropoutReason = null;
        $dropoutDate = null;
        $dropoutRecordedBy = null;
    }

    if ($status === 'graduated') {
        if (empty($graduationDate)) {
            $graduationDate = date('Y-m-d');
        }
    } else {
        $graduationDate = null;
        $graduationNotes = null;
    }

    try {
        $stmt->execute([
            ':full_name' => $fullName,
            ':student_id' => $studentId,
            ':dob' => $dob,
            ':gender' => $gender,
            ':education_level' => $educationLevel,
            ':class_level' => $classLevel,
            ':school_name' => $schoolName,
            ':location_name' => $locationName,
            ':google_map_link' => $googleMapLink,
            ':guardian_name' => $guardianName,
            ':guardian_phone' => $guardianPhone,
            ':guardian_relation' => $guardianRelation,
            ':status' => $status,
            ':bio' => $bio,
            ':dropout_reason' => $dropoutReason,
            ':dropout_date' => $dropoutDate,
            ':dropout_recorded_by' => $dropoutRecordedBy,
            ':graduation_date' => $graduationDate,
            ':graduation_notes' => $graduationNotes
        ]);
        $count++;
    } catch (PDOException $e) {
        $errors[] = "Row " . ($index + 1) . " ($fullName): " . $e->getMessage();
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
