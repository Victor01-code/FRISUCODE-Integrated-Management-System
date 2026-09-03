<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'staff', 'project_manager']); 
require_once __DIR__ . '/../config/db.php';

// Check for AJAX duplicate check requests
if (isset($_GET['ajax_check'])) {
    header('Content-Type: application/json');
    $fullName = trim($_GET['full_name'] ?? '');
    $studentId = trim($_GET['student_id'] ?? '');
    $excludeId = isset($_GET['exclude_id']) ? (int)$_GET['exclude_id'] : 0;
    
    $response = [
        'duplicate_id' => false,
        'duplicate_name' => false,
        'similar_names' => []
    ];
    
    if ($studentId !== '') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM beneficiaries WHERE student_id = ? AND id != ?");
        $stmt->execute([$studentId, $excludeId]);
        $response['duplicate_id'] = $stmt->fetchColumn() > 0;
    }
    
    if ($fullName !== '') {
        // Exact check
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM beneficiaries WHERE LOWER(full_name) = ? AND id != ?");
        $stmt->execute([strtolower($fullName), $excludeId]);
        $response['duplicate_name'] = $stmt->fetchColumn() > 0;
        
        // Similar check (using soundex, Levenshtein, or substrings in PHP)
        $stmt = $pdo->prepare("SELECT id, full_name, student_id FROM beneficiaries WHERE id != ?");
        $stmt->execute([$excludeId]);
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($all as $b) {
            $existingName = $b['full_name'];
            $n1 = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $fullName));
            $n2 = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $existingName));
            
            $dist = levenshtein($n1, $n2);
            $isSubstring = ($n1 !== '' && $n2 !== '' && (strpos($n1, $n2) !== false || strpos($n2, $n1) !== false));
            $soundex1 = soundex($fullName);
            $soundex2 = soundex($existingName);
            
            $maxLen = max(strlen($n1), strlen($n2));
            $threshold = $maxLen > 5 ? 3 : 2; 
            
            if (($dist !== -1 && $dist <= $threshold) || $isSubstring || ($soundex1 !== false && $soundex1 === $soundex2 && strlen($n1) > 3)) {
                $response['similar_names'][] = [
                    'id' => $b['id'],
                    'full_name' => $existingName,
                    'student_id' => $b['student_id']
                ];
            }
        }
    }
    echo json_encode($response);
    exit;
}

$error = '';
$success = '';

// Safe DB Schema patch
try { 
    $pdo->exec("ALTER TABLE beneficiaries ADD COLUMN sponsor_id INT DEFAULT NULL AFTER status"); 
} catch (\PDOException $e) {}

try { 
    $pdo->exec("ALTER TABLE beneficiaries 
                ADD COLUMN location_name VARCHAR(255) DEFAULT '',
                ADD COLUMN google_map_link TEXT DEFAULT '',
                ADD COLUMN class_level VARCHAR(100) DEFAULT '',
                ADD COLUMN guardian_name VARCHAR(255) DEFAULT '',
                ADD COLUMN guardian_phone VARCHAR(50) DEFAULT '',
                ADD COLUMN guardian_relation VARCHAR(100) DEFAULT ''"); 
} catch (\PDOException $e) {}

// ── Lifecycle columns (graduate & dropout tracking) ──
try { $pdo->exec("ALTER TABLE beneficiaries ADD COLUMN dropout_reason TEXT DEFAULT NULL"); } catch (\PDOException $e) {}
try { $pdo->exec("ALTER TABLE beneficiaries ADD COLUMN dropout_date DATE DEFAULT NULL"); } catch (\PDOException $e) {}
try { $pdo->exec("ALTER TABLE beneficiaries ADD COLUMN dropout_recorded_by INT DEFAULT NULL"); } catch (\PDOException $e) {}
try { $pdo->exec("ALTER TABLE beneficiaries ADD COLUMN graduation_date DATE DEFAULT NULL"); } catch (\PDOException $e) {}
try { $pdo->exec("ALTER TABLE beneficiaries ADD COLUMN graduation_notes TEXT DEFAULT NULL"); } catch (\PDOException $e) {}


// Fetch eligible donors
$donors = [];
try {
    $donors = $pdo->query("SELECT id, full_name FROM users WHERE role = 'donor'")->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {}

// Auto-generate next Student ID (Format: FSC-01-0001-YYYY)
$currentYear = date('Y');
$nextStudentId = "FSC-01-0001-$currentYear";
try {
    $stmt = $pdo->prepare("SELECT student_id FROM beneficiaries WHERE student_id LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute(["FSC-01-%-$currentYear"]);
    $lastId = $stmt->fetchColumn();
    if ($lastId) {
        $parts = explode('-', $lastId);
        if (isset($parts[2])) {
            $seq = (int)$parts[2] + 1;
            $nextStudentId = sprintf("FSC-01-%04d-%s", $seq, $currentYear);
        }
    }
} catch (\PDOException $e) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $studentId = trim($_POST['student_id'] ?? $nextStudentId);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $educationLevel = $_POST['education_level'];
    $classLevel = trim($_POST['class_level'] ?? '');
    $schoolName = trim($_POST['school_name']);
    
    $locationName = trim($_POST['location_name'] ?? '');
    $googleMapLink = trim($_POST['google_map_link'] ?? '');
    
    $guardianName = trim($_POST['guardian_name'] ?? '');
    $guardianPhone = trim($_POST['guardian_phone'] ?? '');
    $guardianRelation = trim($_POST['guardian_relation'] ?? '');

    $status = $_POST['status'];
    $sponsorIds = $_POST['sponsor_ids'] ?? [];
    $bio = trim($_POST['bio']);

    // ── Lifecycle fields ──
    $dropoutReason = trim($_POST['dropout_reason'] ?? '');
    $dropoutDate = $_POST['dropout_date'] ?? null;
    $dropoutRecordedBy = null;
    $graduationDate = $_POST['graduation_date'] ?? null;
    $graduationNotes = trim($_POST['graduation_notes'] ?? '');

    if ($status === 'dropped_out') {
        $dropoutRecordedBy = $_SESSION['user_id'] ?? null;
        if (empty($dropoutReason)) {
            $error = "A dropout reason is required when marking a student as Dropped Out.";
        }
        if (empty($dropoutDate)) {
            $dropoutDate = date('Y-m-d');
        }
    }

    if ($status === 'graduated' && empty($graduationDate)) {
        $graduationDate = date('Y-m-d');
    }

    if (empty($fullName) || empty($educationLevel)) {
        $error = $error ?: "Full Name and Education Level are required.";
    }
    
    if (empty($error)) {
        try {
            $checkId = false;
            if (!empty($studentId)) {
                $checkIdStmt = $pdo->prepare("SELECT COUNT(*) FROM beneficiaries WHERE student_id = ?");
                $checkIdStmt->execute([$studentId]);
                $checkId = $checkIdStmt->fetchColumn() > 0;
            }

            $checkNameStmt = $pdo->prepare("SELECT COUNT(*) FROM beneficiaries WHERE LOWER(full_name) = ?");
            $checkNameStmt->execute([strtolower($fullName)]);
            $checkName = $checkNameStmt->fetchColumn() > 0;

            if ($checkId) {
                $error = "A beneficiary with this Student ID is already registered.";
            } elseif ($checkName) {
                $error = "A beneficiary with this Full Name is already registered.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO beneficiaries (
                    full_name, student_id, dob, gender, education_level, class_level, school_name, 
                    location_name, google_map_link, guardian_name, guardian_phone, guardian_relation, 
                    status, bio, dropout_reason, dropout_date, dropout_recorded_by, graduation_date, graduation_notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $fullName, $studentId, $dob, $gender, $educationLevel, $classLevel, $schoolName,
                    $locationName, $googleMapLink, $guardianName, $guardianPhone, $guardianRelation,
                    $status, $bio,
                    $status === 'dropped_out' ? $dropoutReason : null,
                    $status === 'dropped_out' ? $dropoutDate : null,
                    $status === 'dropped_out' ? $dropoutRecordedBy : null,
                    $status === 'graduated' ? $graduationDate : null,
                    $status === 'graduated' ? $graduationNotes : null
                ]);
                $newId = $pdo->lastInsertId();
                
                if (!empty($sponsorIds)) {
                    $sponsorStmt = $pdo->prepare("INSERT IGNORE INTO beneficiary_sponsors (beneficiary_id, sponsor_id) VALUES (?, ?)");
                    foreach ($sponsorIds as $sId) {
                        if (!empty($sId)) {
                            $sponsorStmt->execute([$newId, $sId]);
                        }
                    }
                }
                header("Location: index.php?msg=created"); 
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Register Student</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Leaflet Map Picker -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map { height: 300px; border-radius: 12px; margin-top: 10px; display: none; border: 2px solid #e2e8f0; }
        .map-picker-btn {
            background: #fff;
            border: 2px solid #3b82f6;
            color: #3b82f6;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            margin-left: 10px;
        }
        .map-picker-btn:hover {
            background: #3b82f6;
            color: #fff;
        }
        .conditional-section {
            display: none;
            animation: slideDown 0.3s ease-out;
        }
        .conditional-section.visible {
            display: block;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main" style="scroll-behavior: smooth;">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header" style="margin-bottom: 0;">
            <h2><?= __('Register New Student') ?></h2>
            <a href="index.php" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> <?= __('Registry List') ?></a>
        </div>

        <div class="form-container fade-in" style="max-width: 900px; margin: 30px 40px;">
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="create.php" method="POST">
                
                <h4 style="margin: 0 0 20px; color: #1e293b; font-family: 'Outfit'; font-weight: 800; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-address-card" style="color: #3b82f6;"></i> Primary Information</h4>
                
                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-user"></i> <?= __('Student Full Name *') ?></label>
                        <input type="text" id="full_name" name="full_name" required placeholder="e.g. Amani Juma" autocomplete="off">
                        <div id="name_warning" style="display:none; color: #d97706; font-size: 0.85rem; font-weight: 600; margin-top: 5px;"></div>
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-id-card-clip"></i> <?= __('System ID') ?></label>
                        <input type="text" id="student_id" name="student_id" value="<?= htmlspecialchars($nextStudentId) ?>" style="font-weight: 700;">
                        <div id="id_warning" style="display:none; color: #dc2626; font-size: 0.85rem; font-weight: 600; margin-top: 5px;"><i class="fa-solid fa-triangle-exclamation"></i> This System ID is already in use!</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-cake-candles"></i> <?= __('Date of Birth') ?></label>
                        <input type="date" name="dob" max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-venus-mars"></i> <?= __('Gender') ?></label>
                        <select name="gender">
                            <option value="Male"><?= __('Male') ?></option>
                            <option value="Female"><?= __('Female') ?></option>
                            <option value="Other"><?= __('Other') ?></option>
                        </select>
                    </div>
                </div>

                <h4 style="margin: 40px 0 20px; color: #1e293b; font-family: 'Outfit'; font-weight: 800; display: flex; align-items: center; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 30px;"><i class="fa-solid fa-graduation-cap" style="color: #8b5cf6;"></i> Education Details</h4>

                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-school-flag"></i> <?= __('Education Level *') ?></label>
                        <select name="education_level" id="education_level" required onchange="updateClassLevel()">
                            <option value="Primary"><?= __('Primary Education (Shule ya Msingi)') ?></option>
                            <option value="Secondary_O"><?= __('Secondary O-Level (Kidato 1-4)') ?></option>
                            <option value="Secondary_A"><?= __('Secondary A-Level (Kidato 5-6)') ?></option>
                            <option value="University"><?= __('University / College') ?></option>
                            <option value="Vocational"><?= __('Vocational / TVET (VETA/NACTE)') ?></option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-chalkboard-user"></i> <?= __('Class / Grade Level') ?></label>
                        <select name="class_level" id="class_level">
                            <!-- Populated dynamically by JS -->
                            <option value="">-- Select Education Level First --</option>
                        </select>
                    </div>
                </div>
                
                <div class="input-group">
                    <label><i class="fa-solid fa-school"></i> <?= __('School / Institution Name') ?></label>
                    <input type="text" name="school_name" placeholder="e.g. Arusha Secondary School">
                </div>
                
                <h4 style="margin: 40px 0 20px; color: #1e293b; font-family: 'Outfit'; font-weight: 800; display: flex; align-items: center; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 30px;"><i class="fa-solid fa-people-roof" style="color: #ea580c;"></i> Residential &amp; Family Guardian Details</h4>

                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-map-location-dot"></i> Village / Location Name</label>
                        <input type="text" name="location_name" placeholder="e.g. Njiro, Arusha">
                    </div>
                    <div class="input-group">
                        <label style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                            <span><i class="fa-solid fa-link"></i> Google Maps Link (Optional)</span>
                            <button type="button" class="map-picker-btn" onclick="toggleMap()"><i class="fa-solid fa-location-dot"></i> Pick from Map</button>
                        </label>
                        <input type="url" name="google_map_link" id="google_map_link" placeholder="https://maps.google.com/...">
                        <div id="map"></div>
                        <small id="map-help" style="display:none; color: #3b82f6; font-weight: 600; margin-top: 5px;"><i class="fa-solid fa-info-circle"></i> Click anywhere on the map to set the location.</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-user-group"></i> Parent / Guardian Name</label>
                        <input type="text" name="guardian_name" placeholder="e.g. Mama Juma">
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-phone"></i> Contact Phone</label>
                        <input type="text" name="guardian_phone" placeholder="+255 700 000 000">
                    </div>
                </div>
                <div class="input-group">
                    <label><i class="fa-solid fa-people-arrows"></i> Relationship to Student</label>
                    <select name="guardian_relation">
                        <option value="">-- Select Relation --</option>
                        <option value="Mother">Mother</option>
                        <option value="Father">Father</option>
                        <option value="Grandparent">Grandparent</option>
                        <option value="Aunt/Uncle">Aunt/Uncle</option>
                        <option value="Sibling">Sibling</option>
                        <option value="Other">Other Guardian / Caretaker</option>
                    </select>
                </div>

                <h4 style="margin: 40px 0 20px; color: #1e293b; font-family: 'Outfit'; font-weight: 800; display: flex; align-items: center; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 30px;"><i class="fa-solid fa-heart-circle-check" style="color: #10b981;"></i> Assignment &amp; History</h4>
                
                <div class="input-group">
                    <label><i class="fa-solid fa-clipboard-check"></i> <?= __('Assigned Program Status') ?></label>
                    <div class="type-selector" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <!-- ACTIVE -->
                        <label class="radio-card" id="status-card-active" style="border: 2px solid #16a34a; padding: 20px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 15px; background: #f0fdf4; transition: 0.3s;" onclick="selectStatus('active')">
                            <input type="radio" name="status" value="active" checked style="display:none;">
                            <i class="fa-solid fa-check-circle" style="color: #16a34a; font-size: 1.5rem;"></i>
                            <div>
                                <strong style="display:block; color: #14532d;"><?= __('Active') ?></strong>
                                <small style="color: #16a34a; font-weight: 600;"><?= __('Full Sponsorship') ?></small>
                            </div>
                        </label>
                        <!-- GRADUATED -->
                        <label class="radio-card" id="status-card-graduated" style="border: 2px solid #e2e8f0; padding: 20px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 15px; background: #fff; transition: 0.3s;" onclick="selectStatus('graduated')">
                            <input type="radio" name="status" value="graduated" style="display:none;">
                            <i class="fa-solid fa-medal" style="color: #f59e0b; font-size: 1.5rem;"></i>
                            <div>
                                <strong style="display:block; color: #78350f;"><?= __('Alumni') ?></strong>
                                <small style="color: #ea580c; font-weight: 600;"><?= __('Successfully Graduated') ?></small>
                            </div>
                        </label>
                        <!-- DROPPED OUT -->
                        <label class="radio-card" id="status-card-dropped_out" style="border: 2px solid #e2e8f0; padding: 20px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 15px; background: #fff; transition: 0.3s;" onclick="selectStatus('dropped_out')">
                            <input type="radio" name="status" value="dropped_out" style="display:none;">
                            <i class="fa-solid fa-circle-xmark" style="color: #dc2626; font-size: 1.5rem;"></i>
                            <div>
                                <strong style="display:block; color: #7f1d1d;"><?= __('Dropped Out') ?></strong>
                                <small style="color: #dc2626; font-weight: 600;"><?= __('Agreement Breach') ?></small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- ── GRADUATION DETAILS (conditional) ── -->
                <div id="graduation-section" class="conditional-section" style="margin-top: 20px; padding: 25px; background: linear-gradient(135deg, #fefce8, #fef9c3); border: 2px solid #facc15; border-radius: 16px;">
                    <h4 style="margin: 0 0 20px; color: #854d0e; font-family: 'Outfit'; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-graduation-cap" style="color: #eab308;"></i> <?= __('Graduation Details') ?>
                    </h4>
                    <div class="form-row">
                        <div class="input-group">
                            <label style="color: #854d0e; font-weight: 700;"><i class="fa-solid fa-calendar-check"></i> <?= __('Graduation Date') ?></label>
                            <input type="date" name="graduation_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" style="border-color: #fde047;">
                        </div>
                    </div>
                    <div class="input-group" style="margin-top: 15px;">
                        <label style="color: #854d0e; font-weight: 700;"><i class="fa-solid fa-briefcase"></i> <?= __('Profession Practiced / Graduation Notes') ?></label>
                        <textarea name="graduation_notes" rows="3" placeholder="e.g. Successfully completed web development training and is now working as a freelance developer..." style="width: 100%; border-radius: 12px; border: 2px solid #fde047; padding: 16px; font-family: inherit; resize: vertical; background: #fffef0;"></textarea>
                    </div>
                </div>

                <!-- ── DROPOUT REASON FORM (conditional) ── -->
                <div id="dropout-section" class="conditional-section" style="margin-top: 20px; padding: 25px; background: linear-gradient(135deg, #fef2f2, #fee2e2); border: 2px solid #fca5a5; border-radius: 16px;">
                    <h4 style="margin: 0 0 20px; color: #991b1b; font-family: 'Outfit'; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-file-circle-exclamation" style="color: #dc2626;"></i> <?= __('Dropout Notice — Required') ?>
                    </h4>
                    <div class="alert" style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; color: #9f1239; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <?= __('This form must be completed by the office sponsor, director, or responsible authority. The reason will be visible on the student\'s profile to both parties.') ?>
                    </div>
                    <div class="form-row">
                        <div class="input-group">
                            <label style="color: #991b1b; font-weight: 700;"><i class="fa-solid fa-calendar-xmark"></i> <?= __('Date of Removal') ?></label>
                            <input type="date" name="dropout_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" style="border-color: #fca5a5;">
                        </div>
                        <div class="input-group">
                            <label style="color: #991b1b; font-weight: 700;"><i class="fa-solid fa-user-tie"></i> <?= __('Recorded By') ?></label>
                            <input type="text" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'Current User') ?>" readonly style="background-color: #fff5f5; cursor: not-allowed; color: #991b1b; font-weight: 700; border-color: #fca5a5;">
                        </div>
                    </div>
                    <div class="input-group" style="margin-top: 15px;">
                        <label style="color: #991b1b; font-weight: 700;"><i class="fa-solid fa-pen-fancy"></i> <?= __('Reason for Dropping Out *') ?></label>
                        <textarea name="dropout_reason" id="dropout_reason" rows="4" placeholder="Clearly describe why this student is being removed from the program. Include specific misbehavior, agreement violations, or other circumstances..." style="width: 100%; border-radius: 12px; border: 2px solid #fca5a5; padding: 16px; font-family: inherit; resize: vertical; background: #fffbfb;"></textarea>
                        <small style="color: #b91c1c; font-weight: 600; margin-top: 5px; display: block;">
                            <i class="fa-solid fa-shield-halved"></i> <?= __('This information will be stored permanently and displayed on both the student profile and the dropout report.') ?>
                        </small>
                    </div>
                </div>

                <div class="input-group" style="margin-top: 20px;">
                    <label><i class="fa-solid fa-hand-holding-heart" style="color: #f43f5e;"></i> <?= __('Assign Sponsors / Donors (Optional)') ?></label>
                    <!-- Selected donors chips -->
                    <div id="selected_donors" style="display: flex; flex-wrap: wrap; gap: 8px; min-height: 10px; margin-top: 5px;"></div>
                    <!-- Hidden inputs container for form submission -->
                    <div id="sponsor_hidden_inputs"></div>
                    <!-- Search input -->
                    <input type="text" id="donor_search" placeholder="🔍 Type to search donors by name..." autocomplete="off" style="width: 100%; border-radius: 12px; border: 2px solid #e2e8f0; padding: 10px 14px; margin-top: 8px; font-weight: 500; font-family: inherit;">
                    <!-- Donor list -->
                    <div id="donor_list" style="width: 100%; border-radius: 12px; border: 2px solid #e2e8f0; margin-top: 5px; background: #fef2f2; max-height: 280px; overflow-y: auto;">
                        <?php foreach($donors as $donor): ?>
                            <div class="donor-item" data-id="<?= $donor['id'] ?>" data-name="<?= htmlspecialchars($donor['full_name']) ?>" onclick="toggleDonor(this)" style="padding: 10px 16px; cursor: pointer; font-weight: 600; font-size: 0.92rem; border-bottom: 1px solid #fce7e7; display: flex; align-items: center; gap: 10px; transition: background 0.15s;">
                                <i class="fa-regular fa-square" style="color: #94a3b8; font-size: 1rem;"></i>
                                <span><?= htmlspecialchars($donor['full_name']) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($donors)): ?>
                            <div style="padding: 14px 16px; color: #94a3b8; font-weight: 600; text-align: center;">No donors available</div>
                        <?php endif; ?>
                    </div>
                    <small style="color: #64748b; font-weight: 600; margin-top: 5px; display: block;"><i class="fa-solid fa-info-circle"></i> Click a donor to select/deselect. Selected donors appear above.</small>
                </div>

                <div class="input-group" style="margin-top: 20px;">
                    <label><i class="fa-solid fa-feather-pointed"></i> <?= __('Biography, Living Condition & Needs') ?></label>
                    <textarea name="bio" rows="5" placeholder="Document the student's background, current living conditions, specific needs, and aspirations..." style="width: 100%; border-radius: 12px; border: 2px solid #e2e8f0; padding: 16px; margin-top: 5px; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <div style="margin-top: 50px;">
                    <button type="submit" class="btn-primary-block" style="height: 60px; border-radius: 18px; font-size: 1.1rem; box-shadow: 0 10px 20px -5px rgba(37,99,235,0.4);">
                        <i class="fa-solid fa-user-plus"></i> <?= __('Finalize Secure Registration') ?>
                    </button>
                    <p style="text-align: center; font-size: 0.85rem; color: #94a3b8; margin-top: 20px; font-weight: 600;">
                        <i class="fa-solid fa-shield-halved"></i> <?= __('Information is encrypted securely into the beneficiary registry.') ?>
                    </p>
                </div>
            </form>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
    // ── Tanzania Education Class Level Mapping ──
    const classLevelMap = {
        'Primary': [
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4',
            'Standard 5', 'Standard 6', 'Standard 7'
        ],
        'Secondary_O': [
            'Form 1', 'Form 2', 'Form 3', 'Form 4'
        ],
        'Secondary_A': [
            'Form 5', 'Form 6'
        ],
        'University': [
            'Year 1', 'Year 2', 'Year 3', 'Year 4', 'Year 5', 'Year 6'
        ],
        'Vocational': [
            'Level 1 (Certificate)', 'Level 2 (Certificate)', 
            'Level 3 (Diploma)', 'Level 4 (Higher Diploma)'
        ]
    };

    function updateClassLevel(selectedValue) {
        const level = document.getElementById('education_level').value;
        const classSelect = document.getElementById('class_level');
        const options = classLevelMap[level] || [];

        classSelect.innerHTML = '<option value="">-- Select Class Level --</option>';
        options.forEach(opt => {
            const el = document.createElement('option');
            el.value = opt;
            el.textContent = opt;
            if (selectedValue && selectedValue === opt) el.selected = true;
            classSelect.appendChild(el);
        });
    }

    // Initialize on page load
    updateClassLevel();

    // ── Status Selection with Conditional Sections ──
    function selectStatus(status) {
        const statuses = ['active', 'graduated', 'dropped_out'];
        const colors = {
            active: { border: '#16a34a', bg: '#f0fdf4' },
            graduated: { border: '#f59e0b', bg: '#fffbeb' },
            dropped_out: { border: '#dc2626', bg: '#fef2f2' }
        };

        // Reset all cards
        statuses.forEach(s => {
            const card = document.getElementById('status-card-' + s);
            if (card) {
                card.style.borderColor = '#e2e8f0';
                card.style.background = '#fff';
            }
        });

        // Highlight selected card
        const activeCard = document.getElementById('status-card-' + status);
        if (activeCard) {
            activeCard.querySelector('input').checked = true;
            activeCard.style.borderColor = colors[status].border;
            activeCard.style.background = colors[status].bg;
        }

        // Toggle conditional sections
        document.getElementById('graduation-section').classList.toggle('visible', status === 'graduated');
        document.getElementById('dropout-section').classList.toggle('visible', status === 'dropped_out');

        // Toggle required on dropout reason
        const reasonField = document.getElementById('dropout_reason');
        if (reasonField) {
            if (status === 'dropped_out') {
                reasonField.setAttribute('required', 'required');
            } else {
                reasonField.removeAttribute('required');
            }
        }
    }

    // Initialize status on page load
    selectStatus('active');

    // ── Map Picker ──
    let map;
    let marker;

    function toggleMap() {
        const mapDiv = document.getElementById('map');
        const mapHelp = document.getElementById('map-help');
        
        if (mapDiv.style.display === 'none' || mapDiv.style.display === '') {
            mapDiv.style.display = 'block';
            mapHelp.style.display = 'block';
            
            if (!map) {
                map = L.map('map').setView([-3.3731, 36.6853], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                map.on('click', function(e) {
                    const lat = e.latlng.lat.toFixed(6);
                    const lng = e.latlng.lng.toFixed(6);
                    
                    if (marker) {
                        marker.setLatLng(e.latlng);
                    } else {
                        marker = L.marker(e.latlng).addTo(map);
                    }
                    
                    document.getElementById('google_map_link').value = `https://www.google.com/maps?q=${lat},${lng}`;
                });
            }
            setTimeout(() => map.invalidateSize(), 100);
        } else {
            mapDiv.style.display = 'none';
            mapHelp.style.display = 'none';
        }
    }

    // ── Donor chip-based selector ──
    function toggleDonor(el) {
        const id = el.dataset.id;
        const name = el.dataset.name;
        const icon = el.querySelector('i');
        const isSelected = el.classList.toggle('donor-selected');

        if (isSelected) {
            el.style.background = '#ecfdf5';
            el.style.borderLeft = '3px solid #10b981';
            icon.className = 'fa-solid fa-square-check';
            icon.style.color = '#10b981';
        } else {
            el.style.background = '';
            el.style.borderLeft = '';
            icon.className = 'fa-regular fa-square';
            icon.style.color = '#94a3b8';
        }
        renderSelectedDonors();
    }

    function removeDonor(id) {
        const item = document.querySelector(`.donor-item[data-id="${id}"]`);
        if (item && item.classList.contains('donor-selected')) {
            toggleDonor(item);
        }
    }

    function renderSelectedDonors() {
        const container = document.getElementById('selected_donors');
        const hiddenContainer = document.getElementById('sponsor_hidden_inputs');
        const items = document.querySelectorAll('.donor-item.donor-selected');

        container.innerHTML = '';
        hiddenContainer.innerHTML = '';

        items.forEach(item => {
            const id = item.dataset.id;
            const name = item.dataset.name;
            // Chip tag
            const chip = document.createElement('span');
            chip.style.cssText = 'display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1.5px solid #6ee7b7; color: #065f46; padding: 6px 12px; border-radius: 20px; font-weight: 700; font-size: 0.82rem; cursor: default; animation: fadeIn 0.2s ease;';
            chip.innerHTML = `<i class="fa-solid fa-user-check" style="font-size: 0.75rem;"></i> ${name} <span onclick="removeDonor('${id}')" style="cursor: pointer; margin-left: 2px; color: #dc2626; font-weight: 900; font-size: 1rem; line-height: 1;">&times;</span>`;
            container.appendChild(chip);
            // Hidden input
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'sponsor_ids[]';
            hidden.value = id;
            hiddenContainer.appendChild(hidden);
        });
    }

    // ── Donor search / filter functionality ──
    document.getElementById('donor_search').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('.donor-item').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            item.style.display = name.includes(filter) ? '' : 'none';
        });
    });

    // ── Real-time duplicate validation ──
    let checkTimeout;
    const nameInput = document.getElementById('full_name');
    const idInput = document.getElementById('student_id');
    const nameWarning = document.getElementById('name_warning');
    const idWarning = document.getElementById('id_warning');

    function checkDuplicates() {
        clearTimeout(checkTimeout);
        checkTimeout = setTimeout(() => {
            const nameVal = nameInput.value.trim();
            const idVal = idInput.value.trim();
            if (!nameVal && !idVal) {
                nameWarning.style.display = 'none';
                idWarning.style.display = 'none';
                return;
            }

            fetch(`create.php?ajax_check=1&full_name=${encodeURIComponent(nameVal)}&student_id=${encodeURIComponent(idVal)}`)
                .then(r => r.json())
                .then(data => {
                    // Handle ID warning
                    if (data.duplicate_id) {
                        idWarning.style.display = 'block';
                    } else {
                        idWarning.style.display = 'none';
                    }

                    // Handle Name warnings
                    if (data.duplicate_name) {
                        nameWarning.style.display = 'block';
                        nameWarning.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Warning: A student with this exact name already exists!`;
                        nameWarning.style.color = '#dc2626';
                    } else if (data.similar_names && data.similar_names.length > 0) {
                        nameWarning.style.display = 'block';
                        let listHtml = data.similar_names.map(n => `<li><strong>${n.full_name}</strong> (${n.student_id})</li>`).join('');
                        nameWarning.innerHTML = `
                            <i class="fa-solid fa-triangle-exclamation"></i> Warning: Similar student(s) already registered:
                            <ul style="margin: 5px 0 0 20px; padding: 0; font-size: 0.8rem; text-align: left;">${listHtml}</ul>
                        `;
                        nameWarning.style.color = '#d97706';
                    } else {
                        nameWarning.style.display = 'none';
                    }
                })
                .catch(e => console.error("Error checking duplicates:", e));
        }, 400);
    }

    nameInput.addEventListener('input', checkDuplicates);
    idInput.addEventListener('input', checkDuplicates);
</script>

</body>
</html>
