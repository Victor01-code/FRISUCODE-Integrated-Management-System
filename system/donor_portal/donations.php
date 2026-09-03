<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole('donor');
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user_id'];
$userQuery = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$userQuery->execute([$userId]);
$donorEmail = $userQuery->fetchColumn();

// Fetch personal entire donation history
$donationsStmt = $pdo->prepare("SELECT * FROM public_donations WHERE email = ? ORDER BY created_at DESC");
$donationsStmt->execute([$donorEmail]);
$donations = $donationsStmt->fetchAll(PDO::FETCH_ASSOC);

$totalDonated = array_sum(array_column($donations, 'amount'));
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRISUCODE | <?= __('My Donation History') ?></title>
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
             <h2 style="font-family: 'Outfit'; font-weight: 800; color: #0f172a; margin: 0;"><i class="fa-solid fa-hand-holding-dollar" style="color: #10b981; margin-right: 10px;"></i> <?= __('Donation Ledger') ?></h2>
        </div>
        
        <div class="stats-grid fade-in" style="animation-delay: 0.1s;">
            <div class="stat-card" style="border-left: 5px solid #16a34a; padding: 20px;">
                <h4 style="margin: 0; font-size: 0.95rem; color: #64748b;"><?= __('Lifetime Contribution') ?></h4>
                <strong style="color: #16a34a; font-size: 1.8rem; margin-top: 8px;">$<?php echo number_format($totalDonated, 2); ?></strong>
            </div>
            <div class="stat-card" style="border-left: 5px solid #3b82f6; padding: 20px;">
                <h4 style="margin: 0; font-size: 0.95rem; color: #64748b;"><?= __('Total Transactions') ?></h4>
                <strong style="color: #3b82f6; font-size: 1.8rem; margin-top: 8px;"><?php echo count($donations); ?></strong>
            </div>
            <div class="stat-card" style="border-left: 5px solid #8b5cf6; padding: 20px;">
                <h4 style="margin: 0; font-size: 0.95rem; color: #64748b;"><?= __('Latest Donation Date') ?></h4>
                <strong style="color: #8b5cf6; font-size: 1.4rem; margin-top: 8px;">
                    <?= !empty($donations) ? date('M d, Y', strtotime($donations[0]['created_at'])) : __('Never') ?>
                </strong>
            </div>
        </div>

        <div class="content-box fade-in" style="animation-delay: 0.2s;">
            <div style="padding: 24px 28px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-family: 'Outfit'; font-weight: 800; color: #1e293b;"><?= __('Transaction History') ?></h3>
                <button class="btn-outline-sm" onclick="window.print()" style="padding: 8px 16px; border-radius: 8px;"><i class="fa-solid fa-print"></i> <?= __('Print Statement') ?></button>
            </div>
            <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 24px 24px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><?= __('Date') ?></th>
                            <th><?= __('Transaction ID') ?></th>
                            <th><?= __('Designated Cause') ?></th>
                            <th><?= __('Type') ?></th>
                            <th><?= __('Amount') ?></th>
                            <th><?= __('Status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($donations)): foreach ($donations as $donation): ?>
                        <tr>
                            <td style="font-weight: 600; color: #475569; min-width: 140px;"><?= date('M d, Y', strtotime($donation['created_at'])) ?></td>
                            <td style="font-family: monospace; color: #94a3b8; font-size: 0.9rem; min-width: 180px;"><?= htmlspecialchars($donation['transaction_id'] ?: 'MANUAL-ENTRY') ?></td>
                            <td style="font-weight: 500; color: #1e293b; min-width: 180px;"><?= htmlspecialchars($donation['cause'] ?: 'General Fund') ?></td>
                            <td style="min-width: 120px;">
                                <span class="badge" style="background: #f8fafc; color: #475569; font-weight: 700;"><?= ucfirst(htmlspecialchars($donation['frequency'])) ?></span>
                            </td>
                            <td style="font-weight: 800; color: #16a34a; font-size: 1.05rem; min-width: 120px;">+$<?= number_format($donation['amount'], 2) ?></td>
                            <td style="min-width: 130px;">
                                <span class="badge <?= ($donation['status'] == 'completed') ? 'success' : (($donation['status'] == 'pending') ? 'warning' : 'danger'); ?>" style="font-weight: 800;">
                                    <i class="fa-solid <?= ($donation['status'] == 'completed') ? 'fa-check' : 'fa-clock'; ?>"></i> <?= strtoupper($donation['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fa-solid fa-receipt" style="font-size: 2rem; margin-bottom: 15px; opacity: 0.5; display: block;"></i>
                                <?= __('No donation records exist for your account yet.') ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
