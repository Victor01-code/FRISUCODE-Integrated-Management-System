<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: ../auth/login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User profile not found.");
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $fullName = $_POST['full_name'] ?? $user['full_name'];
        $phone = $_POST['phone'] ?? $user['phone'];
        $address = $_POST['address'] ?? $user['address'];
        $lang = $_POST['pref_lang'] ?? $user['preferred_language'] ?? 'en';
        
        $profilePicPath = $user['profile_picture'];
        
        // Handle file upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_picture']['name'];
            $fileSize = $_FILES['profile_picture']['size'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                $error = __('Invalid file type. Only JPG, PNG and GIF are allowed.');
            } elseif ($fileSize > 5 * 1024 * 1024) { // 5MB limit
                $error = __('File size exceeds 5MB limit.');
            } else {
                $uploadDir = __DIR__ . '/../../assets/uploads/profiles/';
                $newFilename = 'profile_' . $userId . '_' . time() . '.' . $ext;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadDir . $newFilename)) {
                    $profilePicPath = '/frisucode_ms/public/assets/uploads/profiles/' . $newFilename;
                } else {
                    $error = __('Failed to save uploaded image.');
                }
            }
        }
        
        if (empty($error)) {
            $upd = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, preferred_language = ?, profile_picture = ? WHERE id = ?");
            if ($upd->execute([$fullName, $phone, $address, $lang, $profilePicPath, $userId])) {
                $_SESSION['lang'] = $lang;
                $_SESSION['user_name'] = $fullName; // update session name
                $_SESSION['profile_picture'] = $profilePicPath; // update session profile picture
                $success = __('Profile updated successfully.');
                header("Location: view_profile.php?msg=" . urlencode($success));
                exit;
            } else {
                $error = __('Failed to update profile.');
            }
        }
    }
}
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | <?= __('My Profile') ?></title>
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
            <h2><?= __('My Profile & Preferences') ?></h2>
        </div>

        <div class="form-container fade-in" style="max-width: 800px; margin: 30px 40px; animation-delay: 0.1s;">
            <?php if ($msg): ?>
                <div class="alert alert-success" style="border-radius: 12px; margin-bottom: 24px;"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 24px;"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Basic Info Header -->
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 25px; border-bottom: 2px solid #f8fafc;">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-light);">
                <?php else: ?>
                    <div class="profile-img-circle" style="width: 80px; height: 80px; font-size: 2.2rem; background: #2563eb;">
                        <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h3 style="font-family: 'Outfit'; font-weight: 800; margin: 0; font-size: 1.5rem; color: #0f172a;"><?= htmlspecialchars($user['full_name'] ?? 'Profile') ?></h3>
                    <p style="color: #64748b; margin: 4px 0 0; font-weight: 600; font-size: 1rem;"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($user['email'] ?? '') ?></p>
                    <span class="badge primary" style="margin-top: 8px; display: inline-block; font-size: 0.75rem;"><i class="fa-solid fa-shield-halved"></i> <?= strtoupper(str_replace('_', ' ', $user['role'] ?? 'Staff')) ?></span>
                </div>
            </div>

            <!-- Profile Update Form -->
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_profile" value="1">
                
                <h3 style="margin-bottom: 20px; font-size: 1.1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; color: #1e293b;"><i class="fa-solid fa-user-pen"></i> <?= __('Personal Details') ?></h3>
                
                <div class="form-row">
                    <div class="input-group">
                        <label><?= __('Full Name') ?></label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-family: 'Inter', sans-serif;">
                    </div>
                    <div class="input-group">
                        <label><?= __('Phone Number') ?></label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+1 234 567 8900" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-family: 'Inter', sans-serif;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group" style="grid-column: span 2;">
                        <label><?= __('Physical Address / Location') ?></label>
                        <textarea name="address" rows="2" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-family: 'Inter', sans-serif; resize: vertical;"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-camera"></i> <?= __('Upload Profile Picture') ?></label>
                        <input type="file" name="profile_picture" accept="image/jpeg, image/png, image/gif" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px dashed #cbd5e1; background: #f8fafc; cursor: pointer;">
                        <small style="color: #94a3b8; display: block; margin-top: 5px;"><?= __('Recommended: Square image, max 5MB (JPG/PNG).') ?></small>
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-language"></i> <?= __('System Language') ?></label>
                        <select name="pref_lang" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0; font-weight: 600;">
                            <option value="en" <?= ($_SESSION['lang'] ?? 'en') == 'en' ? 'selected' : '' ?>>English (EN)</option>
                            <option value="sw" <?= ($_SESSION['lang'] ?? '') == 'sw' ? 'selected' : '' ?>>Swahili (SW)</option>
                            <option value="fr" <?= ($_SESSION['lang'] ?? '') == 'fr' ? 'selected' : '' ?>>Français (FR)</option>
                            <option value="de" <?= ($_SESSION['lang'] ?? '') == 'de' ? 'selected' : '' ?>>Deutsch (DE)</option>
                            <option value="es" <?= ($_SESSION['lang'] ?? '') == 'es' ? 'selected' : '' ?>>Español (ES)</option>
                        </select>
                    </div>
                </div>

                <!-- Password hint -->
                <div style="margin-top: 10px; margin-bottom: 30px; padding: 15px; background: #fffbeb; border-radius: 12px; border: 1px solid #fde68a;">
                    <p style="margin: 0; color: #92400e; font-size: 0.85rem; font-weight: 600;">
                        <i class="fa-solid fa-lock" style="margin-right: 5px;"></i> <?= __('To change your email address or password, please contact the system administration desk due to security policies.') ?>
                    </p>
                </div>

                <button type="submit" class="btn-primary" style="border-radius: 12px; padding: 14px 28px;">
                    <i class="fa-solid fa-floppy-disk"></i> <?= __('Update Profile') ?>
                </button>
            </form>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>
</body>
</html>
