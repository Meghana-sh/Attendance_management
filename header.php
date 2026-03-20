<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Attendance Management System';
}

$currentPage = basename($_SERVER['PHP_SELF']);
$navigationItems = [
    'index.php' => 'Home',
    'students.php' => 'Students',
    'student_add.php' => 'Add Student',
    'attendance_mark.php' => 'Mark Attendance',
    'attendance_report.php' => 'Attendance Report',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-backdrop" aria-hidden="true"></div>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand-block">
                <p class="eyebrow">DBMS Mini Project</p>
                <h1>Attendance Management System</h1>
                <p class="subtitle">Manage students, daily attendance, and reporting from one place.</p>
            </div>
            <nav class="main-nav" aria-label="Primary">
                <?php foreach ($navigationItems as $file => $label): ?>
                    <a href="<?php echo $file; ?>" class="<?php echo ($currentPage === $file) ? 'active' : ''; ?>"><?php echo $label; ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="topbar-accent">
                <span class="accent-badge">Live Records</span>
                <p>Track attendance by subject, date, and student in a single workflow.</p>
            </div>
        </div>
    </header>
    <main class="container">
