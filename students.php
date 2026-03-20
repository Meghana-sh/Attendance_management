<?php
require_once 'config.php';
$pageTitle = 'Students';
require_once 'header.php';

$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

$sql = 'SELECT student_id, name, usn, department, semester FROM students';
$params = [];
$types = '';

if ($search !== '') {
    $sql .= ' WHERE name LIKE ? OR usn LIKE ? OR department LIKE ?';
    $keyword = '%' . $search . '%';
    $params = [$keyword, $keyword, $keyword];
    $types = 'sss';
}

$sql .= ' ORDER BY student_id DESC';
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="page-heading">
    <p class="section-kicker">Students</p>
    <h2>Student directory</h2>
    <p class="section-copy">Search, review, and manage student records from one place.</p>
</section>

<form method="GET">
    <div class="row">
        <div>
            <label for="search">Search by Name / USN / Department</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter search text">
        </div>
        <div>
            <label>&nbsp;</label>
            <button type="submit">Search</button>
        </div>
    </div>
</form>

<div class="table-shell">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>USN</th>
                <th>Department</th>
                <th>Semester</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['student_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['usn']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td><?php echo $row['semester']; ?></td>
                        <td class="action-links">
                            <a href="student_edit.php?id=<?php echo $row['student_id']; ?>">Edit</a>
                            <a href="student_delete.php?id=<?php echo $row['student_id']; ?>" onclick="return confirm('Delete this student?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No students found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$stmt->close();
require_once 'footer.php';
?>
