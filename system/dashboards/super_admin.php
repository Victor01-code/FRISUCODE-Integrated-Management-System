<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'admin', 'director']);
require_once __DIR__ . '/../config/db.php';

// Fetch Core Stats
$stats = [
    'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'beneficiaries' => $pdo->query("SELECT COUNT(*) FROM beneficiaries WHERE status='active'")->fetchColumn(),
    'graduated' => $pdo->query("SELECT COUNT(*) FROM beneficiaries WHERE status='graduated'")->fetchColumn(),
    'dropped_out' => $pdo->query("SELECT COUNT(*) FROM beneficiaries WHERE status='dropped_out'")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'sponsors' => $pdo->query("SELECT COUNT(*) FROM sponsors")->fetchColumn(),
    'budget' => $pdo->query("SELECT SUM(budget) FROM projects")->fetchColumn(),
    'donations' => $pdo->query("SELECT SUM(amount) FROM public_donations")->fetchColumn()
];

// Fetch Financial Summary
$finance = [
    'income' => $pdo->query("SELECT SUM(amount) FROM finance_records WHERE type='income'")->fetchColumn() ?? 0,
    'expense' => $pdo->query("SELECT SUM(amount) FROM finance_records WHERE type='expense'")->fetchColumn() ?? 0
];
$netBalance = $finance['income'] - $finance['expense'];

// Fetch Recent Beneficiaries
$recentBeneficiaries = $pdo->query("SELECT full_name, education_level, registered_at FROM beneficiaries ORDER BY registered_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Recent Public Donations
$recentDonations = $pdo->query("SELECT full_name, amount, created_at FROM public_donations ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$adminName = $_SESSION['user_name'] ?? 'Admin User';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Super Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .recent-list { list-style: none; padding: 0; margin: 0; }
        .recent-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .recent-item:last-child { border-bottom: none; }
        .recent-info div { font-weight: 600; font-size: 0.9rem; }
        .recent-info small { color: #64748b; font-size: 0.8rem; }
        .recent-amount { font-weight: 700; color: #16a34a; }
        .recent-amount.expense { color: #dc2626; }
        .quick-btn { background: #eff6ff; color: #2061eb; padding: 10px 15px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; text-decoration: none; border: 1px solid #dbeafe; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
        .quick-btn:hover { background: #dbeafe; transform: translateY(-2px); }
        .summary-card { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 15px; }
        .summary-item label { opacity: 0.8; font-size: 0.85rem; display: block; margin-bottom: 5px; }
        .summary-item strong { font-size: 1.5rem; }
    </style>
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header fade-in" style="margin-bottom: 0;">
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Executive Dashboard') ?></h2>
            <div style="font-weight: 600; color: #64748b;"><?php echo date('l, F j, Y'); ?></div>
        </div>

        <div style="margin: 30px 40px 0;">
            <?php include __DIR__ . '/../partials/time_picker.php'; ?>
        </div>

        <section class="summary-card fade-in" style="margin: 30px 40px; animation-delay: 0.1s;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0; font-family: 'Outfit'; font-size: 2rem;"><?= __('Welcome back') ?>, <?php echo htmlspecialchars($adminName); ?>!</h2>
                    <p style="margin: 5px 0 0; opacity: 0.9;"><?= __('Here is the global overview of your organization.') ?></p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 0.9rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;"><?= __('Total Funding') ?></div>
                    <div style="font-size: 2.5rem; font-family: 'Outfit'; font-weight: 800;" id="stat-income" data-val="<?= $finance['income'] ?>">$<?php echo number_format($finance['income'], 2); ?></div>
                </div>
            </div>
            
            <div class="summary-grid">
                <div class="summary-item">
                    <label><i class="fa-solid fa-diagram-project"></i> <?= __('Active Projects') ?></label>
                    <strong id="stat-active-programs" data-val="<?php echo $stats['projects']; ?>"><?php echo number_format($stats['projects']); ?></strong>
                </div>
                <div class="summary-item">
                    <label><i class="fa-solid fa-users"></i> <?= __('Active Beneficiaries') ?></label>
                    <strong><?php echo number_format($stats['beneficiaries']); ?></strong>
                </div>
                <div class="summary-item">
                    <label><i class="fa-solid fa-graduation-cap"></i> <?= __('Graduated Students') ?></label>
                    <strong><?php echo number_format($stats['graduated']); ?></strong>
                </div>
                <div class="summary-item">
                    <label><i class="fa-solid fa-circle-xmark"></i> <?= __('Dropped Out Students') ?></label>
                    <strong><?php echo number_format($stats['dropped_out']); ?></strong>
                </div>
                <div class="summary-item">
                    <label><i class="fa-solid fa-hand-holding-heart"></i> <?= __('Sponsors') ?></label>
                    <strong><?php echo number_format($stats['sponsors']); ?></strong>
                </div>
                <div class="summary-item">
                    <label><i class="fa-solid fa-user-shield"></i> <?= __('System Users') ?></label>
                    <strong><?php echo number_format($stats['users']); ?></strong>
                </div>
                <div class="summary-item">
                    <label><i class="fa-solid fa-money-bill-wave"></i> <?= __('Project Budget') ?></label>
                    <strong id="stat-budget" data-val="<?php echo $stats['budget']; ?>">$<?php echo number_format($stats['budget'], 2); ?></strong>
                </div>
                <div class="summary-item">
                    <label><i class="fa-solid fa-hand-holding-dollar"></i> <?= __('Public Donations') ?></label>
                    <strong id="stat-donations" data-val="<?php echo $stats['donations']; ?>">$<?php echo number_format($stats['donations'], 2); ?></strong>
                </div>
            </div>
        </section>

        <section class="charts-grid flow-row" style="margin: 0 40px;">
            <div class="chart-card fade-in" style="animation-delay: 0.5s;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h3 style="margin: 0; font-family: 'Outfit';"><?= __('Recent Public Donations') ?></h3>
                    <a href="../donations/index.php" class="btn-primary" style="padding: 6px 14px; font-size: 0.8rem;"><?= __('View All') ?></a>
                </div>
                <ul class="recent-list">
                    <?php if(!empty($recentDonations)): foreach($recentDonations as $d): ?>
                        <li class="recent-item" style="padding: 14px 0;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #64748b;">
                                    <?php echo strtoupper(substr($d['full_name'], 0, 1)); ?>
                                </div>
                                <div class="recent-info">
                                    <div style="font-weight: 700;"><?php echo htmlspecialchars($d['full_name']); ?></div>
                                    <small><?php echo date('M d, Y', strtotime($d['created_at'])); ?></small>
                                </div>
                            </div>
                            <div class="recent-amount" style="font-family: 'Outfit'; font-weight: 800; font-size: 1.1rem;">+$<?php echo number_format($d['amount'], 2); ?></div>
                        </li>
                    <?php endforeach; else: ?>
                        <li style="text-align: center; color: #64748b; padding: 40px; border: 2px dashed #e2e8f0; border-radius: 16px;">
                            <i class="fa-solid fa-box-open" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                            <?= __('No recent donations.') ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="chart-card fade-in" style="animation-delay: 0.7s;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h3 style="margin: 0; font-family: 'Outfit';"><?= __('Newly Registered Students') ?></h3>
                    <a href="../beneficiaries/index.php" class="btn-primary" style="padding: 6px 14px; font-size: 0.8rem; background: #6366f1;"><?= __('Full List') ?></a>
                </div>
                <ul class="recent-list">
                    <?php if(!empty($recentBeneficiaries)): foreach($recentBeneficiaries as $b): ?>
                        <li class="recent-item" style="padding: 14px 0;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: #fff7ed; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #ea580c;">
                                    <?php echo strtoupper(substr($b['full_name'], 0, 1)); ?>
                                </div>
                                <div class="recent-info">
                                    <div style="font-weight: 700;"><?php echo htmlspecialchars($b['full_name']); ?></div>
                                    <small><?php echo htmlspecialchars($b['education_level']); ?></small>
                                </div>
                            </div>
                            <div style="font-size: 0.8rem; font-weight: 600; color: #94a3b8; background: #f8fafc; padding: 4px 10px; border-radius: 8px;">
                                <?php echo date('M d', strtotime($b['registered_at'])); ?>
                            </div>
                        </li>
                    <?php endforeach; else: ?>
                        <li style="text-align: center; color: #64748b; padding: 40px; border: 2px dashed #e2e8f0; border-radius: 16px;">
                            <i class="fa-solid fa-user-slash" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                            <?= __('No students registered yet.') ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </section>

        <section class="chart-card fade-in" style="margin: 25px 40px 40px; animation-delay: 0.8s;">
            <h3 style="font-family: 'Outfit'; font-weight: 800; margin-bottom: 20px;"><?= __('Quick Management Actions') ?></h3>
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <a href="../projects/create.php" class="quick-btn" style="background: white; border: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb; margin-bottom: 10px;"><i class="fa-solid fa-diagram-project"></i></div>
                    <span style="font-weight: 700; color: #1e293b; display: block;"><?= __('New Project') ?></span>
                    <small style="color: #64748b; font-weight: 500;"><?= __('Launch a program') ?></small>
                </a>
                <a href="../beneficiaries/create.php" class="quick-btn" style="background: white; border: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #fff7ed; display: flex; align-items: center; justify-content: center; color: #ea580c; margin-bottom: 10px;"><i class="fa-solid fa-user-plus"></i></div>
                    <span style="font-weight: 700; color: #1e293b; display: block;"><?= __('Add Student') ?></span>
                    <small style="color: #64748b; font-weight: 500;"><?= __('Register a beneficiary') ?></small>
                </a>
                <a href="../finance/create.php" class="quick-btn" style="background: white; border: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a; margin-bottom: 10px;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <span style="font-weight: 700; color: #1e293b; display: block;"><?= __('Treasury Entry') ?></span>
                    <small style="color: #64748b; font-weight: 500;"><?= __('Income or Expense') ?></small>
                </a>
                <a href="../users/create.php" class="quick-btn" style="background: white; border: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #f5f3ff; display: flex; align-items: center; justify-content: center; color: #6366f1; margin-bottom: 10px;"><i class="fa-solid fa-user-shield"></i></div>
                    <span style="font-weight: 700; color: #1e293b; display: block;"><?= __('Create Staff') ?></span>
                    <small style="color: #64748b; font-weight: 500;"><?= __('Manage system access') ?></small>
                </a>
            </div>
        </section>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
