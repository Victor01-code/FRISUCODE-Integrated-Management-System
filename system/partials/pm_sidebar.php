<aside class="sidebar">

    <div class="brand" style="padding: 24px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid #f8fafc; background: #fff;">
        <div style="width: 48px; flex-shrink: 0;">
            <img src="../../assets/images/logo.png" alt="FRISUCODE" style="width: 100%; height: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));">
        </div>
        <div>
            <span style="font-family: 'Outfit'; font-weight: 800; font-size: 1.15rem; color: #0f172a; letter-spacing: -0.02em; display: block; line-height: 1;">FRISU<span style="color: var(--primary);">CODE</span></span>
            <span style="display: block; font-size: 0.62rem; font-weight: 700; color: #64748b; letter-spacing: 0.02em; margin-top: 4px; line-height: 1.2; text-transform: uppercase;"><?= __('Project Management') ?></span>
        </div>
    </div>

    <nav style="padding: 20px 16px;">
        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 0 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('Command Center') ?></div>
        
        <a href="/frisucode_ms/system/dashboards/project_manager.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'project_manager.php') ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-layer-group"></i> <span><?= __('PM Dashboard') ?></span>
        </a>
        
        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 30px 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('Project Operations') ?></div>
        
        <a href="/frisucode_ms/system/projects/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], '/projects/') !== false) ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-diagram-project"></i> <span><?= __('All Programs') ?></span>
        </a>
        <a href="/frisucode_ms/system/beneficiaries/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], '/beneficiaries/') !== false) ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-user-graduate"></i> <span><?= __('Beneficiaries') ?></span>
        </a>
        <a href="/frisucode_ms/system/finance/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], '/finance/') !== false) ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-file-invoice-dollar"></i> <span><?= __('Project Budgets') ?></span>
        </a>
        <a href="/frisucode_ms/system/reports/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], '/reports/') !== false) ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-pie"></i> <span><?= __('Reporting') ?></span>
        </a>

        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 30px 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('Team & Tasks') ?></div>
        
        <a href="/frisucode_ms/system/users/index.php" class="<?= (strpos($_SERVER['REQUEST_URI'], '/users/') !== false) ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-users"></i> <span><?= __('Team Directory') ?></span>
        </a>
    </nav>

    <div style="margin-top: auto; padding: 20px 16px; border-top: 1px solid #f8fafc; background: #fff;">
        <a href="/frisucode_ms/system/auth/logout.php" style="display: flex; align-items: center; gap: 12px; color: #ef4444; text-decoration: none; font-size: 0.9rem; font-weight: 800; padding: 14px 16px; border-radius: 12px; transition: 0.3s; background: #fff5f5; border: 1px solid #fee2e2;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fff5f5'">
            <i class="fa-solid fa-power-off"></i> <span><?= __('Sign Out') ?></span>
        </a>
    </div>

</aside>
