<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'staff', 'project_manager', 'me_officer', 'field_officer']);
require_once __DIR__ . '/../config/db.php';

$beneficiaryId = $_GET['id'] ?? null;
if (!$beneficiaryId) {
    header("Location: index.php");
    exit;
}

// Fetch beneficiary name
$bStmt = $pdo->prepare("SELECT id, full_name FROM beneficiaries WHERE id = ?");
$bStmt->execute([$beneficiaryId]);
$beneficiary = $bStmt->fetch(PDO::FETCH_ASSOC);
if (!$beneficiary) {
    die("Student not found.");
}

$error = '';
$success = '';

// Handle DELETE
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    // Get the file URL before deleting so we can remove the file
    $fileStmt = $pdo->prepare("SELECT file_url FROM student_reports WHERE id = ? AND beneficiary_id = ?");
    $fileStmt->execute([$delId, $beneficiaryId]);
    $fileRow = $fileStmt->fetch(PDO::FETCH_ASSOC);
    if ($fileRow && !empty($fileRow['file_url'])) {
        $physicalPath = __DIR__ . '/../../' . ltrim($fileRow['file_url'], '/frisucode_ms/');
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }
    }
    $pdo->prepare("DELETE FROM student_reports WHERE id = ? AND beneficiary_id = ?")->execute([$delId, $beneficiaryId]);
    header("Location: reports.php?id=$beneficiaryId&msg=deleted");
    exit;
}

// Handle POST (add report)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $reportText = trim($_POST['report_text'] ?? '');
    $reportDate = $_POST['report_date'] ?? date('Y-m-d');
    $fileUrl = null;
    $fileName = null;

    if (empty($title)) {
        $error = __('Report title is required.');
    } else {
        // Handle PDF upload
        if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['report_file']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $error = __('Only PDF files are allowed.');
            } else {
                $uploadDir = __DIR__ . '/../../assets/uploads/student_reports/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', basename($_FILES['report_file']['name']));
                $targetPath = $uploadDir . $safeName;

                if (move_uploaded_file($_FILES['report_file']['tmp_name'], $targetPath)) {
                    $fileUrl = '/frisucode_ms/assets/uploads/student_reports/' . $safeName;
                    $fileName = basename($_FILES['report_file']['name']);
                } else {
                    $error = __('File upload failed. Please try again.');
                }
            }
        }

        if (empty($error)) {
            if (empty($reportText) && empty($fileUrl)) {
                $error = __('Please write a report or attach a PDF file (or both).');
            } else {
                try {
                    $ins = $pdo->prepare("INSERT INTO student_reports (beneficiary_id, title, report_text, file_url, file_name, report_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $ins->execute([$beneficiaryId, $title, $reportText ?: null, $fileUrl, $fileName, $reportDate, $_SESSION['user_id']]);
                    header("Location: reports.php?id=$beneficiaryId&msg=saved");
                    exit;
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                }
            }
        }
    }
}

// Fetch existing reports
$reportsStmt = $pdo->prepare("SELECT sr.*, u.full_name AS author_name FROM student_reports sr LEFT JOIN users u ON sr.created_by = u.id WHERE sr.beneficiary_id = ? ORDER BY sr.report_date DESC, sr.created_at DESC");
$reportsStmt->execute([$beneficiaryId]);
$reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRISUCODE | <?= __('Student Reports') ?> - <?= htmlspecialchars($beneficiary['full_name']) ?></title>
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
    <style>
        .report-form { max-width: 700px; }
        .report-form label { display: block; font-weight: 700; color: #1e293b; margin-bottom: 6px; font-size: 0.95rem; }
        .report-form input[type="text"],
        .report-form input[type="date"],
        .report-form textarea { width: 100%; padding: 12px 16px; border-radius: 12px; border: 2px solid #e2e8f0; font-size: 1rem; font-family: 'Inter', sans-serif; transition: 0.2s; background: #f8fafc; box-sizing: border-box; }
        .report-form input:focus, .report-form textarea:focus { border-color: #3b82f6; outline: none; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .report-form textarea { min-height: 160px; resize: vertical; line-height: 1.7; }
        .report-form .field-group { margin-bottom: 22px; }
        .file-upload-zone {
            border: 2px dashed #cbd5e1; border-radius: 16px; padding: 30px; text-align: center;
            background: #f8fafc; cursor: pointer; transition: 0.2s; position: relative;
        }
        .file-upload-zone:hover { border-color: #3b82f6; background: #eff6ff; }
        .file-upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .file-upload-zone .upload-icon { font-size: 2.5rem; color: #94a3b8; margin-bottom: 10px; }
        .file-upload-zone p { color: #64748b; font-weight: 600; margin: 0; }
        .file-upload-zone .file-hint { color: #94a3b8; font-size: 0.85rem; margin-top: 5px; }
        .report-card {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 20px;
            padding: 28px; margin-bottom: 20px; transition: 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
        }
        .report-card:hover { border-color: #cbd5e1; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.06); }
    </style>
</head>
<body style="background: #f8fafc;">

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header fade-in">
             <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                 <h2 style="font-family: 'Outfit'; font-weight: 800; color: #0f172a; margin: 0;">
                    <i class="fa-solid fa-file-lines" style="color: #8b5cf6; margin-right: 10px;"></i> <?= __('Reports for') ?>: <?= htmlspecialchars($beneficiary['full_name']) ?>
                 </h2>
                 <a href="view.php?id=<?= $beneficiaryId ?>" class="btn-secondary" style="border-radius: 12px; font-weight: 700; padding: 10px 20px; text-decoration: none;">
                    <i class="fa-solid fa-arrow-left"></i> <?= __('Back to Profile') ?>
                 </a>
             </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
        <div style="margin: 0 40px 20px; padding: 14px 20px; background: #d1fae5; color: #065f46; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check"></i> <?= __('Report saved successfully.') ?>
        </div>
        <?php endif; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div style="margin: 0 40px 20px; padding: 14px 20px; background: #fee2e2; color: #991b1b; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-trash"></i> <?= __('Report deleted.') ?>
        </div>
        <?php endif; ?>

        <div class="fade-in" style="padding: 0 40px; margin-bottom: 40px; animation-delay: 0.1s;">

            <!-- Add New Report Form -->
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 35px; margin-bottom: 40px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);">
                <h3 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; margin: 0 0 25px; font-size: 1.3rem;">
                    <i class="fa-solid fa-plus-circle" style="color: #8b5cf6;"></i> <?= __('Add New Report') ?>
                </h3>

                <?php if($error): ?>
                <div style="margin-bottom: 20px; padding: 14px 20px; background: #fee2e2; color: #991b1b; border-radius: 12px; font-weight: 600;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="report-form">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="field-group">
                            <label for="title"><i class="fa-solid fa-heading" style="color: #8b5cf6;"></i> <?= __('Report Title') ?> *</label>
                            <input type="text" id="title" name="title" placeholder="<?= __('e.g. Term 1 Academic Progress') ?>" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                        </div>
                        <div class="field-group">
                            <label for="report_date"><i class="fa-regular fa-calendar" style="color: #8b5cf6;"></i> <?= __('Report Date') ?></label>
                            <input type="date" id="report_date" name="report_date" value="<?= htmlspecialchars($_POST['report_date'] ?? date('Y-m-d')) ?>">
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="report_text"><i class="fa-solid fa-pen-nib" style="color: #8b5cf6;"></i> <?= __('Written Report') ?></label>
                        <textarea id="report_text" name="report_text" placeholder="<?= __('Write a detailed report about this student\'s progress, behaviour, achievements, or any concerns...') ?>"><?= htmlspecialchars($_POST['report_text'] ?? '') ?></textarea>
                    </div>

                    <div class="field-group">
                        <label><i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i> <?= __('Attach PDF Report') ?> <span style="color: #94a3b8; font-weight: 500;">(<?= __('optional') ?>)</span></label>
                        <div class="file-upload-zone" id="dropZone">
                            <input type="file" name="report_file" accept=".pdf" id="reportFileInput">
                            <div class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <p id="fileLabel"><?= __('Click or drag a PDF file here') ?></p>
                            <span class="file-hint"><?= __('Maximum file size: 10MB — PDF only') ?></span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; margin-top: 10px;">
                        <button type="submit" class="btn-primary" style="border-radius: 12px; padding: 12px 28px; font-weight: 800; font-size: 1rem; background: linear-gradient(135deg, #8b5cf6, #6d28d9); border: none; cursor: pointer;">
                            <i class="fa-solid fa-paper-plane"></i> <?= __('Submit Report') ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Reports -->
            <h3 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; margin-bottom: 20px; font-size: 1.3rem;">
                <i class="fa-solid fa-folder-open" style="color: #f59e0b;"></i> <?= __('Report History') ?> <span style="color: #94a3b8; font-weight: 600;">(<?= count($reports) ?>)</span>
            </h3>

            <?php if(!empty($reports)): ?>
                <?php foreach($reports as $r): ?>
                <div class="report-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div>
                            <h4 style="margin: 0 0 6px; font-family: 'Outfit'; font-weight: 800; font-size: 1.2rem; color: #0f172a;">
                                <?= htmlspecialchars($r['title']) ?>
                            </h4>
                            <div style="display: flex; gap: 15px; flex-wrap: wrap; font-size: 0.85rem; color: #64748b; font-weight: 600;">
                                <span><i class="fa-regular fa-calendar"></i> <?= date('M d, Y', strtotime($r['report_date'])) ?></span>
                                <span><i class="fa-solid fa-user-pen"></i> <?= htmlspecialchars($r['author_name'] ?: 'System') ?></span>
                                <span><i class="fa-regular fa-clock"></i> <?= date('g:i A', strtotime($r['created_at'])) ?></span>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <?php if(!empty($r['file_url'])): ?>
                            <a href="<?= htmlspecialchars($r['file_url']) ?>" target="_blank" style="background: #fef2f2; color: #dc2626; padding: 8px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 5px; transition: 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                <i class="fa-solid fa-file-pdf"></i> <?= __('PDF') ?>
                            </a>
                            <?php endif; ?>
                            <a href="reports.php?id=<?= $beneficiaryId ?>&delete=<?= $r['id'] ?>" onclick="return confirm('<?= __('Are you sure you want to delete this report?') ?>')" style="background: #f1f5f9; color: #64748b; padding: 8px 12px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 5px; transition: 0.2s;" onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </div>
                    </div>

                    <?php if(!empty($r['report_text'])): ?>
                    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <p style="margin: 0; color: #475569; line-height: 1.8; font-size: 0.98rem; white-space: pre-wrap;"><?= htmlspecialchars($r['report_text']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($r['file_name']) && empty($r['report_text'])): ?>
                    <div style="background: #fef2f2; padding: 15px 20px; border-radius: 12px; display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-file-pdf" style="color: #dc2626; font-size: 1.5rem;"></i>
                        <span style="color: #991b1b; font-weight: 600;"><?= htmlspecialchars($r['file_name']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="background: #fff; padding: 50px; text-align: center; border-radius: 20px; border: 1px solid #e2e8f0;">
                    <i class="fa-solid fa-file-circle-xmark" style="font-size: 3.5rem; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
                    <h4 style="font-family: 'Outfit'; font-weight: 800; color: #1e293b; margin: 0 0 8px;"><?= __('No Reports Yet') ?></h4>
                    <p style="color: #64748b; margin: 0;"><?= __('Use the form above to add the first report for this student.') ?></p>
                </div>
            <?php endif; ?>

        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

<script>
    const fileInput = document.getElementById('reportFileInput');
    const fileLabel = document.getElementById('fileLabel');
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileLabel.textContent = this.files[0].name;
            fileLabel.style.color = '#16a34a';
        } else {
            fileLabel.textContent = '<?= __("Click or drag a PDF file here") ?>';
            fileLabel.style.color = '';
        }
    });
</script>

</body>
</html>
