<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'project_manager', 'staff']);
require_once __DIR__ . '/../config/db.php';

// Fetch Projects
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $projects = [];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Projects</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header fade-in" style="margin-bottom: 0;">
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Project Management') ?></h2>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" class="btn-secondary" style="border-radius: 14px; cursor: pointer;">
                    <i class="fa-solid fa-print"></i> <?= __('Print / Export PDF') ?>
                </button>
                <?php if(in_array($_SESSION['role'], ['super_admin', 'director', 'project_manager'])): ?>
                    <a href="create.php" class="btn-primary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
                        <i class="fa-solid fa-plus"></i> <?= __('Start New Project') ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-box fade-in" style="margin: 32px 40px; animation-delay: 0.1s;">
            <div style="padding: 24px 32px; border-bottom: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; background: #fff; flex-wrap: wrap; gap: 15px;">
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 800; margin: 0; color: #0f172a;"><?= __('Active Initiatives') ?></h3>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" id="searchProjects" placeholder="<?= __('Search by title, status, budget...') ?>" oninput="filterTable()" style="padding: 10px 16px 10px 40px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; font-weight: 600; width: 320px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <span class="badge" style="background: #eff6ff; color: #2563eb; font-weight: 800; padding: 6px 14px; border-radius: 10px; white-space: nowrap;"><?php echo count($projects); ?> <?= __('Registered') ?></span>
                </div>
            </div>
            
            <?php if (count($projects) > 0): ?>
                <div style="width: 100%; max-height: 70vh; overflow: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 24px 24px;">
                    <table class="data-table" id="dataTable">
                        <thead>
                            <tr>
                                <th style="padding-left: 32px;"><?= __('Program Details') ?></th>
                                <th><?= __('Target Phase') ?></th>
                                <th><?= __('Launch Date') ?></th>
                                <th><?= __('Investment') ?></th>
                                <th style="text-align: right; padding-right: 32px;"><?= __('Control') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td style="padding-left: 32px;">
                                        <div style="display: flex; align-items: center; gap: 15px; min-width: 250px;">
                                            <div style="width: 48px; height: 48px; border-radius: 14px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.2rem; flex-shrink: 0;">
                                                <i class="fa-solid fa-diagram-project"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 800; color: #1e293b; font-size: 1rem; font-family: 'Outfit';"><?php echo htmlspecialchars($project['title']); ?></div>
                                                <div style="font-size: 0.85rem; color: #64748b; font-weight: 500; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <?php echo htmlspecialchars($project['description']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="min-width: 140px;">
                                        <?php 
                                        $statusClass = match($project['status']) {
                                            'active' => 'success',
                                            'planning' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'neutral'
                                        };
                                        $statusIcon = match($project['status']) {
                                            'active' => 'fa-play-circle',
                                            'planning' => 'fa-compass',
                                            'completed' => 'fa-circle-check',
                                            'cancelled' => 'fa-circle-xmark',
                                            default => 'fa-circle-dot'
                                        };
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800;">
                                            <i class="fa-solid <?php echo $statusIcon; ?>"></i>
                                            <?php echo strtoupper($project['status']); ?>
                                        </span>
                                    </td>
                                    <td style="color: #475569; font-weight: 600; font-size: 0.95rem; min-width: 160px;">
                                        <i class="fa-regular fa-calendar-days" style="margin-right: 8px; color: #94a3b8;"></i>
                                        <?php echo date('M d, Y', strtotime($project['start_date'])); ?>
                                    </td>
                                    <td style="font-family: 'Outfit'; font-weight: 800; color: #0f172a; font-size: 1.1rem; min-width: 130px;">
                                        $<?php echo number_format($project['budget'], 2); ?>
                                    </td>
                                    <td style="text-align: right; padding-right: 32px; min-width: 120px;">
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <a href="view.php?id=<?php echo $project['id']; ?>" class="icon-btn" title="Detailed Analytics" style="background: #eff6ff; color: #2563eb; width: 38px; height: 38px;">
                                                <i class="fa-solid fa-expand"></i>
                                            </a>
                                            <?php if(in_array($_SESSION['role'], ['super_admin', 'project_manager'])): ?>
                                                <a href="edit.php?id=<?php echo $project['id']; ?>" class="icon-btn" title="Configure Program" style="background: #f8fafc; color: #64748b; width: 38px; height: 38px;">
                                                    <i class="fa-solid fa-sliders"></i>
                                                </a>
                                            <?php endif; ?>
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
                    <h4 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 8px;"><?= __('No matching projects found') ?></h4>
                    <p style="font-weight: 500;"><?= __('Try adjusting your search terms.') ?></p>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 100px 40px; color: #64748b; background: #fff;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 10px;"><?= __('No projects launched yet') ?></h3>
                    <p style="font-weight: 500;"><?= __('Start by initiating your first program impact initiative.') ?></p>
                    <a href="create.php" class="btn-primary" style="margin-top: 20px;"><?= __('Start New Project') ?></a>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
function filterTable() {
    const query = document.getElementById('searchProjects').value.toLowerCase().trim();
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
