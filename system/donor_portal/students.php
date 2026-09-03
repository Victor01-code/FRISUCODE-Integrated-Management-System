<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole('donor');
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user_id'];

$studentStmt = $pdo->prepare("SELECT b.id, b.full_name, b.education_level, b.school_name, b.status, b.student_id, b.registered_at FROM beneficiary_sponsors bs JOIN beneficiaries b ON bs.beneficiary_id = b.id WHERE bs.sponsor_id = ? ORDER BY b.registered_at DESC");
$studentStmt->execute([$userId]);
$students = $studentStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRISUCODE | <?= __('My Sponsored Students') ?></title>
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header fade-in">
             <h2 style="font-family: 'Outfit'; font-weight: 800; color: #0f172a; margin: 0;"><i class="fa-solid fa-children" style="color: #ea580c; margin-right: 10px;"></i> <?= __('My Sponsored Students') ?></h2>
        </div>

        <div class="fade-in" style="padding: 0 40px; margin-bottom: 40px; animation-delay: 0.1s;">
            <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 30px; font-weight: 500;">
                <?= __('These are the individuals specifically assigned to your support network. You are changing their future.') ?>
            </p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
                <?php if(!empty($students)): foreach($students as $s): ?>
                    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; box-shadow: var(--card-shadow); overflow: hidden; transition: 0.3s; position: relative;" onmouseover="this.style.transform='translateY(-6px)'; this.style.borderColor='#ea580c';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#e2e8f0';">
                        
                        <div style="background: linear-gradient(135deg, #f97316, #ea580c); padding: 30px 20px; text-align: center; color: white;">
                            <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border: 4px solid #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin: 0 auto 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <?= strtoupper(substr($s['full_name'], 0, 1)) ?>
                            </div>
                            <h3 style="margin: 0; font-family: 'Outfit'; font-weight: 800; font-size: 1.4rem; letter-spacing: -0.01em;"><?= htmlspecialchars($s['full_name']) ?></h3>
                            <span style="display: inline-block; background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; margin-top: 8px;">
                                ID: <?= htmlspecialchars($s['student_id'] ?: 'Pending') ?>
                            </span>
                        </div>
                        
                        <div style="padding: 24px; display: flex; flex-direction: column; gap: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #64748b; font-weight: 600; font-size: 0.9rem;"><i class="fa-solid fa-graduation-cap" style="width: 20px;"></i> <?= __('Education') ?></span>
                                <strong style="color: #1e293b; font-size: 0.95rem;"><?= htmlspecialchars($s['education_level'] ?: 'N/A') ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #64748b; font-weight: 600; font-size: 0.9rem;"><i class="fa-solid fa-school" style="width: 20px;"></i> <?= __('School') ?></span>
                                <strong style="color: #1e293b; font-size: 0.95rem; text-align: right; max-width: 150px; text-wrap: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($s['school_name'] ?: 'Not Enrolled') ?>"><?= htmlspecialchars($s['school_name'] ?: 'Not Enrolled') ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #64748b; font-weight: 600; font-size: 0.9rem;"><i class="fa-solid fa-clock-rotate-left" style="width: 20px;"></i> <?= __('Joined') ?></span>
                                <strong style="color: #1e293b; font-size: 0.95rem;"><?= date('M Y', strtotime($s['registered_at'])) ?></strong>
                            </div>
                            
                            <div style="margin-top: 10px; padding-top: 15px; border-top: 1px dashed #e2e8f0; display: flex; flex-direction: column; gap: 10px;">
                                <span class="badge <?= ($s['status'] == 'active') ? 'success' : (($s['status'] == 'graduated') ? 'primary' : 'neutral') ?>" style="display: block; text-align: center; padding: 8px; font-weight: 800; font-size: 0.9rem;">
                                    <?= strtoupper($s['status']) ?>
                                </span>
                                <a href="student_details.php?id=<?= $s['id'] ?>" style="display: block; text-align: center; padding: 10px; background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; font-weight: 700; border-radius: 8px; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                    <i class="fa-solid fa-eye" style="margin-right: 5px;"></i> <?= __('View Full Profile') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div style="grid-column: 1 / -1; background: #fff; padding: 60px 40px; text-align: center; border-radius: 20px; border: 1px solid #e2e8f0;">
                        <i class="fa-solid fa-users" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px; display: block;"></i>
                        <h3 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; font-size: 1.5rem; margin: 0 0 10px;"><?= __('No Students Assigned Yet') ?></h3>
                        <p style="color: #64748b; font-size: 1.05rem; margin: 0; max-width: 500px; margin: 0 auto;"><?= __('The organization has not directly linked specific students to your profile yet. Once they allocate funds to direct sponsorships, students will appear here.') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
