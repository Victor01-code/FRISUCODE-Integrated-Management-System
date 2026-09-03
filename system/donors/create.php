<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'finance', 'admin']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../mail/mailer.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect Data
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $orgName = trim($_POST['organization_name']);
    $type = $_POST['sponsor_type'];

    // Basic Validation
    if (empty($fullName) || empty($email)) {
        $error = "Name and Email are required.";
    } else {
        // Check if email already exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetchColumn() > 0) {
            $error = "A donor or user with this email address is already registered.";
        } else {
            // Transaction for double insert (users + sponsors)
            $pdo->beginTransaction();
            try {
                // 1. Create User
                // Generate temp or set password
                $tempPassword = password_hash("Donor@123", PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, 'donor', 'active')");
                $stmt->execute([$fullName, $email, $tempPassword]);
                $userId = $pdo->lastInsertId();

                // 2. Create Sponsor Profile
                $stmt2 = $pdo->prepare("INSERT INTO sponsors (user_id, organization_name, phone, sponsor_type) VALUES (?, ?, ?, ?)");
                $stmt2->execute([$userId, $orgName, $phone, $type]);

                $pdo->commit();
                
                // SEND EMAIL NOTIFICATION TO DONOR
                $subject = "Welcome to FRISUCODE Donor Portal";
                $message = getAccountCredentialTemplate($fullName, $email, "Donor@123", true);
                sendSystemEmail($email, $subject, $message);

                header("Location: index.php?msg=created"); 
                exit;
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = "Failed to create donor: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRISUCODE | Add New Donor</title>
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header">
            <h2><?= __('Register New Donor') ?></h2>
            <a href="index.php" class="btn-secondary"><?= __('Back to List') ?></a>
        </div>

        <div class="form-container fade-in" style="max-width: 900px; margin: 30px 40px; animation-delay: 0.1s;">
             <?php if ($error): ?>
                <div class="alert alert-danger" style="margin-bottom: 20px; border-radius: 12px; font-weight: 600;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="input-group">
                        <label><?= __('Contact Person Name *') ?></label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="input-group">
                        <label><?= __('Email Address *') ?></label>
                        <input type="email" name="email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label><?= __('Phone Number') ?></label>
                        <input type="text" name="phone">
                    </div>
                    <div class="input-group">
                        <label><?= __('Donor Type') ?></label>
                        <select name="sponsor_type">
                            <option value="individual"><?= __('Individual') ?></option>
                            <option value="organization"><?= __('Organization / Company') ?></option>
                            <option value="government"><?= __('Government / Grant') ?></option>
                        </select>
                    </div>
                </div>

                <div class="input-group">
                    <label><?= __('Organization Name (If applicable)') ?></label>
                    <input type="text" name="organization_name">
                </div>

                <button type="submit" class="btn-primary"><?= __('Register Donor') ?></button>
            </form>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
