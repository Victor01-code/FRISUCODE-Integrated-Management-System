<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole('donor');
require_once __DIR__ . '/../config/db.php';

// Fetch all organization projects transparently for the donor
$projectsStmt = $pdo->query("SELECT * FROM projects ORDER BY FIELD(status, 'active', 'planning', 'completed', 'cancelled'), created_at DESC");
$projects = $projectsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRISUCODE | <?= __('Active Programs & Projects') ?></title>
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
             <h2 style="font-family: 'Outfit'; font-weight: 800; color: #0f172a; margin: 0;"><i class="fa-solid fa-diagram-project" style="color: #2563eb; margin-right: 10px;"></i> <?= __('Community Programs') ?></h2>
        </div>

        <div class="fade-in" style="padding: 0 40px; margin-bottom: 40px; animation-delay: 0.1s;">
            <p style="color: #64748b; font-size: 1.05rem; margin-bottom: 30px; font-weight: 500;">
                <?= __('Explore the various active funds, infrastructural projects, and broad community initiatives your generosity supports across the organization.') ?>
            </p>
            
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <?php if(!empty($projects)): foreach($projects as $p): ?>
                    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; box-shadow: var(--card-shadow); padding: 30px; transition: 0.3s; position: relative; overflow: hidden;" onmouseover="this.style.transform='translateX(8px)'; this.style.borderColor='#94a3b8';" onmouseout="this.style.transform='translateX(0)'; this.style.borderColor='#e2e8f0';">
                        
                        <?php if($p['status'] == 'active'): ?>
                            <div style="position: absolute; top: 0; left: 0; bottom: 0; width: 6px; background: #16a34a;"></div>
                        <?php elseif($p['status'] == 'planning'): ?>
                            <div style="position: absolute; top: 0; left: 0; bottom: 0; width: 6px; background: #3b82f6;"></div>
                        <?php elseif($p['status'] == 'completed'): ?>
                            <div style="position: absolute; top: 0; left: 0; bottom: 0; width: 6px; background: #8b5cf6;"></div>
                        <?php else: ?>
                            <div style="position: absolute; top: 0; left: 0; bottom: 0; width: 6px; background: #94a3b8;"></div>
                        <?php endif; ?>

                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-left: 10px;">
                            <div>
                                <h3 style="margin: 0 0 8px; font-family: 'Outfit'; font-weight: 800; font-size: 1.4rem; color: #1e293b;"><?= htmlspecialchars($p['title']) ?></h3>
                                <div style="display: flex; gap: 15px; color: #64748b; font-size: 0.9rem; font-weight: 600;">
                                    <span><i class="fa-regular fa-calendar-check" style="color: #10b981;"></i> <?= __('Launched:') ?> <?= date('M d, Y', strtotime($p['start_date'])) ?></span>
                                    <span><i class="fa-solid fa-bullseye" style="color: #6366f1;"></i> <?= __('Target Goal:') ?> $<?= number_format($p['budget']) ?></span>
                                </div>
                            </div>
                            
                            <span class="badge <?= ($p['status'] == 'active') ? 'success' : (($p['status'] == 'planning') ? 'primary' : (($p['status'] == 'completed') ? 'warning' : 'neutral')) ?>" style="font-weight: 800; padding: 6px 16px; border-radius: 12px; font-size: 0.85rem; text-transform: uppercase;">
                                <?= strtoupper($p['status']) ?>
                            </span>
                        </div>
                        
                        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; border: 1px dashed #cbd5e1; margin-left: 10px;">
                            <p style="margin: 0; color: #475569; font-size: 1rem; line-height: 1.7; font-family: 'Inter'; font-weight: 500; white-space: pre-wrap;"><?= htmlspecialchars($p['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div style="background: #fff; padding: 60px 40px; text-align: center; border-radius: 20px; border: 1px solid #e2e8f0;">
                        <i class="fa-solid fa-hammer" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px; display: block;"></i>
                        <h3 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; font-size: 1.5rem; margin: 0 0 10px;"><?= __('No Programs Listed') ?></h3>
                        <p style="color: #64748b; font-size: 1.05rem; margin: 0; max-width: 500px; margin: 0 auto;"><?= __('The organization has not registered any active projects into the system yet.') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
