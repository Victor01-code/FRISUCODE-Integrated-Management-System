<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'project_manager']); 
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/db.php';

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $budget = $_POST['budget'];
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];

    if (empty($title) || empty($start_date)) {
        $error = "Project Title and Start Date are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, start_date, end_date, budget, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $start_date, $end_date, $budget, $status, $created_by]);
            header("Location: index.php?msg=created");
            exit;
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Start New Project</title>
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

        <div class="page-header" style="margin-bottom: 0;">
            <h2><?= __('Initiate New Project') ?></h2>
            <a href="index.php" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> <?= __('Cancel & Return') ?></a>
        </div>

        <div class="form-container fade-in">
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="create.php" method="POST" id="projectForm">
                <div class="input-group">
                    <label><i class="fa-solid fa-font"></i> <?= __('Project Title *') ?></label>
                    <input type="text" name="title" required placeholder="e.g. Arusha Secondary School Renovation">
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-align-left"></i> <?= __('Detailed Description') ?></label>
                    <textarea name="description" rows="4" placeholder="Outline the main objectives and scope..."></textarea>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-calendar-day"></i> <?= __('Expected Start Date *') ?></label>
                        <input type="date" name="start_date" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-calendar-check"></i> <?= __('Target Completion') ?></label>
                        <input type="date" name="end_date">
                    </div>
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-money-bill-transfer"></i> <?= __('Allocated Budget (USD)') ?></label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #64748b; font-weight: 800; font-family: 'Outfit';">$</span>
                        <input type="number" name="budget" step="0.01" value="0.00" style="padding-left: 35px; font-family: 'Outfit'; font-weight: 700; font-size: 1.25rem;">
                    </div>
                </div>

                <div class="input-group" style="margin-top: 30px;">
                    <label><i class="fa-solid fa-list-check"></i> <?= __('Initial Project Status') ?></label>
                    <div class="radio-cards-grid">
                        <label class="radio-card active">
                            <input type="radio" name="status" value="planning" checked style="display:none;">
                            <div class="icon-box"><i class="fa-solid fa-compass" style="color: #3b82f6;"></i></div>
                            <div>
                                <strong style="display:block;"><?= __('Planning Phase') ?></strong>
                                <small><?= __('Resource allocation & strategy') ?></small>
                            </div>
                        </label>

                        <label class="radio-card">
                            <input type="radio" name="status" value="active" style="display:none;">
                            <div class="icon-box"><i class="fa-solid fa-person-digging" style="color: #16a34a;"></i></div>
                            <div>
                                <strong style="display:block;"><?= __('Active Execution') ?></strong>
                                <small><?= __('On-ground implementation') ?></small>
                            </div>
                        </label>

                        <label class="radio-card">
                            <input type="radio" name="status" value="completed" style="display:none;">
                            <div class="icon-box"><i class="fa-solid fa-circle-check" style="color: #6366f1;"></i></div>
                            <div>
                                <strong style="display:block;"><?= __('Completed') ?></strong>
                                <small><?= __('Goal achieved & audit done') ?></small>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <button type="submit" class="btn-primary-block">
                        <i class="fa-solid fa-rocket"></i> <?= __('Launch Project Mission') ?>
                    </button>
                    <p style="text-align: center; font-size: 0.85rem; color: #94a3b8; margin-top: 20px;">
                        <i class="fa-solid fa-database"></i> <?= __('Project parameters will be synchronized across all management reports.') ?>
                    </p>
                </div>
            </form>
        </div>

        <script>
            document.querySelectorAll('.radio-card').forEach(card => {
                card.addEventListener('click', function() {
                    document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    this.querySelector('input').checked = true;
                });
            });
        </script>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
