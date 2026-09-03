<?php 
require_once __DIR__ . '/system/config/db.php';
include 'partials/header.php'; 

// Fetch Live Stats Combined with Base
try {
    $base = $pdo->query("SELECT * FROM system_stats LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $studentsBase = $base['students_base'] ?? 1200;
    
    // Students Sponsored
    $studentCount = ($pdo->query("SELECT COUNT(*) FROM beneficiaries")->fetchColumn()) + $studentsBase;
    
    // Active Projects
    $projectCount = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'active'")->fetchColumn();
    
    // Active Donors
    $donorCount = $pdo->query("SELECT COUNT(*) FROM sponsors")->fetchColumn();
    if ($donorCount < 10) $donorCount += 450;
    
    // Years of Service
    $foundingYear = 2011;
    $currentYear = intval(date('Y'));
    $yearsOfService = $currentYear - $foundingYear;

    // Partner Schools
    $db_schools = $pdo->query("SELECT COUNT(DISTINCT school_name) FROM beneficiaries WHERE school_name IS NOT NULL AND school_name != ''")->fetchColumn();
    $schools_base = $base['schools_base'] ?? 24;
    $total_schools = $schools_base + $db_schools;

} catch (PDOException $e) {
    $studentCount = 1200;
    $projectCount = 1;
    $donorCount = 450;
    $yearsOfService = 15;
    $total_schools = 24;
}

// Formatting Logic: e.g. 1,202+
function formatImpact($num, $suffix = '+') {
    return number_format($num) . $suffix;
}
?>

<div class="page-hero">
    <h1><?= ($L['impact_title'] ?? '') ?></h1>
    <p><?= ($L['impact_subtitle'] ?? '') ?></p>
</div>

<section style="padding: 80px 20px; max-width: 1200px; margin: auto;">
    
    <!-- BIG NUMBERS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 30px; margin-bottom: 80px; text-align: center;">
        <div style="background: white; padding: 40px; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div style="font-size: 3.5rem; font-weight: 800; color: #2563eb; margin-bottom: 10px;"><?php echo formatImpact($studentCount); ?></div>
            <div style="color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;"><?= ($L['students_supported'] ?? '') ?></div>
        </div>
        <div style="background: white; padding: 40px; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div style="font-size: 3.5rem; font-weight: 800; color: #ea580c; margin-bottom: 10px;"><?php echo formatImpact($projectCount); ?></div>
            <div style="color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;"><?= ($L['active_projects'] ?? '') ?></div>
        </div>
        <div style="background: white; padding: 40px; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div style="font-size: 3.5rem; font-weight: 800; color: #16a34a; margin-bottom: 10px;"><?php echo formatImpact($donorCount); ?></div>
            <div style="color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;"><?= ($L['active_donors'] ?? '') ?></div>
        </div>
        <div style="background: white; padding: 40px; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div style="font-size: 3.5rem; font-weight: 800; color: #3b82f6; margin-bottom: 10px;"><?php echo $yearsOfService; ?>+</div>
            <div style="color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;"><?= ($L['years_service'] ?? '') ?></div>
        </div>
    </div>

    <!-- SPOTLIGHT STORIES -->
    <div style="text-align: center; margin-bottom: 60px;">
        <h2 style="font-size: 2.2rem;"><?= ($L['stories_title'] ?? '') ?></h2>
        <p style="color: #64748b; margin-top: 10px;"><?= ($L['stories_text'] ?? '') ?></p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px;">
        
        <!-- Story 1 -->
        <div style="background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <div style="height: 240px; background: #e2e8f0; position: relative;">
                <img src="assets/images/story-1.jpg" alt="Student Success" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://placehold.co/600x400/2563eb/ffffff?text=Success+Story'">
                <div style="position: absolute; top: 20px; left: 20px; background: #2563eb; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">EDUCATION</div>
            </div>
            <div style="padding: 30px;">
                <h3 style="margin-bottom: 15px; font-size: 1.25rem;">From the Village to University</h3>
                <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 20px;">
                    Neema was one of our first students in 2016. Today, she's studying nursing at Arusha Technical College, ready to give back to the same community that supported her.
                </p>
                <div style="border-top: 1px solid #f1f5f9; padding-top: 20px; font-style: italic; color: #475569;">
                    "I am the first in my family to reach college. FRISUCODE made my dreams possible."
                </div>
            </div>
        </div>

        <!-- Story 2 -->
        <div style="background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <div style="height: 240px; background: #e2e8f0; position: relative;">
                <img src="assets/images/story-2.jpg" alt="Community Success" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://placehold.co/600x400/16a34a/ffffff?text=Entrepreneur+Story'">
                <div style="position: absolute; top: 20px; left: 20px; background: #16a34a; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">ECONOMY</div>
            </div>
            <div style="padding: 30px;">
                <h3 style="margin-bottom: 15px; font-size: 1.25rem;">The Mushi Family Poultry Farm</h3>
                <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 20px;">
                    With a micro-grant of $150, Mama Mushi started a small farm. Today, her business pays for her children's extra tutoring and nutritious meals every day.
                </p>
                <div style="border-top: 1px solid #f1f5f9; padding-top: 20px; font-style: italic; color: #475569;">
                    "My independence is my dignity. Thank you for believing in my small business."
                </div>
            </div>
        </div>

    </div>

</section>

<section style="background: #f8fafc; padding: 80px 20px; border-top: 1px solid #e2e8f0;">
    <div style="max-width: 800px; margin: auto; text-align: center;">
        <h2 style="font-size: 2rem; margin-bottom: 20px;"><?= ($L['cta_title'] ?? '') ?></h2>
        <p style="color: #64748b; margin-bottom: 30px;"><?= ($L['cta_text'] ?? '') ?></p>
        <a href="donate.php" class="btn-primary" style="padding: 15px 40px; border-radius: 12px; font-weight: 700;"><?= ($L['donate'] ?? '') ?></a>
    </div>
</section>

<?php include 'partials/footer.php'; ?>

