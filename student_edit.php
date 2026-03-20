<?php
require_once 'config.php';
$pageTitle = 'Update Student';
require_once 'header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$message = '';
$messageType = '';

if ($id <= 0) {
    echo '<div class="message error">Invalid student ID.</div>';
    require_once 'footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $usn = clean_input($_POST['usn'] ?? '');
    $department = clean_input($_POST['department'] ?? '');
    $semester = (int) ($_POST['semester'] ?? 0);

    if ($name === '' || $usn === '' || $department === '' || $semester < 1 || $semester > 8) {
        $message = 'Please fill all fields correctly.';
        $messageType = 'error';
    } else {
        $stmt = $conn->prepare('UPDATE students SET name = ?, usn = ?, department = ?, semester = ? WHERE student_id = ?');
        $stmt->bind_param('sssii', $name, $usn, $department, $semester, $id);

        if ($stmt->execute()) {
            $message = 'Student details updated successfully.';
            $messageType = 'success';
        } else {
            $message = 'Update failed. USN may already exist.';
            $messageType = 'error';
        }

        $stmt->close();
    }
}

$stmt = $conn->prepare('SELECT student_id, name, usn, department, semester FROM students WHERE student_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    echo '<div class="message error">Student not found.</div>';
    require_once 'footer.php';
    exit;
}
?>

<section class="page-heading">
    <p class="section-kicker">Students</p>
    <h2>Update student</h2>
    <p class="section-copy">Edit existing student details while keeping the linked attendance history intact.</p>
</section>

<?php if ($message !== ''): ?>
    <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="row">
        <div>
            <label for="name">Student Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>
        <div>
            <label for="usn">USN</label>
            <input type="text" id="usn" name="usn" value="<?php echo htmlspecialchars($student['usn']); ?>" required>
        </div>
    </div>

    <div class="row">
        <div>
            <label for="department">Department</label>
            <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($student['department']); ?>" required>
        </div>
        <div>
            <label for="semester">Semester (1-8)</label>
            <input type="number" id="semester" name="semester" min="1" max="8" value="<?php echo $student['semester']; ?>" required>
        </div>
    </div>

    <button type="submit">Update Student</button>
</form>

<?php require_once 'footer.php'; ?>
