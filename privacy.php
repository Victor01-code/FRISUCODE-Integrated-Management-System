<?php 
require_once __DIR__ . '/system/config/db.php';
include 'partials/header.php'; 
?>

<div class="page-hero">
    <h1><?= ($L['privacy'] ?? 'Privacy Policy') ?></h1>
    <p><?= ($L['privacy_subtitle'] ?? 'How we protect your personal information and respect your privacy') ?></p>
</div>

<section style="padding: 80px 20px; max-width: 800px; margin: auto; line-height: 1.6; color: #475569; font-family: 'Inter', sans-serif;">
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['privacy_sec1_title'] ?? '1. Information We Collect') ?></h2>
    <p><?= ($L['privacy_sec1_text'] ?? 'We collect information you provide directly to us, such as when you make a donation, register for our newsletter, contact us, or participate in our programs. This may include your name, email address, phone number, and payment details.') ?></p>
    
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['privacy_sec2_title'] ?? '2. How We Use Your Information') ?></h2>
    <p><?= ($L['privacy_sec2_text'] ?? 'We use the information we collect to process donations, send updates and newsletters, respond to inquiries, and improve our services and programs to support children and communities.') ?></p>
    
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['privacy_sec3_title'] ?? '3. Data Security and Protection') ?></h2>
    <p><?= ($L['privacy_sec3_text'] ?? 'We implement a variety of security measures to maintain the safety of your personal information. Your personal data is stored in secured networks and is only accessible by a limited number of persons with special access rights.') ?></p>
    
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['privacy_sec4_title'] ?? '4. Cookies') ?></h2>
    <p><?= ($L['privacy_sec4_text'] ?? 'We use cookies to understand and save your preferences for future visits and compile aggregate data about site traffic and site interaction so that we can offer better site experiences in the future.') ?></p>
    
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['privacy_sec5_title'] ?? '5. Contact Us') ?></h2>
    <p><?= ($L['privacy_sec5_text'] ?? 'If you have any questions regarding this privacy policy, you may contact us using the information on our contact page.') ?></p>
</section>

<?php include 'partials/footer.php'; ?>
