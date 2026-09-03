<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'admin']); 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../mail/mailer.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch User
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($fullName) || empty($email)) {
        $error = "Full Name and Email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            if (!empty($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ?, status = ?, password = ? WHERE id = ?");
                $updateStmt->execute([$fullName, $email, $role, $status, $hashedPassword, $id]);

                // SEND EMAIL NOTIFICATION FOR PASSWORD UPDATE
                $subject = "Security Notification: FRISUCODE Account Credentials Updated";
                $message = getAccountCredentialTemplate($fullName, $email, $newPassword, false);
                sendSystemEmail($email, $subject, $message);
            } else {
                $updateStmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ?, status = ? WHERE id = ?");
                $updateStmt->execute([$fullName, $email, $role, $status, $id]);
            }
            header("Location: index.php?msg=updated");
            exit;
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>Edit User: <?php echo htmlspecialchars($user['full_name']); ?></title>
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
            <h2 style="font-family: 'Outfit'; font-weight: 800;">Edit Staff Profile</h2>
            <a href="index.php" class="btn-light" style="border-radius: 12px; font-weight: 700;">
                <i class="fa-solid fa-arrow-left"></i> Cancel
            </a>
        </div>

        <div class="form-container fade-in" style="max-width: 900px; margin: 30px 40px; animation-delay: 0.1s;">
            <?php if ($error): ?>
                <div class="alert alert-danger" style="margin-bottom: 20px; border-radius: 12px; font-weight: 600;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <!-- Current User Info Header -->
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 25px; border-bottom: 2px solid #f8fafc;">
                    <div class="profile-img-circle" style="width: 64px; height: 64px; font-size: 1.5rem; background: #2563eb;">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h3 style="font-family: 'Outfit'; font-weight: 800; margin: 0; color: #0f172a;"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                        <p style="color: #64748b; margin: 4px 0 0; font-weight: 600;"><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="badge primary" style="margin-top: 8px; display: inline-block;"><?php echo strtoupper(str_replace('_', ' ', $user['role'])); ?></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-user" style="color: #3b82f6;"></i> Full Name *
                        </label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required placeholder="Staff full name" style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px; font-weight: 600;">
                    </div>
                    <div class="input-group">
                        <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-envelope" style="color: #6366f1;"></i> Email Address *
                        </label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required placeholder="email@frisucode.org" style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px; font-weight: 600;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-user-shield" style="color: #f59e0b;"></i> System Role
                        </label>
                        <select name="role" style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px; font-weight: 600;">
                            <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Field Staff</option>
                            <option value="project_manager" <?php echo $user['role'] === 'project_manager' ? 'selected' : ''; ?>>Project Manager</option>
                            <option value="finance" <?php echo $user['role'] === 'finance' ? 'selected' : ''; ?>>Finance Officer</option>
                            <option value="me_officer" <?php echo $user['role'] === 'me_officer' ? 'selected' : ''; ?>>M&E Officer</option>
                            <option value="field_officer" <?php echo $user['role'] === 'field_officer' ? 'selected' : ''; ?>>Field Officer</option>
                            <option value="director" <?php echo $user['role'] === 'director' ? 'selected' : ''; ?>>Director</option>
                            <option value="super_admin" <?php echo $user['role'] === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                            <option value="donor" <?php echo $user['role'] === 'donor' ? 'selected' : ''; ?>>Donor</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-toggle-on" style="color: #16a34a;"></i> Account Status
                        </label>
                        <select name="status" style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px; font-weight: 600;">
                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive (Suspended)</option>
                        </select>
                    </div>
                </div>

                <div class="input-group" style="margin-top: 20px;">
                    <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-key" style="color: #dc2626;"></i> New Password (Optional)
                    </label>
                    <input type="password" name="new_password" placeholder="Leave blank to keep current password" style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px; font-weight: 600;">
                    <small style="color: #94a3b8; font-size: 0.8rem; margin-top: 6px; display: block;">Only fill this if you want to change the user's password.</small>
                </div>

                <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center;">
                    <p style="color: #64748b; font-size: 0.85rem; font-weight: 500; margin: 0;">
                        <i class="fa-solid fa-shield-halved"></i> User ID: #<?php echo $user['id']; ?> · Since <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                    </p>
                    <button type="submit" class="btn-primary" style="padding: 14px 32px; border-radius: 14px; font-weight: 800; font-family: 'Outfit'; font-size: 1rem; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.25);">
                        <i class="fa-solid fa-user-check"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
