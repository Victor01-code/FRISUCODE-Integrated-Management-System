<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'finance', 'project_manager']);
require_once __DIR__ . '/../config/db.php';

$notifs = [];

try {
    // Latest Donations
    $stmt = $pdo->query("SELECT id, full_name, amount, created_at FROM public_donations ORDER BY created_at DESC LIMIT 20");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $notifs[] = [
            'type' => 'donation',
            'icon' => 'blue',
            'fa' => 'fa-hand-holding-dollar',
            'title' => sprintf(__('Donation received from %s ($%s)'), htmlspecialchars($row['full_name']), number_format($row['amount'], 2)),
            'time' => strtotime($row['created_at']),
            'link' => '/frisucode_ms/system/donations/index.php'
        ];
    }
    
    // Latest Beneficiaries
    $stmt = $pdo->query("SELECT id, full_name, registered_at FROM beneficiaries ORDER BY registered_at DESC LIMIT 20");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $notifs[] = [
            'type' => 'beneficiary',
            'icon' => 'green',
            'fa' => 'fa-graduation-cap',
            'title' => sprintf(__('New student registered: %s'), htmlspecialchars($row['full_name'])),
            'time' => strtotime($row['registered_at']),
            'link' => '/frisucode_ms/system/beneficiaries/edit.php?id=' . $row['id']
        ];
    }

    // Latest Projects
    $stmt = $pdo->query("SELECT id, title, created_at FROM projects ORDER BY created_at DESC LIMIT 20");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $notifs[] = [
            'type' => 'project',
            'icon' => 'orange',
            'fa' => 'fa-rocket',
            'title' => sprintf(__('New project created: %s'), htmlspecialchars($row['title'])),
            'time' => strtotime($row['created_at']),
            'link' => '/frisucode_ms/system/projects/edit.php?id=' . $row['id']
        ];
    }

    // Latest News
    $stmt = $pdo->query("SELECT id, title, published_date FROM news ORDER BY published_date DESC LIMIT 20");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $notifs[] = [
            'type' => 'news',
            'icon' => 'purple',
            'fa' => 'fa-newspaper',
            'title' => sprintf(__('New news article drafted/published: %s'), htmlspecialchars($row['title'])),
            'time' => strtotime($row['published_date']),
            'link' => '/frisucode_ms/system/news/edit.php?id=' . $row['id']
        ];
    }
    
    usort($notifs, function($a, $b) {
        return $b['time'] - $a['time'];
    });
    
} catch (PDOException $e) {}

// Pagination or Limiting for View
$notifs = array_slice($notifs, 0, 50); // Show max 50 recent items
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | System Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .notifications-container {
            background: #fff;
            border-radius: 24px;
            padding: 30px;
            margin: 0 40px 40px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .notification-row {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            padding: 20px 0;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            transition: 0.2s;
        }
        .notification-row:hover {
            background: #fbfdff;
            transform: translateX(5px);
        }
        .notification-row:last-child {
            border-bottom: none;
        }
        .notif-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .notif-icon.blue { background: #eff6ff; color: #3b82f6; }
        .notif-icon.green { background: #f0fdf4; color: #16a34a; }
        .notif-icon.orange { background: #fff7ed; color: #f97316; }
        .notif-icon.purple { background: #f5f3ff; color: #8b5cf6; }
        
        .notif-content {
            flex: 1;
        }
        .notif-content h4 {
            margin: 0 0 5px;
            font-size: 1.1rem;
            color: #1e293b;
            font-weight: 600;
        }
        .notif-content span {
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 600;
            background: #f8fafc;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
        }
    </style>
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header fade-in" style="margin-bottom: 0;">
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><i class="fa-regular fa-bell"></i> <?= __('System Notification Center') ?></h2>
        </div>

        <div class="notifications-container fade-in" style="animation-delay: 0.1s;">
            <?php if(empty($notifs)): ?>
                <div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                    <i class="fa-regular fa-bell-slash" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                    <h3 style="color: #64748b;font-family:'Outfit';"><?= __('No system events found.') ?></h3>
                </div>
            <?php else: ?>
                <?php foreach($notifs as $n): ?>
                    <a href="<?= htmlspecialchars($n['link']) ?>" class="notification-row">
                        <div class="notif-icon <?= $n['icon'] ?>">
                            <i class="fa-solid <?= $n['fa'] ?>"></i>
                        </div>
                        <div class="notif-content">
                            <h4><?= $n['title'] ?></h4>
                            <span><i class="fa-regular fa-clock"></i> <?= date('F j, Y, g:i a', $n['time']) ?></span>
                        </div>
                        <div>
                            <div class="icon-btn" style="background:#f1f5f9;border:none;">
                                <i class="fa-solid fa-chevron-right"></i>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
