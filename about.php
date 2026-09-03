<?php 
require_once __DIR__ . '/system/config/db.php';
include 'partials/header.php'; 

// Years of Service calculation
$foundingYear = 2011;
$currentYear = intval(date('Y'));
$yearsOfService = $currentYear - $foundingYear;
?>

<div class="page-hero">
    <h1><?= ($L['about_title'] ?? '') ?></h1>
    <p><?= ($L['about_subtitle'] ?? '') ?> — Tanzanian NGO Reg: #67812</p>
</div>

<!-- MISSION SECTION -->
<section style="padding: 80px 20px; max-width: 1200px; margin: auto;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; align-items: center;">
        <div>
            <span style="color: #2563eb; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;"><?= ($L['about_title'] ?? '') ?></span>
            <h2 style="font-size: 2.5rem; margin: 10px 0 20px; line-height: 1.2;"><?= ($L['who_we_are_title'] ?? '') ?></h2>
            <p style="font-size: 1.1rem; color: #475569; margin-bottom: 20px;">
                <?= ($L['who_we_are_text'] ?? '') ?>
            </p>
        </div>
        <div style="position: relative;">
            <img src="assets/images/FRISUCODE_Founder-05.jpg" alt="Children Smiling" style="width: 100%; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);" onerror="this.src='https://placehold.co/600x400/2563eb/ffffff?text=Our+Impact'">
            <div class="years-badge" style="position: absolute; bottom: -20px; right: -20px; background: #ea580c; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 10px 20px rgba(234, 88, 12, 0.3);">
                <strong style="font-size: 2rem; display: block;"><?= $yearsOfService ?>+</strong>
                <span><?= ($L['years_impact'] ?? 'Years of Impact') ?></span>
            </div>
        </div>
    </div>
</section>

<!-- VALUES SECTION -->
<section style="background: #f1f5f9; padding: 80px 20px;">
    <div style="max-width: 1200px; margin: auto; text-align: center;">
        <h2 style="font-size: 2rem; margin-bottom: 50px;"><?= ($L['our_philosophy'] ?? 'Our Core Philosophy') ?></h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            
            <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <div style="width: 60px; height: 60px; background: #eff6ff; color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 20px;">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <h3 style="margin-bottom: 15px;"><?= ($L['mission_title'] ?? '') ?></h3>
                <p style="color: #64748b;"><?= ($L['mission_text'] ?? '') ?></p>
            </div>

            <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <div style="width: 60px; height: 60px; background: #fff7ed; color: #ea580c; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 20px;">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <h3 style="margin-bottom: 15px;"><?= ($L['vision_title'] ?? '') ?></h3>
                <p style="color: #64748b;"><?= ($L['vision_text'] ?? '') ?></p>
            </div>

            <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <div style="width: 60px; height: 60px; background: #f0fdf4; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 20px;">
                    <i class="fa-solid fa-handshake-angle"></i>
                </div>
                <h3 style="margin-bottom: 15px;"><?= ($L['our_values'] ?? 'Our Values') ?></h3>
                <p style="color: #64748b;"><?= ($L['our_values_desc'] ?? 'Integrity, Transparency, and a Deep Commitment to the grassroots communities we serve every single day.') ?></p>
            </div>

        </div>
    </div>
</section>

<!-- TEAM SECTION -->
<section style="padding: 80px 20px; max-width: 1200px; margin: auto;">
    <div style="text-align: center; margin-bottom: 60px;">
        <h2 style="font-size: 2.2rem;"><?= ($L['hearts_behind'] ?? 'The Hearts Behind FRISUCODE') ?></h2>
        <p style="color: #64748b; margin-top: 10px;"><?= ($L['meet_dedicated'] ?? 'Meet the dedicated team working on the ground in Arusha.') ?></p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px;">
        
        <div style="text-align: center;">
            <div style="width: 180px; height: 180px; border-radius: 50%; margin: 0 auto 20px; overflow: hidden; border: 5px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                <img src="assets/images/team-director.jpg" alt="Baraka Mshana" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://placehold.co/200x200/e2e8f0/64748b?text=Baraka'">
            </div>
            <h4 style="font-size: 1.2rem; margin-bottom: 5px;">Baraka Mshana</h4>
            <span style="color: #2563eb; font-weight: 600; font-size: 0.9rem;"><?= ($L['exec_director'] ?? 'Executive Director') ?></span>
        </div>

        <div style="text-align: center;">
            <div style="width: 180px; height: 180px; border-radius: 50%; margin: 0 auto 20px; overflow: hidden; border: 5px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                <img src="assets/images/team-manager.jpg" alt="Sarah Johnson" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://placehold.co/200x200/e2e8f0/64748b?text=Staff'">
            </div>
            <h4 style="font-size: 1.2rem; margin-bottom: 5px;">Frank</h4>
            <span style="color: #2563eb; font-weight: 600; font-size: 0.9rem;"><?= ($L['prog_operations'] ?? 'Program Operations') ?></span>
        </div>

        <div style="text-align: center;">
            <div style="width: 180px; height: 180px; border-radius: 50%; margin: 0 auto 20px; overflow: hidden; border: 5px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                <img src="assets/images/team-finance.jpg" alt="David Kimaro" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://placehold.co/200x200/e2e8f0/64748b?text=Staff'">
            </div>
            <h4 style="font-size: 1.2rem; margin-bottom: 5px;">David </h4>
            <span style="color: #2563eb; font-weight: 600; font-size: 0.9rem;"><?= ($L['finance_logistics'] ?? 'Finance & Logistics') ?></span>
        </div>

    </div>
</section>

<?php include 'partials/footer.php'; ?>
