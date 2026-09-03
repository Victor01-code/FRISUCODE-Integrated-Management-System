<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'finance', 'project_manager', 'admin']);
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT s.*, u.email, u.full_name, u.created_at as joined_date FROM sponsors s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
    $stmt->execute([$id]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$donor) {
        header("Location: index.php?error=notfound");
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Donor Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            border-radius: 24px;
            padding: 40px;
            color: white;
            display: flex;
            align-items: center;
            gap: 30px;
            margin: 32px 40px;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border: 4px solid rgba(255,255,255,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #fff;
        }
        .info-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            margin: 0 40px 32px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        .info-item label {
            display: block;
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .info-item div {
            font-size: 1.1rem;
            color: #0f172a;
            font-weight: 600;
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
            <div style="display:flex;align-items:center;gap:15px;">
                <a href="index.php" class="btn-secondary" style="border-radius:12px;"><i class="fa-solid fa-arrow-left"></i></a>
                <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Donor Profile') ?></h2>
            </div>
        </div>

        <div class="profile-header fade-in">
            <div class="profile-avatar">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
            <div>
                <h1 style="margin:0 0 10px; font-family:'Outfit'; font-weight:800; font-size:2.5rem;">
                    <?= htmlspecialchars($donor['organization_name'] ?: $donor['full_name']) ?>
                </h1>
                <div style="display:flex; gap:15px; font-weight:600; font-size:0.95rem; opacity:0.9;">
                    <span><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($donor['email']) ?></span>
                    <span><i class="fa-solid fa-tag"></i> <?= strtoupper($donor['sponsor_type']) ?></span>
                </div>
            </div>
        </div>

        <div class="info-card fade-in" style="animation-delay: 0.1s;">
            <h3 style="margin: 0 0 25px; color: #1e293b; font-family: 'Outfit'; font-size: 1.25rem;"><i class="fa-solid fa-address-card" style="color:var(--primary);margin-right:10px;"></i> <?= __('Contact & Organizational Details') ?></h3>
            <div class="info-grid">
                <div class="info-item">
                    <label><?= __('Contact Person Name') ?></label>
                    <div><?= htmlspecialchars($donor['full_name']) ?></div>
                </div>
                <div class="info-item">
                    <label><?= __('Organization Name') ?></label>
                    <div><?= htmlspecialchars($donor['organization_name'] ?: 'N/A') ?></div>
                </div>
                <div class="info-item">
                    <label><?= __('Phone Number') ?></label>
                    <div><?= htmlspecialchars($donor['phone'] ?: 'No Phone Added') ?></div>
                </div>
                <div class="info-item">
                    <label><?= __('Email Address') ?></label>
                    <div><a href="mailto:<?= htmlspecialchars($donor['email']) ?>" style="color:var(--primary);text-decoration:none;"><?= htmlspecialchars($donor['email']) ?></a></div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <label><?= __('Physical Address') ?></label>
                    <div><?= nl2br(htmlspecialchars($donor['address'] ?: 'No Address Added')) ?></div>
                </div>
            </div>
        </div>

        <div class="info-card fade-in" style="animation-delay: 0.2s;">
            <h3 style="margin: 0 0 20px; color: #1e293b; font-family: 'Outfit'; font-size: 1.25rem;"><i class="fa-solid fa-seedling" style="color:#10b981;margin-right:10px;"></i> <?= __('Sponsorship History & Impact') ?></h3>
            <div style="padding:40px;text-align:center;background:#f8fafc;border-radius:16px;border:2px dashed #cbd5e1;">
                <div style="width: 60px; height: 60px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #94a3b8; font-size: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h4 style="color:#475569;margin-bottom:5px;"><?= __('Impact Tracking Module') ?></h4>
                <p style="color:#94a3b8;font-size:0.9rem;max-width:400px;margin:0 auto;"><?= __('Detailed sponsorship and donation history will be linked here as the finance module expands.') ?></p>
            </div>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
