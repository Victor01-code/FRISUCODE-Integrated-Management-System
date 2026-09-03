<aside class="sidebar">

    <div class="brand" style="padding: 24px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid #f8fafc; background: #fff;">
        <div style="width: 48px; flex-shrink: 0;">
            <img src="../../assets/images/logo.png" alt="FRISUCODE" style="width: 100%; height: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));">
        </div>
        <div>
            <span style="font-family: 'Outfit'; font-weight: 800; font-size: 1.15rem; color: #0f172a; letter-spacing: -0.02em; display: block; line-height: 1;">FRISU<span style="color: #16a34a;">CODE</span></span>
            <span style="display: block; font-size: 0.62rem; font-weight: 700; color: #64748b; letter-spacing: 0.02em; margin-top: 4px; line-height: 1.2; text-transform: uppercase;"><?= __('Donor Portal') ?></span>
        </div>
    </div>

    <nav style="padding: 20px 16px;">
        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 0 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('My Portal') ?></div>
        
        <a href="/frisucode_ms/system/dashboards/donor.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'donor.php') ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-house-chimney"></i> <span><?= __('Dashboard') ?></span>
        </a>
        
        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 30px 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('My Contributions') ?></div>
        
        <a href="/frisucode_ms/system/donor_portal/donations.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'donations.php') ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-hand-holding-dollar"></i> <span><?= __('Donation History') ?></span>
        </a>
        <a href="/frisucode_ms/system/donor_portal/impact.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'impact.php') ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-chart-line"></i> <span><?= __('Impact Report') ?></span>
        </a>
        
        <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; padding: 30px 12px 10px; letter-spacing: 0.15em; text-transform: uppercase;"><?= __('Resources') ?></div>
        
        <a href="/frisucode_ms/system/donor_portal/students.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'students.php') ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-user-graduate"></i> <span><?= __('Sponsored Students') ?></span>
        </a>
        <a href="/frisucode_ms/system/donor_portal/projects.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'projects.php') ? 'active' : '' ?>" style="margin-bottom: 4px;">
            <i class="fa-solid fa-diagram-project"></i> <span><?= __('Active Projects') ?></span>
        </a>
    </nav>

    <div style="margin-top: auto; padding: 20px 16px; border-top: 1px solid #f8fafc; background: #fff;">
        <a href="/frisucode_ms/system/auth/logout.php" style="display: flex; align-items: center; gap: 12px; color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 800; padding: 14px 16px; border-radius: 12px; transition: 0.3s; background: #f8fafc; border: 1px solid #f1f5f9;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
            <i class="fa-solid fa-power-off"></i> <span><?= __('Sign Out') ?></span>
        </a>
    </div>

</aside>
