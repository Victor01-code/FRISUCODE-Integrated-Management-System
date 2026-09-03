<?php
/**
 * System Mailer Component
 * Centralized function to handle email notifications across the FRISUCODE Management System.
 */

function sendSystemEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: FRISUCODE System <no-reply@frisucode.org>" . "\r\n";

    // Since this is likely a local development environment (XAMPP), 
    // we also log all sent emails to a file for easy verification.
    $logDir = __DIR__ . '/logs/';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    $logFile = $logDir . 'mail_log_' . date('Y-m-d') . '.txt';
    $logContent = "--------------------------------------------------\n";
    $logContent .= "DATE: " . date('Y-m-d H:i:s') . "\n";
    $logContent .= "TO: " . $to . "\n";
    $logContent .= "SUBJECT: " . $subject . "\n";
    $logContent .= "MESSAGE: " . strip_tags($message) . "\n";
    $logContent .= "--------------------------------------------------\n\n";
    
    file_put_contents($logFile, $logContent, FILE_APPEND);

    // Attempt to send via PHP's mail function (requires local mail server configuration)
    // The user can later swap this with PHPMailer or a 3rd party API easily.
    try {
        @mail($to, $subject, $message, $headers);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Utility to generate the HTML template for account credentials
 */
function getAccountCredentialTemplate($name, $email, $password, $isNew = true) {
    if ($isNew) {
        $action = "New account created";
        $title = "Welcome to FRISUCODE System";
        $intro = "Welcome to the family! We are excited to have you on board. Your account for the FRISUCODE Integrated Management System has been successfully set up.";
    } else {
        $action = "Credentials updated";
        $title = "Account Security Update";
        $intro = "This is a security notification to inform you that your login credentials for the FRISUCODE Management System have been updated by an administrator.";
    }
    
    return "
    <html>
    <head>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #334155; }
            .container { max-width: 600px; margin: 20px auto; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
            .header { background: linear-gradient(135deg, #1e3a8a, #3b82f6); padding: 30px; text-align: center; color: white; }
            .content { padding: 40px; background: white; }
            .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 0.8rem; color: #94a3b8; }
            .creds-box { background: #f1f5f9; padding: 20px; border-radius: 12px; margin: 25px 0; border: 1px solid #e2e8f0; }
            .button { display: inline-block; background: #2563eb; color: white !important; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 700; margin-top: 20px; }
            strong { color: #0f172a; }
        </style>
    <link rel='icon' type='image/png' href='/frisucode_ms/assets/images/logo.png'>
</head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1 style='margin:0; font-size: 1.5rem;'>FRISUCODE</h1>
                <p style='margin:5px 0 0; opacity: 0.8;'>$title</p>
            </div>
            <div class='content'>
                <h2 style='margin-top:0; color: #1e3a8a;'>Hello, $name</h2>
                <p style='font-size: 1.1rem; color: #475569;'>$intro</p>
                <p>Please find your unique access credentials below. Keep this information secure.</p>
                
                <div class='creds-box'>
                    <strong>Login URL:</strong> <a href='http://" . $_SERVER['HTTP_HOST'] . "/frisucode_ms/system/auth/login.php'>Access Portal</a><br>
                    <strong>Email:</strong> $email<br>
                    <strong>Password:</strong> $password
                </div>
                
                <p style='color: #ef4444; font-weight: 600; font-size: 0.9rem;'>
                    <strong>Security Warning:</strong> For your protection, do not share these credentials with anyone. Please change your password immediately after your first login.
                </p>
                
                <center>
                    <a href='http://" . $_SERVER['HTTP_HOST'] . "/frisucode_ms/system/auth/login.php' class='button'>Log In to Portal</a>
                </center>
            </div>
            <div class='footer'>
                <p>This is an automated system message. Please do not reply directly to this email.</p>
                © " . date('Y') . " FRISUCODE Organizational Management. All Rights Reserved.
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Utility to generate the HTML template for donation receipts
 */
function getDonationReceiptTemplate($name, $amount, $cause, $txnId, $method) {
    $date = date('F j, Y');
    return "
    <html>
    <head>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #334155; }
            .container { max-width: 600px; margin: 20px auto; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
            .header { background: linear-gradient(135deg, #059669, #10b981); padding: 30px; text-align: center; color: white; }
            .content { padding: 40px; background: white; }
            .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 0.8rem; color: #94a3b8; }
            .receipt-card { background: #f8fafc; padding: 25px; border-radius: 12px; margin: 25px 0; border: 1px dashed #cbd5e1; }
            .button { display: inline-block; background: #059669; color: white !important; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 700; margin-top: 20px; }
            .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px; }
            .stat-item { font-size: 0.85rem; }
            strong { color: #0f172a; }
            .hero-text { font-size: 1.25rem; font-weight: 700; color: #065f46; margin-bottom: 10px; }
        </style>
    <link rel='icon' type='image/png' href='/frisucode_ms/assets/images/logo.png'>
</head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1 style='margin:0; font-size: 1.5rem;'>FRISUCODE</h1>
                <p style='margin:5px 0 0; opacity: 0.8;'>Official Donation Receipt</p>
            </div>
            <div class='content'>
                <div class='hero-text'>Empowerment Initiated!</div>
                <p>Dear <strong>$name</strong>, thank you for your generous contribution. Your support is actively helping us drive community transformation.</p>
                
                <div class='receipt-card'>
                    <div style='text-align: center; font-weight: 800; font-size: 1.5rem; color: #059669; margin-bottom: 20px;'>
                        $" . number_format($amount, 2) . "
                    </div>
                    <div style='border-top: 1px solid #e2e8f0; padding-top: 15px;'>
                        <strong>Designated Cause:</strong> $cause<br>
                        <strong>Transaction ID:</strong> <code style='background:#f1f5f9; padding:2px 4px;'>$txnId</code><br>
                        <strong>Payment Method:</strong> " . ucfirst($method) . "<br>
                        <strong>Date:</strong> $date
                    </div>
                </div>
                
                <p>This donation will go directly toward our programs. You can follow the progress of our projects and see the cumulative impact of your support on our official portal.</p>
                
                <center>
                    <a href='http://" . $_SERVER['HTTP_HOST'] . "/frisucode_ms/impact.php' class='button'>View Our Impact</a>
                </center>
            </div>
            <div class='footer'>
                <p>FRISUCODE is a registered non-profit organization. This email serves as your official digital receipt.</p>
                © " . date('Y') . " FRISUCODE. All Rights Reserved.
            </div>
        </div>
    </body>
    </html>";
}
