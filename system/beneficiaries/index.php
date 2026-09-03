<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'admin', 'staff', 'director', 'project_manager']);
require_once __DIR__ . '/../config/db.php';

// Fetch Beneficiaries
try {
    $stmt = $pdo->query("SELECT * FROM beneficiaries ORDER BY registered_at DESC");
    $beneficiaries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $beneficiaries = [];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Beneficiaries</title>
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
            /* Force show all rows if they match the search, or just show all visible */
            #dataTable { display: table !important; }
        }
        /* Hide actions column specifically during print */
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
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><?= __('Student Registry') ?></h2>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" class="btn-secondary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); cursor: pointer;">
                    <i class="fa-solid fa-print"></i> <?= __('Print / Export PDF') ?>
                </button>
                <button onclick="document.getElementById('importFile').click()" class="btn-secondary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); cursor: pointer;">
                    <i class="fa-solid fa-file-import"></i> <?= __('Import CSV/Excel') ?>
                </button>
                <input type="file" id="importFile" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" style="display: none;" onchange="handleFileUpload(event)">
                <a href="create.php" class="btn-primary" style="border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
                    <i class="fa-solid fa-user-plus"></i> <?= __('Register Student') ?>
                </a>
            </div>
        </div>

        <div class="content-box fade-in" style="margin: 32px 40px; animation-delay: 0.1s;">
            <div style="padding: 24px 32px; border-bottom: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; background: #fff; flex-wrap: wrap; gap: 15px;">
                <h3 style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 800; margin: 0; color: #0f172a;"><?= __('Education Impact List') ?></h3>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;"></i>
                        <input type="text" id="searchStudents" placeholder="<?= __('Search by name, school, level, status...') ?>" oninput="filterTable()" style="padding: 10px 16px 10px 40px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; font-weight: 600; width: 320px; outline: none; transition: border-color 0.2s; font-family: inherit;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <span class="badge" style="background: #eff6ff; color: #2563eb; font-weight: 800; padding: 6px 14px; border-radius: 10px; white-space: nowrap;"><?php echo count($beneficiaries); ?> <?= __('Registered') ?></span>
                </div>
            </div>
            
            <!-- Status Filter Tabs -->
            <div style="display: flex; gap: 10px; padding: 15px 32px; background: #f8fafc; border-bottom: 2px solid #f1f5f9; flex-wrap: wrap;">
                <button onclick="filterStatus('all')" class="status-tab" id="tab-all" style="background: #eff6ff; color: #2563eb; border: none; padding: 8px 16px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: inherit; font-size: 0.85rem;">
                    <i class="fa-solid fa-users"></i> <?= __('All Students') ?>
                </button>
                <button onclick="filterStatus('active')" class="status-tab" id="tab-active" style="background: #fff; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: inherit; font-size: 0.85rem;">
                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> <?= __('Active') ?>
                </button>
                <button onclick="filterStatus('graduated')" class="status-tab" id="tab-graduated" style="background: #fff; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: inherit; font-size: 0.85rem;">
                    <i class="fa-solid fa-award" style="color: #f59e0b;"></i> <?= __('Graduated') ?>
                </button>
                <button onclick="filterStatus('dropped_out')" class="status-tab" id="tab-dropped_out" style="background: #fff; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: inherit; font-size: 0.85rem;">
                    <i class="fa-solid fa-circle-xmark" style="color: #dc2626;"></i> <?= __('Dropped Out') ?>
                </button>
            </div>

            <?php if (count($beneficiaries) > 0): ?>
                <div style="width: 100%; max-height: 70vh; overflow: auto; -webkit-overflow-scrolling: touch; border-radius: 0 0 24px 24px;">
                    <table class="data-table" id="dataTable">
                        <thead>
                            <tr>
                                <th style="padding-left: 32px;"><?= __('Student Profile') ?></th>
                                <th><?= __('Education Level') ?></th>
                                <th><?= __('Institutional Details') ?></th>
                                <th><?= __('Current Status') ?></th>
                                <th style="text-align: right; padding-right: 32px;"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($beneficiaries as $student): ?>
                                <tr data-status="<?php echo htmlspecialchars($student['status']); ?>">
                                    <td style="padding-left: 32px;">
                                        <div style="display: flex; align-items: center; gap: 15px; min-width: 200px;">
                                            <div class="profile-img-circle" style="width: 44px; height: 44px; background: #f1f5f9; color: #3b82f6; font-size: 1rem; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-shrink: 0;">
                                                <?php echo strtoupper(substr($student['full_name'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 800; color: #1e293b; font-family: 'Outfit'; font-size: 1rem;"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Reg: <?php echo date('M d, Y', strtotime($student['registered_at'])); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-weight: 700; color: #475569; min-width: 140px;">
                                        <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 8px; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($student['education_level']); ?>
                                        </span>
                                    </td>
                                    <td style="min-width: 180px;">
                                        <div style="display: flex; align-items: center; gap: 8px; color: #1e293b; font-weight: 600; font-size: 0.9rem;">
                                            <i class="fa-solid fa-school-flag" style="color: #cbd5e1;"></i>
                                            <?php echo htmlspecialchars($student['school_name']); ?>
                                        </div>
                                    </td>
                                    <td style="min-width: 120px;">
                                        <?php 
                                        $statusClass = match($student['status']) {
                                            'active' => 'success',
                                            'graduated' => 'success',
                                            'dropped_out' => 'danger',
                                            default => 'warning'
                                        };
                                        $statusIcon = match($student['status']) {
                                            'active' => 'fa-check-circle',
                                            'graduated' => 'fa-award',
                                            'dropped_out' => 'fa-circle-xmark',
                                            default => 'fa-circle-question'
                                        };
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800;">
                                            <i class="fa-solid <?php echo $statusIcon; ?>"></i>
                                            <?php echo strtoupper($student['status']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right; padding-right: 32px; min-width: 120px;">
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <a href="view.php?id=<?php echo $student['id']; ?>" class="icon-btn" title="View Full Profile" style="background: #eff6ff; color: #2563eb; width: 38px; height: 38px;">
                                                <i class="fa-solid fa-user-graduate"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $student['id']; ?>" class="icon-btn" title="Edit Record" style="background: #f8fafc; color: #64748b; width: 38px; height: 38px;">
                                                <i class="fa-solid fa-user-pen"></i>
                                            </a>
                                            <?php if(in_array($_SESSION['role'], ['super_admin', 'admin', 'director'])): ?>
                                                <a href="delete.php?id=<?php echo $student['id']; ?>" class="icon-btn" title="Delete Student" style="background: #fef2f2; color: #dc2626; width: 38px; height: 38px;" onclick="return confirm('⚠️ Are you sure you want to permanently delete this student record? This action cannot be undone.');">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- No results message (hidden by default) -->
                <div id="noResults" style="display: none; text-align: center; padding: 60px 40px; color: #64748b;">
                    <i class="fa-solid fa-filter-circle-xmark" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <h4 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 8px;"><?= __('No matching students found') ?></h4>
                    <p style="font-weight: 500;"><?= __('Try adjusting your search terms.') ?></p>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: 100px 40px; color: #64748b; background: #fff;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fa-solid fa-users-slash" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="font-family: 'Outfit'; color: #1e293b; margin-bottom: 10px;"><?= __('Registry is empty') ?></h3>
                    <p style="font-weight: 500;"><?= __('No students have been registered in the system yet.') ?></p>
                    <a href="create.php" class="btn-primary" style="margin-top: 20px;"><?= __('Register New Student') ?></a>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
let currentStatusFilter = 'all';

function filterStatus(status) {
    currentStatusFilter = status;
    
    // Reset all tabs to inactive styling
    const tabs = document.querySelectorAll('.status-tab');
    tabs.forEach(tab => {
        tab.style.background = '#fff';
        tab.style.color = '#64748b';
        tab.style.border = '1px solid #e2e8f0';
    });
    
    // Set active tab styling
    const activeTab = document.getElementById('tab-' + status);
    if (activeTab) {
        activeTab.style.background = '#eff6ff';
        activeTab.style.color = '#2563eb';
        activeTab.style.border = 'none';
    }
    
    filterTable();
}

function filterTable() {
    const query = document.getElementById('searchStudents').value.toLowerCase().trim();
    const table = document.getElementById('dataTable');
    if (!table) return;
    const rows = table.querySelectorAll('tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const status = row.getAttribute('data-status') || '';
        const matchText = !query || text.includes(query);
        const matchStatus = currentStatusFilter === 'all' || status === currentStatusFilter;
        const match = matchText && matchStatus;
        
        row.style.display = match ? '' : 'none';
        if (match) visibleCount++;
    });

    const noResults = document.getElementById('noResults');
    if (noResults) {
        noResults.style.display = (visibleCount === 0 && (query || currentStatusFilter !== 'all')) ? 'block' : 'none';
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
                // Post data to server
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
        // Reset file input
        document.getElementById('importFile').value = "";
    };
    reader.readAsArrayBuffer(file);
}
</script>

</body>
</html>
