<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'admin', 'director', 'project_manager', 'finance']);
require_once __DIR__ . '/../config/db.php';

$type = $_GET['type'] ?? '';

if (!$type) {
    die("No report type specified.");
}

// Function to output CSV correctly
function downloadCSV($filename, $headers, $data) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

try {
    switch ($type) {
        case 'beneficiaries':
            $stmt = $pdo->query("
                SELECT b.student_id, b.full_name, b.dob, b.gender, b.education_level, 
                       b.class_level, b.school_name, b.location_name, b.status, 
                       (SELECT GROUP_CONCAT(u.full_name SEPARATOR ', ') FROM beneficiary_sponsors bs JOIN users u ON bs.sponsor_id = u.id WHERE bs.beneficiary_id = b.id) as sponsor_name, b.registered_at 
                FROM beneficiaries b 
                ORDER BY b.registered_at DESC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $headers = ['Student ID', 'Full Name', 'Date of Birth', 'Gender', 'Education Level', 'Class/Grade', 'School Name', 'Location', 'Status', 'Sponsor Name', 'Registered At'];
            downloadCSV('Beneficiary_Report_' . date('Y-m-d'), $headers, $data);
            break;

        case 'graduates':
            $stmt = $pdo->query("
                SELECT b.student_id, b.full_name, b.dob, b.gender, b.education_level, 
                       b.class_level, b.school_name, b.location_name, b.graduation_date, b.graduation_notes,
                       (SELECT GROUP_CONCAT(u.full_name SEPARATOR ', ') FROM beneficiary_sponsors bs JOIN users u ON bs.sponsor_id = u.id WHERE bs.beneficiary_id = b.id) as sponsor_name, b.registered_at 
                FROM beneficiaries b 
                WHERE b.status = 'graduated'
                ORDER BY b.graduation_date DESC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $headers = ['Student ID', 'Full Name', 'Date of Birth', 'Gender', 'Education Level', 'Class/Grade', 'School Name', 'Location', 'Graduation Date', 'Graduation Notes', 'Sponsor Name', 'Registered At'];
            downloadCSV('Graduate_Report_' . date('Y-m-d'), $headers, $data);
            break;

        case 'dropouts':
            $stmt = $pdo->query("
                SELECT b.student_id, b.full_name, b.dob, b.gender, b.education_level, 
                       b.class_level, b.school_name, b.location_name, b.dropout_date, b.dropout_reason,
                       (SELECT u.full_name FROM users u WHERE u.id = b.dropout_recorded_by) as recorded_by,
                       (SELECT GROUP_CONCAT(u.full_name SEPARATOR ', ') FROM beneficiary_sponsors bs JOIN users u ON bs.sponsor_id = u.id WHERE bs.beneficiary_id = b.id) as sponsor_name, b.registered_at 
                FROM beneficiaries b 
                WHERE b.status = 'dropped_out'
                ORDER BY b.dropout_date DESC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $headers = ['Student ID', 'Full Name', 'Date of Birth', 'Gender', 'Education Level', 'Class/Grade', 'School Name', 'Location', 'Dropout Date', 'Dropout Reason', 'Recorded By', 'Sponsor Name', 'Registered At'];
            downloadCSV('Dropout_Report_' . date('Y-m-d'), $headers, $data);
            break;

        case 'finance':
            $stmt = $pdo->query("
                SELECT id, type, amount, description, date, created_at 
                FROM finance_records 
                ORDER BY date DESC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $headers = ['Transaction ID', 'Type (Income/Expense)', 'Amount', 'Description', 'Transaction Date', 'Logged At'];
            downloadCSV('Financial_Audit_Report_' . date('Y-m-d'), $headers, $data);
            break;

        case 'donors':
            $stmt = $pdo->query("
                SELECT u.full_name, u.email, u.created_at as joined_date, 
                       (SELECT COUNT(*) FROM beneficiary_sponsors bs WHERE bs.sponsor_id = u.id) as sponsored_students
                FROM users u 
                WHERE u.role = 'donor' 
                ORDER BY sponsored_students DESC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $headers = ['Donor Name', 'Email Address', 'Date Joined', 'Sponsored Students Count'];
            downloadCSV('Donor_Retention_Report_' . date('Y-m-d'), $headers, $data);
            break;

        case 'projects':
            $stmt = $pdo->query("
                SELECT title, status, budget, start_date, end_date, created_at 
                FROM projects 
                ORDER BY created_at DESC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $headers = ['Project Title', 'Status', 'Budget', 'Start Date', 'End Date', 'Created At'];
            downloadCSV('Project_Impact_Report_' . date('Y-m-d'), $headers, $data);
            break;

        default:
            die("Invalid report type.");
    }
} catch (PDOException $e) {
    die("Error generating report: " . $e->getMessage());
}
