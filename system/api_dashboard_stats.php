<?php
require_once __DIR__ . '/auth/auth_check.php';
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json');

$period = $_GET['period'] ?? 'all';
$donorEmail = $_GET['email'] ?? null;

$whereDateCreated = "";
$whereDateDate = "";
$periodLabel = "All Time";
$dateFrom = "";
$dateTo = date('Y-m-d');

switch ($period) {
    case 'today':
        $whereDateCreated = "DATE(created_at) = CURDATE()";
        $whereDateDate = "date = CURDATE()";
        $periodLabel = "Today";
        $dateFrom = date('Y-m-d');
        break;
    case 'week':
        $whereDateCreated = "created_at >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
        $whereDateDate = "date >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
        $periodLabel = "This Week";
        $dateFrom = date('Y-m-d', strtotime('monday this week'));
        break;
    case 'month':
        $whereDateCreated = "MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $whereDateDate = "MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
        $periodLabel = "This Month";
        $dateFrom = date('Y-m-01');
        break;
    case 'year':
        $whereDateCreated = "YEAR(created_at) = YEAR(CURDATE())";
        $whereDateDate = "YEAR(date) = YEAR(CURDATE())";
        $periodLabel = "This Year";
        $dateFrom = date('Y-01-01');
        break;
    case '3year':
        $whereDateCreated = "created_at >= DATE_SUB(CURDATE(), INTERVAL 3 YEAR)";
        $whereDateDate = "date >= DATE_SUB(CURDATE(), INTERVAL 3 YEAR)";
        $periodLabel = "Last 3 Years";
        $dateFrom = date('Y-m-d', strtotime('-3 years'));
        break;
    case '5year':
        $whereDateCreated = "created_at >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)";
        $whereDateDate = "date >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)";
        $periodLabel = "Last 5 Years";
        $dateFrom = date('Y-m-d', strtotime('-5 years'));
        break;
    default:
        $whereDateCreated = "1=1";
        $whereDateDate = "1=1";
        $periodLabel = "All Time";
        $dateFrom = "Beginning";
        break;
}

// Stats initialization
$donations_total = 0;
$donations_count = 0;
$expenses_total = 0;
$income_total = 0;
$active_programs = 0;
$budget_total = 0;

try {
    // Public Donations
    $pdQuery = "SELECT SUM(amount) as total, COUNT(id) as cnt FROM public_donations WHERE status='completed' AND $whereDateCreated";
    if ($donorEmail) {
        $pdQuery .= " AND email = :email";
        $stmt = $pdo->prepare($pdQuery);
        $stmt->execute(['email' => $donorEmail]);
    } else {
        $stmt = $pdo->query($pdQuery);
    }
    $pdRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $donations_total = $pdRow['total'] ?? 0;
    $donations_count = $pdRow['cnt'] ?? 0;

    // Finance Records (Expenses & Income)
    if (!$donorEmail) {
        $finExpQuery = "SELECT SUM(amount) FROM finance_records WHERE type='expense' AND $whereDateDate";
        $expenses_total = $pdo->query($finExpQuery)->fetchColumn() ?? 0;

        $finIncQuery = "SELECT SUM(amount) FROM finance_records WHERE type='income' AND $whereDateDate";
        $income_total = $pdo->query($finIncQuery)->fetchColumn() ?? 0;
    }

    // Projects
    $projQuery = "SELECT COUNT(*) FROM projects WHERE status='active' AND $whereDateCreated";
    $active_programs = $pdo->query($projQuery)->fetchColumn() ?? 0;
    
    $budgetQuery = "SELECT SUM(budget) FROM projects WHERE status='active' AND $whereDateCreated";
    $budget_total = $pdo->query($budgetQuery)->fetchColumn() ?? 0;

    echo json_encode([
        'success' => true,
        'donations_total' => (float)$donations_total,
        'donations_count' => (int)$donations_count,
        'expenses_total' => (float)$expenses_total,
        'income_total' => (float)$income_total,
        'net_balance' => (float)($income_total - $expenses_total),
        'active_programs' => (int)$active_programs,
        'budget_total' => (float)$budget_total,
        'period_label' => $periodLabel,
        'date_from' => $dateFrom,
        'date_to' => ($period == 'all' ? 'Now' : $dateTo)
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
