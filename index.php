<?php
require_once 'config.php';
$pageTitle = 'Dashboard';
require_once 'header.php';

$studentCount = 0;
$subjectCount = 0;
$attendanceCount = 0;

$result = $conn->query('SELECT COUNT(*) AS total FROM students');
if ($result) {
    $studentCount = (int) $result->fetch_assoc()['total'];
}

$result = $conn->query('SELECT COUNT(*) AS total FROM subjects');
if ($result) {
    $subjectCount = (int) $result->fetch_assoc()['total'];
}

$result = $conn->query('SELECT COUNT(*) AS total FROM attendance');
if ($result) {
    $attendanceCount = (int) $result->fetch_assoc()['total'];
}
?>

<section class="hero-panel">
    <div>
        <p class="section-kicker">Dashboard</p>
        <h2>Monitor attendance activity at a glance.</h2>
        <p class="section-copy">Use the navigation to maintain student records, capture daily attendance, and review reports without switching tools.</p>
    </div>
    <div class="hero-note">
        <span class="accent-badge">Connected Modules</span>
        <p>Students, subjects, and attendance logs are linked through a normalized relational database.</p>
    </div>
</section>

<div class="grid">
    <div class="card">
        <p class="card-label">Total Students</p>
        <p class="metric-value"><?php echo $studentCount; ?></p>
        <p class="card-meta">Registered learners available for attendance tracking.</p>
    </div>
    <div class="card">
        <p class="card-label">Total Subjects</p>
        <p class="metric-value"><?php echo $subjectCount; ?></p>
        <p class="card-meta">Subjects configured with faculty details.</p>
    </div>
    <div class="card">
        <p class="card-label">Attendance Entries</p>
        <p class="metric-value"><?php echo $attendanceCount; ?></p>
        <p class="card-meta">Saved daily records across students and subjects.</p>
    </div>
</div>

<section class="quick-actions">
    <a href="student_add.php">Add a new student</a>
    <a href="attendance_mark.php">Record today&apos;s attendance</a>
    <a href="attendance_report.php">Open attendance reports</a>
</section>

<?php require_once 'footer.php'; ?>
