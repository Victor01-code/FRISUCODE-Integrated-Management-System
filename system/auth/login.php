<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;
$error = $_GET['error'] ?? '';

$langFile = __DIR__ . "/../../lang/{$lang}.php";
if (!file_exists($langFile)) {
    $langFile = __DIR__ . "/../../lang/en.php";
}
require $langFile;
if (!isset($L) || !is_array($L)) { $L = []; }
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | <?= $L['auth_login_title'] ?? 'Smart Office Login' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<!-- Language Switcher -->
<div class="lang-switch">
    <form method="get">
        <select name="lang" onchange="this.form.submit()">
            <option value="en" <?= $lang=='en'?'selected':'' ?>>English</option>
            <option value="sw" <?= $lang=='sw'?'selected':'' ?>>Swahili</option>
            <option value="fr" <?= $lang=='fr'?'selected':'' ?>>Français</option>
            <option value="de" <?= $lang=='de'?'selected':'' ?>>Deutsch</option>
            <option value="es" <?= $lang=='es'?'selected':'' ?>>Español</option>
        </select>
    </form>
</div>

<div class="login-wrapper">
    <div class="login-card">

        <div class="logo">
            <img src="../../assets/images/logo.png" alt="FRISUCODE">
        </div>

        <h1>FRISUCODE</h1>
        <p class="subtitle"><?= $L['auth_smart_office'] ?? 'Smart Office & Donor Portal' ?></p>
        <p class="tagline"><?= $L['auth_empowering'] ?? 'Empowering Communities Through Education' ?></p>

        <?php if ($error === 'invalid'): ?>
            <div style="background: #fef2f2; color: #991b1b; padding: 14px 18px; border-radius: 14px; font-size: 0.9rem; font-weight: 600; margin-bottom: 20px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $L['auth_invalid'] ?? 'Invalid email or password. Please try again.' ?>
            </div>
        <?php elseif ($error === 'unauthorized'): ?>
            <div style="background: #fefce8; color: #854d0e; padding: 14px 18px; border-radius: 14px; font-size: 0.9rem; font-weight: 600; margin-bottom: 20px; border: 1px solid #fde68a; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= $L['auth_unauthorized'] ?? 'Unauthorized access. Contact your system administrator.' ?>
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">

            <label><i class="fa-solid fa-envelope" style="margin-right: 6px; color: #94a3b8;"></i> <?= $L['auth_email'] ?? 'Email Address' ?></label>
            <input type="email" name="email" placeholder="<?= $L['auth_email_placeholder'] ?? 'your.email@example.com' ?>" required>

            <label style="margin-top: 20px;"><i class="fa-solid fa-lock" style="margin-right: 6px; color: #94a3b8;"></i> <?= $L['auth_password'] ?? 'Password' ?></label>
            <input type="password" name="password" placeholder="<?= $L['auth_password_placeholder'] ?? 'Enter your password' ?>" required>

            <div class="login-options">
                <label>
                    <input type="checkbox" name="remember">
                    <?= $L['auth_remember'] ?? 'Remember me' ?>
                </label>

                <a href="forgot_password.php" class="forgot"><?= $L['auth_forgot'] ?? 'Forgot password?' ?></a>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-right-to-bracket"></i> <?= $L['auth_signin'] ?? 'Sign In' ?>
            </button>
            <a href="../../index.php" class="btn-outline" style="text-align: center; margin-top: 10px; text-decoration: none;">&larr; <?= $L['auth_return_web'] ?? 'Return to Website' ?></a>
        </form>

        <div class="support">
            <?= $L['auth_need_help'] ?? 'Need help?' ?> <a href="support.php"><?= $L['auth_contact_support'] ?? 'Contact Support' ?></a>
        </div>
    </div>

    <footer>
        © <?= date('Y') ?> <?= $L['auth_rights'] ?? 'FRISUCODE. All rights reserved.' ?>
    </footer>
</div>

</body>
</html>
