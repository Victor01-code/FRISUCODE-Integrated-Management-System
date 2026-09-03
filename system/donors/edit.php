<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'finance', 'admin']);
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch Sponsor and User Details
$stmt = $pdo->prepare("SELECT s.*, u.email, u.full_name, u.status FROM sponsors s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
$stmt->execute([$id]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donor) {
    die("Donor not found.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $orgName = trim($_POST['organization_name']);
    $type = $_POST['sponsor_type'];

    if (empty($fullName) || empty($email)) {
        $error = "Primary Contact Name and Email are required.";
    } else {
        $pdo->beginTransaction();
        try {
            // Update User Profile
            $uStmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $uStmt->execute([$fullName, $email, $donor['user_id']]);

            // Update Sponsor Profile
            $sStmt = $pdo->prepare("UPDATE sponsors SET organization_name = ?, phone = ?, sponsor_type = ? WHERE id = ?");
            $sStmt->execute([$orgName, $phone, $type, $id]);

            $pdo->commit();
            header("Location: index.php?msg=updated");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Failed to update donor: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Edit Donor Profile</title>
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
            <h2 style="font-family: 'Outfit'; font-weight: 800;">Edit Partnership Details</h2>
            <a href="index.php" class="btn-light"><i class="fa-solid fa-arrow-left"></i> Back to Registry</a>
        </div>

        <div class="form-container fade-in">
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="edit.php?id=<?= $id ?>" method="POST">
                <h4 style="font-family: 'Outfit'; color: #1e293b; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Primary Contact Details</h4>
                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-user-tag"></i> Representative Name *</label>
                        <input type="text" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? $donor['full_name']) ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-envelope-open-text"></i> Communication Email *</label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? $donor['email']) ?>">
                    </div>
                </div>
                
                <h4 style="font-family: 'Outfit'; color: #1e293b; margin-top: 10px; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Organization Details</h4>
                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-building"></i> Organization Name <span style="font-size: 0.8em; color: #94a3b8;">(If applicable)</span></label>
                        <input type="text" name="organization_name" placeholder="e.g. UNICEF, Google Foundation" value="<?= htmlspecialchars($_POST['organization_name'] ?? $donor['organization_name']) ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-phone"></i> Official Phone</label>
                        <input type="text" name="phone" placeholder="+123 456 789" value="<?= htmlspecialchars($_POST['phone'] ?? $donor['phone']) ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-handshake-angle"></i> Partnership Category</label>
                    <select name="sponsor_type" required>
                        <option value="individual" <?= ($donor['sponsor_type'] === 'individual') ? 'selected' : '' ?>>Individual Philanthropist</option>
                        <option value="corporate" <?= ($donor['sponsor_type'] === 'corporate') ? 'selected' : '' ?>>Corporate Sponsor</option>
                        <option value="ngo" <?= ($donor['sponsor_type'] === 'ngo') ? 'selected' : '' ?>>Non-Governmental Organization</option>
                        <option value="government" <?= ($donor['sponsor_type'] === 'government') ? 'selected' : '' ?>>Government / Agency</option>
                    </select>
                </div>

                <div class="form-actions" style="margin-top: 30px;">
                    <button type="submit" class="btn-primary" style="padding: 14px 24px; font-weight: 800; border-radius: 12px;"><i class="fa-solid fa-floppy-disk"></i> Save Profile Changes</button>
                    <a href="index.php" class="btn-secondary" style="padding: 14px 24px; border-radius: 12px; margin-left: 10px;">Cancel</a>
                </div>
            </form>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
