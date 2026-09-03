<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'finance']);
require_once __DIR__ . '/../config/db.php';

// Fetch Public Donations
try {
    $stmt = $pdo->query("SELECT * FROM public_donations ORDER BY created_at DESC");
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $donations = [];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRISUCODE | Public Donations</title>
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header fade-in">
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Public Contributions') ?></h2>
            <div class="header-actions">
                <span class="badge" style="background: #eff6ff; color: #2563eb; font-weight: 800; padding: 6px 14px; border-radius: 10px;">
                    <i class="fa-solid fa-heart" style="margin-right: 6px;"></i>
                    <?php echo count($donations); ?> <?= __('Donations') ?>
                </span>
            </div>
        </div>

        <div class="content-box fade-in" style="margin: 32px 40px; animation-delay: 0.1s;">
            <div style="padding: 24px 32px; border-bottom: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; background: #fff; flex-wrap: wrap; gap: 15px;">
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 800; margin: 0; color: #0f172a;"><?= __('Website Donation History') ?></h3>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" id="searchDonations" placeholder="<?= __('Search by name, email, cause, amount...') ?>" oninput="filterTable()" style="padding: 10px 16px 10px 40px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; font-weight: 600; width: 340px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 600; white-space: nowrap;"><i class="fa-solid fa-shield-check"></i> <?= __('Secure Stripe Processing') ?></span>
                </div>
            </div>

            <?php if(count($donations) > 0): ?>
                <div style="width: 100%; max-height: 600px; overflow: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 24px 24px;">
                    <table class="data-table" id="dataTable">
                        <thead>
                            <tr>
                                <th style="padding-left: 32px;"><?= __('Submission Date') ?></th>
                                <th><?= __('Donor Profile') ?></th>
                                <th><?= __('Contribution') ?></th>
                                <th><?= __('Target Cause') ?></th>
                                <th><?= __('Execution Status') ?></th>
                                <th style="padding-right: 32px; text-align: right;"><?= __('Trace ID') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($donations as $row): ?>
                                <tr>
                                    <td style="padding-left: 32px; min-width: 160px;">
                                        <div style="font-weight: 600; color: #1e293b;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></div>
                                        <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">
                                            <?php echo date('H:i A', strtotime($row['created_at'])); ?>
                                        </div>
                                    </td>
                                    <td style="min-width: 250px;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div class="profile-img-circle" style="width: 38px; height: 38px; background: #fdf2f8; color: #db2777; font-size: 0.9rem; flex-shrink: 0;">
                                                <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 800; color: #1e293b; font-family: 'Outfit';"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;"><?php echo htmlspecialchars($row['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="min-width: 130px;">
                                        <div style="font-family: 'Outfit'; font-weight: 800; color: #16a34a; font-size: 1.1rem;">
                                            $<?php echo number_format($row['amount'], 2); ?>
                                        </div>
                                    </td>
                                    <td style="min-width: 180px;">
                                        <div style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: #475569; background: #f1f5f9; padding: 4px 10px; border-radius: 8px; font-size: 0.85rem;">
                                            <i class="fa-solid fa-tag" style="color: #cbd5e1;"></i>
                                            <?php echo htmlspecialchars($row['cause']); ?>
                                        </div>
                                    </td>
                                    <td style="min-width: 120px;">
                                        <span class="badge success" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800;">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <?php echo strtoupper($row['status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding-right: 32px; text-align: right; min-width: 120px;">
                                        <span style="font-family: monospace; color: #94a3b8; font-weight: 700; background: #f8fafc; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem;">
                                            <?php echo substr($row['transaction_id'], 0, 12); ?>...
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- No results message -->
                <div id="noResults" style="display: none; text-align: center; padding: 60px 40px; color: #64748b;">
                    <i class="fa-solid fa-filter-circle-xmark" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <h4 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 8px;"><?= __('No matching donations found') ?></h4>
                    <p style="font-weight: 500;"><?= __('Try adjusting your search terms.') ?></p>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 100px 40px; color: #64748b; background: #fff;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #fdf2f8; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-hand-holding-heart" style="font-size: 2.5rem; color: #db2777;"></i>
                    </div>
                    <h3 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 10px;"><?= __('No digital contributions yet') ?></h3>
                    <p style="font-weight: 500;"><?= __('When people donate through the website, they will appear here automatically.') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
function filterTable() {
    const query = document.getElementById('searchDonations').value.toLowerCase().trim();
    const table = document.getElementById('dataTable');
    if (!table) return;
    const rows = table.querySelectorAll('tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const match = !query || text.includes(query);
        row.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });

    const noResults = document.getElementById('noResults');
    if (noResults) {
        noResults.style.display = (visibleCount === 0 && query) ? 'block' : 'none';
    }
}
</script>

</body>
</html>
