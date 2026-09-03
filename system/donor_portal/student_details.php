<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole('donor');
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user_id'];
$studentId = $_GET['id'] ?? 0;

if (!$studentId) {
    header("Location: students.php");
    exit;
}

// Ensure the donor is actually sponsoring this student for security
$stmt = $pdo->prepare("SELECT b.* FROM beneficiaries b 
    JOIN beneficiary_sponsors bs ON b.id = bs.beneficiary_id 
    WHERE bs.sponsor_id = ? AND b.id = ?");
$stmt->execute([$userId, $studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    // If not found or not sponsored by this donor
    die("Student not found or you are not authorized to view this profile.");
}

// Fetch reports for this student
$reportsStmt = $pdo->prepare("SELECT sr.*, u.full_name AS author_name FROM student_reports sr LEFT JOIN users u ON sr.created_by = u.id WHERE sr.beneficiary_id = ? ORDER BY sr.report_date DESC, sr.created_at DESC");
$reportsStmt->execute([$studentId]);
$reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRISUCODE | <?= __('Student Profile') ?> - <?= htmlspecialchars($student['full_name']) ?></title>
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body style="background: #f8fafc;">

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header fade-in">
             <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                 <h2 style="font-family: 'Outfit'; font-weight: 800; color: #0f172a; margin: 0;"><i class="fa-solid fa-user-graduate" style="color: #ea580c; margin-right: 10px;"></i> <?= __('Student Profile') ?></h2>
                 <a href="students.php" class="btn-outline-sm" style="text-decoration: none; padding: 8px 16px; border-radius: 8px; font-weight: 700;"><i class="fa-solid fa-arrow-left"></i> <?= __('Back to Students') ?></a>
             </div>
        </div>

        <div class="fade-in" style="padding: 0 40px; margin-bottom: 40px; animation-delay: 0.1s; max-width: 900px; margin: 0 auto;">
            
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; box-shadow: var(--card-shadow); overflow: hidden;">
                <!-- Header Banner -->
                <div style="background: linear-gradient(135deg, #f97316, #ea580c); padding: 40px; text-align: center; color: white;">
                    <?php if (!empty($student['photo_url'])): ?>
                        <div style="width: 120px; height: 120px; border: 5px solid rgba(255,255,255,0.3); border-radius: 50%; margin: 0 auto 20px; overflow: hidden; box-shadow: 0 8px 16px rgba(0,0,0,0.1);">
                            <img src="<?= htmlspecialchars($student['photo_url']) ?>" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php else: ?>
                        <div style="width: 120px; height: 120px; background: rgba(255,255,255,0.2); border: 5px solid rgba(255,255,255,0.3); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 800; margin: 0 auto 20px; box-shadow: 0 8px 16px rgba(0,0,0,0.1);">
                            <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <h3 style="margin: 0; font-family: 'Outfit'; font-weight: 800; font-size: 2rem; letter-spacing: -0.01em;"><?= htmlspecialchars($student['full_name']) ?></h3>
                    <div style="margin-top: 10px; display: flex; justify-content: center; gap: 15px; font-weight: 600; font-size: 0.95rem;">
                        <span style="background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); padding: 6px 16px; border-radius: 20px;">
                            <i class="fa-solid fa-id-badge"></i> <?= htmlspecialchars($student['student_id'] ?: 'Pending ID') ?>
                        </span>
                        <span class="badge <?= ($student['status'] == 'active') ? 'success' : (($student['status'] == 'graduated') ? 'primary' : 'neutral') ?>" style="border: 2px solid rgba(255,255,255,0.5); padding: 6px 16px; font-weight: 800; border-radius: 20px;">
                            <?= strtoupper($student['status']) ?>
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div style="padding: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    <!-- Academic Info -->
                    <div>
                        <h4 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;"><i class="fa-solid fa-book-open" style="color: #ea580c;"></i> <?= __('Academic Details') ?></h4>
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 15px;">
                            <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                <span style="color: #64748b; font-weight: 600;"><?= __('Education Level') ?></span>
                                <strong style="color: #0f172a; text-align: right;"><?= htmlspecialchars($student['education_level'] ?: 'N/A') ?></strong>
                            </li>
                            <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                <span style="color: #64748b; font-weight: 600;"><?= __('School/Institution') ?></span>
                                <strong style="color: #0f172a; text-align: right;"><?= htmlspecialchars($student['school_name'] ?: 'Not Enrolled') ?></strong>
                            </li>
                            <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                <span style="color: #64748b; font-weight: 600;"><?= __('Class Level') ?></span>
                                <strong style="color: #0f172a; text-align: right;"><?= htmlspecialchars($student['class_level'] ?: 'N/A') ?></strong>
                            </li>
                            <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                <span style="color: #64748b; font-weight: 600;"><?= __('Joined Program') ?></span>
                                <strong style="color: #0f172a; text-align: right;"><?= date('F j, Y', strtotime($student['registered_at'])) ?></strong>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Personal Info -->
                    <div>
                        <h4 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;"><i class="fa-solid fa-address-card" style="color: #ea580c;"></i> <?= __('Personal Background') ?></h4>
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 15px;">
                            <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                <span style="color: #64748b; font-weight: 600;"><?= __('Gender') ?></span>
                                <strong style="color: #0f172a; text-align: right;"><?= htmlspecialchars($student['gender'] ?: 'N/A') ?></strong>
                            </li>
                            <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                <span style="color: #64748b; font-weight: 600;"><?= __('Date of Birth') ?></span>
                                <strong style="color: #0f172a; text-align: right;">
                                    <?php 
                                        if(!empty($student['dob']) && $student['dob'] != '0000-00-00'){
                                            $age = date_diff(date_create($student['dob']), date_create('today'))->y;
                                            echo date('M d, Y', strtotime($student['dob'])) . " ($age yrs)";
                                        } else {
                                            echo 'N/A';
                                        }
                                    ?>
                                </strong>
                            </li>
                            <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                <span style="color: #64748b; font-weight: 600;"><?= __('Location') ?></span>
                                <strong style="color: #0f172a; text-align: right;">
                                    <?= htmlspecialchars($student['location_name'] ?: 'N/A') ?>
                                    <?php if(!empty($student['google_map_link'])): ?>
                                        <a href="<?= htmlspecialchars($student['google_map_link']) ?>" target="_blank" style="color: #3b82f6; margin-left: 5px;"><i class="fa-solid fa-map-location-dot"></i></a>
                                    <?php endif; ?>
                                </strong>
                            </li>
                            <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                                <span style="color: #64748b; font-weight: 600;"><?= __('Guardian') ?></span>
                                <strong style="color: #0f172a; text-align: right;">
                                    <?= htmlspecialchars($student['guardian_name'] ?: 'N/A') ?>
                                    <?php if(!empty($student['guardian_relation'])): ?>
                                        <span style="color: #94a3b8; font-size: 0.85rem;">(<?= htmlspecialchars($student['guardian_relation']) ?>)</span>
                                    <?php endif; ?>
                                </strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Biography Section -->
                <?php if(!empty(trim($student['bio']))): ?>
                <div style="padding: 0 40px 40px; margin-top: -10px;">
                    <h4 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;"><i class="fa-solid fa-quote-left" style="color: #ea580c;"></i> <?= __('Biography & Story') ?></h4>
                    <div style="background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <p style="margin: 0; color: #475569; font-size: 1.05rem; line-height: 1.8; white-space: pre-wrap; font-family: 'Inter';"><?= htmlspecialchars($student['bio']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Student Reports Section -->
                <?php if(!empty($reports)): ?>
                <div style="padding: 0 40px 40px; margin-top: -10px;">
                    <h4 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">
                        <i class="fa-solid fa-file-lines" style="color: #8b5cf6;"></i> <?= __('Progress Reports') ?>
                        <span style="color: #94a3b8; font-weight: 600; font-size: 0.9rem; margin-left: 8px;">(<?= count($reports) ?>)</span>
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <?php foreach($reports as $r): ?>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 22px; transition: 0.2s;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                <div>
                                    <h5 style="margin: 0 0 6px; font-family: 'Outfit'; font-weight: 800; font-size: 1.1rem; color: #0f172a;">
                                        <i class="fa-solid fa-clipboard-list" style="color: #8b5cf6; margin-right: 5px;"></i> <?= htmlspecialchars($r['title']) ?>
                                    </h5>
                                    <div style="display: flex; gap: 12px; flex-wrap: wrap; font-size: 0.8rem; color: #64748b; font-weight: 600;">
                                        <span><i class="fa-regular fa-calendar"></i> <?= date('M d, Y', strtotime($r['report_date'])) ?></span>
                                        <span><i class="fa-solid fa-user-pen"></i> <?= htmlspecialchars($r['author_name'] ?: 'Staff') ?></span>
                                    </div>
                                </div>
                                <?php if(!empty($r['file_url'])): ?>
                                <a href="<?= htmlspecialchars($r['file_url']) ?>" target="_blank" style="background: #fef2f2; color: #dc2626; padding: 8px 14px; border-radius: 10px; font-weight: 700; font-size: 0.8rem; text-decoration: none; display: flex; align-items: center; gap: 5px; white-space: nowrap; transition: 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                    <i class="fa-solid fa-file-pdf"></i> <?= __('Download PDF') ?>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php if(!empty($r['report_text'])): ?>
                            <div style="background: #fff; padding: 16px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                <p style="margin: 0; color: #475569; line-height: 1.7; font-size: 0.95rem; white-space: pre-wrap;"><?= htmlspecialchars($r['report_text']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
