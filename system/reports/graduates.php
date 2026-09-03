<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'admin', 'staff', 'director', 'project_manager']);
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM beneficiaries WHERE status='graduated' ORDER BY graduation_date DESC");
    $graduates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $graduates = [];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Graduate Report</title>
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
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Graduate Alumni Report') ?></h2>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" class="btn-secondary" style="border-radius: 14px; cursor: pointer;">
                    <i class="fa-solid fa-print"></i> <?= __('Print / Export PDF') ?>
                </button>
                <a href="export.php?type=graduates" class="btn-primary" style="border-radius: 14px; background: linear-gradient(135deg, #10b981, #059669); border: none;">
                    <i class="fa-solid fa-file-csv"></i> <?= __('Export CSV') ?>
                </a>
            </div>
        </div>

        <div class="content-box fade-in" style="margin: 32px 40px; animation-delay: 0.1s;">
            <div style="padding: 24px 32px; border-bottom: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; background: #fff; flex-wrap: wrap; gap: 15px;">
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 800; margin: 0; color: #0f172a;"><?= __('Successful Graduates') ?></h3>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" id="searchGraduates" placeholder="<?= __('Search graduates...') ?>" oninput="filterTable()" style="padding: 10px 16px 10px 40px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; font-weight: 600; width: 320px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <span class="badge" style="background: #ecfdf5; color: #10b981; font-weight: 800; padding: 6px 14px; border-radius: 10px; white-space: nowrap;"><?php echo count($graduates); ?> <?= __('Graduated') ?></span>
                </div>
            </div>
            
            <?php if (count($graduates) > 0): ?>
                <div style="width: 100%; max-height: 70vh; overflow: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 24px 24px;">
                    <table class="data-table" id="dataTable">
                        <thead>
                            <tr>
                                <th style="padding-left: 32px;"><?= __('Graduate Profile') ?></th>
                                <th><?= __('Completed Phase') ?></th>
                                <th><?= __('Graduation Date') ?></th>
                                <th><?= __('Profession & Notes') ?></th>
                                <th style="text-align: right; padding-right: 32px;" class="actions-column"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($graduates as $student): ?>
                                <tr>
                                    <td style="padding-left: 32px;">
                                        <div style="display: flex; align-items: center; gap: 15px; min-width: 200px;">
                                            <div class="profile-img-circle" style="width: 44px; height: 44px; background: #fffbeb; color: #f59e0b; font-size: 1rem; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-shrink: 0;">
                                                <?php echo strtoupper(substr($student['full_name'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 800; color: #1e293b; font-family: 'Outfit'; font-size: 1rem;"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">ID: <?php echo htmlspecialchars($student['student_id'] ?? 'Pending'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-weight: 700; color: #475569; min-width: 140px;">
                                        <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 8px; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($student['education_level']); ?>
                                        </span>
                                    </td>
                                    <td style="font-weight: 600; color: #334155; min-width: 140px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fa-solid fa-calendar-check" style="color: #cbd5e1;"></i>
                                            <?= !empty($student['graduation_date']) ? date('M d, Y', strtotime($student['graduation_date'])) : 'N/A' ?>
                                        </div>
                                    </td>
                                    <td style="min-width: 250px; font-weight: 500; font-size: 0.9rem; color: #64748b; max-width: 350px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($student['graduation_notes'] ?? '') ?>">
                                        <?= htmlspecialchars($student['graduation_notes'] ?: __('No notes provided.')) ?>
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
                    <h4 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 8px;"><?= __('No matching graduates found') ?></h4>
                    <p style="font-weight: 500;"><?= __('Try adjusting your search terms.') ?></p>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 100px 40px; color: #64748b; background: #fff;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-graduation-cap" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 10px;"><?= __('No graduates yet') ?></h3>
                    <p style="font-weight: 500;"><?= __('There are no graduated alumni registered in the system.') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
function filterTable() {
    const query = document.getElementById('searchGraduates').value.toLowerCase().trim();
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
