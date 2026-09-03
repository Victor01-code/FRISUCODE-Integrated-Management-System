<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'finance', 'project_manager']);
require_once __DIR__ . '/../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $desc = trim($_POST['description']);
    $date = $_POST['date'];

    if (empty($amount) || empty($desc)) {
        $error = "Amount and Description required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO finance_records (type, amount, description, date, recorded_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$type, $amount, $desc, $date, $_SESSION['user_id']]);
            header("Location: index.php?msg=recorded");
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Record Transaction</title>
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
            <h2><?= __('Record Treasury Entry') ?></h2>
            <a href="index.php" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> <?= __('Financial Ledger') ?></a>
        </div>

        <div class="form-container fade-in">
             <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                
                <div class="input-group">
                    <label><i class="fa-solid fa-layer-group"></i> <?= __('Transaction Category *') ?></label>
                    <div class="radio-cards-grid">
                        <label class="radio-card active" data-type="income">
                            <input type="radio" name="type" value="income" checked style="display:none;">
                            <div class="icon-box"><i class="fa-solid fa-hand-holding-dollar" style="color: #16a34a;"></i></div>
                            <div>
                                <strong style="display: block; color: #166534;"><?= __('General Income') ?></strong>
                                <small style="color: #64748b;"><?= __('Donations & Funding') ?></small>
                            </div>
                        </label>
                        <label class="radio-card" data-type="expense">
                            <input type="radio" name="type" value="expense" style="display:none;">
                            <div class="icon-box"><i class="fa-solid fa-money-bill-transfer" style="color: #dc2626;"></i></div>
                            <div>
                                <strong style="display: block; color: #991b1b;"><?= __('General Expense') ?></strong>
                                <small style="color: #64748b;"><?= __('Operational Costs') ?></small>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-coins"></i> <?= __('Amount (USD) *') ?></label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-weight: 800; color: #64748b; font-family: 'Outfit';">$</span>
                            <input type="number" step="0.01" name="amount" required placeholder="0.00" style="padding-left: 35px; font-family: 'Outfit'; font-weight: 800; font-size: 1.25rem;">
                        </div>
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-calendar-check"></i> <?= __('Transaction Date *') ?></label>
                        <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-comment-dots"></i> <?= __('Description / Particulars *') ?></label>
                    <textarea name="description" rows="3" required placeholder="e.g. Monthly school fees for 15 primary students..."></textarea>
                </div>

                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <button type="submit" class="btn-primary-block">
                        <i class="fa-solid fa-file-invoice-dollar"></i> <?= __('Commit to Ledger') ?>
                    </button>
                    <p style="text-align: center; font-size: 0.85rem; color: #94a3b8; margin-top: 20px;">
                        <i class="fa-solid fa-clock-rotate-left"></i> <?= __('Entries are logged permanently and factored into real-time totals.') ?>
                    </p>
                </div>
            </form>
        </div>

        <script>
            document.querySelectorAll('.radio-card').forEach(card => {
                card.addEventListener('click', function() {
                    const grid = this.closest('.radio-cards-grid');
                    grid.querySelectorAll('.radio-card').forEach(c => {
                        c.classList.remove('active');
                        c.style.background = '#fff';
                        c.style.borderColor = '#f1f5f9';
                    });
                    
                    this.classList.add('active');
                    this.querySelector('input').checked = true;
                    
                    // Dynamic styling based on type
                    if(this.dataset.type === 'income') {
                        this.style.background = '#f0fdf4';
                        this.style.borderColor = '#16a34a';
                    } else {
                        this.style.background = '#fef2f2';
                        this.style.borderColor = '#dc2626';
                    }
                });
            });
        </script>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
