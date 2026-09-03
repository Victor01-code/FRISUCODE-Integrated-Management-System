<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Logic to determine Page Title based on URL
$current_url = $_SERVER['REQUEST_URI'];
$title_data = ['title' => __('System Module'), 'sub' => __('FRISUCODE Smart Office')];

if (strpos($current_url, '/dashboards/super_admin.php') !== false) {
    $title_data = ['title' => __('Executive Dashboard'), 'sub' => __('Real-time overview of organization impact')];
} elseif (strpos($current_url, '/dashboards/staff.php') !== false) {
    $title_data = ['title' => __('Staff Dashboard'), 'sub' => __('Your workspace and assigned tasks')];
} elseif (strpos($current_url, '/dashboards/project_manager.php') !== false) {
    $title_data = ['title' => __('Project Manager Dashboard'), 'sub' => __('Manage active programs and resources')];
} elseif (strpos($current_url, '/dashboards/donor.php') !== false) {
    $title_data = ['title' => __('Donor Portal'), 'sub' => __('Your impact and contribution overview')];
} elseif (strpos($current_url, '/dashboards/finance.php') !== false) {
    $title_data = ['title' => __('Finance Dashboard'), 'sub' => __('Real-time financial control and oversight')];
} elseif (strpos($current_url, '/projects/') !== false) {
    if (strpos($current_url, 'create.php') !== false) {
        $title_data = ['title' => __('New Program'), 'sub' => __('Initiating a community initiative')];
    } else {
        $title_data = ['title' => __('Project Management'), 'sub' => __('Overseeing active programs and goals')];
    }
} elseif (strpos($current_url, '/beneficiaries/') !== false) {
    if (strpos($current_url, 'create.php') !== false) {
        $title_data = ['title' => __('Student Registration'), 'sub' => __('Adding a new beneficiary to the database')];
    } else {
        $title_data = ['title' => __('Student Registry'), 'sub' => __('Managing sponsored students and progress')];
    }
} elseif (strpos($current_url, '/finance/') !== false) {
    if (strpos($current_url, 'create.php') !== false) {
        $title_data = ['title' => __('New Entry'), 'sub' => __('Recording financial movement in the ledger')];
    } else {
        $title_data = ['title' => __('Financial Ledger'), 'sub' => __('Tracking organizational inflow and outflow')];
    }
} elseif (strpos($current_url, '/donations/') !== false) {
    $title_data = ['title' => __('Public Contributions'), 'sub' => __('Reviewing donations received from the website')];
} elseif (strpos($current_url, '/donors/') !== false) {
    $title_data = ['title' => __('Partner Registry'), 'sub' => __('Managing official sponsors and partner organizations')];
} elseif (strpos($current_url, '/reports/') !== false) {
    $title_data = ['title' => __('Impact Analytics'), 'sub' => __('Data-driven insights and reporting')];
} elseif (strpos($current_url, '/users/') !== false) {
    $title_data = ['title' => __('Staff Directory'), 'sub' => __('Managing system access and personnel roles')];
} elseif (strpos($current_url, '/settings/') !== false) {
    $title_data = ['title' => __('Global Settings'), 'sub' => __('System-wide configurations and branding')];
} elseif (strpos($current_url, '/news/') !== false) {
    if (strpos($current_url, 'create.php') !== false) {
        $title_data = ['title' => __('Add News Article'), 'sub' => __('Create a new article for the public website')];
    } elseif (strpos($current_url, 'edit.php') !== false) {
        $title_data = ['title' => __('Edit News Article'), 'sub' => __('Update an existing news publication')];
    } else {
        $title_data = ['title' => __('News & Updates'), 'sub' => __('Manage public news and announcements')];
    }
}

$adminName = $_SESSION['user_name'] ?? 'Admin User';
$adminRole = strtoupper(str_replace('_', ' ', $_SESSION['role'] ?? 'Administrator'));
$lang = $_SESSION['lang'] ?? 'en';
?>
<header class="topbar">
    <!-- MOBILE TOGGLE -->
    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fa-solid fa-bars-staggered"></i>
    </button>

    <!-- PAGE TITLE -->
    <div>
        <h1><?= $title_data['title'] ?></h1>
        <p><?= $title_data['sub'] ?></p>
    </div>

    <script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
    }
    </script>

    <!-- RIGHT ACTIONS -->
    <div class="top-actions">

        <!-- Language Selector -->
        <form method="get" class="lang-select">
            <?php
            // Preserve all current GET params except lang
            foreach ($_GET as $key => $val) {
                if ($key !== 'lang') {
                    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($val) . '">';
                }
            }
            ?>
            <select name="lang" onchange="this.form.submit()">
                <option value="en" <?= ($lang ?? 'en')=='en'?'selected':'' ?>>EN</option>
                <option value="sw" <?= ($lang ?? '')=='sw'?'selected':'' ?>>SW</option>
                <option value="fr" <?= ($lang ?? '')=='fr'?'selected':'' ?>>FR</option>
                <option value="de" <?= ($lang ?? '')=='de'?'selected':'' ?>>DE</option>
                <option value="es" <?= ($lang ?? '')=='es'?'selected':'' ?>>ES</option>
            </select>
        </form>

        <?php
        $headerNotifs = [];
        
        if (isset($pdo)) {
            try {
                // Latest Donation
                $stmt = $pdo->query("SELECT id, full_name, amount, created_at FROM public_donations ORDER BY created_at DESC LIMIT 5");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $headerNotifs[] = [
                        'time' => strtotime($row['created_at'])
                    ];
                }
                // Latest Beneficiary
                $stmt = $pdo->query("SELECT id, full_name, registered_at FROM beneficiaries ORDER BY registered_at DESC LIMIT 5");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $headerNotifs[] = [
                        'time' => strtotime($row['registered_at'])
                    ];
                }
                // Latest Project
                $stmt = $pdo->query("SELECT id, title, created_at FROM projects ORDER BY created_at DESC LIMIT 5");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $headerNotifs[] = [
                        'time' => strtotime($row['created_at'])
                    ];
                }
            } catch (PDOException $e) {}
        }
        
        // Count total as a simple aggregate. Since we don't have a notifications table with 'is_read', 
        // we'll just show the total count of recent items.
        $headerNotifCount = count($headerNotifs);
        ?>

        <!-- Notifications -->
        <a href="/frisucode_ms/system/notifications/index.php" class="icon-btn" title="<?= __('System Notifications') ?>">
            <i class="fa-regular fa-bell"></i>
            <?php if($headerNotifCount > 0): ?>
                <span class="badge"><?= $headerNotifCount ?></span>
            <?php endif; ?>
        </a>



        <!-- Profile -->
        <div class="profile">
            <div class="profile-trigger">
                <?php
                $sysUserId = $_SESSION['user_id'] ?? null;
                $pPic = null;
                if ($sysUserId && isset($pdo)) {
                    try {
                        $usrStmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
                        $usrStmt->execute([$sysUserId]);
                        $pPic = $usrStmt->fetchColumn();
                    } catch(PDOException $e) {}
                }
                
                if (!empty($pPic)): ?>
                    <img src="<?= htmlspecialchars($pPic) ?>" alt="Profile" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-light);">
                <?php else: ?>
                    <div class="profile-img-circle">
                        <?= strtoupper(substr($adminName, 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <span style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($adminName) ?></span>
                <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; color: #94a3b8;"></i>
            </div>

            <div class="profile-menu">
                <div class="profile-info">
                    <strong style="font-family: 'Outfit';"><?= htmlspecialchars($adminName) ?></strong>
                    <div style="font-size: 0.65rem; color: #3b82f6; font-weight: 800; background: #eff6ff; padding: 3px 8px; border-radius: 6px; display: inline-block; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.05em;">
                        <?= $adminRole ?>
                    </div>
                </div>

                <a href="/frisucode_ms/system/users/view_profile.php"><i class="fa-regular fa-user-circle"></i> <?= __('View Profile') ?></a>
                <a href="/frisucode_ms/system/users/view_profile.php#preferences"><i class="fa-solid fa-sliders"></i> <?= __('Preferences') ?></a>
                <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 8px 0;">
                <a href="/frisucode_ms/system/auth/logout.php" class="logout" style="font-weight: 700;">
                    <i class="fa-solid fa-power-off"></i> <?= __('Secure Sign Out') ?>
                </a>
            </div>
        </div>

    </div>
</header>
