<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'project_manager']);
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch project
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    die("Project not found.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $budget = $_POST['budget'];
    $status = $_POST['status'];

    if (empty($title)) {
        $error = "Title is required.";
    } else {
        try {
            $updateStmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, start_date = ?, end_date = ?, budget = ?, status = ? WHERE id = ?");
            $updateStmt->execute([$title, $description, $start_date, $end_date, $budget, $status, $id]);
            header("Location: index.php?msg=updated");
            exit;
        } catch (PDOException $e) {
            $error = "Update Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project: <?php echo htmlspecialchars($project['title']); ?></title>
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
            <h2 style="font-family: 'Outfit'; font-weight: 800;">Edit Project</h2>
            <a href="index.php" class="btn-light" style="border-radius: 12px; font-weight: 700;">
                <i class="fa-solid fa-arrow-left"></i> Cancel Changes
            </a>
        </div>

        <div class="form-container fade-in" style="max-width: 900px; margin: 30px 40px; animation-delay: 0.1s;">
            <?php if ($error): ?>
                <div class="alert alert-danger" style="margin-bottom: 20px; border-radius: 12px; font-weight: 600;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-row">
                    <div class="input-group" style="flex: 2;">
                        <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-font" style="color: #3b82f6;"></i> Project Title *
                        </label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required placeholder="e.g. Arusha Secondary School Renovation" style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px; font-weight: 600;">
                    </div>
                </div>

                <div class="input-group">
                    <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-align-left" style="color: #3b82f6;"></i> Scope & Objectives
                    </label>
                    <textarea name="description" rows="4" placeholder="Outline the main objectives and scope of this project..." style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px; font-weight: 500; font-family: inherit;"><?php echo htmlspecialchars($project['description']); ?></textarea>
                </div>

                <div class="form-row" style="margin-top: 20px;">
                    <div class="input-group">
                        <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-calendar-day" style="color: #16a34a;"></i> Launch Date
                        </label>
                        <input type="date" name="start_date" value="<?php echo $project['start_date']; ?>" style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px; font-weight: 600;">
                    </div>
                    <div class="input-group">
                        <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-calendar-check" style="color: #dc2626;"></i> Projected End
                        </label>
                        <input type="date" name="end_date" value="<?php echo $project['end_date']; ?>" style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px; font-weight: 600;">
                    </div>
                </div>

                <div class="form-row" style="margin-top: 20px;">
                    <div class="input-group" style="flex: 1;">
                        <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-money-bill-transfer" style="color: #16a34a;"></i> Allocated Investment (USD)
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 16px; top: 12px; font-weight: 800; color: #64748b;">$</span>
                            <input type="number" name="budget" step="0.01" value="<?php echo $project['budget']; ?>" placeholder="0.00" style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 12px 16px 12px 32px; font-weight: 800; font-family: 'Outfit'; color: #16a34a;">
                        </div>
                    </div>
                </div>

                <div class="input-group" style="margin-top: 30px;">
                    <label style="font-family: 'Outfit'; font-weight: 700; color: #1e293b; margin-bottom: 15px; display: block;">Strategic Project Phase</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <label id="label-planning" style="border: 2px solid <?php echo $project['status'] == 'planning' ? '#2563eb' : '#e2e8f0'; ?>; padding: 20px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 15px; transition: 0.3s; background: <?php echo $project['status'] == 'planning' ? '#eff6ff' : '#fff'; ?>;" onclick="selectStatus(this, 'planning')">
                            <input type="radio" name="status" value="planning" <?php echo $project['status'] == 'planning' ? 'checked' : ''; ?> style="display: none;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 1.2rem;">
                                <i class="fa-solid fa-compass-drafting"></i>
                            </div>
                            <div>
                                <strong style="display: block; color: <?php echo $project['status'] == 'planning' ? '#1e40af' : '#1e293b'; ?>; font-size: 1.05rem;">Planning</strong>
                                <small style="color: #64748b; font-weight: 500;">Drafting & Resource allocation</small>
                            </div>
                        </label>
                        <label id="label-active" style="border: 2px solid <?php echo $project['status'] == 'active' ? '#16a34a' : '#e2e8f0'; ?>; padding: 20px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 15px; transition: 0.3s; background: <?php echo $project['status'] == 'active' ? '#f0fdf4' : '#fff'; ?>;" onclick="selectStatus(this, 'active')">
                            <input type="radio" name="status" value="active" <?php echo $project['status'] == 'active' ? 'checked' : ''; ?> style="display: none;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #16a34a; font-size: 1.2rem;">
                                <i class="fa-solid fa-play"></i>
                            </div>
                            <div>
                                <strong style="display: block; color: <?php echo $project['status'] == 'active' ? '#14532d' : '#1e293b'; ?>; font-size: 1.05rem;">Active</strong>
                                <small style="color: #64748b; font-weight: 500;">Currently in execution phase</small>
                            </div>
                        </label>
                        <label id="label-completed" style="border: 2px solid <?php echo $project['status'] == 'completed' ? '#8b5cf6' : '#e2e8f0'; ?>; padding: 20px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 15px; transition: 0.3s; background: <?php echo $project['status'] == 'completed' ? '#f5f3ff' : '#fff'; ?>;" onclick="selectStatus(this, 'completed')">
                            <input type="radio" name="status" value="completed" <?php echo $project['status'] == 'completed' ? 'checked' : ''; ?> style="display: none;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #8b5cf6; font-size: 1.2rem;">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div>
                                <strong style="display: block; color: <?php echo $project['status'] == 'completed' ? '#4c1d95' : '#1e293b'; ?>; font-size: 1.05rem;">Completed</strong>
                                <small style="color: #64748b; font-weight: 500;">Successully finalized</small>
                            </div>
                        </label>
                        <label id="label-cancelled" style="border: 2px solid <?php echo $project['status'] == 'cancelled' ? '#94a3b8' : '#e2e8f0'; ?>; padding: 20px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 15px; transition: 0.3s; background: <?php echo $project['status'] == 'cancelled' ? '#f8fafc' : '#fff'; ?>;" onclick="selectStatus(this, 'cancelled')">
                            <input type="radio" name="status" value="cancelled" <?php echo $project['status'] == 'cancelled' ? 'checked' : ''; ?> style="display: none;">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.2rem;">
                                <i class="fa-solid fa-ban"></i>
                            </div>
                            <div>
                                <strong style="display: block; color: <?php echo $project['status'] == 'cancelled' ? '#334155' : '#1e293b'; ?>; font-size: 1.05rem;">Cancelled</strong>
                                <small style="color: #64748b; font-weight: 500;">Halted or discontinued</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #f8fafc; display: flex; justify-content: space-between; align-items: center;">
                    <p style="color: #64748b; font-size: 0.85rem; font-weight: 500; margin: 0;">
                        <i class="fa-solid fa-clock-rotate-left"></i> Changes are recorded in audit logs.
                    </p>
                    <button type="submit" class="btn-primary" style="padding: 14px 32px; border-radius: 14px; font-weight: 800; font-family: 'Outfit'; font-size: 1rem; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.25);">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Update Project Record
                    </button>
                </div>
            </form>
        </div>

        <script>
        function selectStatus(card, status) {
            // Reset all
            const statuses = ['planning', 'active', 'completed', 'cancelled'];
            statuses.forEach(s => {
                const c = document.getElementById('label-' + s);
                c.style.borderColor = '#e2e8f0';
                c.style.background = '#fff';
                c.querySelector('strong').style.color = '#1e293b';
            });
            
            // Set active
            const colors = {
                'planning': ['#2563eb', '#eff6ff', '#1e40af'],
                'active': ['#16a34a', '#f0fdf4', '#14532d'],
                'completed': ['#8b5cf6', '#f5f3ff', '#4c1d95'],
                'cancelled': ['#94a3b8', '#f8fafc', '#334155']
            };
            
            card.querySelector('input').checked = true;
            card.style.borderColor = colors[status][0];
            card.style.background = colors[status][1];
            card.querySelector('strong').style.color = colors[status][2];
        }
        </script>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
