# 📧 FRISUCODE Email Notification System

## Overview
The FRISUCODE Management System now features an automated email notification component. This system ensures that all new staff members and donors receive their login credentials securely and immediately upon account creation or credential updates.

## Component Structure
*   **Mailer Utility**: `system/mail/mailer.php` (Core logic and HTML templates)
*   **Developer Logs**: `system/mail/logs/` (Captured outputs during local development)

## Integration Points
Automated emails are dispatched during the following actions:

| Action | Source File | Subject |
| :--- | :--- | :--- |
| **New User Creation** | `system/users/create.php` | Welcome to FRISUCODE System - Your Login Credentials |
| **New Donor Creation** | `system/donors/create.php` | Welcome to FRISUCODE Donor Portal |
| **Admin Password Update** | `system/users/edit.php` | Security Notification: FRISUCODE Account Credentials Updated |

## Testing in Local Environments (XAMPP)
Since most local development environments do not have a configured SMTP server, the system includes a **Traffic Logger**. 

To verify that emails are "sending" correctly:
1. Perform a user creation or password update in the browser.
2. Navigate to `c:/xampp/htdocs/frisucode_ms/system/mail/logs/`.
3. Open the latest `mail_log_Y-m-d.txt` file.
4. You will see the recipient email, subject, and the exact message content that was dispatched.

---

## Production Setup (SMTP)
To move from local logging to real email delivery:

1.  **Configure PHP.ini**: Ensure your `SMTP` and `smtp_port` settings are configured in your server's `php.ini`.
2.  **Using PHPMailer (Recommended)**: For more robust delivery (SSL/TLS support), replace the `mail()` call in `system/mail/mailer.php` with a PHPMailer implementation.
    
    *Example logic update:*
    ```php
    // In system/mail/mailer.php
    function sendSystemEmail($to, $subject, $message) {
        // ... Load PHPMailer ...
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.yourserver.com';
        // ... Auth settings ...
        $mail->send();
    }
    ```

---

## Security Best Practices
*   **Credential Handling**: The system sends plaintext passwords during the initial onboarding only.
*   **Password Re-hashing**: All passwords are securely hashed using `password_hash(..., PASSWORD_DEFAULT)` before being stored in the database.
*   **User Policy**: The email templates explicitly instruct users to change their passwords immediately upon first login.
