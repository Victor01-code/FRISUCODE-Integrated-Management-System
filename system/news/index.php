<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'project_manager', 'me_officer', 'field_officer', 'finance']);
require_once __DIR__ . '/../config/db.php';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([(int)$_GET['delete']]);
    header("Location: index.php?deleted=1");
    exit;
}

// Fetch all news
$news = $pdo->query("SELECT * FROM news ORDER BY published_date DESC")->fetchAll(PDO::FETCH_ASSOC);
$total = count($news);
$published = count(array_filter($news, fn($n) => ($n['status'] ?? 'published') === 'published'));
$drafts = $total - $published;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | News &amp; Updates Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div style="padding: 0 40px 40px;">

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success" style="background:#f0fdf4;border:1px solid #86efac;color:#166534;padding:14px 20px;border-radius:12px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-circle-check"></i> <?= __('News item deleted successfully.') ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['saved'])): ?>
                <div class="alert alert-success" style="background:#f0fdf4;border:1px solid #86efac;color:#166534;padding:14px 20px;border-radius:12px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-circle-check"></i> <?= __('News article saved successfully.') ?>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="page-header" style="margin-bottom: 30px;">
                <div>
                    <h2><?= __('News & Updates Management') ?></h2>
                    <p style="color:#64748b;font-size:0.9rem;margin-top:4px;"><?= __('Manage articles published on the public website') ?></p>
                </div>
                <a href="create.php" class="btn-primary">
                    <i class="fa-solid fa-plus"></i> <?= __('Add News Article') ?>
                </a>
            </div>

            <!-- Stats -->
            <div class="stats-grid" style="margin-bottom: 30px;">
                <div class="stat-card fade-in">
                    <div style="display:flex;justify-content:space-between;">
                        <h4><?= __('Total Articles') ?></h4>
                        <div style="width:40px;height:40px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#2563eb;">
                            <i class="fa-regular fa-newspaper"></i>
                        </div>
                    </div>
                    <strong><?= $total ?></strong>
                    <span class="neutral" style="background:#f8fafc;padding:4px 8px;border-radius:6px;font-size:0.75rem;"><?= __('All Articles') ?></span>
                </div>
                <div class="stat-card fade-in" style="animation-delay:.1s;">
                    <div style="display:flex;justify-content:space-between;">
                        <h4><?= __('Published') ?></h4>
                        <div style="width:40px;height:40px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#16a34a;">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <strong style="color:#16a34a;"><?= $published ?></strong>
                    <span class="up" style="background:#f0fdf4;padding:4px 8px;border-radius:6px;font-size:0.75rem;color:#16a34a;"><?= __('Live on website') ?></span>
                </div>
                <div class="stat-card fade-in" style="animation-delay:.2s;">
                    <div style="display:flex;justify-content:space-between;">
                        <h4><?= __('Drafts') ?></h4>
                        <div style="width:40px;height:40px;border-radius:12px;background:#fefce8;display:flex;align-items:center;justify-content:center;color:#d97706;">
                            <i class="fa-solid fa-file-pen"></i>
                        </div>
                    </div>
                    <strong style="color:#d97706;"><?= $drafts ?></strong>
                    <span class="neutral" style="background:#fefce8;padding:4px 8px;border-radius:6px;font-size:0.75rem;color:#d97706;"><?= __('Not yet published') ?></span>
                </div>
            </div>

            <!-- News Table -->
            <div class="card" style="padding:0;overflow:hidden;">
                <div class="table-scroll-wrapper" style="max-height: 600px; overflow: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= __('Title') ?></th>
                                <th><?= __('Category') ?></th>
                                <th><?= __('Date') ?></th>
                                <th><?= __('Status') ?></th>
                                <th><?= __('Media') ?></th>
                                <th><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($news)): ?>
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:60px 20px;color:#94a3b8;">
                                        <i class="fa-regular fa-newspaper" style="font-size:3rem;margin-bottom:12px;display:block;opacity:.4;"></i>
                                        <strong><?= __('No news articles yet') ?></strong><br>
                                        <small><?= __('Add your first article to share updates with the public.') ?></small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($news as $i => $item): ?>
                                    <tr>
                                        <td style="color:#94a3b8;font-weight:700;font-size:0.85rem;"><?= $i + 1 ?></td>
                                        <td>
                                            <div style="font-weight:700;color:#1e293b;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($item['title']) ?></div>
                                            <?php if (!empty($item['author'])): ?>
                                                <div style="font-size:0.75rem;color:#94a3b8;margin-top:2px;"><i class="fa-solid fa-user" style="font-size:.65rem;"></i> <?= htmlspecialchars($item['author']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span style="background:#eff6ff;color:#2563eb;padding:4px 10px;border-radius:20px;font-size:0.78rem;font-weight:700;">
                                                <?= htmlspecialchars($item['category'] ?? 'General') ?>
                                            </span>
                                        </td>
                                        <td style="color:#64748b;font-size:0.9rem;white-space:nowrap;">
                                            <?= $item['published_date'] ? date('M d, Y', strtotime($item['published_date'])) : '—' ?>
                                        </td>
                                        <td>
                                            <?php $status = $item['status'] ?? 'published'; ?>
                                            <?php if ($status === 'published'): ?>
                                                <span style="background:#f0fdf4;color:#16a34a;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:700;">
                                                    <i class="fa-solid fa-circle" style="font-size:.4rem;vertical-align:middle;margin-right:4px;"></i><?= __('Published') ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="background:#fefce8;color:#d97706;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:700;">
                                                    <i class="fa-solid fa-circle" style="font-size:.4rem;vertical-align:middle;margin-right:4px;"></i><?= __('Draft') ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php $mtype = $item['media_type'] ?? 'none'; ?>
                                            <?php if ($mtype !== 'none' && !empty($item['media_url'])): ?>
                                                <span style="background:#f8fafc;color:#64748b;padding:4px 10px;border-radius:20px;font-size:0.78rem;font-weight:600;">
                                                    <i class="fa-solid fa-<?= $mtype === 'video' ? 'video' : 'image' ?>"></i> <?= ucfirst($mtype) ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color:#cbd5e1;font-size:0.85rem;">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:8px;align-items:center;">
                                                <a href="/frisucode_ms/public/news_view.php?id=<?= $item['id'] ?>" target="_blank" title="<?= __('View on Public Site') ?>" style="width:34px;height:34px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#64748b;transition:0.2s;" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='#f8fafc';this.style.color='#64748b'">
                                                    <i class="fa-solid fa-eye" style="font-size:0.85rem;"></i>
                                                </a>
                                                <a href="edit.php?id=<?= $item['id'] ?>" title="<?= __('Edit') ?>" style="width:34px;height:34px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#64748b;transition:0.2s;" onmouseover="this.style.background='#fefce8';this.style.color='#d97706'" onmouseout="this.style.background='#f8fafc';this.style.color='#64748b'">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size:0.85rem;"></i>
                                                </a>
                                                <a href="index.php?delete=<?= $item['id'] ?>" onclick="return confirm('<?= __('Delete this news article permanently?') ?>')" title="<?= __('Delete') ?>" style="width:34px;height:34px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#64748b;transition:0.2s;" onmouseover="this.style.background='#fff5f5';this.style.color='#ef4444'" onmouseout="this.style.background='#f8fafc';this.style.color='#64748b'">
                                                    <i class="fa-solid fa-trash" style="font-size:0.85rem;"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
