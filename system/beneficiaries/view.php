<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'staff', 'project_manager']);
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Ensure database patch for safety
try { $pdo->exec("ALTER TABLE beneficiaries ADD COLUMN sponsor_id INT DEFAULT NULL AFTER status"); } catch (\PDOException $e) {}

// Fetch Beneficiary along with requested Sponsor logic
$stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

// Fetch the user who recorded the dropout (if any)
$dropoutRecorderName = '';
if (!empty($student['dropout_recorded_by'])) {
    try {
        $recStmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $recStmt->execute([$student['dropout_recorded_by']]);
        $dropoutRecorderName = $recStmt->fetchColumn() ?: 'Unknown User';
    } catch (\PDOException $e) { $dropoutRecorderName = 'Unknown'; }
}

$sponsors = [];
try {
    $stmtSponsors = $pdo->prepare("
        SELECT u.full_name as sponsor_name, u.email as sponsor_email 
        FROM beneficiary_sponsors bs 
        JOIN users u ON bs.sponsor_id = u.id 
        WHERE bs.beneficiary_id = ?
    ");
    $stmtSponsors->execute([$id]);
    $sponsors = $stmtSponsors->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {}

$statusColor = match($student['status']) {
    'active' => '#10b981',
    'graduated' => '#f59e0b',
    'dropped_out' => '#dc2626',
    default => '#64748b'
};
$statusBg = match($student['status']) {
    'active' => '#d1fae5',
    'graduated' => '#fef3c7',
    'dropped_out' => '#fee2e2',
    default => '#f1f5f9'
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRISUCODE | <?= __('View Student:') ?> <?= htmlspecialchars($student['full_name']) ?></title>
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
    <style>
        .profile-header-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 24px;
            padding: 40px;
            color: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 30px;
            box-shadow: 0 20px 40px -10px rgba(15,23,42,0.3);
            margin-bottom: 40px;
        }
        .profile-header-banner::before {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
        }
        .avatar-large {
            width: 140px;
            height: 140px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 15px 30px -5px rgba(59,130,246,0.4);
            border: 4px solid rgba(255,255,255,0.1);
            z-index: 1;
        }
        .info-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
            border-color: #cbd5e1;
        }
        .info-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
        }
        .card-personal::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .card-education::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
        .card-sponsor::before { background: linear-gradient(90deg, #f97316, #fb923c); }
        .card-residential::before { background: linear-gradient(90deg, #10b981, #34d399); }
        .card-guardian::before { background: linear-gradient(90deg, #ec4899, #f472b6); }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-value {
            color: #0f172a;
            font-weight: 700;
            font-size: 1rem;
            text-align: right;
        }
        .section-title {
            margin: 0 0 25px;
            color: #1e293b;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .bio-section {
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
            position: relative;
        }
        .bio-section p {
            line-height: 1.8;
            color: #334155;
            font-size: 1.1rem;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body style="background: #f8fafc; font-family: 'Inter', sans-serif;">

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>
        
        <div class="page-header fade-in" style="margin: 20px 40px 0;">
             <h2 style="font-family: 'Outfit'; font-weight: 800; color: #0f172a; margin: 0; font-size: 1.8rem;"><?= __('Student Profile Overview') ?></h2>
             <div class="header-actions">
                 <a href="index.php" class="btn-secondary" style="border-radius: 12px; font-weight: 700; padding: 10px 20px;"><i class="fa-solid fa-arrow-left"></i> <?= __('Back') ?></a>
                 <a href="edit.php?id=<?= $id; ?>" class="btn-primary" style="border-radius: 12px; font-weight: 700; padding: 10px 20px; background: linear-gradient(135deg, #3b82f6, #2563eb); border: none;"><i class="fa-solid fa-pen-to-square"></i> <?= __('Edit Profile') ?></a>
                 <a href="reports.php?id=<?= $id; ?>" class="btn-primary" style="border-radius: 12px; font-weight: 700; padding: 10px 20px; background: linear-gradient(135deg, #8b5cf6, #6d28d9); border: none;"><i class="fa-solid fa-file-lines"></i> <?= __('Manage Reports') ?></a>
             </div>
        </div>

        <div class="form-container fade-in" style="max-width: 1100px; margin: 30px 40px; animation-delay: 0.1s;">
            
            <div class="profile-header-banner">
                <div class="avatar-large">
                    <?= strtoupper(substr($student['full_name'], 0, 1)); ?>
                </div>
                
                <div style="flex: 1; z-index: 1;">
                     <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                         <h2 style="margin: 0; font-size: 2.5rem; color: #fff; font-family: 'Outfit'; font-weight: 800; letter-spacing: -0.02em;"><?= htmlspecialchars($student['full_name']); ?></h2>
                         <span style="background: <?= $statusBg ?>; color: <?= $statusColor ?>; font-weight: 800; font-size: 0.85rem; padding: 6px 16px; border-radius: 20px; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fa-solid <?= ($student['status'] == 'active') ? 'fa-check-circle' : (($student['status'] == 'graduated') ? 'fa-medal' : (($student['status'] == 'dropped_out') ? 'fa-circle-xmark' : 'fa-ban')); ?>"></i> <?= strtoupper($student['status']); ?>
                         </span>
                     </div>
                     
                     <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 15px;">
                         <div style="display: flex; align-items: center; gap: 8px; color: #94a3b8; font-weight: 600;">
                            <i class="fa-solid fa-id-card-clip" style="color: #60a5fa;"></i> ID: <span style="color: #f1f5f9;"><?= htmlspecialchars($student['student_id'] ?: 'Pending'); ?></span>
                         </div>
                         <div style="display: flex; align-items: center; gap: 8px; color: #94a3b8; font-weight: 600;">
                            <i class="fa-solid fa-calendar-check" style="color: #34d399;"></i> Enrolled: <span style="color: #f1f5f9;"><?= date('F j, Y', strtotime($student['registered_at'])) ?></span>
                         </div>
                     </div>
                </div>
            </div>

            <!-- ── DROPOUT NOTICE (conditional) ── -->
            <?php if ($student['status'] === 'dropped_out'): ?>
            <div style="background: linear-gradient(135deg, #fef2f2, #fee2e2); border: 2px solid #fca5a5; border-radius: 24px; padding: 30px; margin-bottom: 40px; box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.1);">
                <h3 style="margin: 0 0 15px; color: #991b1b; font-family: 'Outfit'; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-xmark" style="color: #dc2626; font-size: 1.5rem;"></i> <?= __('Dropout Notice') ?>
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; border-bottom: 1px dashed #fca5a5; padding-bottom: 20px; margin-bottom: 20px;">
                    <div>
                        <span style="display: block; color: #b91c1c; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;"><?= __('Date of Removal') ?></span>
                        <strong style="color: #7f1d1d; font-size: 1.1rem;"><?= !empty($student['dropout_date']) ? date('M d, Y', strtotime($student['dropout_date'])) : 'N/A' ?></strong>
                    </div>
                    <div>
                        <span style="display: block; color: #b91c1c; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;"><?= __('Recorded By') ?></span>
                        <strong style="color: #7f1d1d; font-size: 1.1rem;"><?= htmlspecialchars($dropoutRecorderName ?: 'N/A') ?></strong>
                    </div>
                </div>
                <div>
                    <span style="display: block; color: #b91c1c; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 8px;"><?= __('Reason for Removal') ?></span>
                    <div style="background: #fff; padding: 20px; border-radius: 14px; border: 1px solid #fee2e2; color: #475569; font-weight: 500; line-height: 1.6; font-family: 'Inter';">
                        <?= nl2br(htmlspecialchars($student['dropout_reason'] ?? '')) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- ── GRADUATION DETAILS (conditional) ── -->
            <?php if ($student['status'] === 'graduated'): ?>
            <div style="background: linear-gradient(135deg, #fefce8, #fef9c3); border: 2px solid #facc15; border-radius: 24px; padding: 30px; margin-bottom: 40px; box-shadow: 0 10px 25px -5px rgba(234, 179, 8, 0.1);">
                <h3 style="margin: 0 0 15px; color: #854d0e; font-family: 'Outfit'; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-graduation-cap" style="color: #eab308; font-size: 1.5rem;"></i> <?= __('Graduation Details') ?>
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; border-bottom: 1px dashed #fde047; padding-bottom: 20px; margin-bottom: 20px;">
                    <div>
                        <span style="display: block; color: #a16207; font-weight: 600; font-size: 0.85rem; text-transform: uppercase;"><?= __('Graduation Date') ?></span>
                        <strong style="color: #713f12; font-size: 1.1rem;"><?= !empty($student['graduation_date']) ? date('M d, Y', strtotime($student['graduation_date'])) : 'N/A' ?></strong>
                    </div>
                </div>
                <div>
                    <span style="display: block; color: #a16207; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 8px;"><?= __('Profession Practiced / Graduation Notes') ?></span>
                    <div style="background: #fff; padding: 20px; border-radius: 14px; border: 1px solid #fef9c3; color: #475569; font-weight: 500; line-height: 1.6; font-family: 'Inter';">
                        <?= nl2br(htmlspecialchars($student['graduation_notes'] ?? '')) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Deep Info Grids -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; margin-bottom: 40px;">
                
                <!-- Personal Box -->
                <div class="info-card card-personal">
                    <h4 class="section-title"><i class="fa-solid fa-address-card" style="color: #3b82f6; font-size: 1.4rem;"></i> <?= __('Personal Details') ?></h4>
                    <div class="info-row">
                        <span class="info-label"><?= __('Date of Birth') ?></span>
                        <span class="info-value"><?= (!empty($student['dob']) && $student['dob'] !== '0000-00-00') ? date('M d, Y', strtotime($student['dob'])) : 'Unknown'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?= __('Gender') ?></span>
                        <span class="info-value"><?= htmlspecialchars($student['gender'] ?: 'Not Specified'); ?></span>
                    </div>
                </div>

                <!-- Education Box -->
                <div class="info-card card-education">
                    <h4 class="section-title"><i class="fa-solid fa-graduation-cap" style="color: #8b5cf6; font-size: 1.4rem;"></i> <?= __('Education Status') ?></h4>
                    <div class="info-row">
                        <span class="info-label"><?= __('Current Phase') ?></span>
                        <span class="info-value"><?= htmlspecialchars($student['education_level'] ?: 'Not Assigned'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?= __('Class / Grade') ?></span>
                        <span class="info-value"><?= htmlspecialchars($student['class_level'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?= __('Enrolled Inside') ?></span>
                        <span class="info-value" style="max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($student['school_name'] ?: 'Not Enrolled'); ?>"><?= htmlspecialchars($student['school_name'] ?: 'Not Enrolled'); ?></span>
                    </div>
                </div>

                <!-- Sponsorship Box -->
                <div class="info-card card-sponsor" style="background: #fffaf5; border-color: #ffedd5;">
                    <h4 class="section-title" style="color: #9a3412;"><i class="fa-solid fa-hand-holding-heart" style="color: #f97316; font-size: 1.4rem;"></i> <?= __('Sponsorship Tracker') ?></h4>
                    <?php if(!empty($sponsors)): ?>
                        <?php foreach($sponsors as $index => $sponsor): ?>
                            <div style="margin-bottom: 15px; <?= ($index < count($sponsors) - 1) ? 'border-bottom: 1px dashed #fdba74; padding-bottom: 15px;' : '' ?>">
                                <div class="info-row" style="border: none; padding: 4px 0;">
                                    <span class="info-label" style="color: #ea580c;"><?= __('Active Sponsor') ?></span>
                                    <span class="info-value" style="color: #9a3412;"><?= htmlspecialchars($sponsor['sponsor_name'] ?: 'Unknown'); ?></span>
                                </div>
                                <div class="info-row" style="border: none; padding: 4px 0;">
                                    <span class="info-label" style="color: #ea580c;"><i class="fa-solid fa-envelope"></i></span>
                                    <span class="info-value" style="color: #9a3412; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($sponsor['sponsor_email'] ?: 'N/A'); ?>"><?= htmlspecialchars($sponsor['sponsor_email'] ?: 'N/A'); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: #ea580c; padding: 20px 0;">
                            <i class="fa-solid fa-circle-exclamation" style="font-size: 2.5rem; margin-bottom: 15px; opacity: 0.5;"></i>
                            <p style="margin: 0; font-weight: 600; font-size: 0.95rem;"><?= __('No sponsor is globally assigned to this student file.') ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Contact & Residential -->
                <div class="info-card card-residential" style="background: #f0fdf4; border-color: #dcfce7;">
                    <h4 class="section-title" style="color: #14532d;"><i class="fa-solid fa-house-user" style="color: #10b981; font-size: 1.4rem;"></i> <?= __('Residential Details') ?></h4>
                    <div class="info-row" style="border-color: #bbf7d0;">
                        <span class="info-label" style="color: #16a34a;"><?= __('Location') ?></span>
                        <span class="info-value" style="color: #14532d;"><?= htmlspecialchars($student['location_name'] ?? 'Not Recorded'); ?></span>
                    </div>
                    <?php if(!empty($student['google_map_link'])): ?>
                    <div class="info-row" style="border-color: #bbf7d0;">
                        <span class="info-label" style="color: #16a34a;"><?= __('Google Maps') ?></span>
                        <a href="<?= htmlspecialchars($student['google_map_link']); ?>" target="_blank" class="btn-sm" style="background: #10b981; padding: 6px 12px; font-size: 0.85rem; border-radius: 8px; color: #fff; text-decoration: none; font-weight: 600;"><i class="fa-solid fa-map-location-dot"></i> View Pin</a>
                    </div>
                    <?php else: ?>
                    <div class="info-row" style="border-color: #bbf7d0;">
                        <span class="info-label" style="color: #16a34a;"><?= __('Coordinates map') ?></span>
                        <span class="info-value" style="color: #14532d;">N/A</span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Guardian Block -->
                <div class="info-card card-guardian">
                    <h4 class="section-title"><i class="fa-solid fa-users-rectangle" style="color: #ec4899; font-size: 1.4rem;"></i> <?= __('Family / Guardian') ?></h4>
                    <div class="info-row">
                        <span class="info-label"><?= __('Primary Name') ?></span>
                        <span class="info-value" style="max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($student['guardian_name'] ?? 'Not Recorded'); ?>"><?= htmlspecialchars($student['guardian_name'] ?? 'Unknown'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?= __('Relationship') ?></span>
                        <span class="info-value"><?= htmlspecialchars($student['guardian_relation'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?= __('Phone Contact') ?></span>
                        <span class="info-value"><?= htmlspecialchars($student['guardian_phone'] ?? 'N/A'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Biography Full Width -->
            <div class="bio-section">
                <i class="fa-solid fa-quote-right" style="position: absolute; right: 30px; top: 30px; font-size: 8rem; color: #f1f5f9; z-index: 0; opacity: 0.5;"></i>
                <h4 class="section-title" style="position: relative; z-index: 1;">
                    <i class="fa-solid fa-feather-pointed" style="color: #10b981; font-size: 1.4rem;"></i> <?= __('Biography & Verified Background History') ?>
                </h4>
                
                <div style="position: relative; z-index: 1;">
                    <?php if(!empty($student['bio'])): ?>
                        <p><?= nl2br(htmlspecialchars($student['bio'])); ?></p>
                    <?php else: ?>
                        <p style="color: #94a3b8; font-style: italic; font-weight: 500;"><?= __('No biography data has been provided or logged in this registry yet.') ?></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        
        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
