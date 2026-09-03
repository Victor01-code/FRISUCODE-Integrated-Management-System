<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole('donor');
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user_id'];

// Get donor email
$userQuery = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$userQuery->execute([$userId]);
$donorEmail = $userQuery->fetchColumn();

// 1. Sponsored Students Count
$studentCountStmt = $pdo->prepare("SELECT COUNT(*) FROM beneficiary_sponsors WHERE sponsor_id = ?");
$studentCountStmt->execute([$userId]);
$sponsoredStudentsCount = $studentCountStmt->fetchColumn() ?: 0;

// 2. Active Projects Total
$activeProjectsTotal = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'active'")->fetchColumn() ?: 0;

// 3. Total Funding Committed
$sumStmt = $pdo->prepare("SELECT SUM(amount) FROM public_donations WHERE email = ? AND status = 'completed'");
$sumStmt->execute([$donorEmail]);
$sum = $sumStmt->fetchColumn() ?: 0;

// Fetch News/Updates as "Impact Updates"
$updates = $pdo->query("SELECT title, content, published_date FROM news ORDER BY published_date DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRISUCODE | <?= __('My Impact Report') ?></title>
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
             <h2 style="font-family: 'Outfit'; font-weight: 800; color: #0f172a; margin: 0;"><i class="fa-solid fa-chart-pie" style="color: #3b82f6; margin-right: 10px;"></i> <?= __('Personal Impact Overview') ?></h2>
        </div>

        <div class="form-container fade-in" style="max-width: 1000px; margin: 20px 40px; padding: 40px; border-radius: 24px; animation-delay: 0.1s;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h3 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; font-size: 2rem; margin: 0 0 10px;"><?= __('Your Generosity in Numbers') ?></h3>
                <p style="color: #64748b; font-size: 1.1rem; max-width: 600px; margin: 0 auto;"><?= __('Below represents the verified outcome driven by your active contributions through FRISUCODE.') ?></p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 50px;">
                <div style="background: #fff7ed; padding: 30px; border-radius: 20px; border: 1px solid #ffedd5; text-align: center;">
                    <div style="width: 64px; height: 64px; background: #ea580c; color: #fff; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                        <i class="fa-solid fa-children"></i>
                    </div>
                    <strong style="color: #ea580c; font-size: 3rem; font-weight: 800; display: block; line-height: 1; font-family: 'Outfit';"><?= $sponsoredStudentsCount ?></strong>
                    <span style="color: #9a3412; font-weight: 700; font-size: 1rem; display: block; margin-top: 10px;"><?= __('Lives Sponsored') ?></span>
                </div>
                
                <div style="background: #f0fdf4; padding: 30px; border-radius: 20px; border: 1px solid #dcfce7; text-align: center;">
                    <div style="width: 64px; height: 64px; background: #16a34a; color: #fff; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <strong style="color: #16a34a; font-size: 3rem; font-weight: 800; display: block; line-height: 1; font-family: 'Outfit';"><?= $activeProjectsTotal ?></strong>
                    <span style="color: #14532d; font-weight: 700; font-size: 1rem; display: block; margin-top: 10px;"><?= __('Organizations Programs Benefiting') ?></span>
                </div>

                <div style="background: #eff6ff; padding: 30px; border-radius: 20px; border: 1px solid #dbeafe; text-align: center;">
                    <div style="width: 64px; height: 64px; background: #2563eb; color: #fff; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px;">
                        <i class="fa-solid fa-medal"></i>
                    </div>
                    <strong style="color: #2563eb; font-size: 2.2rem; font-weight: 800; display: block; line-height: 1.3; font-family: 'Outfit';">$<?= number_format($sum) ?></strong>
                    <span style="color: #1e40af; font-weight: 700; font-size: 1rem; display: block; margin-top: 10px;"><?= __('Total Funding Committed') ?></span>
                </div>
            </div>

            <!-- Program Updates / News -->
            <h3 style="font-family: 'Outfit'; font-weight: 800; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 25px;"><i class="fa-solid fa-bullhorn" style="color: #dc2626;"></i> <?= __('Recent Program Discoveries & News') ?></h3>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php if(!empty($updates)): foreach($updates as $news): ?>
                    <div style="padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; background: #fff; transition: 0.3s;" onmouseover="this.style.borderColor='#94a3b8'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <h4 style="margin: 0; font-family: 'Outfit'; font-weight: 800; font-size: 1.2rem; color: #0f172a;"><?= htmlspecialchars($news['title']) ?></h4>
                            <span class="badge" style="background: #f1f5f9; color: #64748b;"><i class="fa-regular fa-calendar"></i> <?= date('M d, Y', strtotime($news['published_date'])) ?></span>
                        </div>
                        <p style="margin: 0; color: #475569; line-height: 1.6;"><?= nl2br(htmlspecialchars($news['content'])) ?></p>
                    </div>
                <?php endforeach; else: ?>
                    <div style="text-align: center; color: #94a3b8; padding: 30px;">
                        <p><?= __('No recent updates exist yet.') ?></p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
