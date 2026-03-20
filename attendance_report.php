<?php
require_once 'config.php';
$pageTitle = 'Attendance Report';
require_once 'header.php';

$subjectId = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;
$fromDate = isset($_GET['from_date']) ? clean_input($_GET['from_date']) : '';
$toDate = isset($_GET['to_date']) ? clean_input($_GET['to_date']) : '';
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

$subjects = $conn->query('SELECT subject_id, subject_name, faculty_name FROM subjects ORDER BY subject_name');

$query = 'SELECT a.attendance_id, a.date, a.status,
                 s.student_id, s.name, s.usn, s.department, s.semester,
                 sub.subject_name, sub.faculty_name
          FROM attendance a
          INNER JOIN students s ON a.student_id = s.student_id
          INNER JOIN subjects sub ON a.subject_id = sub.subject_id
          WHERE 1=1';

$params = [];
$types = '';

if ($subjectId > 0) {
    $query .= ' AND a.subject_id = ?';
    $params[] = $subjectId;
    $types .= 'i';
}

if ($fromDate !== '') {
    $query .= ' AND a.date >= ?';
    $params[] = $fromDate;
    $types .= 's';
}

if ($toDate !== '') {
    $query .= ' AND a.date <= ?';
    $params[] = $toDate;
    $types .= 's';
}

if ($search !== '') {
    $query .= ' AND (s.name LIKE ? OR s.usn LIKE ?)';
    $keyword = '%' . $search . '%';
    $params[] = $keyword;
    $params[] = $keyword;
    $types .= 'ss';
}

$query .= ' ORDER BY a.date DESC, s.name ASC';

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$summaryQuery = 'SELECT s.student_id, s.name, s.usn,
                        COUNT(a.attendance_id) AS total_classes,
                        SUM(CASE WHEN a.status = "Present" THEN 1 ELSE 0 END) AS presents,
                        SUM(CASE WHEN a.status = "Absent" THEN 1 ELSE 0 END) AS absents,
                        ROUND((SUM(CASE WHEN a.status = "Present" THEN 1 ELSE 0 END) / COUNT(a.attendance_id)) * 100, 2) AS attendance_percent
                 FROM attendance a
                 INNER JOIN students s ON a.student_id = s.student_id
                 WHERE 1=1';

$summaryParams = [];
$summaryTypes = '';

if ($subjectId > 0) {
    $summaryQuery .= ' AND a.subject_id = ?';
    $summaryParams[] = $subjectId;
    $summaryTypes .= 'i';
}

if ($fromDate !== '') {
    $summaryQuery .= ' AND a.date >= ?';
    $summaryParams[] = $fromDate;
    $summaryTypes .= 's';
}

if ($toDate !== '') {
    $summaryQuery .= ' AND a.date <= ?';
    $summaryParams[] = $toDate;
    $summaryTypes .= 's';
}

if ($search !== '') {
    $summaryQuery .= ' AND (s.name LIKE ? OR s.usn LIKE ?)';
    $keyword = '%' . $search . '%';
    $summaryParams[] = $keyword;
    $summaryParams[] = $keyword;
    $summaryTypes .= 'ss';
}

$summaryQuery .= ' GROUP BY s.student_id, s.name, s.usn ORDER BY s.name';

$summaryStmt = $conn->prepare($summaryQuery);
if (!empty($summaryParams)) {
    $summaryStmt->bind_param($summaryTypes, ...$summaryParams);
}
$summaryStmt->execute();
$summaryResult = $summaryStmt->get_result();
?>

<section class="page-heading">
    <p class="section-kicker">Reports</p>
    <h2>Attendance reports</h2>
    <p class="section-copy">Filter attendance history by subject, date range, or student to review performance trends.</p>
</section>

<form method="GET">
    <div class="row">
        <div>
            <label for="subject_id">Subject</label>
            <select id="subject_id" name="subject_id">
                <option value="">All Subjects</option>
                <?php if ($subjects && $subjects->num_rows > 0): ?>
                    <?php while ($subject = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo $subject['subject_id']; ?>" <?php echo ($subjectId === (int) $subject['subject_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subject['subject_name'] . ' - ' . $subject['faculty_name']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>
        <div>
            <label for="from_date">From Date</label>
            <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>">
        </div>
        <div>
            <label for="to_date">To Date</label>
            <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>">
        </div>
        <div>
            <label for="search">Search Student (Name/USN)</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter name or USN">
        </div>
    </div>
    <button type="submit">Generate Report</button>
</form>

<h3>Summary Report</h3>
<div class="table-shell">
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>USN</th>
                <th>Total Classes</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Attendance %</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($summaryResult && $summaryResult->num_rows > 0): ?>
                <?php while ($row = $summaryResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['usn']); ?></td>
                        <td><?php echo $row['total_classes']; ?></td>
                        <td><?php echo $row['presents']; ?></td>
                        <td><?php echo $row['absents']; ?></td>
                        <td><?php echo $row['attendance_percent']; ?>%</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No attendance data found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<h3>Detailed Records</h3>
<div class="table-shell">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Student</th>
                <th>USN</th>
                <th>Subject</th>
                <th>Faculty</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['usn']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['faculty_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No attendance records found for selected filters.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$stmt->close();
$summaryStmt->close();
require_once 'footer.php';
?>
