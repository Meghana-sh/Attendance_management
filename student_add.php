<?php
require_once 'config.php';
$pageTitle = 'Add Student';
require_once 'header.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $usn = clean_input($_POST['usn'] ?? '');
    $department = clean_input($_POST['department'] ?? '');
    $semester = (int) ($_POST['semester'] ?? 0);

    if ($name === '' || $usn === '' || $department === '' || $semester < 1 || $semester > 8) {
        $message = 'Please fill all fields correctly.';
        $messageType = 'error';
    } else {
        $stmt = $conn->prepare('INSERT INTO students (name, usn, department, semester) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('sssi', $name, $usn, $department, $semester);

        if ($stmt->execute()) {
            $message = 'Student added successfully.';
            $messageType = 'success';
        } else {
            $message = 'Failed to add student. USN may already exist.';
            $messageType = 'error';
        }

        $stmt->close();
    }
}
?>

<section class="page-heading">
    <p class="section-kicker">Students</p>
    <h2>Add student</h2>
    <p class="section-copy">Create a new student profile with department and semester details.</p>
</section>

<?php if ($message !== ''): ?>
    <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="row">
        <div>
            <label for="name">Student Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="usn">USN</label>
            <input type="text" id="usn" name="usn" required>
        </div>
    </div>

    <div class="row">
        <div>
            <label for="department">Department</label>
            <input type="text" id="department" name="department" required>
        </div>
        <div>
            <label for="semester">Semester (1-8)</label>
            <input type="number" id="semester" name="semester" min="1" max="8" required>
        </div>
    </div>

    <button type="submit">Add Student</button>
</form>

<?php require_once 'footer.php'; ?>
