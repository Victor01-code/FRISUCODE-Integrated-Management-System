<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['project_manager', 'super_admin', 'director']);

require_once __DIR__ . '/../config/db.php';

$pmName = $_SESSION['user_name'] ?? 'Project Manager';

// Fetch relevant stats for PM
// 1. Total Active Projects
$activeProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status='active'")->fetchColumn();

// 2. Beneficiaries
$activeBeneficiaries = $pdo->query("SELECT COUNT(*) FROM beneficiaries WHERE status='active'")->fetchColumn();
$totalGraduated = $pdo->query("SELECT COUNT(*) FROM beneficiaries WHERE status='graduated'")->fetchColumn();
$totalDropped = $pdo->query("SELECT COUNT(*) FROM beneficiaries WHERE status='dropped_out'")->fetchColumn();

// 3. Overall Budget (Active Projects)
$totalBudget = $pdo->query("SELECT SUM(budget) FROM projects WHERE status='active'")->fetchColumn();
if (!$totalBudget) $totalBudget = 0;

// 4. Pending / Planning Projects
$planningProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status='planning'")->fetchColumn();

// Recent Projects Feed
$recentProjects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Project Manager Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">

    <?php renderSidebar(); ?>

    <div class="main">

        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header" style="margin-bottom: 20px;">
            <h2><?= __('Project Management Command Center') ?></h2>
        </div>

        <!-- WELCOME SECTION -->
        <section class="welcome-section" style="margin-bottom: 30px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; padding: 30px; border-radius: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="font-size: 1.8rem; margin-bottom: 5px; font-family: 'Outfit';"><?= __('Welcome back') ?>, <?php echo htmlspecialchars($pmName); ?>! 📈</h2>
                    <p style="opacity: 0.9; font-size: 1rem;"><?= __('Here is the current status of our community programs and impact.') ?></p>
                </div>
                <a href="../projects/create.php" style="background: white; color: #1e3a8a; padding: 12px 24px; border-radius: 12px; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-plus"></i> <?= __('New Program') ?>
                </a>
            </div>
        </section>

        <?php include __DIR__ . '/../partials/time_picker.php'; ?>

        <!-- STATS -->
        <section class="stats-grid" style="margin-bottom: 30px;">
            <div class="stat-card" style="border-left: 4px solid #3b82f6;">
                <h4><?= __('Active Programs') ?></h4>
                <strong id="stat-active-programs" data-val="<?php echo $activeProjects; ?>"><?php echo $activeProjects; ?></strong>
                <span class="active"><i class="fa-solid fa-bolt"></i> <?= __('In Progress') ?></span>
            </div>

            <div class="stat-card" style="border-left: 4px solid #10b981;">
                <h4><?= __('Active Budget Allocation') ?></h4>
                <strong id="stat-budget" data-val="<?php echo $totalBudget; ?>">$<?php echo number_format($totalBudget, 2); ?></strong>
                <span class="neutral"><i class="fa-solid fa-money-bill-wave"></i> <?= __('Funding') ?></span>
            </div>

            <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
                <h4><?= __('Active Beneficiaries') ?></h4>
                <strong><?php echo number_format($activeBeneficiaries); ?></strong>
                <span class="neutral"><i class="fa-solid fa-users"></i> <?= __('Enrolled') ?></span>
            </div>

            <div class="stat-card" style="border-left: 4px solid #10b981;">
                <h4><?= __('Graduated Students') ?></h4>
                <strong><?php echo number_format($totalGraduated); ?></strong>
                <span class="active"><i class="fa-solid fa-graduation-cap"></i> <?= __('Successful') ?></span>
            </div>

            <div class="stat-card" style="border-left: 4px solid #dc2626;">
                <h4><?= __('Dropped Out Students') ?></h4>
                <strong><?php echo number_format($totalDropped); ?></strong>
                <span class="danger"><i class="fa-solid fa-circle-xmark"></i> <?= __('Removed') ?></span>
            </div>

            <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                <h4><?= __('In Planning Phase') ?></h4>
                <strong><?php echo $planningProjects; ?></strong>
                <span class="warning"><i class="fa-solid fa-clock"></i> <?= __('Upcoming') ?></span>
            </div>
        </section>

        <!-- CONTENT GRIDS -->
        <section class="charts-grid flow-row">
            
            <!-- Project Feed -->
            <div class="chart-card wide" style="grid-column: span 2;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3><?= __('Program Portfolio') ?></h3>
                    <a href="../projects/index.php" class="btn-sm btn-outline"><?= __('View All') ?></a>
                </div>
                
                <?php if (empty($recentProjects)): ?>
                    <div style="text-align: center; padding: 40px; color: #64748b; background: #f8fafc; border-radius: 16px; border: 2px dashed #e2e8f0;">
                        <i class="fa-solid fa-diagram-project" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                        <p><?= __('No programs exist yet. Time to launch an initiative.') ?></p>
                    </div>
                <?php else: ?>
                    <div style="width: 100%; max-height: 400px; overflow-y: auto; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 12px;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.8rem; text-transform: uppercase;">
                                    <th style="padding: 12px 0; min-width: 200px;"><?= __('Program Name') ?></th>
                                    <th style="min-width: 120px;"><?= __('Status') ?></th>
                                    <th style="min-width: 120px;"><?= __('Budget') ?></th>
                                    <th style="min-width: 100px;"><?= __('Timeline') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentProjects as $project): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 16px 0; font-weight: 600; color: #1e293b;">
                                            <a href="../projects/view.php?id=<?= $project['id'] ?>" style="color: inherit; text-decoration: none;">
                                                <?= htmlspecialchars($project['title']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php 
                                            // Badge coloring
                                            $badgeClass = 'neutral';
                                            if($project['status'] == 'active') $badgeClass = 'active';
                                            if($project['status'] == 'planning') $badgeClass = 'warning';
                                            if($project['status'] == 'cancelled') $badgeClass = 'danger';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($project['status']) ?></span>
                                        </td>
                                        <td style="font-weight: 600;">$<?= number_format($project['budget'], 2) ?></td>
                                        <td style="color: #64748b; font-size: 0.9rem;">
                                            <?= $project['start_date'] ? date('M Y', strtotime($project['start_date'])) : 'TBD' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions & Tasks -->
            <div class="chart-card">
                <h3 style="margin-bottom: 20px;"><?= __('Manager Tools') ?></h3>
                
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="../projects/create.php" style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; color: #1e293b; font-weight: 600; transition: 0.2s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-rocket"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.95rem;"><?= __('Launch Program') ?></div>
                            <div style="font-size: 0.8rem; color: #64748b; font-weight: 500;"><?= __('Draft a new initiative') ?></div>
                        </div>
                    </a>

                    <a href="../reports/index.php" style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; color: #1e293b; font-weight: 600; transition: 0.2s;" onmouseover="this.style.borderColor='#8b5cf6'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #f5f3ff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.95rem;"><?= __('Impact Reports') ?></div>
                            <div style="font-size: 0.8rem; color: #64748b; font-weight: 500;"><?= __('Analyze program success') ?></div>
                        </div>
                    </a>

                    <a href="../finance/index.php" style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; color: #1e293b; font-weight: 600; transition: 0.2s;" onmouseover="this.style.borderColor='#10b981'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.95rem;"><?= __('Budget Tracking') ?></div>
                            <div style="font-size: 0.8rem; color: #64748b; font-weight: 500;"><?= __('Monitor expenditures') ?></div>
                        </div>
                    </a>
                </div>
            </div>

        </section>

        <?php include __DIR__ . '/../partials/footer.php'; ?>

    </div>
</div>

</body>
</html>
