<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'admin', 'staff', 'director', 'project_manager']);
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Reports & Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            padding: 32px;
        }
        .report-card {
            background: #ffffff;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .report-card:hover {
            border-color: #2563eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .report-icon {
            width: 48px;
            height: 48px;
            background: #f1f5f9;
            color: #2563eb;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 20px;
        }
        .report-card h3 {
            font-size: 1.1rem;
            margin: 0 0 10px;
            color: #1e293b;
        }
        .report-card p {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .report-actions {
            display: flex;
            gap: 10px;
        }
        .btn-outline-sm {
            padding: 6px 12px;
            border: 1px solid #e2e8f0;
            background: transparent;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-outline-sm:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }
    </style>
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header" style="margin-bottom: 0;">
            <h2><?= __('Impact Reports & Analytics') ?></h2>
            <button class="btn-sm"><i class="fa-solid fa-wand-magic-sparkles"></i> <?= __('Custom Report Builder') ?></button>
        </div>

        <section class="reports-grid">
            
            <div class="report-card">
                <div>
                    <div class="report-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h3><?= __('Beneficiary Progress') ?></h3>
                    <p><?= __('Detailed performance tracking of sponsored students across all regions.') ?></p>
                </div>
                <div class="report-actions">
                    <a href="export.php?type=beneficiaries" class="btn-sm"><i class="fa-solid fa-download"></i> <?= __('Download CSV') ?></a>
                </div>
            </div>

            <div class="report-card">
                <div>
                    <div class="report-icon" style="color: #6366f1; background: #eef2ff;"><i class="fa-solid fa-award"></i></div>
                    <h3><?= __('Graduate Professionals') ?></h3>
                    <p><?= __('Report on graduated beneficiaries successfully practicing their professionals.') ?></p>
                </div>
                <div class="report-actions">
                    <a href="export.php?type=graduates" class="btn-sm" style="background: #6366f1;"><i class="fa-solid fa-download"></i> <?= __('Download CSV') ?></a>
                </div>
            </div>

            <div class="report-card">
                <div>
                    <div class="report-icon" style="color: #16a34a; background: #f0fdf4;"><i class="fa-solid fa-money-bill-trend-up"></i></div>
                    <h3><?= __('Financial Audit') ?></h3>
                    <p><?= __('Breakdown of organizational spending vs funding goals for the current quarter.') ?></p>
                </div>
                <div class="report-actions">
                    <a href="export.php?type=finance" class="btn-sm" style="background: #16a34a;"><i class="fa-solid fa-download"></i> <?= __('Download CSV') ?></a>
                </div>
            </div>

            <div class="report-card">
                <div>
                    <div class="report-icon" style="color: #ea580c; background: #fff7ed;"><i class="fa-solid fa-hand-holding-heart"></i></div>
                    <h3><?= __('Donor Retention') ?></h3>
                    <p><?= __('Analysis of donor churn rate and recurrence metrics for sustainable planning.') ?></p>
                </div>
                <div class="report-actions">
                    <a href="export.php?type=donors" class="btn-sm" style="background: #ea580c;"><i class="fa-solid fa-download"></i> <?= __('Download CSV') ?></a>
                </div>
            </div>

            <div class="report-card">
                <div>
                    <div class="report-icon" style="color: #6366f1; background: #eef2ff;"><i class="fa-solid fa-chart-pie"></i></div>
                    <h3><?= __('Project Impact Score') ?></h3>
                    <p><?= __('Visual representation of community development ROI for ongoing programs.') ?></p>
                </div>
                <div class="report-actions">
                    <a href="export.php?type=projects" class="btn-sm" style="background: #6366f1;"><i class="fa-solid fa-download"></i> <?= __('Download CSV') ?></a>
                </div>
            </div>

        </section>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
