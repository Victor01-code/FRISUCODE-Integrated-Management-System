<?php
$finRole = $_SESSION['role'] ?? 'finance';
$isDirector = ($finRole === 'director' || $finRole === 'super_admin');
?>
<aside class="sidebar">

    <div class="brand" style="padding: 24px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid #f8fafc; background: #fff;">
        <div style="width: 48px; flex-shrink: 0;">
            <img src="../../assets/images/logo.png" alt="FRISUCODE" style="width: 100%; height: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));">
        </div>
        <div>
            <span style="font-family: 'Outfit'; font-weight: 800; font-size: 1.15rem; color: #0f172a; letter-spacing: -0.02em; display: block; line-height: 1;">FRISU<span style="color: var(--primary);">CODE</span></span>
            <span style="display: block; font-size: 0.62rem; font-weight: 700; color: #64748b; letter-spacing: 0.02em; margin-top: 4px; line-height: 1.2; text-transform: uppercase;"><?= __('Finance Control Center') ?></span>
        </div>
    </div>

    <nav style="padding: 20px 16px; overflow-y: auto; flex: 1;">

        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 0 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('Finance Overview') ?></div>

        <a href="/frisucode_ms/system/dashboards/finance.php"
           class="<?= (basename($_SERVER['PHP_SELF']) === 'finance.php') ? 'active' : '' ?>"
           style="margin-bottom: 4px;">
            <i class="fa-solid fa-gauge-high"></i> <span><?= __('Finance Dashboard') ?></span>
        </a>

        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 30px 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('Financial Tracking') ?></div>

        <a href="/frisucode_ms/system/finance/index.php"
           class="<?= (strpos($_SERVER['REQUEST_URI'], '/finance/index') !== false) ? 'active' : '' ?>"
           style="margin-bottom: 4px;">
            <i class="fa-solid fa-book-open"></i> <span><?= __('Central Ledger') ?></span>
        </a>

        <a href="/frisucode_ms/system/finance/create.php"
           class="<?= (strpos($_SERVER['REQUEST_URI'], '/finance/create') !== false) ? 'active' : '' ?>"
           style="margin-bottom: 4px;">
            <i class="fa-solid fa-file-invoice-dollar"></i> <span><?= __('Log Transaction') ?></span>
        </a>

        <a href="/frisucode_ms/system/donations/index.php"
           class="<?= (strpos($_SERVER['REQUEST_URI'], '/donations/') !== false) ? 'active' : '' ?>"
           style="margin-bottom: 4px;">
            <i class="fa-solid fa-circle-dollar-to-slot"></i> <span><?= __('Web Donations') ?></span>
        </a>

        <a href="/frisucode_ms/system/donors/index.php"
           class="<?= (strpos($_SERVER['REQUEST_URI'], '/donors/') !== false) ? 'active' : '' ?>"
           style="margin-bottom: 4px;">
            <i class="fa-solid fa-hand-holding-heart"></i> <span><?= __('Partner Registry') ?></span>
        </a>

        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 30px 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('Analysis') ?></div>

        <a href="/frisucode_ms/system/reports/index.php"
           class="<?= (strpos($_SERVER['REQUEST_URI'], '/reports/') !== false) ? 'active' : '' ?>"
           style="margin-bottom: 4px;">
            <i class="fa-solid fa-chart-pie"></i> <span><?= __('Analytics') ?></span>
        </a>

        <?php if ($isDirector): ?>
        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 30px 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('Director View') ?></div>

        <a href="/frisucode_ms/system/dashboards/super_admin.php" style="margin-bottom: 4px;">
            <i class="fa-solid fa-house-chimney"></i> <span><?= __('Executive Dashboard') ?></span>
        </a>
        <a href="/frisucode_ms/system/projects/index.php" style="margin-bottom: 4px;">
            <i class="fa-solid fa-diagram-project"></i> <span><?= __('Active Programs') ?></span>
        </a>
        <a href="/frisucode_ms/system/users/index.php" style="margin-bottom: 4px;">
            <i class="fa-solid fa-users-gear"></i> <span><?= __('Staff Access') ?></span>
        </a>
        <?php endif; ?>

    </nav>

    <div style="margin-top: auto; padding: 20px 16px; border-top: 1px solid #f8fafc; background: #fff;">
        <a href="/frisucode_ms/system/auth/logout.php"
           style="display: flex; align-items: center; gap: 12px; color: #ef4444; text-decoration: none; font-size: 0.9rem; font-weight: 800; padding: 14px 16px; border-radius: 12px; transition: 0.3s; background: #fff5f5; border: 1px solid #fee2e2;"
           onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fff5f5'">
            <i class="fa-solid fa-power-off"></i> <span><?= __('Sign Out') ?></span>
        </a>
    </div>

</aside>
