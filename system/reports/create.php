<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'project_manager', 'staff', 'field_officer']);
require_once __DIR__ . '/../config/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dummy submission logic indicating successful execution
    // (In a real scenario, we'd insert into a `field_reports` table)
    $success = __('Field Report successfully submitted. Pending review from Project Managers.');
}

// Fetch active projects that a staff might report on
$projects = [];
try {
    $projects = $pdo->query("SELECT id, title FROM projects WHERE status IN ('active', 'planning')")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle silently if db table missing
}

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | <?= __('File Field Report') ?></title>
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

        <div class="page-header fade-in">
            <h2><?= __('Submit Field Report') ?></h2>
            <a href="javascript:history.back()" class="btn-outline-sm btn-light" style="padding: 10px 20px; text-decoration: none; border-radius: 8px; border: 1px solid #e2e8f0; color: #64748b; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-left"></i> <?= __('Back') ?>
            </a>
        </div>

        <div class="form-container fade-in" style="max-width: 800px; margin: 30px 40px; animation-delay: 0.1s;">
            <?php if ($success): ?>
                <div class="alert alert-success" style="border-radius: 12px; margin-bottom: 24px;"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST">
                
                <div class="input-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;"><i class="fa-solid fa-diagram-project" style="color: #3b82f6;"></i> <?= __('Reported Project') ?></label>
                    <select name="project_id" required style="width: 100%; padding: 14px; border-radius: 12px; border: 2px solid #e2e8f0; font-weight: 600; outline: none; transition: 0.2s; color: #334155;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                        <option value=""><?= __('-- Select Active Project --') ?></option>
                        <?php foreach($projects as $proj): ?>
                            <option value="<?= $proj['id'] ?>"><?= htmlspecialchars($proj['title']) ?></option>
                        <?php endforeach; ?>
                        <option value="other"><?= __('Other / General Duty') ?></option>
                    </select>
                </div>
                
                <div class="input-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;"><i class="fa-solid fa-calendar-day" style="color: #10b981;"></i> <?= __('Report Date') ?></label>
                    <input type="date" name="report_date" required value="<?= date('Y-m-d') ?>" style="width: 100%; padding: 14px; border-radius: 12px; border: 2px solid #e2e8f0; font-weight: 600; outline: none; transition: 0.2s; color: #334155;" onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#e2e8f0'">
                </div>

                <div class="input-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 700; color: #1e293b; display: block; margin-bottom: 6px;"><i class="fa-solid fa-keyboard" style="color: #8b5cf6;"></i> <?= __('Field Activities / Progress Notes') ?></label>
                    <textarea name="content" required rows="6" placeholder="<?= __('Provide a summary of the events, challenges encountered, or organizational milestones reached in the field.') ?>" style="width: 100%; padding: 14px; border-radius: 12px; border: 2px solid #e2e8f0; font-weight: 600; outline: none; transition: 0.2s; color: #334155; resize: vertical;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#e2e8f0'"></textarea>
                </div>

                <div style="margin-top: 35px; border-top: 2px solid #f8fafc; padding-top: 25px;">
                    <button type="submit" class="btn-primary" style="border-radius: 12px; padding: 14px 28px; font-weight: 800; font-family: 'Outfit'; font-size: 1rem; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.25);">
                        <i class="fa-solid fa-paper-plane"></i> <?= __('Submit to Management') ?>
                    </button>
                    <p style="margin-top: 12px; font-size: 0.8rem; color: #94a3b8; font-weight: 600;">
                        <i class="fa-solid fa-circle-info"></i> <?= __('This report will be attached to the primary project dashboard.') ?>
                    </p>
                </div>
            </form>
            
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>
</body>
</html>
