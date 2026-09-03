<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'project_manager']); 
require_once __DIR__ . '/../config/db.php';

if (in_array($_SESSION['role'], ['super_admin', 'director', 'admin']) && isset($_GET['action'], $_GET['id'])) {
    $actionId = (int)$_GET['id'];
    $action = $_GET['action'];
    
    // Prevent self-modification
    if ($actionId !== (int)$_SESSION['user_id']) {
        if ($action === 'terminate') {
            $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = ?")->execute([$actionId]);
            header("Location: index.php?msg=terminated");
            exit;
        } elseif ($action === 'activate') {
            $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?")->execute([$actionId]);
            header("Location: index.php?msg=activated");
            exit;
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$actionId]);
            header("Location: index.php?msg=deleted");
            exit;
        }
    } else {
        header("Location: index.php?error=self_action");
        exit;
    }
}

try {
    $stmt = $pdo->query("SELECT id, full_name, email, role, status, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Staff Directory</title>
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
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Staff Repository') ?></h2>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" class="btn-secondary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); cursor: pointer;">
                    <i class="fa-solid fa-print"></i> <?= __('Print / Export PDF') ?>
                </button>
                <button onclick="document.getElementById('importFile').click()" class="btn-secondary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); cursor: pointer;">
                    <i class="fa-solid fa-file-import"></i> <?= __('Import CSV/Excel') ?>
                </button>
                <input type="file" id="importFile" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style="display: none;" onchange="handleFileUpload(event)">
                <?php if(in_array($_SESSION['role'], ['super_admin', 'director', 'admin'])): ?>
                <a href="create.php" class="btn-primary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
                    <i class="fa-solid fa-user-plus"></i> <?= __('Add New Personnel') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'terminated'): ?>
        <div style="margin: 20px 40px 0; padding: 14px 20px; background: #fffbeb; color: #b45309; border-radius: 12px; font-weight: 700; border: 1px solid #fef3c7;">
            <i class="fa-solid fa-user-slash"></i> <?= __('User access has been terminated.') ?>
        </div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'activated'): ?>
        <div style="margin: 20px 40px 0; padding: 14px 20px; background: #f0fdf4; color: #15803d; border-radius: 12px; font-weight: 700; border: 1px solid #dcfce7;">
            <i class="fa-solid fa-user-check"></i> <?= __('User access has been restored.') ?>
        </div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div style="margin: 20px 40px 0; padding: 14px 20px; background: #fef2f2; color: #b91c1c; border-radius: 12px; font-weight: 700; border: 1px solid #fee2e2;">
            <i class="fa-solid fa-trash"></i> <?= __('User has been completely deleted.') ?>
        </div>
        <?php elseif(isset($_GET['error']) && $_GET['error'] === 'self_action'): ?>
        <div style="margin: 20px 40px 0; padding: 14px 20px; background: #fef2f2; color: #b91c1c; border-radius: 12px; font-weight: 700; border: 1px solid #fee2e2;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= __('You cannot modify or delete your own account here.') ?>
        </div>
        <?php endif; ?>

        <div class="content-box fade-in" style="margin: 32px 40px; animation-delay: 0.1s;">
            <div style="padding: 24px 32px; border-bottom: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; background: #fff; flex-wrap: wrap; gap: 15px;">
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 800; margin: 0; color: #0f172a;"><?= __('Authorized Personnel') ?></h3>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" id="searchUsers" placeholder="<?= __('Search by name, email, role...') ?>" oninput="filterTable()" style="padding: 10px 16px 10px 40px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; font-weight: 600; width: 320px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <span class="badge" style="background: #f8fafc; color: #64748b; font-weight: 800; padding: 6px 14px; border-radius: 10px; white-space: nowrap;"><?php echo count($users); ?> <?= __('Total Users') ?></span>
                </div>
            </div>
            
            <?php if (count($users) > 0): ?>
                <div style="width: 100%; max-height: 600px; overflow: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 24px 24px;">
                    <table class="data-table" id="dataTable">
                        <thead>
                            <tr>
                                <th style="padding-left: 32px; min-width: 250px;"><?= __('Personnel Name') ?></th>
                                <th style="min-width: 200px;"><?= __('Electronic Mail') ?></th>
                                <th style="min-width: 180px;"><?= __('Access Privilege') ?></th>
                                <th style="min-width: 140px;"><?= __('Onboarding Date') ?></th>
                                <th style="padding-right: 32px; text-align: right; min-width: 120px;"><?= __('System Control') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td style="padding-left: 32px;">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <?php 
                                            $avatarColors = [
                                                'super_admin' => ['#fef2f2', '#dc2626'],
                                                'director' => ['#fffbeb', '#d97706'],
                                                'finance' => ['#f0fdf4', '#16a34a'],
                                                'project_manager' => ['#eff6ff', '#2563eb'],
                                                'default' => ['#f1f5f9', '#475569']
                                            ];
                                            $colors = $avatarColors[$u['role']] ?? $avatarColors['default'];
                                            ?>
                                            <div class="profile-img-circle" style="width: 44px; height: 44px; background: <?php echo $colors[0]; ?>; color: <?php echo $colors[1]; ?>; font-size: 1rem; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-shrink: 0;">
                                                <?php echo strtoupper(substr($u['full_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 800; color: #1e293b; font-family: 'Outfit'; font-size: 1rem;"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                                <?php if($u['status'] === 'active'): ?>
                                                    <div style="font-size: 0.8rem; color: #16a34a; font-weight: 700;"><i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 3px;"></i> <?= __('Access Active') ?></div>
                                                <?php else: ?>
                                                    <div style="font-size: 0.8rem; color: #dc2626; font-weight: 700;"><i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 3px;"></i> <?= __('Access Terminated') ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color: #475569; font-weight: 600; font-size: 0.95rem;"><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td>
                                        <?php 
                                        $roleClass = match($u['role']) {
                                            'super_admin' => 'danger',
                                            'director' => 'warning',
                                            'finance' => 'success',
                                            'project_manager' => 'primary',
                                            default => 'neutral'
                                        };
                                        ?>
                                        <span class="badge <?php echo $roleClass; ?>" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800; padding: 6px 14px;">
                                            <i class="fa-solid fa-shield"></i>
                                            <?php echo strtoupper(str_replace('_', ' ', $u['role'])); ?>
                                        </span>
                                    </td>
                                    <td style="color: #64748b; font-weight: 700; font-size: 0.9rem;">
                                        <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                                    </td>
                                    <td style="padding-right: 32px; text-align: right;">
                                        <?php if(in_array($_SESSION['role'], ['super_admin', 'director', 'admin'])): ?>
                                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                                <a href="edit.php?id=<?php echo $u['id']; ?>" class="icon-btn" title="<?= __('Update Profile') ?>" style="background: #f8fafc; color: #3b82f6; width: 38px; height: 38px;">
                                                    <i class="fa-solid fa-user-pen"></i>
                                                </a>
                                                <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                                    <?php if($u['status'] === 'active'): ?>
                                                        <a href="index.php?action=terminate&id=<?php echo $u['id']; ?>" onclick="return confirm('<?= __('Are you sure you want to terminate access for this user?') ?>');" class="icon-btn" title="<?= __('Terminate Access') ?>" style="background: #fffbeb; color: #d97706; width: 38px; height: 38px;">
                                                            <i class="fa-solid fa-user-slash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="index.php?action=activate&id=<?php echo $u['id']; ?>" class="icon-btn" title="<?= __('Restore Access') ?>" style="background: #f0fdf4; color: #16a34a; width: 38px; height: 38px;">
                                                            <i class="fa-solid fa-user-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="index.php?action=delete&id=<?php echo $u['id']; ?>" onclick="return confirm('<?= __('Are you sure you want to completely DELETE this user? This cannot be undone.') ?>');" class="icon-btn" title="<?= __('Delete User') ?>" style="background: #fef2f2; color: #dc2626; width: 38px; height: 38px;">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- No results message -->
                <div id="noResults" style="display: none; text-align: center; padding: 60px 40px; color: #64748b;">
                    <i class="fa-solid fa-filter-circle-xmark" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <h4 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 8px;"><?= __('No matching personnel found') ?></h4>
                    <p style="font-weight: 500;"><?= __('Try adjusting your search terms.') ?></p>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 100px 40px; color: #64748b; background: #fff;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-user-lock" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 10px;"><?= __('User base is empty') ?></h3>
                    <p style="font-weight: 500;"><?= __('Please register authorized staff to access the management portal.') ?></p>
                    <a href="create.php" class="btn-primary" style="margin-top: 20px;"><?= __('Initialize Staff Entry') ?></a>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
function filterTable() {
    const query = document.getElementById('searchUsers').value.toLowerCase().trim();
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
