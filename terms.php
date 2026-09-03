<?php 
require_once __DIR__ . '/system/config/db.php';
include 'partials/header.php'; 
?>

<div class="page-hero">
    <h1><?= ($L['terms'] ?? 'Terms of Service') ?></h1>
    <p><?= ($L['terms_subtitle'] ?? 'Please read these terms carefully before using our website') ?></p>
</div>

<section style="padding: 80px 20px; max-width: 800px; margin: auto; line-height: 1.6; color: #475569; font-family: 'Inter', sans-serif;">
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['terms_sec1_title'] ?? '1. Terms') ?></h2>
    <p><?= ($L['terms_sec1_text'] ?? 'By accessing this web site, you are agreeing to be bound by these web site Terms and Conditions of Use, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws.') ?></p>
    
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['terms_sec2_title'] ?? '2. Use License') ?></h2>
    <p><?= ($L['terms_sec2_text'] ?? 'Permission is granted to temporarily download one copy of the materials (information or software) on FRISUCODE\'s web site for personal, non-commercial transitory viewing only.') ?></p>
    
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['terms_sec3_title'] ?? '3. Disclaimer') ?></h2>
    <p><?= ($L['terms_sec3_text'] ?? 'The materials on FRISUCODE\'s web site are provided "as is". FRISUCODE makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties, including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property.') ?></p>
    
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['terms_sec4_title'] ?? '4. Limitations') ?></h2>
    <p><?= ($L['terms_sec4_text'] ?? 'In no event shall FRISUCODE or its sponsors be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on FRISUCODE\'s site.') ?></p>
    
    <h2 style="color: #1e293b; margin-top: 30px; font-family: 'Outfit', sans-serif;"><?= ($L['terms_sec5_title'] ?? '5. Revisions and Errata') ?></h2>
    <p><?= ($L['terms_sec5_text'] ?? 'The materials appearing on FRISUCODE\'s web site could include technical, typographical, or photographic errors. FRISUCODE does not warrant that any of the materials on its web site are accurate, complete, or current.') ?></p>
</section>

<?php include 'partials/footer.php'; ?>
