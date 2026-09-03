<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole('donor');
require_once __DIR__ . '/../config/db.php';

$donorName = $_SESSION['user_name'] ?? 'Valued Donor';
$userId = $_SESSION['user_id'];

// Fetch sponsor profile
$sponsorStmt = $pdo->prepare("SELECT * FROM sponsors WHERE user_id = ?");
$sponsorStmt->execute([$userId]);
$sponsor = $sponsorStmt->fetch(PDO::FETCH_ASSOC);

// Fetch donor user details for identifying personal records
$userQuery = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$userQuery->execute([$userId]);
$donorEmail = $userQuery->fetchColumn();

// Fetch overall organization stats
$totalStudents = $pdo->query("SELECT COUNT(*) FROM beneficiaries WHERE status='active'")->fetchColumn();
$totalProjects = $pdo->query("SELECT COUNT(*) FROM projects WHERE status='active'")->fetchColumn();

// Fetch PERSONAL Total Donations
$pdStmt = $pdo->prepare("SELECT SUM(amount) FROM public_donations WHERE email = ? AND status='completed'");
$pdStmt->execute([$donorEmail]);
$totalDonations = $pdStmt->fetchColumn() ?? 0;

// Silent Database Patch for Sponsor ID if it doesn't exist
try {
    $pdo->exec("ALTER TABLE beneficiaries ADD COLUMN sponsor_id INT DEFAULT NULL AFTER status");
} catch (\PDOException $e) { /* Column might already exist */ }

// Fetch recent active projects
$recentProjects = $pdo->query("SELECT title, description, status, start_date, budget FROM projects WHERE status IN ('active','planning') ORDER BY created_at DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);

// Fetch students assigned to this specific donor
$studentStmt = $pdo->prepare("SELECT b.full_name, b.education_level, b.school_name, b.status FROM beneficiary_sponsors bs JOIN beneficiaries b ON bs.beneficiary_id = b.id WHERE bs.sponsor_id = ? AND b.status='active' ORDER BY b.registered_at DESC LIMIT 5");
$studentStmt->execute([$userId]);
$recentStudents = $studentStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch personal recent donations
$donationsStmt = $pdo->prepare("SELECT full_name, amount, cause, created_at FROM public_donations WHERE email = ? ORDER BY created_at DESC LIMIT 5");
$donationsStmt->execute([$donorEmail]);
$recentDonations = $donationsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Donor Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .impact-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #e2e8f0;
            box-shadow: var(--card-shadow);
            transition: 0.3s;
        }
        .impact-card:hover {
            transform: translateY(-4px);
            border-color: #16a34a;
        }
        .impact-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 20px;
        }
        .project-mini-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            transition: 0.2s;
        }
        .project-mini-card:hover {
            background: #fff;
            border-color: var(--primary);
        }
        .student-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .student-row:last-child { border-bottom: none; }
    </style>
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <!-- WELCOME BANNER -->
        <div class="fade-in" style="margin: 30px 40px; background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%); border-radius: 24px; padding: 40px; color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -30px; right: -30px; font-size: 10rem; color: rgba(255,255,255,0.06); transform: rotate(15deg);">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
            <div style="position: relative; z-index: 1;">
                <h2 style="font-family: 'Outfit'; font-weight: 800; font-size: 1.8rem; margin: 0 0 10px;"><?= __('Welcome back') ?>, <?php echo htmlspecialchars($donorName); ?> 🌟</h2>
                <p style="opacity: 0.9; font-size: 1.05rem; margin: 0; max-width: 600px; line-height: 1.7;">
                    <?= __('Your generosity transforms lives. Here\'s a snapshot of the impact your support creates across our community programs.') ?>
                </p>
            </div>
        </div>

        <div style="margin: 0 40px;">
            <?php 
            $timePickerEmail = $donorEmail;
            include __DIR__ . '/../partials/time_picker.php'; 
            ?>
        </div>

        <!-- IMPACT STATS -->
        <section class="stats-grid fade-in" id="impact" style="animation-delay: 0.1s;">
            <div class="stat-card" style="border-left: 5px solid #16a34a;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h4><?= __('Students Sponsored') ?></h4>
                        <strong style="color: #16a34a;"><?php echo number_format($totalStudents); ?></strong>
                    </div>
                    <div class="impact-icon" style="background: #f0fdf4; color: #16a34a; width: 44px; height: 44px; margin-bottom: 0;">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>
                <div style="margin-top: 12px; font-size: 0.8rem; color: #16a34a; font-weight: 700;">
                    <i class="fa-solid fa-arrow-trend-up"></i> <?= __('Active enrollments') ?>
                </div>
            </div>

            <div class="stat-card" style="border-left: 5px solid #2563eb;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h4><?= __('Active Programs') ?></h4>
                        <strong id="stat-active-programs" data-val="<?php echo $totalProjects; ?>" style="color: #2563eb;"><?php echo number_format($totalProjects); ?></strong>
                    </div>
                    <div class="impact-icon" style="background: #eff6ff; color: #2563eb; width: 44px; height: 44px; margin-bottom: 0;">
                        <i class="fa-solid fa-diagram-project"></i>
                    </div>
                </div>
                <div style="margin-top: 12px; font-size: 0.8rem; color: #2563eb; font-weight: 700;">
                    <i class="fa-solid fa-play-circle"></i> <?= __('Community initiatives') ?>
                </div>
            </div>

            <div class="stat-card" style="border-left: 5px solid #f59e0b;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h4><?= __('Total Donated') ?></h4>
                        <strong id="stat-donations" data-val="<?php echo $totalDonations; ?>" style="color: #f59e0b;">$<?php echo number_format($totalDonations, 2); ?></strong>
                    </div>
                    <div class="impact-icon" style="background: #fefce8; color: #f59e0b; width: 44px; height: 44px; margin-bottom: 0;">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>
                <div style="margin-top: 12px; font-size: 0.8rem; color: #f59e0b; font-weight: 700;">
                    <i class="fa-solid fa-globe"></i> <?= __('Combined generosity') ?>
                </div>
            </div>
        </section>

        <!-- ACTIVE PROJECTS + RECENT DONATIONS -->
        <section class="charts-grid fade-in" style="margin-top: 10px; animation-delay: 0.2s;" id="projects">
            
            <div class="chart-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h3 style="margin: 0; font-family: 'Outfit';"><?= __('Active Community Programs') ?></h3>
                    <span class="badge" style="background: #eff6ff; color: #2563eb; font-weight: 800;"><?php echo count($recentProjects); ?> <?= __('Programs') ?></span>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 16px; max-height: 400px; overflow-y: auto; padding-right: 5px;">
                    <?php if(!empty($recentProjects)): foreach($recentProjects as $proj): ?>
                        <div class="project-mini-card">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 42px; height: 42px; border-radius: 12px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                                        <i class="fa-solid fa-diagram-project"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 800; color: #1e293b; font-family: 'Outfit';"><?php echo htmlspecialchars($proj['title']); ?></div>
                                        <div style="font-size: 0.8rem; color: #64748b; font-weight: 500;"><?php echo substr(htmlspecialchars($proj['description']), 0, 60); ?>...</div>
                                    </div>
                                </div>
                                <span class="badge <?php echo $proj['status'] == 'active' ? 'success' : 'warning'; ?>" style="font-weight: 800;">
                                    <?php echo strtoupper($proj['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="text-align: center; padding: 40px; color: #94a3b8;">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                            <?= __('No active programs at this time.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="chart-card" id="donations">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h3 style="margin: 0; font-family: 'Outfit';"><?= __('Recent Contributions') ?></h3>
                </div>
                
                <div>
                    <?php if(!empty($recentDonations)): foreach($recentDonations as $d): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #f1f5f9;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 10px; background: #f0fdf4; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #16a34a;">
                                    <?php echo strtoupper(substr($d['full_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;"><?php echo htmlspecialchars($d['full_name']); ?></div>
                                    <small style="color: #94a3b8;"><?php echo htmlspecialchars($d['cause']); ?> · <?php echo date('M d', strtotime($d['created_at'])); ?></small>
                                </div>
                            </div>
                            <div style="font-family: 'Outfit'; font-weight: 800; color: #16a34a; font-size: 1rem;">+$<?php echo number_format($d['amount'], 2); ?></div>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="text-align: center; padding: 40px; color: #94a3b8;">
                            <i class="fa-solid fa-heart" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                            <?= __('No donation records yet.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </section>

        <!-- SPONSORED STUDENTS -->
        <section class="chart-card fade-in" style="margin: 24px 40px 40px; animation-delay: 0.3s;" id="students">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 style="margin: 0; font-family: 'Outfit'; font-weight: 800;"><?= __('Students Being Supported') ?></h3>
                <span class="badge" style="background: #fff7ed; color: #ea580c; font-weight: 800;">
                    <i class="fa-solid fa-user-graduate" style="margin-right: 4px;"></i> <?php echo count($recentStudents); ?> <?= __('Students') ?>
                </span>
            </div>
            
            <?php if(!empty($recentStudents)): foreach($recentStudents as $s): ?>
                <div class="student-row">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #fff7ed; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #ea580c;">
                        <?php echo strtoupper(substr($s['full_name'], 0, 1)); ?>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 800; color: #1e293b; font-family: 'Outfit';"><?php echo htmlspecialchars($s['full_name']); ?></div>
                        <small style="color: #64748b; font-weight: 600;">
                            <i class="fa-solid fa-graduation-cap" style="margin-right: 4px;"></i> <?php echo htmlspecialchars($s['education_level']); ?> · <?php echo htmlspecialchars($s['school_name']); ?>
                        </small>
                    </div>
                    <span class="badge success" style="font-weight: 800;">
                        <i class="fa-solid fa-check-circle"></i> <?php echo strtoupper($s['status']); ?>
                    </span>
                </div>
            <?php endforeach; else: ?>
                <div style="text-align: center; padding: 50px; color: #94a3b8;">
                    <i class="fa-solid fa-users" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                    <?= __('Student profiles will appear here as the program grows.') ?>
                </div>
            <?php endif; ?>
        </section>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>