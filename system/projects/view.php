<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'project_manager', 'staff']); // Staff can view
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch project with creator name
$stmt = $pdo->prepare("SELECT p.*, u.full_name as created_by_name FROM projects p LEFT JOIN users u ON p.created_by = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    die("Project not found.");
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details: <?php echo htmlspecialchars($project['title']); ?></title>
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header">
            <h2><?php echo htmlspecialchars($project['title']); ?></h2>
            <a href="index.php" class="btn-secondary">Back to List</a>
        </div>

        <div class="content-box">
            <div class="project-details">
                <p><strong>Status:</strong> <span class="badge"><?php echo ucfirst($project['status']); ?></span></p>
                <p><strong>Description:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                
                <hr>
                
                <p><strong>Start Date:</strong> <?php echo $project['start_date']; ?></p>
                <p><strong>End Date:</strong> <?php echo $project['end_date'] ?: 'Ongoing'; ?></p>
                <p><strong>Budget:</strong> $<?php echo number_format($project['budget'], 2); ?></p>
                <p><strong>Created By:</strong> <?php echo htmlspecialchars($project['created_by_name'] ?? 'Unknown'); ?></p>
            </div>
            
            <?php if(in_array($_SESSION['role'], ['super_admin', 'project_manager'])): ?>
                <div class="actions" style="margin-top: 2rem;">
                    <a href="edit.php?id=<?php echo $project['id']; ?>" class="btn-primary">Edit Project</a>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
