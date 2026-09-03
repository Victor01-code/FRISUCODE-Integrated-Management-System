<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'admin', 'staff', 'director', 'project_manager']);
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("
        SELECT b.*, u.full_name as recorder_name 
        FROM beneficiaries b 
        LEFT JOIN users u ON b.dropout_recorded_by = u.id 
        WHERE b.status='dropped_out' 
        ORDER BY b.dropout_date DESC
    ");
    $dropouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $dropouts = [];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Dropout Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
    <style>
        @media print {
            body { background: #fff !important; }
            .sidebar, .top-header, .page-header, .search-container, .badge, .icon-btn, .btn-primary, .btn-secondary, .footer { display: none !important; }
            .main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
            .content-box { box-shadow: none !important; margin: 0 !important; border: none !important; }
            table { width: 100% !important; border-collapse: collapse !important; }
            th, td { border: 1px solid #ccc !important; padding: 8px !important; text-align: left !important; }
            .actions-column { display: none !important; }
            #dataTable { display: table !important; }
        }
        @media print {
            th:last-child, td:last-child { display: none !important; }
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header fade-in" style="margin-bottom: 0;">
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Dropout & Removal Report') ?></h2>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" class="btn-secondary" style="border-radius: 14px; cursor: pointer;">
                    <i class="fa-solid fa-print"></i> <?= __('Print / Export PDF') ?>
                </button>
                <a href="export.php?type=dropouts" class="btn-primary" style="border-radius: 14px; background: linear-gradient(135deg, #ef4444, #dc2626); border: none;">
                    <i class="fa-solid fa-file-csv"></i> <?= __('Export CSV') ?>
                </a>
            </div>
        </div>

        <div class="content-box fade-in" style="margin: 32px 40px; animation-delay: 0.1s;">
            <div style="padding: 24px 32px; border-bottom: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; background: #fff; flex-wrap: wrap; gap: 15px;">
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 800; margin: 0; color: #0f172a;"><?= __('Removed Program Enrollees') ?></h3>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" id="searchDropouts" placeholder="<?= __('Search dropouts...') ?>" oninput="filterTable()" style="padding: 10px 16px 10px 40px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; font-weight: 600; width: 320px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <span class="badge" style="background: #fef2f2; color: #ef4444; font-weight: 800; padding: 6px 14px; border-radius: 10px; white-space: nowrap;"><?php echo count($dropouts); ?> <?= __('Dropped Out') ?></span>
                </div>
            </div>
            
            <?php if (count($dropouts) > 0): ?>
                <div style="width: 100%; max-height: 70vh; overflow: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 24px 24px;">
                    <table class="data-table" id="dataTable">
                        <thead>
                            <tr>
                                <th style="padding-left: 32px;"><?= __('Student Profile') ?></th>
                                <th><?= __('Date of Removal') ?></th>
                                <th><?= __('Recorded By') ?></th>
                                <th><?= __('Reason for Removal') ?></th>
                                <th style="text-align: right; padding-right: 32px;" class="actions-column"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dropouts as $student): ?>
                                <tr>
                                    <td style="padding-left: 32px;">
                                        <div style="display: flex; align-items: center; gap: 15px; min-width: 200px;">
                                            <div class="profile-img-circle" style="width: 44px; height: 44px; background: #fef2f2; color: #ef4444; font-size: 1rem; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-shrink: 0;">
                                                <?php echo strtoupper(substr($student['full_name'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 800; color: #1e293b; font-family: 'Outfit'; font-size: 1rem;"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">ID: <?php echo htmlspecialchars($student['student_id'] ?? 'Pending'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-weight: 600; color: #334155; min-width: 140px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fa-solid fa-calendar-xmark" style="color: #fca5a5;"></i>
                                            <?= !empty($student['dropout_date']) ? date('M d, Y', strtotime($student['dropout_date'])) : 'N/A' ?>
                                        </div>
                                    </td>
                                    <td style="font-weight: 700; color: #475569; min-width: 140px;">
                                        <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 8px; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($student['recorder_name'] ?: 'System'); ?>
                                        </span>
                                    </td>
                                    <td style="min-width: 250px; font-weight: 500; font-size: 0.9rem; color: #dc2626; max-width: 350px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($student['dropout_reason'] ?? '') ?>">
                                        <?= htmlspecialchars($student['dropout_reason'] ?: __('No reason provided.')) ?>
                                    </td>
                                    <td style="text-align: right; padding-right: 32px; min-width: 120px;" class="actions-column">
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <a href="../beneficiaries/view.php?id=<?php echo $student['id']; ?>" class="icon-btn" title="View Full Profile" style="background: #eff6ff; color: #2563eb; width: 38px; height: 38px;">
                                                <i class="fa-solid fa-user-graduate"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- No results message -->
                <div id="noResults" style="display: none; text-align: center; padding: 60px 40px; color: #64748b;">
                    <i class="fa-solid fa-filter-circle-xmark" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <h4 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 8px;"><?= __('No matching dropouts found') ?></h4>
                    <p style="font-weight: 500;"><?= __('Try adjusting your search terms.') ?></p>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 100px 40px; color: #64748b; background: #fff;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-users-slash" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 10px;"><?= __('No dropouts') ?></h3>
                    <p style="font-weight: 500;"><?= __('No students have dropped out or been removed from the registry.') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
function filterTable() {
    const query = document.getElementById('searchDropouts').value.toLowerCase().trim();
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
