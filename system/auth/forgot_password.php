<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;

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
    <title>FRISUCODE | <?= $L['auth_reset_title'] ?? 'Reset Password' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="logo">
            <img src="../../assets/images/logo.png" alt="FRISUCODE">
        </div>

        <h1><?= $L['auth_reset_title'] ?? 'Reset Password' ?></h1>
        <p class="tagline"><?= $L['auth_reset_tagline'] ?? 'Enter your email and we\'ll send you a link to reset your password.' ?></p>

        <form action="forgot_process.php" method="POST">
            <div style="text-align: left; margin-bottom: 20px;">
                <label><?= $L['auth_email'] ?? 'Email Address' ?></label>
                <input type="email" name="email" placeholder="<?= $L['auth_reset_email_placeholder'] ?? 'registered.email@example.com' ?>" required style="width:100%; padding:12px; margin-top:5px; border-radius:8px; border:1px solid #ddd;">
            </div>

            <button type="submit" class="btn-primary"><?= $L['auth_send_link'] ?? 'Send Reset Link' ?></button>
            <a href="login.php" class="btn-outline" style="text-align: center; margin-top: 10px; text-decoration: none;">&larr; <?= $L['auth_back_login'] ?? 'Back to Login' ?></a>
        </form>

        <div class="support" style="margin-top: 25px;">
            <?= $L['auth_remembered'] ?? 'Remembered it?' ?> <a href="login.php"><?= $L['auth_try_signing'] ?? 'Try signing in again.' ?></a>
        </div>
    </div>

    <footer>
        © <?= date('Y') ?> <?= $L['auth_rights'] ?? 'FRISUCODE. All rights reserved.' ?>
    </footer>
</div>

</body>
</html>
