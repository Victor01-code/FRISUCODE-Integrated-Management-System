<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'finance', 'admin']);
require_once __DIR__ . '/../config/db.php';

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delId = (int)$_GET['id']; // This is the sponsor ID
    // We should delete the user record, which cascades to sponsors (or we delete both)
    $stmt = $pdo->prepare("SELECT user_id FROM sponsors WHERE id = ?");
    $stmt->execute([$delId]);
    $uId = $stmt->fetchColumn();
    if ($uId) {
        $pdo->prepare("DELETE FROM sponsors WHERE id = ?")->execute([$delId]);
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uId]);
    }
    header("Location: index.php?msg=deleted");
    exit;
}

// Fetch Donors
try {
    $stmt = $pdo->query("SELECT s.*, u.email, u.full_name FROM sponsors s JOIN users u ON s.user_id = u.id ORDER BY u.created_at DESC");
    $donors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $donors = [];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Donor Registry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Donor Intelligence') ?></h2>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" class="btn-secondary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); cursor: pointer;">
                    <i class="fa-solid fa-print"></i> <?= __('Print / Export PDF') ?>
                </button>
                <button onclick="document.getElementById('importFile').click()" class="btn-secondary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); cursor: pointer;">
                    <i class="fa-solid fa-file-import"></i> <?= __('Import CSV/Excel') ?>
                </button>
                <input type="file" id="importFile" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style="display: none;" onchange="handleFileUpload(event)">
                <a href="create.php" class="btn-primary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.22);">
                    <i class="fa-solid fa-plus-circle"></i> <?= __('Onboard New Partner') ?>
                </a>
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div style="margin: 20px 40px 0; padding: 14px 20px; background: #fef2f2; color: #b91c1c; border-radius: 12px; font-weight: 700; border: 1px solid #fee2e2;">
            <i class="fa-solid fa-trash"></i> <?= __('Donor has been successfully removed.') ?>
        </div>
        <?php endif; ?>

        <div class="content-box fade-in" style="margin: 32px 40px; animation-delay: 0.1s;">
            <div style="padding: 24px 32px; border-bottom: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; background: #fff; flex-wrap: wrap; gap: 15px;">
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 800; margin: 0; color: #0f172a;"><?= __('Philanthropic Partners') ?></h3>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" id="searchDonors" placeholder="<?= __('Search by name, email, type...') ?>" oninput="filterTable()" style="padding: 10px 16px 10px 40px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; font-weight: 600; width: 320px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <span class="badge" style="background: #eff6ff; color: #2563eb; font-weight: 800; padding: 6px 14px; border-radius: 10px; white-space: nowrap;"><?php echo count($donors); ?> <?= __('Active Stakeholders') ?></span>
                </div>
            </div>
            
            <?php if(count($donors) > 0): ?>
                <div style="width: 100%; max-height: 600px; overflow: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 24px 24px;">
                    <table class="data-table" id="dataTable">
                        <thead>
                            <tr>
                                <th style="padding-left: 32px;"><?= __('Engagement Profile') ?></th>
                                <th><?= __('Partnership Type') ?></th>
                                <th><?= __('Direct Contact') ?></th>
                                <th><?= __('Verified Status') ?></th>
                                <th style="padding-right: 32px; text-align: right;"><?= __('Project Access') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($donors as $donor): ?>
                                <tr>
                                    <td style="padding-left: 32px; min-width: 250px;">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <div class="profile-img-circle" style="width: 44px; height: 44px; background: #fff1f2; color: #e11d48; font-size: 1.1rem; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-shrink: 0;">
                                                <i class="fa-solid fa-hand-holding-heart"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 800; color: #1e293b; font-family: 'Outfit'; font-size: 1rem;"><?php echo htmlspecialchars($donor['organization_name'] ?: $donor['full_name']); ?></div>
                                                <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600; text-transform: lowercase;"><?php echo htmlspecialchars($donor['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="min-width: 160px;">
                                        <span class="badge warning" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800; padding: 6px 14px; border-radius: 10px;">
                                            <i class="fa-solid fa-star"></i>
                                            <?php echo strtoupper($donor['sponsor_type']); ?>
                                        </span>
                                    </td>
                                    <td style="min-width: 180px;">
                                        <div style="font-size: 0.95rem; color: #475569; font-weight: 600;">
                                            <i class="fa-solid fa-phone" style="width: 20px; color: #cbd5e1;"></i>
                                            <?php echo htmlspecialchars($donor['phone']); ?>
                                        </div>
                                    </td>
                                    <td style="min-width: 150px;">
                                        <span class="badge success" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800; padding: 6px 14px;">
                                            <i class="fa-solid fa-circle-check"></i>
                                            OPERATIONAL
                                        </span>
                                    </td>
                                    <td style="padding-right: 32px; text-align: right; min-width: 120px;">
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <a href="view.php?id=<?php echo $donor['id']; ?>" class="icon-btn" title="View Partnership Details" style="background: #eff6ff; color: #2563eb; width: 38px; height: 38px;">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $donor['id']; ?>" class="icon-btn" title="Edit Donor Profile" style="background: #f8fafc; color: #3b82f6; width: 38px; height: 38px;">
                                                <i class="fa-solid fa-user-pen"></i>
                                            </a>
                                            <a href="index.php?action=delete&id=<?php echo $donor['id']; ?>" onclick="return confirm('<?= __('Are you sure you want to completely DELETE this donor? This cannot be undone.') ?>');" class="icon-btn" title="Delete Donor" style="background: #fef2f2; color: #dc2626; width: 38px; height: 38px;">
                                                <i class="fa-solid fa-trash-can"></i>
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
                    <h4 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 8px;"><?= __('No matching donors found') ?></h4>
                    <p style="font-weight: 500;"><?= __('Try adjusting your search terms.') ?></p>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 100px 40px; color: #64748b; background: #fff;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #fdf2f8; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-users-viewfinder" style="font-size: 2.5rem; color: #db2777;"></i>
                    </div>
                    <h3 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 10px;"><?= __('No partner relationships found') ?></h3>
                    <p style="font-weight: 500;"><?= __('When you register official sponsors or partners, they will appear in this registry.') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
function filterTable() {
    const query = document.getElementById('searchDonors').value.toLowerCase().trim();
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

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, {type: 'array'});
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            const json = XLSX.utils.sheet_to_json(worksheet, {defval: ""});
            
            if (json.length === 0) {
                alert("The file is empty.");
                return;
            }

            if(confirm("Found " + json.length + " records. Proceed with import?")) {
                fetch('import_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(json)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let msg = "Successfully imported " + data.count + " records.";
                        if (data.errors && data.errors.length > 0) {
                            msg += "\n\nSkipped " + data.errors.length + " duplicate/invalid records:\n" + data.errors.join("\n");
                        }
                        alert(msg);
                        window.location.reload();
                    } else {
                        let msg = "Error: " + data.message;
                        if (data.errors && data.errors.length > 0) {
                            msg += "\n\nDetails:\n" + data.errors.join("\n");
                        }
                        alert(msg);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred during import.");
                });
            }
        } catch (error) {
            console.error("Error reading file:", error);
            alert("Failed to parse the file. Please ensure it is a valid CSV or Excel file.");
        }
        document.getElementById('importFile').value = "";
    };
    reader.readAsArrayBuffer(file);
}
</script>

</body>
</html>
