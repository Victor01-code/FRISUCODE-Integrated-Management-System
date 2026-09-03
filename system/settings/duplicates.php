<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director']);
require_once __DIR__ . '/../config/db.php';

$success = '';
$error = '';

// ── Handle DELETE action ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'delete_user_duplicate') {
        $keepId = (int)$_POST['keep_id'];
        $deleteId = (int)$_POST['delete_id'];

        if ($keepId === $deleteId) {
            $error = "Cannot keep and delete the same record.";
        } else {
            try {
                $pdo->beginTransaction();

                // Reassign beneficiary_sponsors from the duplicate to the kept record
                $pdo->prepare("UPDATE IGNORE beneficiary_sponsors SET sponsor_id = ? WHERE sponsor_id = ?")->execute([$keepId, $deleteId]);
                $pdo->prepare("DELETE FROM beneficiary_sponsors WHERE sponsor_id = ?")->execute([$deleteId]);

                // Reassign finance_records
                $pdo->prepare("UPDATE finance_records SET recorded_by = ? WHERE recorded_by = ?")->execute([$keepId, $deleteId]);

                // Reassign projects
                $pdo->prepare("UPDATE projects SET created_by = ? WHERE created_by = ?")->execute([$keepId, $deleteId]);

                // Delete sponsor profile if exists
                $pdo->prepare("DELETE FROM sponsors WHERE user_id = ?")->execute([$deleteId]);

                // Delete the duplicate user
                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$deleteId]);

                $pdo->commit();
                $success = "Duplicate user merged successfully. All related records have been reassigned.";
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $error = "Merge failed: " . $e->getMessage();
            }
        }
    }

    if ($action === 'delete_beneficiary_duplicate') {
        $keepId = (int)$_POST['keep_id'];
        $deleteId = (int)$_POST['delete_id'];

        if ($keepId === $deleteId) {
            $error = "Cannot keep and delete the same record.";
        } else {
            try {
                $pdo->beginTransaction();

                // Reassign beneficiary_sponsors links
                $pdo->prepare("UPDATE IGNORE beneficiary_sponsors SET beneficiary_id = ? WHERE beneficiary_id = ?")->execute([$keepId, $deleteId]);
                $pdo->prepare("DELETE FROM beneficiary_sponsors WHERE beneficiary_id = ?")->execute([$deleteId]);

                // Reassign student_reports
                $pdo->prepare("UPDATE student_reports SET beneficiary_id = ? WHERE beneficiary_id = ?")->execute([$keepId, $deleteId]);

                // Delete the duplicate beneficiary
                $pdo->prepare("DELETE FROM beneficiaries WHERE id = ?")->execute([$deleteId]);

                $pdo->commit();
                $success = "Duplicate beneficiary merged successfully. All related records have been reassigned.";
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $error = "Merge failed: " . $e->getMessage();
            }
        }
    }
}

// ── Scan for duplicate Users (by email, case-insensitive) ──
$dupUsers = [];
try {
    $stmt = $pdo->query("
        SELECT LOWER(email) as dup_email, COUNT(*) as cnt 
        FROM users 
        GROUP BY LOWER(email) 
        HAVING COUNT(*) > 1
    ");
    $dupEmails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dupEmails as $dup) {
        $detailStmt = $pdo->prepare("SELECT id, full_name, email, role, status, created_at FROM users WHERE LOWER(email) = ? ORDER BY created_at ASC");
        $detailStmt->execute([$dup['dup_email']]);
        $dupUsers[] = [
            'email' => $dup['dup_email'],
            'count' => $dup['cnt'],
            'records' => $detailStmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }
} catch (PDOException $e) {}

// ── Scan for duplicate Beneficiaries (by full_name, case-insensitive) ──
$dupBeneficiaries = [];
try {
    $stmt = $pdo->query("
        SELECT LOWER(full_name) as dup_name, COUNT(*) as cnt 
        FROM beneficiaries 
        GROUP BY LOWER(full_name) 
        HAVING COUNT(*) > 1
    ");
    $dupNames = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dupNames as $dup) {
        $detailStmt = $pdo->prepare("SELECT id, full_name, student_id, school_name, status, registered_at FROM beneficiaries WHERE LOWER(full_name) = ? ORDER BY registered_at ASC");
        $detailStmt->execute([$dup['dup_name']]);
        $dupBeneficiaries[] = [
            'name' => $dup['dup_name'],
            'count' => $dup['cnt'],
            'records' => $detailStmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }
} catch (PDOException $e) {}

// ── Scan for duplicate Beneficiaries (by student_id) ──
$dupStudentIds = [];
try {
    $stmt = $pdo->query("
        SELECT student_id, COUNT(*) as cnt 
        FROM beneficiaries 
        WHERE student_id IS NOT NULL AND student_id != ''
        GROUP BY student_id 
        HAVING COUNT(*) > 1
    ");
    $dupIds = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($dupIds as $dup) {
        $detailStmt = $pdo->prepare("SELECT id, full_name, student_id, school_name, status, registered_at FROM beneficiaries WHERE student_id = ? ORDER BY registered_at ASC");
        $detailStmt->execute([$dup['student_id']]);
        $dupStudentIds[] = [
            'student_id' => $dup['student_id'],
            'count' => $dup['cnt'],
            'records' => $detailStmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }
} catch (PDOException $e) {}

$totalDuplicates = count($dupUsers) + count($dupBeneficiaries) + count($dupStudentIds);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Duplicates Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
    <style>
        .dup-section { margin: 30px 40px; }
        .dup-card {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 12px rgba(0,0,0,0.04);
            border: 1px solid #f1f5f9;
            transition: box-shadow 0.3s;
        }
        .dup-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .dup-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .dup-card-header h4 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: #0f172a;
            margin: 0;
        }
        .dup-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .dup-badge-red { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .dup-badge-amber { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .dup-badge-green { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .dup-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
        }
        .dup-table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .dup-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .dup-table tr:last-child td { border-bottom: none; }
        .dup-table tr:hover td { background: #f8fafc; }

        .merge-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 10px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }
        .btn-keep {
            background: #f0fdf4;
            color: #16a34a;
            border: 1.5px solid #bbf7d0;
        }
        .btn-keep:hover { background: #dcfce7; }
        .btn-delete {
            background: #fef2f2;
            color: #dc2626;
            border: 1.5px solid #fecaca;
        }
        .btn-delete:hover { background: #fee2e2; }

        .stats-row {
            display: flex;
            gap: 20px;
            margin: 24px 40px;
            flex-wrap: wrap;
        }
        .stat-card {
            flex: 1;
            min-width: 200px;
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .stat-icon-red { background: #fef2f2; color: #dc2626; }
        .stat-icon-blue { background: #eff6ff; color: #2563eb; }
        .stat-icon-amber { background: #fffbeb; color: #d97706; }
        .stat-icon-green { background: #f0fdf4; color: #16a34a; }
        .stat-value {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            color: #0f172a;
            line-height: 1;
        }
        .stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            margin-top: 4px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            color: #0f172a;
            margin: 0 0 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            color: #64748b;
            font-size: 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 16px;
            display: block;
            color: #cbd5e1;
        }
        .empty-state p {
            font-size: 1rem;
            font-weight: 600;
        }

        .tab-nav {
            display: flex;
            gap: 4px;
            margin: 20px 40px 0;
            background: #f1f5f9;
            border-radius: 14px;
            padding: 5px;
            width: fit-content;
        }
        .tab-btn {
            padding: 10px 22px;
            border-radius: 10px;
            border: none;
            background: transparent;
            font-weight: 700;
            font-size: 0.85rem;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tab-btn.active {
            background: #fff;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .tab-btn:hover:not(.active) { color: #334155; }
        .tab-btn .tab-count {
            background: #ef4444;
            color: #fff;
            padding: 2px 8px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
        }
        .tab-btn.active .tab-count { background: #dc2626; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header fade-in" style="margin-bottom: 0;">
            <h2 style="font-family: 'Outfit'; font-weight: 800;"><i class="fa-solid fa-clone" style="color: var(--primary); margin-right: 8px;"></i> <?= __('Duplicates Manager') ?></h2>
            <a href="index.php" class="btn-secondary" style="border-radius: 14px;">
                <i class="fa-solid fa-arrow-left"></i> <?= __('Back to Settings') ?>
            </a>
        </div>

        <?php if ($success): ?>
        <div style="margin: 20px 40px 0; padding: 14px 20px; background: #f0fdf4; color: #15803d; border-radius: 12px; font-weight: 700; border: 1px solid #dcfce7;">
            <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div style="margin: 20px 40px 0; padding: 14px 20px; background: #fef2f2; color: #b91c1c; border-radius: 12px; font-weight: 700; border: 1px solid #fee2e2;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Stats Row -->
        <div class="stats-row fade-in">
            <div class="stat-card">
                <div class="stat-icon <?= $totalDuplicates > 0 ? 'stat-icon-red' : 'stat-icon-green' ?>">
                    <i class="fa-solid <?= $totalDuplicates > 0 ? 'fa-triangle-exclamation' : 'fa-shield-check' ?>"></i>
                </div>
                <div>
                    <div class="stat-value"><?= $totalDuplicates ?></div>
                    <div class="stat-label"><?= __('Duplicate Groups Found') ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="stat-value"><?= count($dupUsers) ?></div>
                    <div class="stat-label"><?= __('Duplicate Users (by Email)') ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-amber">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <div class="stat-value"><?= count($dupBeneficiaries) + count($dupStudentIds) ?></div>
                    <div class="stat-label"><?= __('Duplicate Beneficiaries') ?></div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="tab-nav fade-in">
            <button class="tab-btn active" onclick="switchTab('users', this)">
                <i class="fa-solid fa-users"></i> <?= __('Users / Donors') ?>
                <?php if (count($dupUsers) > 0): ?><span class="tab-count"><?= count($dupUsers) ?></span><?php endif; ?>
            </button>
            <button class="tab-btn" onclick="switchTab('beneficiaries', this)">
                <i class="fa-solid fa-user-graduate"></i> <?= __('Beneficiaries (Name)') ?>
                <?php if (count($dupBeneficiaries) > 0): ?><span class="tab-count"><?= count($dupBeneficiaries) ?></span><?php endif; ?>
            </button>
            <button class="tab-btn" onclick="switchTab('student_ids', this)">
                <i class="fa-solid fa-id-card"></i> <?= __('Beneficiaries (Student ID)') ?>
                <?php if (count($dupStudentIds) > 0): ?><span class="tab-count"><?= count($dupStudentIds) ?></span><?php endif; ?>
            </button>
        </div>

        <!-- Tab: Duplicate Users -->
        <div id="tab-users" class="tab-content active">
            <div class="dup-section">
                <h3 class="section-title"><i class="fa-solid fa-envelope"></i> <?= __('Duplicate Users by Email Address') ?></h3>

                <?php if (empty($dupUsers)): ?>
                <div class="dup-card">
                    <div class="empty-state">
                        <i class="fa-solid fa-circle-check"></i>
                        <p><?= __('No duplicate user emails found. Your database is clean!') ?></p>
                    </div>
                </div>
                <?php else: ?>
                    <?php foreach ($dupUsers as $group): ?>
                    <div class="dup-card">
                        <div class="dup-card-header">
                            <h4><i class="fa-solid fa-envelope" style="color: #2563eb; margin-right: 8px;"></i> <?= htmlspecialchars($group['email']) ?></h4>
                            <span class="dup-badge dup-badge-red"><i class="fa-solid fa-clone"></i> <?= $group['count'] ?> <?= __('duplicates') ?></span>
                        </div>
                        <table class="dup-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?= __('Full Name') ?></th>
                                    <th><?= __('Email') ?></th>
                                    <th><?= __('Role') ?></th>
                                    <th><?= __('Status') ?></th>
                                    <th><?= __('Created') ?></th>
                                    <th style="text-align: center;"><?= __('Action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($group['records'] as $i => $rec): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #64748b;">#<?= $rec['id'] ?></td>
                                    <td style="font-weight: 700;"><?= htmlspecialchars($rec['full_name']) ?></td>
                                    <td><?= htmlspecialchars($rec['email']) ?></td>
                                    <td><span class="dup-badge dup-badge-amber"><?= htmlspecialchars($rec['role']) ?></span></td>
                                    <td><span class="dup-badge <?= $rec['status'] === 'active' ? 'dup-badge-green' : 'dup-badge-red' ?>"><?= htmlspecialchars($rec['status']) ?></span></td>
                                    <td style="color: #64748b; font-size: 0.8rem;"><?= date('M d, Y', strtotime($rec['created_at'])) ?></td>
                                    <td style="text-align: center;">
                                        <?php
                                        // The first record (oldest) gets "Keep", the rest get "Delete"
                                        $otherIds = array_column($group['records'], 'id');
                                        $keepId = $group['records'][0]['id'];
                                        ?>
                                        <?php if ($i === 0): ?>
                                            <span class="merge-btn btn-keep"><i class="fa-solid fa-shield-halved"></i> <?= __('Keep (Oldest)') ?></span>
                                        <?php else: ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to merge and delete User #<?= $rec['id'] ?>? All related records will be reassigned to User #<?= $keepId ?>.');">
                                                <input type="hidden" name="action" value="delete_user_duplicate">
                                                <input type="hidden" name="keep_id" value="<?= $keepId ?>">
                                                <input type="hidden" name="delete_id" value="<?= $rec['id'] ?>">
                                                <button type="submit" class="merge-btn btn-delete"><i class="fa-solid fa-trash-can"></i> <?= __('Merge & Delete') ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab: Duplicate Beneficiaries (by Name) -->
        <div id="tab-beneficiaries" class="tab-content">
            <div class="dup-section">
                <h3 class="section-title"><i class="fa-solid fa-user-graduate"></i> <?= __('Duplicate Beneficiaries by Full Name') ?></h3>

                <?php if (empty($dupBeneficiaries)): ?>
                <div class="dup-card">
                    <div class="empty-state">
                        <i class="fa-solid fa-circle-check"></i>
                        <p><?= __('No duplicate beneficiary names found. Your database is clean!') ?></p>
                    </div>
                </div>
                <?php else: ?>
                    <?php foreach ($dupBeneficiaries as $group): ?>
                    <div class="dup-card">
                        <div class="dup-card-header">
                            <h4><i class="fa-solid fa-user" style="color: #d97706; margin-right: 8px;"></i> <?= htmlspecialchars(ucwords($group['name'])) ?></h4>
                            <span class="dup-badge dup-badge-red"><i class="fa-solid fa-clone"></i> <?= $group['count'] ?> <?= __('duplicates') ?></span>
                        </div>
                        <table class="dup-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?= __('Full Name') ?></th>
                                    <th><?= __('Student ID') ?></th>
                                    <th><?= __('School') ?></th>
                                    <th><?= __('Status') ?></th>
                                    <th><?= __('Registered') ?></th>
                                    <th style="text-align: center;"><?= __('Action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($group['records'] as $i => $rec): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #64748b;">#<?= $rec['id'] ?></td>
                                    <td style="font-weight: 700;"><?= htmlspecialchars($rec['full_name']) ?></td>
                                    <td><?= htmlspecialchars($rec['student_id'] ?: '—') ?></td>
                                    <td><?= htmlspecialchars($rec['school_name'] ?: '—') ?></td>
                                    <td><span class="dup-badge <?= $rec['status'] === 'active' ? 'dup-badge-green' : 'dup-badge-amber' ?>"><?= htmlspecialchars($rec['status']) ?></span></td>
                                    <td style="color: #64748b; font-size: 0.8rem;"><?= date('M d, Y', strtotime($rec['registered_at'])) ?></td>
                                    <td style="text-align: center;">
                                        <?php $keepId = $group['records'][0]['id']; ?>
                                        <?php if ($i === 0): ?>
                                            <span class="merge-btn btn-keep"><i class="fa-solid fa-shield-halved"></i> <?= __('Keep (Oldest)') ?></span>
                                        <?php else: ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to merge and delete Beneficiary #<?= $rec['id'] ?>? All related records will be reassigned to Beneficiary #<?= $keepId ?>.');">
                                                <input type="hidden" name="action" value="delete_beneficiary_duplicate">
                                                <input type="hidden" name="keep_id" value="<?= $keepId ?>">
                                                <input type="hidden" name="delete_id" value="<?= $rec['id'] ?>">
                                                <button type="submit" class="merge-btn btn-delete"><i class="fa-solid fa-trash-can"></i> <?= __('Merge & Delete') ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab: Duplicate Beneficiaries (by Student ID) -->
        <div id="tab-student_ids" class="tab-content">
            <div class="dup-section">
                <h3 class="section-title"><i class="fa-solid fa-id-card"></i> <?= __('Duplicate Beneficiaries by Student ID') ?></h3>

                <?php if (empty($dupStudentIds)): ?>
                <div class="dup-card">
                    <div class="empty-state">
                        <i class="fa-solid fa-circle-check"></i>
                        <p><?= __('No duplicate student IDs found. Your database is clean!') ?></p>
                    </div>
                </div>
                <?php else: ?>
                    <?php foreach ($dupStudentIds as $group): ?>
                    <div class="dup-card">
                        <div class="dup-card-header">
                            <h4><i class="fa-solid fa-id-badge" style="color: #7c3aed; margin-right: 8px;"></i> <?= htmlspecialchars($group['student_id']) ?></h4>
                            <span class="dup-badge dup-badge-red"><i class="fa-solid fa-clone"></i> <?= $group['count'] ?> <?= __('duplicates') ?></span>
                        </div>
                        <table class="dup-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?= __('Full Name') ?></th>
                                    <th><?= __('Student ID') ?></th>
                                    <th><?= __('School') ?></th>
                                    <th><?= __('Status') ?></th>
                                    <th><?= __('Registered') ?></th>
                                    <th style="text-align: center;"><?= __('Action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($group['records'] as $i => $rec): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #64748b;">#<?= $rec['id'] ?></td>
                                    <td style="font-weight: 700;"><?= htmlspecialchars($rec['full_name']) ?></td>
                                    <td><?= htmlspecialchars($rec['student_id']) ?></td>
                                    <td><?= htmlspecialchars($rec['school_name'] ?: '—') ?></td>
                                    <td><span class="dup-badge <?= $rec['status'] === 'active' ? 'dup-badge-green' : 'dup-badge-amber' ?>"><?= htmlspecialchars($rec['status']) ?></span></td>
                                    <td style="color: #64748b; font-size: 0.8rem;"><?= date('M d, Y', strtotime($rec['registered_at'])) ?></td>
                                    <td style="text-align: center;">
                                        <?php $keepId = $group['records'][0]['id']; ?>
                                        <?php if ($i === 0): ?>
                                            <span class="merge-btn btn-keep"><i class="fa-solid fa-shield-halved"></i> <?= __('Keep (Oldest)') ?></span>
                                        <?php else: ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to merge and delete Beneficiary #<?= $rec['id'] ?>? All related records will be reassigned to Beneficiary #<?= $keepId ?>.');">
                                                <input type="hidden" name="action" value="delete_beneficiary_duplicate">
                                                <input type="hidden" name="keep_id" value="<?= $keepId ?>">
                                                <input type="hidden" name="delete_id" value="<?= $rec['id'] ?>">
                                                <button type="submit" class="merge-btn btn-delete"><i class="fa-solid fa-trash-can"></i> <?= __('Merge & Delete') ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');
    btn.classList.add('active');
}
</script>

</body>
</html>
