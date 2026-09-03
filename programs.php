<?php include 'partials/header.php'; ?>

<div class="page-hero">
    <h1><?= ($L['programs_title'] ?? '') ?></h1>
    <p><?= ($L['programs_subtitle'] ?? '') ?></p>
</div>

<section style="padding: 80px 20px; max-width: 1100px; margin: auto;">
    
    <!-- EDUCATION -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 60px; align-items: center; margin-bottom: 100px;">
        <div>
            <div style="width: 50px; height: 50px; background: #eff6ff; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-bottom: 20px;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h2 style="font-size: 2rem; margin-bottom: 20px;"><?= ($L['program_edu'] ?? '') ?></h2>
            <p style="color: #475569; font-size: 1.1rem; line-height: 1.8; margin-bottom: 25px;">
                <?= ($L['program_edu_desc'] ?? '') ?>
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> <?= ($L['school_fees'] ?? 'School Fees') ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> <?= ($L['uniforms'] ?? 'Uniforms') ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> <?= ($L['mentorship'] ?? 'Mentorship') ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> <?= ($L['stationery'] ?? 'Stationery') ?>
                </div>
            </div>
        </div>
        <div style="border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);">
            <img src="assets/images/program-edu.png" alt="Students in school" style="width: 100%; display: block;" onerror="this.src='https://placehold.co/600x600/2563eb/ffffff?text=Education+Support'">
        </div>
    </div>

    <!-- HEALTH -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 60px; align-items: center; margin-bottom: 100px; direction: rtl;">
        <div style="direction: ltr;">
            <div style="width: 50px; height: 50px; background: #fef2f2; color: #dc2626; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-bottom: 20px;">
                <i class="fa-solid fa-house-medical"></i>
            </div>
            <h2 style="font-size: 2rem; margin-bottom: 20px;"><?= ($L['program_health'] ?? '') ?></h2>
            <p style="color: #475569; font-size: 1.1rem; line-height: 1.8; margin-bottom: 25px;">
                <?= ($L['program_health_desc'] ?? '') ?>
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #dc2626;"></i> <?= ($L['insurance'] ?? 'Insurance') ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #dc2626;"></i> <?= ($L['first_aid'] ?? 'First Aid') ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #dc2626;"></i> <?= ($L['nutrition'] ?? 'Nutrition') ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #dc2626;"></i> <?= ($L['workshops'] ?? 'Workshops') ?>
                </div>
            </div>
        </div>
        <div style="border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);">
            <img src="assets/images/program-health.png" alt="Healthcare support" style="width: 100%; display: block;" onerror="this.src='https://placehold.co/600x600/dc2626/ffffff?text=Health+Care'">
        </div>
    </div>

    <!-- ECONOMIC -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 60px; align-items: center;">
        <div>
            <div style="width: 50px; height: 50px; background: #f0fdf4; color: #16a34a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; margin-bottom: 20px;">
                <i class="fa-solid fa-seedling"></i>
            </div>
            <h2 style="font-size: 2rem; margin-bottom: 20px;"><?= ($L['program_community'] ?? '') ?></h2>
            <p style="color: #475569; font-size: 1.1rem; line-height: 1.8; margin-bottom: 25px;">
                <?= ($L['program_community_desc'] ?? '') ?>
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> <?= ($L['micro_loans'] ?? 'Micro-loans') ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> <?= ($L['farming'] ?? 'Farming') ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> <?= ($L['training'] ?? 'Training') ?>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i> <?= ($L['self_help_groups'] ?? 'Self-help Groups') ?>
                </div>
            </div>
        </div>
        <div style="border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);">
            <img src="assets/images/program-eco.png" alt="Economic empowerment" style="width: 100%; display: block;" onerror="this.src='https://placehold.co/600x600/16a34a/ffffff?text=Economic+Support'">
        </div>
    </div>

</section>

<section style="background: #1e3a8a; color: white; padding: 80px 20px; text-align: center; margin-top: 100px;">
    <h2 style="font-size: 2.2rem; margin-bottom: 20px;"><?= ($L['cta_title'] ?? '') ?></h2>
    <p style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 30px;"><?= ($L['cta_text'] ?? '') ?></p>
    <a href="donate.php" class="btn-primary" style="background: #ea580c; border:none; padding: 15px 40px; font-size: 1.1rem; border-radius: 12px; color:white; font-weight: 700;"><?= ($L['donate'] ?? '') ?></a>
</section>

<?php include 'partials/footer.php'; ?>

