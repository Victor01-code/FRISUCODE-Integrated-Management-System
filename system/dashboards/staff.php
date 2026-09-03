<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['staff', 'field_officer']);

require_once __DIR__ . '/../config/db.php';

$staffName = $_SESSION['user_name'] ?? 'Staff Member';

// Fetch relevant stats
$myProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status='active'")->fetchColumn();
$myBeneficiaries = $pdo->query("SELECT COUNT(*) FROM beneficiaries")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Staff Dashboard</title>
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

        <!-- WELCOME SECTION -->
        <section class="welcome-section">
            <h2><?= __('Welcome back') ?>, <?php echo htmlspecialchars($staffName); ?>!</h2>
            <p><?= __('Your work today makes a difference.') ?></p>
        </section>

        <!-- STATS -->
        <section class="stats-grid">
            <div class="stat-card">
                <h4><?= __('Assigned Tasks') ?></h4>
                <strong>3</strong> <!-- Placeholder for now -->
                <span class="warning"><?= __('Pending') ?></span>
            </div>

            <div class="stat-card">
                <h4><?= __('Active Projects') ?></h4>
                <strong><?php echo $myProjects; ?></strong>
                <span class="active"><?= __('Ongoing') ?></span>
            </div>

            <div class="stat-card">
                <h4><?= __('Beneficiaries Managed') ?></h4>
                <strong><?php echo $myBeneficiaries; ?></strong>
                <span class="neutral"><?= __('Students') ?></span>
            </div>
        </section>

        <!-- CHARTS / CONTENT -->
        <section class="charts-grid flow-row">
            <div class="chart-card wide">
                <h3><?= __('My Tasks & Schedule') ?></h3>
                <div class="task-list">
                    <div class="task-item">
                        <span class="status pending"><?= __('Pending') ?></span>
                        <p>Submit monthly report for 'Clean Water Initiative'</p>
                        <span class="date">Due: Feb 15</span>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <h3><?= __('Quick Actions') ?></h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;"><a href="../beneficiaries/create.php"> &rarr; <?= __('Register New Beneficiary') ?></a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="../projects/index.php"> &rarr; <?= __('Update Project Status') ?></a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="../reports/create.php"> &rarr; <?= __('File Field Report') ?></a></li>
                </ul>
            </div>
        </section>

        <?php include __DIR__ . '/../partials/footer.php'; ?>

    </div>
</div>

</body>
</html>
