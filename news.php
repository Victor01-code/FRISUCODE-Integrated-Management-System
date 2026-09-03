<?php
require_once __DIR__ . '/system/config/db.php';
include 'partials/header.php';

// Fetch all published news with optional category filter
$categoryFilter = $_GET['category'] ?? '';
$validCategories = ['Education', 'Health', 'Community', 'Finance', 'Events', 'General'];

$where = "WHERE status = 'published'";
$params = [];

if ($categoryFilter && in_array($categoryFilter, $validCategories)) {
    $where .= " AND category = ?";
    $params[] = $categoryFilter;
}

try {
    $news = $pdo->prepare("SELECT * FROM news $where ORDER BY published_date DESC");
    $news->execute($params);
    $newsItems = $news->fetchAll(PDO::FETCH_ASSOC);

    // Fetch available categories
    $catStmt = $pdo->query("SELECT DISTINCT category FROM news WHERE status = 'published' ORDER BY category");
    $availableCategories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $newsItems = [];
    $availableCategories = [];
}
?>

<div class="page-hero" style="padding: 80px 5%;">
    <div style="max-width: 700px; margin: auto; text-align: center;">
        <span style="background: rgba(255,255,255,0.15); padding: 6px 18px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; display: inline-block; margin-bottom: 20px;">
            <i class="fa-regular fa-newspaper"></i> <?= $L['news_updates'] ?? 'News & Updates' ?>
        </span>
        <h1 style="font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; line-height: 1.1; margin-bottom: 1.2rem;">
            <?= $L['news_title'] ?? 'Latest News & Impact Stories' ?>
        </h1>
        <p style="font-size: 1.1rem; opacity: 0.85; max-width: 600px; margin: auto;">
            <?= $L['news_subtitle'] ?? 'Stay informed about FRISUCODE\'s work, projects, and community impact across East Africa.' ?>
        </p>
    </div>
</div>

<section style="padding: 60px 5%; background: #f8fafc;">
    <div style="max-width: 1200px; margin: auto;">

        <!-- Category Filter -->
        <?php if (!empty($availableCategories)): ?>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:40px;justify-content:center;">
            <a href="news.php?lang=<?= $lang ?>" style="padding:8px 20px;border-radius:20px;font-weight:700;font-size:0.88rem;text-decoration:none;transition:0.2s;background:<?= !$categoryFilter ? 'var(--primary)' : 'white' ?>;color:<?= !$categoryFilter ? 'white' : '#64748b' ?>;border:2px solid <?= !$categoryFilter ? 'var(--primary)' : '#e2e8f0' ?>;">
                <?= $L['all_categories'] ?? 'All Categories' ?>
            </a>
            <?php foreach ($availableCategories as $cat): ?>
                <a href="news.php?category=<?= urlencode($cat) ?>&lang=<?= $lang ?>" style="padding:8px 20px;border-radius:20px;font-weight:700;font-size:0.88rem;text-decoration:none;transition:0.2s;background:<?= $categoryFilter === $cat ? 'var(--primary)' : 'white' ?>;color:<?= $categoryFilter === $cat ? 'white' : '#64748b' ?>;border:2px solid <?= $categoryFilter === $cat ? 'var(--primary)' : '#e2e8f0' ?>;">
                    <?= htmlspecialchars($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- News Grid -->
        <?php if (!empty($newsItems)): ?>
            <div class="cards">
                <?php foreach ($newsItems as $item): ?>
                    <div class="card news-card" style="padding: 0; overflow: hidden;">
                        <?php if (($item['media_type'] ?? 'none') === 'image' && !empty($item['media_url'])): ?>
                            <div style="height: 220px; overflow: hidden;">
                                <img src="<?= htmlspecialchars($item['media_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </div>
                        <?php elseif (($item['media_type'] ?? 'none') === 'video' && !empty($item['media_url'])): ?>
                            <div style="height: 220px; overflow: hidden; background: #0f172a; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-play-circle" style="font-size: 3rem; color: white; opacity: 0.6;"></i>
                            </div>
                        <?php else: ?>
                            <div style="height: 120px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); display: flex; align-items: center; justify-content: center;">
                                <i class="fa-regular fa-newspaper" style="font-size: 2.5rem; color: rgba(255,255,255,0.3);"></i>
                            </div>
                        <?php endif; ?>

                        <div style="padding: 1.75rem 2rem;">
                            <div style="display:flex;gap:10px;align-items:center;margin-bottom:12px;flex-wrap:wrap;">
                                <span style="background:#eff6ff;color:#2563eb;padding:3px 10px;border-radius:12px;font-size:0.75rem;font-weight:700;">
                                    <?= htmlspecialchars($item['category'] ?? 'General') ?>
                                </span>
                                <small style="color:#94a3b8;font-size:0.8rem;">
                                    <i class="fa-regular fa-calendar" style="font-size:.7rem;"></i>
                                    <?= $item['published_date'] ? date('M d, Y', strtotime($item['published_date'])) : '' ?>
                                </small>
                            </div>

                            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; line-height: 1.3;">
                                <?= htmlspecialchars($item['title']) ?>
                            </h3>
                            <p style="font-size: 0.95rem; color: #64748b; line-height: 1.6; margin-bottom: 16px;">
                                <?= htmlspecialchars(substr($item['content'], 0, 140)) ?>...
                            </p>

                            <?php if (!empty($item['author'])): ?>
                                <div style="font-size:0.8rem;color:#94a3b8;margin-bottom:16px;">
                                    <i class="fa-solid fa-user" style="font-size:.7rem;"></i> <?= htmlspecialchars($item['author']) ?>
                                </div>
                            <?php endif; ?>

                            <a href="news_view.php?id=<?= $item['id'] ?>&lang=<?= $lang ?>" style="display:inline-flex;align-items:center;gap:6px;color:var(--primary);font-weight:700;font-size:0.9rem;text-decoration:none;transition:0.2s;" onmouseover="this.style.gap='12px'" onmouseout="this.style.gap='6px'">
                                <?= $L['read_more'] ?? 'Read More' ?> <i class="fa-solid fa-arrow-right" style="font-size:.8rem;"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div style="text-align:center;padding:80px 20px;color:#94a3b8;">
                <i class="fa-regular fa-newspaper" style="font-size:4rem;margin-bottom:20px;display:block;opacity:.3;"></i>
                <h3 style="font-size:1.4rem;color:#64748b;margin-bottom:8px;"><?= $L['no_news_yet'] ?? 'No News Articles Yet' ?></h3>
                <p><?= $L['no_news_text'] ?? 'Check back soon for updates from the field.' ?></p>
                <a href="index.php?lang=<?= $lang ?>" class="btn-primary" style="margin-top:24px;display:inline-block;"><?= $L['back_home'] ?? 'Back to Home' ?></a>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- CTA -->
<section class="cta">
    <h2><?= $L['cta_title'] ?? 'You Can Change a Life Today' ?></h2>
    <p><?= $L['cta_text'] ?? 'Your support helps children stay in school and communities thrive.' ?></p>
    <a href="donate.php?lang=<?= $lang ?>" class="btn-primary"><?= $L['donate'] ?? 'Donate Now' ?></a>
</section>

<?php include 'partials/footer.php'; ?>
