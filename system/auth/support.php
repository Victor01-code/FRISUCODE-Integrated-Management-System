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
    <title>FRISUCODE | <?= $L['auth_support_title'] ?? 'System Support' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .support-option {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 12px;
            text-decoration: none;
            color: #374151;
            transition: 0.2s;
            text-align: left;
        }
        .support-option:hover {
            border-color: #2563eb;
            background: #f8fbff;
            transform: translateY(-2px);
        }
        .support-option i {
            font-size: 20px;
            color: #2563eb;
            background: #eff6ff;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
        .support-option div strong {
            display: block;
            font-size: 15px;
        }
        .support-option div span {
            font-size: 13px;
            color: #6b7280;
        }
    </style>
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="logo">
            <img src="../../assets/images/logo.png" alt="FRISUCODE">
        </div>

        <h1><?= $L['auth_support_title'] ?? 'System Support' ?></h1>
        <p class="tagline"><?= $L['auth_support_tagline'] ?? 'Having trouble with your account or the Smart Office? We\'re here to help.' ?></p>

        <div style="margin: 25px 0;">
            <a href="mailto:support@frisucode_ms.com" class="support-option">
                <i class="fa-solid fa-envelope"></i>
                <div>
                    <strong><?= $L['auth_support_email'] ?? 'Email Support' ?></strong>
                    <span><?= $L['auth_support_email_desc'] ?? 'Response within 24 hours' ?></span>
                </div>
            </a>

            <a href="tel:+255754917546" class="support-option">
                <i class="fa-solid fa-phone"></i>
                <div>
                    <strong><?= $L['auth_support_call'] ?? 'Call System Admin' ?></strong>
                    <span><?= $L['auth_support_call_desc'] ?? 'Mon - Fri, 8AM - 5PM' ?></span>
                </div>
            </a>

            <a href="https://wa.me/255754917546" class="support-option" target="_blank">
                <i class="fa-brands fa-whatsapp"></i>
                <div>
                    <strong><?= $L['auth_support_wa'] ?? 'WhatsApp Help' ?></strong>
                    <span><?= $L['auth_support_wa_desc'] ?? 'Instant chat for urgent issues' ?></span>
                </div>
            </a>
        </div>

        <a href="login.php" class="btn-primary" style="text-decoration: none; display: block;"><?= $L['auth_back_login'] ?? 'Back to Login' ?></a>
    </div>

    <footer>
        © <?= date('Y') ?> <?= $L['auth_rights'] ?? 'FRISUCODE. All rights reserved.' ?>
    </footer>
</div>

</body>
</html>
