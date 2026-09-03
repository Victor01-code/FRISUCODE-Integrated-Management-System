<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/system/config/db.php';

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $lang;

$langFile = __DIR__ . "/lang/{$lang}.php";
if (!file_exists($langFile)) {
    $langFile = __DIR__ . "/lang/en.php";
}
require $langFile;
if (!isset($L) || !is_array($L)) { $L = []; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: donate.php");
    exit;
}

// Collect Inputs
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$fullName = $firstName . ' ' . $lastName;
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$amount = $_POST['amount'] ?? 0;
$frequency = $_POST['frequency'] ?? 'once';
$cause = $_POST['cause'] ?? 'General Fund';
$paymentMethod = $_POST['payment_method'] ?? 'card';
$transactionId = ($paymentMethod === 'card' ? 'STRIPE-' : 'MPESA-') . strtoupper(uniqid());

if (empty($email) || $amount <= 0) {
    header("Location: donate.php?error=invalid");
    exit;
}

/* 
   ---------------------------------------------------------
   PAYMENT GATEWAY INTEGRATION (VISA/MASTERCARD)
   ---------------------------------------------------------
   To make this live with real Visa/Mastercards, you would 
   typically use "Stripe" or "Flutterwave".
   
   Example logic for Stripe:
   1. Include Stripe library (composer require stripe/stripe-php)
   2. \Stripe\Stripe::setApiKey('your_secret_key');
   3. Create a PaymentIntent or Checkout Session.
   4. Redirect user to Stripe's secure payment page.
   ---------------------------------------------------------
*/

try {
    $pdo->beginTransaction();

    // 1. Record in Public Donations
    $stmt = $pdo->prepare("INSERT INTO public_donations (full_name, email, phone, amount, frequency, cause, payment_method, transaction_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'completed')");
    $stmt->execute([$fullName, $email, $phone, $amount, $frequency, $cause, $paymentMethod, $transactionId]);

    // 2. Also record in central Finance Ledger as Income
    $financeDesc = "Public Donation ($paymentMethod) from $fullName - ID: $transactionId";
    $stmt2 = $pdo->prepare("INSERT INTO finance_records (type, amount, description, date) VALUES ('income', ?, ?, CURRENT_DATE)");
    $stmt2->execute([$amount, $financeDesc]);

    $pdo->commit();

    // 3. Dispatch Professional Email Receipt
    require_once __DIR__ . '/system/mail/mailer.php';
    $subject = "❤️ Thank You: Your Impact at FRISUCODE has Been Initiated! [Ref: $transactionId]";
    $emailMessage = getDonationReceiptTemplate($fullName, $amount, $cause, $transactionId, $paymentMethod);
    sendSystemEmail($email, $subject, $emailMessage);

    // Success State
    $success = true;
} catch (PDOException $e) {
    $pdo->rollBack();
    $error = "Payment recording failed: " . $e->getMessage();
    $success = false;
}

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $L['donate_success_title'] ?? 'Thank You - FRISUCODE' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .thank-you-card {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .icon-success {
            font-size: 60px;
            color: #22c55e;
            margin-bottom: 20px;
        }
        .txn-id {
            background: #f1f5f9;
            padding: 10px;
            display: inline-block;
            border-radius: 6px;
            font-family: monospace;
            margin: 15px 0;
            font-size: 0.9rem;
        }
        .method-badge {
            display: inline-block;
            padding: 5px 12px;
            background: #e0f2fe;
            color: #0369a1;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
    </style>
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body style="background: #f8fafc;">

    <div class="thank-you-card">
        <?php if ($success): ?>
            <div class="icon-success"><i class="fa-solid fa-circle-check"></i></div>
            <div class="method-badge">
                <i class="fa-solid <?php echo $paymentMethod === 'card' ? 'fa-credit-card' : 'fa-mobile-screen'; ?>"></i>
                <?= $L['donate_paid_via'] ?? 'Paid via' ?> <?php echo ucfirst($paymentMethod); ?>
            </div>
            
            <h1><?= $L['donate_empowerment'] ?? 'Empowerment Initiated!' ?></h1>
            <p><?= $L['donate_thank_you'] ?? 'Thank you,' ?> <strong><?php echo htmlspecialchars($firstName); ?></strong>. <?= $L['donate_generous'] ?? 'Your generous donation of' ?> <strong>$<?php echo number_format($amount, 2); ?></strong> <?= $L['donate_towards'] ?? 'towards' ?> <strong><?php echo htmlspecialchars($cause); ?></strong> <?= $L['donate_received'] ?? 'has been successfully received.' ?></p>
            
            <div class="txn-id"><?= $L['donate_txn_ref'] ?? 'Transaction Reference:' ?> <?php echo $transactionId; ?></div>
            
            <p style="color: #64748b; font-size: 0.95rem;"><?= $L['donate_support_via'] ?? 'Your support via' ?> <?php echo $paymentMethod==='card'?($L['donate_card_name'] ?? 'Mastercard/Visa'):($L['donate_mobile_name'] ?? 'Mobile Money'); ?> <?= $L['donate_helps_us'] ?? 'helps us stay in the field longer. A confirmation has been sent to your email.' ?></p>
            
            <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: center;">
                <a href="index.php" class="btn-primary"><?= $L['donate_back_home'] ?? 'Back to Home' ?></a>
                <a href="impact.php" class="btn-outline"><?= $L['donate_see_impact'] ?? 'See Your Impact' ?></a>
            </div>
        <?php else: ?>
            <div class="icon-error" style="color: #ef4444; font-size: 60px;"><i class="fa-solid fa-circle-xmark"></i></div>
            <h1><?= $L['donate_went_wrong'] ?? 'Something went wrong' ?></h1>
            <p><?php echo htmlspecialchars($error); ?></p>
            <a href="donate.php" class="btn-primary"><?= $L['donate_try_again'] ?? 'Try Again' ?></a>
        <?php endif; ?>
    </div>

</body>
</html>
