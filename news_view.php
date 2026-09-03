<?php
require_once __DIR__ . '/system/config/db.php';
include 'partials/header.php';

$newsId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$news = null;

if ($newsId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ? AND status = 'published'");
        $stmt->execute([$newsId]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $news = null;
    }
}

// Fallback if not found
if (!$news) {
    $news = [
        'id'           => 0,
        'title'        => $L['news_fallback_title'] ?? 'Project Update: Community Support Initiative',
        'published_date' => date('Y-m-d'),
        'content'      => $L['news_fallback_content'] ?? 'We are pleased to announce that our latest community support initiative in the Arusha region has successfully reached over 50 families. This project focuses on long-term sustainability and educational resources for the local school children.',
        'media_type'   => 'image',
        'media_url'    => 'https://placehold.co/1200x600/1e40af/ffffff?text=Community+Project+Update',
        'category'     => 'General',
        'author'       => 'FRISUCODE Team',
    ];
}

// Fetch related news (same category, different id)
try {
    $relStmt = $pdo->prepare("SELECT id, title, published_date, media_url, media_type, category FROM news WHERE status = 'published' AND id != ? ORDER BY published_date DESC LIMIT 3");
    $relStmt->execute([$newsId]);
    $related = $relStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $related = [];
}
?>

<div class="page-hero" style="padding: clamp(60px, 10vw, 100px) 5%;">
    <div style="max-width: 900px; margin: auto; text-align: center;">
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-bottom:18px;">
            <span style="background: rgba(255,255,255,0.15); padding: 5px 15px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">
                <?= htmlspecialchars($news['category'] ?? strtoupper($news['media_type'] ?? 'UPDATE')) ?>
            </span>
        </div>
        <h1 style="margin-top: 10px; font-size: clamp(1.8rem, 5vw, 3rem); line-height: 1.15; font-weight: 800;">
            <?= htmlspecialchars($news['title']) ?>
        </h1>
        <div style="margin-top: 20px; opacity: 0.8; display:flex;gap:20px;justify-content:center;flex-wrap:wrap;font-size:0.9rem;font-weight:600;">
            <?php if (!empty($news['author'])): ?>
                <span><i class="fa-solid fa-user"></i> <?= htmlspecialchars($news['author']) ?></span>
            <?php endif; ?>
            <span><i class="fa-solid fa-calendar-day"></i> <?= date('F d, Y', strtotime($news['published_date'])) ?></span>
        </div>
    </div>
</div>

<article style="padding: clamp(30px, 6vw, 60px) 5%; max-width: 900px; margin: auto;">

    <?php if (($news['media_type'] ?? '') === 'image' && !empty($news['media_url'])): ?>
        <img src="<?= htmlspecialchars($news['media_url']) ?>" alt="<?= htmlspecialchars($news['title']) ?>" style="width: 100%; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); margin-bottom: 40px; max-height: 500px; object-fit: cover;">
    <?php elseif (($news['media_type'] ?? '') === 'video' && !empty($news['media_url'])): ?>
        <div style="margin-bottom: 40px;">
            <video width="100%" controls style="border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                <source src="<?= htmlspecialchars($news['media_url']) ?>" type="video/mp4">
                <?= $L['video_not_supported'] ?? 'Your browser does not support the video tag.' ?>
            </video>
        </div>
    <?php endif; ?>

    <div style="font-size: 1.15rem; color: #1e293b; line-height: 1.85; font-family: 'Inter', system-ui, sans-serif;">
        <?php
        $paragraphs = explode("\n\n", str_replace("\r\n", "\n", $news['content']));
        $totalParas = count($paragraphs);
        $m1_inserted = false;
        $m2_inserted = false;
        
        foreach ($paragraphs as $i => $para) {
            echo '<p style="margin-bottom: 24px;">' . nl2br(htmlspecialchars(trim($para))) . '</p>';
            
            // Insert extra media 1 after ~1st third of content
            if (!$m1_inserted && $i >= ($totalParas / 3) - 1 && !empty($news['extra_media_1'])) {
                $ext = strtolower(pathinfo($news['extra_media_1'], PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm'])) {
                    echo '<video width="100%" controls style="border-radius:16px; margin: 30px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.05);"><source src="'.htmlspecialchars($news['extra_media_1']).'" type="video/mp4"></video>';
                } else {
                    echo '<img src="'.htmlspecialchars($news['extra_media_1']).'" alt="" style="width:100%; border-radius:16px; margin: 30px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">';
                }
                $m1_inserted = true;
            }
            
            // Insert extra media 2 after ~2nd third of content
            if (!$m2_inserted && $i >= ($totalParas * 2 / 3) - 1 && !empty($news['extra_media_2'])) {
                $ext = strtolower(pathinfo($news['extra_media_2'], PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm'])) {
                    echo '<video width="100%" controls style="border-radius:16px; margin: 30px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.05);"><source src="'.htmlspecialchars($news['extra_media_2']).'" type="video/mp4"></video>';
                } else {
                    echo '<img src="'.htmlspecialchars($news['extra_media_2']).'" alt="" style="width:100%; border-radius:16px; margin: 30px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">';
                }
                $m2_inserted = true;
            }
        }
        
        // If they weren't inserted because there were too few paragraphs, show them at the end
        if (!$m1_inserted && !empty($news['extra_media_1'])) {
            echo '<img src="'.htmlspecialchars($news['extra_media_1']).'" alt="" style="width:100%; border-radius:16px; margin: 30px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">';
        }
        if (!$m2_inserted && !empty($news['extra_media_2'])) {
            echo '<img src="'.htmlspecialchars($news['extra_media_2']).'" alt="" style="width:100%; border-radius:16px; margin: 30px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">';
        }
        ?>
    </div>

    <?php if (!empty($news['attachment_url'])): ?>
    <div style="margin-top: 40px; padding: 25px; background: #eff6ff; border-radius: 16px; border: 1px solid #bfdbfe; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
        <div style="width: 50px; height: 50px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <i class="fa-solid fa-file-pdf"></i>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <h4 style="margin: 0 0 5px; font-size: 1.1rem; color: #1e3a8a; font-family: 'Outfit';">
                <?= htmlspecialchars($news['attachment_name'] ?: 'Download Attachment') ?>
            </h4>
            <span style="font-size: 0.85rem; color: #3b82f6; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;"><?= __('Document Available') ?></span>
        </div>
        <a href="<?= htmlspecialchars($news['attachment_url']) ?>" target="_blank" download class="btn-primary" style="background: #2563eb; padding: 10px 20px;">
            <i class="fa-solid fa-download"></i> <?= __('Download File') ?>
        </a>
    </div>
    <?php endif; ?>

    <!-- Share Box -->
    <div style="margin-top: 60px; padding: 35px 40px; background: #f8fafc; border-radius: 20px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
        <div>
            <h4 style="font-size: 1.1rem; margin-bottom: 5px; font-family:'Outfit';"><?= $L['share_update'] ?? 'Share this update' ?></h4>
            <p style="color: #64748b; font-size: 0.9rem;"><?= $L['share_text'] ?? 'Help us spread the word about FRISUCODE\'s work.' ?></p>
        </div>
        <div style="display: flex; gap: 12px;">
            <?php $shareUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>
            <?php $shareTitle = urlencode($news['title']); ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" aria-label="Share on Facebook" style="width: 45px; height: 45px; background: #1877f2; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" aria-label="Share on Twitter" style="width: 45px; height: 45px; background: #000; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>" target="_blank" aria-label="Share on WhatsApp" style="width: 45px; height: 45px; background: #25d366; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
    </div>

    <!-- Back link -->
    <div style="margin-top: 50px; text-align: center;">
        <a href="news.php?lang=<?= $lang ?>" style="color: #2563eb; font-weight: 700; text-decoration: none; font-size: 1rem; display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #eff6ff; border-radius: 12px; transition: 0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
            <i class="fa-solid fa-arrow-left"></i> <?= $L['back_to_news'] ?? 'Back to All News' ?>
        </a>
    </div>

</article>

<!-- Related News -->
<?php if (!empty($related)): ?>
<section style="padding: 60px 5%; background: #f8fafc;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="font-size: 1.8rem; font-weight: 800; text-align: center; margin-bottom: 10px; font-family: 'Outfit';">
            <?= $L['more_updates'] ?? 'More Updates' ?>
        </h2>
        <p style="text-align:center;color:#64748b;margin-bottom:40px;"><?= $L['more_updates_text'] ?? 'Continue reading our latest news.' ?></p>
        <div class="cards">
            <?php foreach ($related as $item): ?>
                <div class="card news-card" style="padding:0;overflow:hidden;">
                    <?php if (($item['media_type'] ?? 'none') === 'image' && !empty($item['media_url'])): ?>
                        <div style="height:180px;overflow:hidden;">
                            <img src="<?= htmlspecialchars($item['media_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    <?php else: ?>
                        <div style="height:100px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);display:flex;align-items:center;justify-content:center;">
                            <i class="fa-regular fa-newspaper" style="font-size:2rem;color:rgba(255,255,255,0.3);"></i>
                        </div>
                    <?php endif; ?>
                    <div style="padding:1.5rem;">
                        <small style="color:#2563eb;font-weight:700;font-size:0.8rem;"><?= htmlspecialchars($item['category'] ?? '') ?> · <?= $item['published_date'] ? date('M d, Y', strtotime($item['published_date'])) : '' ?></small>
                        <h3 style="font-size:1.05rem;margin:8px 0 12px;line-height:1.35;font-weight:700;"><?= htmlspecialchars($item['title']) ?></h3>
                        <a href="news_view.php?id=<?= $item['id'] ?>&lang=<?= $lang ?>" style="color:var(--primary);font-weight:700;font-size:0.88rem;">
                            <?= $L['read_more'] ?? 'Read More' ?> <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'partials/footer.php'; ?>
