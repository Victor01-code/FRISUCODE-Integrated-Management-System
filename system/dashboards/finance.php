<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'finance', 'director']);
require_once __DIR__ . '/../config/db.php';

// ── Core financial totals ──────────────────────────────────────────────────
$income  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM finance_records WHERE type='income'")->fetchColumn();
$expense = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM finance_records WHERE type='expense'")->fetchColumn();
$netBalance = $income - $expense;

// ── Public donations total ─────────────────────────────────────────────────
$pubDonations = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM public_donations")->fetchColumn();

// ── Project budget vs actual spend ────────────────────────────────────────
$projects = $pdo->query("SELECT title, budget FROM projects WHERE budget > 0 ORDER BY budget DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

// ── Monthly income/expense for the last 6 months ──────────────────────────
$monthlyData = [];
for ($i = 5; $i >= 0; $i--) {
    $month    = date('Y-m', strtotime("-$i months"));
    $label    = date('M Y', strtotime("-$i months"));
    $inc  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM finance_records WHERE type='income'  AND DATE_FORMAT(date,'%Y-%m')='$month'")->fetchColumn();
    $exp  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM finance_records WHERE type='expense' AND DATE_FORMAT(date,'%Y-%m')='$month'")->fetchColumn();
    $monthlyData[] = ['label' => $label, 'income' => (float)$inc, 'expense' => (float)$exp];
}

// ── Recent 10 transactions ─────────────────────────────────────────────────
$recentTx = $pdo->query("SELECT * FROM finance_records ORDER BY date DESC, created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// ── Donations count ────────────────────────────────────────────────────────
$donationCount = $pdo->query("SELECT COUNT(*) FROM public_donations")->fetchColumn();

$adminName = $_SESSION['user_name'] ?? 'Finance';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Finance Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* ── Finance-specific styles ── */
        .fin-hero {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 100%);
            border-radius: 20px;
            padding: 32px 40px;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 32px;
        }
        .fin-hero::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 220px; height: 220px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .fin-hero::after {
            content: '';
            position: absolute;
            bottom: -60px; right: 80px;
            width: 160px; height: 160px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .fin-hero h2 { font-family: 'Outfit'; font-size: 1.6rem; font-weight: 800; margin: 0 0 6px; }
        .fin-hero p  { opacity: 0.8; margin: 0; font-size: 0.95rem; }
        .fin-hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 30px;
            padding: 4px 14px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .fin-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }
        .fin-kpi {
            background: #fff;
            border-radius: 18px;
            padding: 24px 22px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .fin-kpi:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .fin-kpi-icon {
            width: 46px; height: 46px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 16px;
        }
        .fin-kpi-label { font-size: 0.72rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
        .fin-kpi-value { font-family: 'Outfit'; font-size: 1.85rem; font-weight: 800; line-height: 1; margin-bottom: 8px; }
        .fin-kpi-sub   { font-size: 0.78rem; font-weight: 700; display: flex; align-items: center; gap: 5px; }
        .fin-charts-grid {
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 24px;
            margin-bottom: 28px;
        }
        .fin-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid #f1f5f9;
        }
        .fin-card-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 22px;
        }
        .fin-card-title { font-family: 'Outfit'; font-size: 1.05rem; font-weight: 800; color: #1e293b; margin: 0; }
        .fin-card-sub   { font-size: 0.78rem; color: #94a3b8; font-weight: 600; margin-top: 2px; }
        .fin-bottom-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }
        .tx-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 11px 0;
            border-bottom: 1px solid #f8fafc;
        }
        .tx-row:last-child { border-bottom: none; }
        .tx-info strong { font-size: 0.9rem; color: #1e293b; font-weight: 700; display: block; }
        .tx-info small  { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }
        .tx-badge {
            font-size: 0.7rem; font-weight: 800;
            text-transform: uppercase;
            padding: 3px 9px; border-radius: 20px;
            letter-spacing: 0.04em;
        }
        .tx-income  { background: #f0fdf4; color: #16a34a; }
        .tx-expense { background: #fef2f2; color: #dc2626; }
        .tx-amount-income  { font-family: 'Outfit'; font-weight: 800; color: #16a34a; font-size: 1rem; }
        .tx-amount-expense { font-family: 'Outfit'; font-weight: 800; color: #dc2626; font-size: 1rem; }
        .quick-action-btn {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 18px; border-radius: 14px;
            background: #f8fafc; border: 1px solid #f1f5f9;
            text-decoration: none; color: #334155;
            font-weight: 700; font-size: 0.88rem;
            transition: 0.2s; margin-bottom: 10px;
        }
        .quick-action-btn:last-child { margin-bottom: 0; }
        .quick-action-btn:hover { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; transform: translateX(4px); }
        .quick-action-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .budget-bar-wrap { margin-bottom: 14px; }
        .budget-bar-label { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .budget-bar-label span { font-size: 0.8rem; font-weight: 700; color: #475569; }
        .budget-bar-label strong { font-size: 0.8rem; font-weight: 800; color: #1e293b; font-family: 'Outfit'; }
        .budget-bar-track { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
        .budget-bar-fill  { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #3b82f6, #60a5fa); transition: width 0.8s ease; }
        @media (max-width: 1100px) {
            .fin-kpi-grid    { grid-template-columns: repeat(2, 1fr); }
            .fin-charts-grid { grid-template-columns: 1fr; }
            .fin-bottom-grid { grid-template-columns: 1fr; }
        }
    </style>
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div style="padding: 32px 40px 0;">

            <!-- ── HERO BAR ── -->
            <div class="fin-hero fade-in">
                <div class="fin-hero-badge"><i class="fa-solid fa-shield-halved"></i> <?= __('Finance Control Center') ?></div>
                <h2><?= __('Welcome back') ?>, <?= htmlspecialchars($adminName) ?>! 📊</h2>
                <p><?= __('Here is a real-time snapshot of all organizational financial activity.') ?></p>
                <div style="display: flex; gap: 16px; margin-top: 20px; flex-wrap: wrap;">
                    <a href="/frisucode_ms/system/finance/create.php" style="background: rgba(255,255,255,0.2); color: #fff; padding: 10px 20px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 0.88rem; border: 1px solid rgba(255,255,255,0.25); backdrop-filter: blur(4px);">
                        <i class="fa-solid fa-plus"></i> <?= __('Log New Transaction') ?>
                    </a>
                    <a href="/frisucode_ms/system/finance/index.php" style="background: rgba(255,255,255,0.1); color: #fff; padding: 10px 20px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 0.88rem; border: 1px solid rgba(255,255,255,0.15);">
                        <i class="fa-solid fa-book-open"></i> <?= __('Full Finance Log') ?>
                    </a>
                    <a href="/frisucode_ms/system/reports/index.php" style="background: rgba(255,255,255,0.1); color: #fff; padding: 10px 20px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 0.88rem; border: 1px solid rgba(255,255,255,0.15);">
                        <i class="fa-solid fa-chart-pie"></i> <?= __('Impact Reports') ?>
                    </a>
                </div>
            </div>

            <?php include __DIR__ . '/../partials/time_picker.php'; ?>

            <!-- ── KPI CARDS ── -->
            <div class="fin-kpi-grid fade-in" style="animation-delay:0.05s;">

                <!-- Total Income -->
                <div class="fin-kpi">
                    <div class="fin-kpi-icon" style="background:#f0fdf4; color:#16a34a;">
                        <i class="fa-solid fa-circle-arrow-up"></i>
                    </div>
                    <div class="fin-kpi-label"><?= __('Total Income') ?></div>
                    <div class="fin-kpi-value" id="stat-income" data-val="<?= $income ?>" style="color:#16a34a;">$<?= number_format($income, 2) ?></div>
                    <div class="fin-kpi-sub" style="color:#16a34a;">
                        <i class="fa-solid fa-caret-up"></i> <?= __('All-time Inflow') ?>
                    </div>
                </div>

                <!-- Total Expenses -->
                <div class="fin-kpi">
                    <div class="fin-kpi-icon" style="background:#fef2f2; color:#dc2626;">
                        <i class="fa-solid fa-circle-arrow-down"></i>
                    </div>
                    <div class="fin-kpi-label"><?= __('Total Expenses') ?></div>
                    <div class="fin-kpi-value" id="stat-expenses" data-val="<?= $expense ?>" style="color:#dc2626;">$<?= number_format($expense, 2) ?></div>
                    <div class="fin-kpi-sub" style="color:#dc2626;">
                        <i class="fa-solid fa-caret-down"></i> <?= __('Organization Expenses') ?>
                    </div>
                </div>

                <!-- Net Balance -->
                <?php $balColor = $netBalance >= 0 ? '#1e40af' : '#dc2626'; ?>
                <div class="fin-kpi" style="background: linear-gradient(135deg, #eff6ff, #fff);">
                    <div class="fin-kpi-icon" style="background:#dbeafe; color:#1e40af;">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                    <div class="fin-kpi-label"><?= __('Net Balance') ?></div>
                    <div class="fin-kpi-value" id="stat-net-balance" data-val="<?= $netBalance ?>" style="color:<?= $balColor ?>;">
                        <?= $netBalance >= 0 ? '+' : '' ?>$<?= number_format(abs($netBalance), 2) ?>
                    </div>
                    <div class="fin-kpi-sub" style="color:#3b82f6;">
                        <i class="fa-solid fa-shield-check"></i> <?= __('Verified Balance') ?>
                    </div>
                </div>

                <!-- Public Donations -->
                <div class="fin-kpi">
                    <div class="fin-kpi-icon" style="background:#fdf4ff; color:#a21caf;">
                        <i class="fa-solid fa-hand-holding-heart"></i>
                    </div>
                    <div class="fin-kpi-label"><?= __('Public Donations') ?></div>
                    <div class="fin-kpi-value" id="stat-donations" data-val="<?= $pubDonations ?>" style="color:#a21caf;">$<?= number_format($pubDonations, 2) ?></div>
                    <div class="fin-kpi-sub" style="color:#a21caf;">
                        <i class="fa-solid fa-globe"></i> <span id="stat-donations-count" data-val="<?= $donationCount ?>"><?= $donationCount ?></span> <?= __('From Website') ?>
                    </div>
                </div>

            </div>

            <!-- ── CHART ROW ── -->
            <div class="fin-charts-grid fade-in" style="animation-delay:0.1s;">

                <!-- Monthly Trend -->
                <div class="fin-card">
                    <div class="fin-card-header">
                        <div>
                            <p class="fin-card-title"><?= __('Monthly Income vs Expenses') ?></p>
                            <p class="fin-card-sub"><?= __('Last 6 months financial trend') ?></p>
                        </div>
                        <span style="font-size:0.75rem; color:#94a3b8; font-weight:700; background:#f8fafc; padding:4px 10px; border-radius:8px;">
                            <i class="fa-solid fa-clock-rotate-left"></i> <?= __('Live') ?>
                        </span>
                    </div>
                    <canvas id="monthlyChart" height="120"></canvas>
                </div>

                <!-- Income vs Expense Doughnut -->
                <div class="fin-card" style="display:flex; flex-direction:column;">
                    <div class="fin-card-header">
                        <div>
                            <p class="fin-card-title"><?= __('Fund Distribution') ?></p>
                            <p class="fin-card-sub"><?= __('Income vs Expenses ratio') ?></p>
                        </div>
                    </div>
                    <div style="flex:1; display:flex; align-items:center; justify-content:center; position:relative;">
                        <canvas id="donutChart" width="200" height="200"></canvas>
                    </div>
                    <div style="display:flex; justify-content:center; gap:24px; margin-top:16px;">
                        <div style="display:flex;align-items:center;gap:7px;font-size:0.82rem;font-weight:700;color:#475569;">
                            <span style="width:12px;height:12px;border-radius:4px;background:#16a34a;display:inline-block;"></span>
                            <?= __('Income') ?>
                        </div>
                        <div style="display:flex;align-items:center;gap:7px;font-size:0.82rem;font-weight:700;color:#475569;">
                            <span style="width:12px;height:12px;border-radius:4px;background:#dc2626;display:inline-block;"></span>
                            <?= __('Expenses') ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── BOTTOM ROW ── -->
            <div class="fin-bottom-grid fade-in" style="animation-delay:0.15s;">

                <!-- Recent Transactions -->
                <div class="fin-card">
                    <div class="fin-card-header">
                        <div>
                            <p class="fin-card-title"><?= __('Recent Transactions') ?></p>
                            <p class="fin-card-sub"><?= __('Latest 10 ledger entries') ?></p>
                        </div>
                        <a href="/frisucode_ms/system/finance/index.php" style="font-size:0.8rem; font-weight:700; color:#3b82f6; text-decoration:none;">
                            <?= __('View All') ?> <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <?php if (count($recentTx) > 0): ?>
                        <?php foreach ($recentTx as $tx): ?>
                            <?php $isIncome = ($tx['type'] === 'income'); ?>
                            <div class="tx-row">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:38px; height:38px; border-radius:10px; background:<?= $isIncome ? '#f0fdf4' : '#fef2f2' ?>; display:flex; align-items:center; justify-content:center; color:<?= $isIncome ? '#16a34a' : '#dc2626' ?>; flex-shrink:0;">
                                        <i class="fa-solid <?= $isIncome ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' ?>"></i>
                                    </div>
                                    <div class="tx-info">
                                        <strong><?= htmlspecialchars(mb_strimwidth($tx['description'], 0, 38, '…')) ?></strong>
                                        <small><?= date('M d, Y', strtotime($tx['date'])) ?></small>
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                                    <span class="tx-badge <?= $isIncome ? 'tx-income' : 'tx-expense' ?>"><?= $isIncome ? __('Income') : __('Expense') ?></span>
                                    <span class="<?= $isIncome ? 'tx-amount-income' : 'tx-amount-expense' ?>">
                                        <?= $isIncome ? '+' : '-' ?>$<?= number_format($tx['amount'], 2) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align:center; padding:50px 20px; color:#94a3b8;">
                            <i class="fa-solid fa-file-invoice-dollar" style="font-size:2.5rem; color:#e2e8f0; display:block; margin-bottom:12px;"></i>
                            <p style="font-weight:600;"><?= __('No transactions recorded yet.') ?></p>
                            <a href="/frisucode_ms/system/finance/create.php" class="btn-primary" style="margin-top:12px; display:inline-flex;"><?= __('Record First Entry') ?></a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right column -->
                <div style="display:flex; flex-direction:column; gap:24px;">

                    <!-- Quick Actions -->
                    <div class="fin-card">
                        <div class="fin-card-header">
                            <p class="fin-card-title"><?= __('Finance Quick Actions') ?></p>
                        </div>
                        <a href="/frisucode_ms/system/finance/create.php" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fa-solid fa-plus-circle"></i></div>
                            <div>
                                <div><?= __('Log New Transaction') ?></div>
                                <div style="font-size:0.74rem; color:#94a3b8; font-weight:600;"><?= __('Income or Expense') ?></div>
                            </div>
                        </a>
                        <a href="/frisucode_ms/system/finance/index.php" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fa-solid fa-book-open"></i></div>
                            <div>
                                <div><?= __('Full Finance Log') ?></div>
                                <div style="font-size:0.74rem; color:#94a3b8; font-weight:600;"><?= __('Consolidated Ledger') ?></div>
                            </div>
                        </a>
                        <a href="/frisucode_ms/system/donations/index.php" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:#fdf4ff; color:#a21caf;"><i class="fa-solid fa-hand-holding-heart"></i></div>
                            <div>
                                <div><?= __('Web Donations') ?></div>
                                <div style="font-size:0.74rem; color:#94a3b8; font-weight:600;"><?= __('Public Contributions') ?></div>
                            </div>
                        </a>
                        <a href="/frisucode_ms/system/donors/index.php" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:#fff7ed; color:#ea580c;"><i class="fa-solid fa-star"></i></div>
                            <div>
                                <div><?= __('Partner Registry') ?></div>
                                <div style="font-size:0.74rem; color:#94a3b8; font-weight:600;"><?= __('Philanthropic Partners') ?></div>
                            </div>
                        </a>
                        <a href="/frisucode_ms/system/reports/index.php" class="quick-action-btn">
                            <div class="quick-action-icon" style="background:#eef2ff; color:#6366f1;"><i class="fa-solid fa-chart-pie"></i></div>
                            <div>
                                <div><?= __('Financial Audit') ?></div>
                                <div style="font-size:0.74rem; color:#94a3b8; font-weight:600;"><?= __('Impact Reports & Analytics') ?></div>
                            </div>
                        </a>
                    </div>

                    <!-- Budget Allocation -->
                    <?php if (count($projects) > 0): ?>
                    <div class="fin-card">
                        <div class="fin-card-header">
                            <div>
                                <p class="fin-card-title"><?= __('Project Budget Allocation') ?></p>
                                <p class="fin-card-sub"><?= __('Funds assigned per program') ?></p>
                            </div>
                        </div>
                        <?php
                        $maxBudget = max(array_column($projects, 'budget'));
                        foreach ($projects as $proj):
                            $pct = $maxBudget > 0 ? round(($proj['budget'] / $maxBudget) * 100) : 0;
                        ?>
                        <div class="budget-bar-wrap">
                            <div class="budget-bar-label">
                                <span><?= htmlspecialchars(mb_strimwidth($proj['title'], 0, 22, '…')) ?></span>
                                <strong>$<?= number_format($proj['budget'], 0) ?></strong>
                            </div>
                            <div class="budget-bar-track">
                                <div class="budget-bar-fill" style="width:<?= $pct ?>%;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                </div>

            </div>

        </div><!-- /padding wrapper -->

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
// ── Monthly Trend Bar Chart ──────────────────────────────────────────────
const monthLabels  = <?= json_encode(array_column($monthlyData, 'label')) ?>;
const incomeData   = <?= json_encode(array_column($monthlyData, 'income')) ?>;
const expenseData  = <?= json_encode(array_column($monthlyData, 'expense')) ?>;

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthLabels,
        datasets: [
            {
                label: '<?= __('Income') ?>',
                data: incomeData,
                backgroundColor: 'rgba(22,163,74,0.75)',
                borderRadius: 8,
                borderSkipped: false,
            },
            {
                label: '<?= __('Expenses') ?>',
                data: expenseData,
                backgroundColor: 'rgba(220,38,38,0.65)',
                borderRadius: 8,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top', labels: { font: { weight: '700', family: 'Inter' }, usePointStyle: true, pointStyleWidth: 10 } },
            tooltip: {
                callbacks: {
                    label: ctx => ' $' + Number(ctx.raw).toLocaleString(undefined,{minimumFractionDigits:2})
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { weight: '600' } } },
            y: {
                grid: { color: '#f1f5f9' },
                ticks: { callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v), font: { weight: '600' } }
            }
        }
    }
});

// ── Donut Chart ────────────────────────────────────────────────────────────
const totalIncome  = <?= (float)$income ?>;
const totalExpense = <?= (float)$expense ?>;

new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: ['<?= __('Income') ?>', '<?= __('Expenses') ?>'],
        datasets: [{
            data: [totalIncome || 1, totalExpense || 0],
            backgroundColor: ['#16a34a', '#dc2626'],
            borderColor: ['#fff', '#fff'],
            borderWidth: 3,
            hoverOffset: 8,
        }]
    },
    options: {
        cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' $' + Number(ctx.raw).toLocaleString(undefined,{minimumFractionDigits:2})
                }
            }
        }
    }
});
</script>
</body>
</html>
