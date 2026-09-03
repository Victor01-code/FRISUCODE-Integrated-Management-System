<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director']); 
require_once __DIR__ . '/../config/db.php';

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = __('Organization settings updated successfully.');
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | System Settings</title>
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

        <div class="page-header" style="margin-bottom: 0;">
            <h2><?= __('System Global Settings') ?></h2>
        </div>

        <div class="form-container">
            <?php if ($success): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <h3 style="margin-bottom: 25px; font-size: 1.1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; color: #1e293b;"><?= __('Organization Branding') ?></h3>
                
                <div class="input-group">
                    <label><?= __('Organization Name') ?></label>
                    <input type="text" value="FRISUCODE" placeholder="e.g. FRISUCODE">
                </div>

                <div class="input-group">
                    <label><?= __('Official Email Address') ?></label>
                    <input type="email" value="frisucode641@gmail.com">
                </div>

                <h3 style="margin-top: 40px; margin-bottom: 25px; font-size: 1.1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; color: #1e293b;"><?= __('Currency & Localization') ?></h3>
                
                <div class="form-row">
                    <div class="input-group">
                        <label><?= __('Base Currency') ?></label>
                        <select>
                            <option value="USD" selected>USD - United States Dollar</option>
                            <option value="TZS">TZS - Tanzanian Shilling</option>
                            <option value="EUR">EUR - Euro</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label><?= __('Primary Timezone') ?></label>
                        <select>
                            <option value="EAT" selected>East Africa Time (GMT+3)</option>
                            <option value="UTC">Coordinated Universal Time (UTC)</option>
                        </select>
                    </div>
                </div>

                <h3 style="margin-top: 40px; margin-bottom: 25px; font-size: 1.1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; color: #1e293b;"><?= __('Security Settings') ?></h3>
                
                <div class="input-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" checked style="width: auto;">
                        <?= __('Force 2nd Factor for Admins') ?>
                    </label>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn-primary-block">
                        <i class="fa-solid fa-floppy-disk"></i> <?= __('Save Global Settings') ?>
                    </button>
                </div>
            </form>
        </div>

        <div class="form-container" style="margin-top: 30px;">
            <h3 style="margin-bottom: 25px; font-size: 1.1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; color: #1e293b;"><?= __('Data Integrity') ?></h3>
            
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                <div>
                    <strong style="display: block; color: #0f172a; font-size: 1rem;"><?= __('Duplicates Manager') ?></strong>
                    <span style="color: #64748b; font-size: 0.85rem;"><?= __('Scan, identify, and merge duplicate records across users, donors, and beneficiaries.') ?></span>
                </div>
                <a href="duplicates.php" class="btn-primary" style="border-radius: 12px; white-space: nowrap;">
                    <i class="fa-solid fa-clone"></i> <?= __('Open Manager') ?>
                </a>
            </div>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
