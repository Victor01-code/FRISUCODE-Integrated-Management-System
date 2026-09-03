<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director']); 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../mail/mailer.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    if (empty($fullName) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check if email already exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetchColumn() > 0) {
                $error = "A user with this email address is already registered.";
            } else {
                $pdo->beginTransaction();
                
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$fullName, $email, $hashedPassword, $role]);
                
                $newUserId = $pdo->lastInsertId();

                if ($role === 'donor') {
                    $sponsorStmt = $pdo->prepare("INSERT INTO sponsors (user_id, organization_name, sponsor_type) VALUES (?, ?, 'individual')");
                    $sponsorStmt->execute([$newUserId, $fullName]);
                }
                
                $pdo->commit();
                
                // SEND EMAIL NOTIFICATION
                $subject = "Welcome to FRISUCODE System - Your Login Credentials";
                $message = getAccountCredentialTemplate($fullName, $email, $password, true);
                sendSystemEmail($email, $subject, $message);

                header("Location: index.php?msg=created");
                exit;
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Register System User</title>
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
            <h2 style="font-family: 'Outfit'; font-weight: 800;">Register System User</h2>
            <a href="index.php" class="btn-light"><i class="fa-solid fa-arrow-left"></i> Staff Directory</a>
        </div>

        <div class="form-container fade-in">
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="create.php" method="POST" id="userForm">
                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-user-tag"></i> Full Legal Name *</label>
                        <input type="text" name="full_name" required placeholder="e.g. Victor Ezekiel" value="<?= htmlspecialchars($fullName ?? '') ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-envelope-open-text"></i> Business Email *</label>
                        <input type="email" name="email" required placeholder="staff.name@frisucode.org" value="<?= htmlspecialchars($email ?? '') ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-key"></i> Initial Account Password *</label>
                    <input type="password" name="password" required placeholder="Create a secure password">
                    <small style="color: #94a3b8; font-size: 0.75rem; margin-top: 5px; display: block;">Users will be prompted to change their password on first login.</small>
                </div>

                <div class="input-group" style="margin-top: 30px;">
                    <label><i class="fa-solid fa-user-shield"></i> Access Privilege (Role Assignment)</label>
                    <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 15px;">This selection determines the dashboard and system permissions granted to this user.</p>
                    
                    <div class="radio-cards-grid">
                        <label class="radio-card active">
                            <input type="radio" name="role" value="staff" checked>
                            <div class="icon-box"><i class="fa-solid fa-person-digging"></i></div>
                            <div>
                                <strong style="display:block;">Field Staff</strong>
                                <small>Reports & Beneficiaries Access</small>
                            </div>
                        </label>

                        <label class="radio-card">
                            <input type="radio" name="role" value="project_manager">
                            <div class="icon-box"><i class="fa-solid fa-diagram-project"></i></div>
                            <div>
                                <strong style="display:block;">Project Manager</strong>
                                <small>Full Planning & Goal Control</small>
                            </div>
                        </label>

                        <label class="radio-card">
                            <input type="radio" name="role" value="finance">
                            <div class="icon-box"><i class="fa-solid fa-vault"></i></div>
                            <div>
                                <strong style="display:block;">Finance Officer</strong>
                                <small>Treasury & Budgetary Control</small>
                            </div>
                        </label>

                        <label class="radio-card">
                            <input type="radio" name="role" value="director">
                            <div class="icon-box"><i class="fa-solid fa-user-tie"></i></div>
                            <div>
                                <strong style="display:block;">Director</strong>
                                <small>High-level System Oversight</small>
                            </div>
                        </label>

                        <label class="radio-card">
                            <input type="radio" name="role" value="super_admin">
                            <div class="icon-box"><i class="fa-solid fa-shield-halved"></i></div>
                            <div>
                                <strong style="display:block;">Super Admin</strong>
                                <small>Full System Configurations</small>
                            </div>
                        </label>

                        <label class="radio-card">
                            <input type="radio" name="role" value="donor">
                            <div class="icon-box"><i class="fa-solid fa-hand-holding-heart"></i></div>
                            <div>
                                <strong style="display:block;">Philanthropic Donor</strong>
                                <small>External Access to Reports</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <button type="submit" class="btn-primary-block">
                        <i class="fa-solid fa-user-plus"></i> Finalize User Registration
                    </button>
                    <p style="text-align: center; font-size: 0.85rem; color: #94a3b8; margin-top: 20px;">
                        <i class="fa-solid fa-lock"></i> All staff data is encrypted and saved to the secure personnel registry.
                    </p>
                </div>
            </form>
        </div>

        <script>
            document.querySelectorAll('.radio-card').forEach(card => {
                card.addEventListener('click', function() {
                    document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    this.querySelector('input').checked = true;
                });
            });
        </script>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
