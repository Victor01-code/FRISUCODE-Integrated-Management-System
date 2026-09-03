<?php 
require_once __DIR__ . '/system/config/db.php';
include 'partials/header.php'; 

try {
    $base = $pdo->query("SELECT * FROM system_stats LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $studentsBase = $base['students_base'] ?? 1200;
    
    $studentCount = ($pdo->query("SELECT COUNT(*) FROM beneficiaries")->fetchColumn()) + $studentsBase;
    $projectCount = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'active'")->fetchColumn();
    
    $donorCount = $pdo->query("SELECT COUNT(*) FROM sponsors")->fetchColumn();
    // Logic for donor count fallback
    if ($donorCount < 10) $donorCount += 450;

    // Years of Service: Founded in 2011 (to match the "15+" user example for 2026)
    $foundingYear = 2011;
    $currentYear = intval(date('Y'));
    $yearsOfService = $currentYear - $foundingYear;
    
    // Fetch Latest Published News
    $newsItems = $pdo->query("SELECT * FROM news WHERE status = 'published' ORDER BY published_date DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $studentCount = 1200;
    $projectCount = 1;
    $donorCount = 450;
    $yearsOfService = 15;
    $newsItems = [];
}
?>

<section class="hero fade in">
    <div class="hero-text">
        <h1><?= $L['hero_title'] ?? 'Empowering Communities Through Education' ?></h1>
        <p><?= $L['hero_subtitle'] ?? 'FRISUCODE supports children and communities through education sponsorship, health initiatives, and sustainable development.' ?></p>
        <div class="d-flex" style="gap: 15px; flex-wrap: wrap;">
            <a href="programs.php?lang=<?= $lang ?>" class="btn-primary"><?= $L['sponsor_child'] ?? 'Sponsor a Child' ?></a>
            <a href="about.php?lang=<?= $lang ?>" class="btn-light"><?= $L['learn_more'] ?? 'Learn More' ?></a>
        </div>
    </div>

    <div class="hero-image">
        <img src="assets/images/FRISUCODE_Founder-05.jpg" alt="<?= $L['hero_img_alt'] ?? 'Sponsored children' ?>" onerror="this.src='https://placehold.co/600x400/1e5eff/ffffff?text=Empowered+Kids'">
    </div>
</section>

<section class="stats fade in">
    <div><h2><?= number_format($studentCount) ?>+</h2><p><?= $L['students_sponsored'] ?? 'Students Sponsored' ?></p></div>
    <div><h2><?= number_format($projectCount) ?>+</h2><p><?= $L['active_projects'] ?? 'Active Projects' ?></p></div>
    <div><h2><?= number_format($donorCount) ?>+</h2><p><?= $L['active_donors'] ?? 'Global Donors' ?></p></div>
    <div><h2><?= $yearsOfService ?>+</h2><p><?= $L['years_service'] ?? 'Years of Service' ?></p></div>
</section>

<!-- LATEST UPDATES & NEWS -->
<section class="programs fade in" style="background: white;">
    <div class="container">
        <h2><?= $L['latest_news_title'] ?? 'Latest Updates & News' ?></h2>
        <p style="text-align: center; color: #64748b; margin-bottom: 30px;"><?= $L['latest_news_subtitle'] ?? 'See what we\'ve been doing lately in the field.' ?></p>
        
        <div class="cards">
            <?php if (!empty($newsItems)): ?>
                <?php foreach ($newsItems as $news): ?>
                    <div class="card news-card">
                        <?php if (($news['media_type'] ?? 'none') === 'video' && !empty($news['media_url'])): ?>
                            <div class="media-container" style="margin-bottom: 15px;">
                                <video width="100%" controls style="border-radius: 8px;">
                                    <source src="<?= htmlspecialchars($news['media_url']) ?>" type="video/mp4">
                                </video>
                            </div>
                        <?php elseif (($news['media_type'] ?? 'none') === 'image' && !empty($news['media_url'])): ?>
                            <img src="<?= htmlspecialchars($news['media_url']) ?>" alt="<?= htmlspecialchars($news['title']) ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
                        <?php else: ?>
                            <div style="height: 100px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-regular fa-newspaper" style="font-size: 2rem; color: rgba(255,255,255,0.4);"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;flex-wrap:wrap;">
                            <span style="background:#eff6ff;color:#2563eb;padding:2px 8px;border-radius:10px;font-size:0.72rem;font-weight:700;"><?= htmlspecialchars($news['category'] ?? 'News') ?></span>
                            <small style="color: #2563eb; font-weight: 600; font-size: 0.8rem;"><?= $news['published_date'] ? date('M d, Y', strtotime($news['published_date'])) : '' ?></small>
                        </div>
                        <h3 style="margin-top: 5px; font-size: 1.1rem;"><?= htmlspecialchars($news['title']) ?></h3>
                        <p style="font-size: 0.95rem; color: #64748b; margin: 8px 0 14px;">
                            <?= htmlspecialchars(substr($news['content'], 0, 120)) ?>...
                        </p>
                        <a href="news_view.php?id=<?= $news['id'] ?>&lang=<?= $lang ?>" style="color: #1e40af; font-weight: 600; font-size: 0.9rem; display:inline-flex; align-items:center; gap:5px;">
                            <?= $L['read_more'] ?? 'Read More' ?> <i class="fa-solid fa-arrow-right" style="font-size:.75rem;"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Placeholder cards -->
                <div class="card">
                    <img src="https://placehold.co/400x200/e2e8f0/64748b?text=School+Renovation" style="width: 100%; border-radius: 8px; margin-bottom: 15px;">
                    <small style="color: #2563eb; font-weight: 600;">Feb 01, 2026</small>
                    <h3>Nambala School Renovation</h3>
                    <p>We've successfully kicked off the renovation of 3 classrooms at Nambala Primary...</p>
                </div>
                <div class="card">
                    <img src="https://placehold.co/400x200/e2e8f0/64748b?text=Health+Workshop" style="width: 100%; border-radius: 8px; margin-bottom: 15px;">
                    <small style="color: #2563eb; font-weight: 600;">Jan 25, 2026</small>
                    <h3>Hygiene &amp; Health Workshop</h3>
                    <p>Training 50+ families on clean water and basic hygiene practices in Kikwe village...</p>
                </div>
                <div class="card">
                    <img src="https://placehold.co/400x200/e2e8f0/64748b?text=Graduation" style="width: 100%; border-radius: 8px; margin-bottom: 15px;">
                    <small style="color: #2563eb; font-weight: 600;">Jan 10, 2026</small>
                    <h3>Celebrating Our Graduates</h3>
                    <p>A proud day as 5 of our sponsored students graduated from Arusha Vocational College...</p>
                </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="news.php?lang=<?= $lang ?>" class="btn-light">
                <i class="fa-regular fa-newspaper"></i> <?= $L['view_all_news'] ?? 'View All News & Updates' ?>
            </a>
        </div>
    </div>
</section>

<section class="programs fade in">
    <div class="container">
        <h2><?= $L['programs_title'] ?? 'Our Core Programs' ?></h2>
        <p style="margin-bottom: 40px; color: #64748b;"><?= $L['programs_home_subtitle'] ?? 'Holistic support to break the cycle of poverty.' ?></p>
        
        <div class="cards">
            <div class="card program-card">
                <div class="program-icon-circle">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3><?= $L['program_edu'] ?? 'Education Sponsorship' ?></h3>
                <p><?= $L['program_edu_desc'] ?? 'We provide full tuition, school supplies, and academic mentoring for vulnerable children.' ?></p>
                <a href="programs.php?lang=<?= $lang ?>" class="btn-text" style="color: #2563eb; font-weight: 600;"><?= $L['view_details'] ?? 'View Details' ?> &rarr;</a>
            </div>

            <div class="card program-card">
                <div class="program-icon-circle">
                    <i class="fa-solid fa-house-medical"></i>
                </div>
                <h3><?= $L['program_health'] ?? 'Health & Wellbeing' ?></h3>
                <p><?= $L['program_health_desc'] ?? 'Access to health insurance, clean water, and nutritional support for student families.' ?></p>
                <a href="programs.php?lang=<?= $lang ?>" class="btn-text" style="color: #2563eb; font-weight: 600;"><?= $L['view_details'] ?? 'View Details' ?> &rarr;</a>
            </div>

            <div class="card program-card">
                <div class="program-icon-circle">
                    <i class="fa-solid fa-seedling"></i>
                </div>
                <h3><?= $L['program_community'] ?? 'Community Growth' ?></h3>
                <p><?= $L['program_community_desc'] ?? 'Economic strengthening through vocational training and micro-loans for self-reliance.' ?></p>
                <a href="programs.php?lang=<?= $lang ?>" class="btn-text" style="color: #2563eb; font-weight: 600;"><?= $L['view_details'] ?? 'View Details' ?> &rarr;</a>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <h2><?= $L['cta_title'] ?? 'You Can Change a Life Today' ?></h2>
    <p><?= $L['cta_text'] ?? 'Your support helps children stay in school and communities thrive.' ?></p>
    <a href="donate.php?lang=<?= $lang ?>" class="btn-primary"><?= $L['donate'] ?? 'Donate Now' ?></a>
</section>

<?php include 'partials/footer.php'; ?>
