<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'finance', 'project_manager']);
require_once __DIR__ . '/../config/db.php';

try {
    // Basic income/expense query
    $stmt = $pdo->query("SELECT * FROM finance_records ORDER BY date DESC, created_at DESC LIMIT 50");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sums
    $income = $pdo->query("SELECT SUM(amount) FROM finance_records WHERE type='income'")->fetchColumn() ?: 0;
    $expense = $pdo->query("SELECT SUM(amount) FROM finance_records WHERE type='expense'")->fetchColumn() ?: 0;

} catch (PDOException $e) {
    $records = [];
    $income = 0; $expense = 0;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Finance Ledger</title>
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

        <div class="page-header fade-in" style="margin-bottom: 0;">
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Financial Repository') ?></h2>
            <a href="create.php" class="btn-primary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
                <i class="fa-solid fa-plus-circle"></i> <?= __('Log New Transaction') ?>
            </a>
        </div>

        <div class="stats-grid fade-in" style="margin: 32px 40px; animation-delay: 0.1s;">
            <div class="stat-card" style="border-left: 5px solid #16a34a;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p style="color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;"><?= __('Gross Revenue') ?></p>
                        <strong style="color: #16a34a; font-size: 1.8rem; font-family: 'Outfit';">$<?php echo number_format($income, 2); ?></strong>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                        <i class="fa-solid fa-circle-arrow-up"></i>
                    </div>
                </div>
                <div style="margin-top: 15px; font-size: 0.8rem; color: #16a34a; font-weight: 700;">
                    <i class="fa-solid fa-caret-up"></i> <?= __('All-time Inflow') ?>
                </div>
            </div>

            <div class="stat-card" style="border-left: 5px solid #dc2626;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p style="color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;"><?= __('Total Outflow') ?></p>
                        <strong style="color: #dc2626; font-size: 1.8rem; font-family: 'Outfit';">$<?php echo number_format($expense, 2); ?></strong>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: #fef2f2; display: flex; align-items: center; justify-content: center; color: #dc2626;">
                        <i class="fa-solid fa-circle-arrow-down"></i>
                    </div>
                </div>
                <div style="margin-top: 15px; font-size: 0.8rem; color: #dc2626; font-weight: 700;">
                    <i class="fa-solid fa-caret-down"></i> <?= __('Organization Expenses') ?>
                </div>
            </div>

            <div class="stat-card" style="border-left: 5px solid #3b82f6; background: linear-gradient(to bottom right, #ffffff, #eff6ff);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <p style="color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;"><?= __('Net Reserve') ?></p>
                        <strong style="color: #1e40af; font-size: 1.8rem; font-family: 'Outfit';">$<?php echo number_format($income - $expense, 2); ?></strong>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: #dbeafe; display: flex; align-items: center; justify-content: center; color: #1e40af;">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                </div>
                <div style="margin-top: 15px; font-size: 0.8rem; color: #3b82f6; font-weight: 700;">
                    <i class="fa-solid fa-shield-check"></i> <?= __('Verified Balance') ?>
                </div>
            </div>
        </div>

        <div class="content-box fade-in" style="margin: 0 40px 40px; animation-delay: 0.2s;">
            <div style="padding: 24px 32px; border-bottom: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 800; margin: 0; color: #0f172a;"><?= __('Consolidated Ledger') ?></h3>
                <span style="font-size: 0.85rem; color: #64748b; font-weight: 600;"><i class="fa-solid fa-clock-rotate-left"></i> <?= __('Real-time Sync Enabled') ?></span>
            </div>
            
            <?php if(count($records) > 0): ?>
                <div style="width: 100%; max-height: 600px; overflow: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 24px 24px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 32px;"><?= __('Transaction Date') ?></th>
                                <th><?= __('Description & Particulars') ?></th>
                                <th><?= __('Classification') ?></th>
                                <th><?= __('Entry Amount') ?></th>
                                <th style="padding-right: 32px; text-align: right;"><?= __('Internal ID') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($records as $row): ?>
                                <tr>
                                    <td style="padding-left: 32px; min-width: 160px;">
                                        <div style="font-weight: 600; color: #1e293b;"><?php echo date('M d, Y', strtotime($row['date'])); ?></div>
                                        <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;"><?php echo date('H:i A', strtotime($row['created_at'])); ?></div>
                                    </td>
                                    <td style="min-width: 250px;">
                                        <div style="font-weight: 700; color: #334155; font-size: 0.95rem;"><?php echo htmlspecialchars($row['description']); ?></div>
                                    </td>
                                    <td style="min-width: 140px;">
                                        <?php 
                                        $isIncome = ($row['type'] == 'income');
                                        ?>
                                        <span class="badge <?php echo $isIncome ? 'success' : 'danger'; ?>" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800; padding: 6px 14px;">
                                            <i class="fa-solid <?php echo $isIncome ? 'fa-arrow-down-long' : 'fa-arrow-up-long'; ?>"></i>
                                            <?php echo strtoupper($row['type']); ?>
                                        </span>
                                    </td>
                                    <td style="min-width: 140px;">
                                        <div style="font-family: 'Outfit'; font-weight: 800; font-size: 1.15rem; color: <?php echo $isIncome ? '#16a34a' : '#dc2626'; ?>">
                                            <?php echo $isIncome ? '+' : '-'; ?>$<?php echo number_format($row['amount'], 2); ?>
                                        </div>
                                    </td>
                                    <td style="padding-right: 32px; text-align: right; min-width: 120px;">
                                        <span style="font-family: monospace; color: #94a3b8; font-weight: 700; background: #f8fafc; padding: 4px 8px; border-radius: 6px;">
                                            #TX-<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 100px 40px; color: #64748b; background: #fff;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-file-invoice-dollar" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 10px;"><?= __('Ledger is empty') ?></h3>
                    <p style="font-weight: 500;"><?= __('No financial transactions have been recorded yet.') ?></p>
                    <a href="create.php" class="btn-primary" style="margin-top: 20px;"><?= __('Record First Entry') ?></a>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
