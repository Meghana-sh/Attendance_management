<?php
require_once 'config.php';
$pageTitle = 'Mark Attendance';
require_once 'header.php';

$message = '';
$messageType = '';
$attendanceDate = isset($_POST['attendance_date']) ? clean_input($_POST['attendance_date']) : date('Y-m-d');
$selectedSubjectId = isset($_POST['subject_id']) ? (int) $_POST['subject_id'] : 0;

$subjects = $conn->query('SELECT subject_id, subject_name, faculty_name FROM subjects ORDER BY subject_name');
$students = $conn->query('SELECT student_id, name, usn, department, semester FROM students ORDER BY name');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    if ($selectedSubjectId <= 0 || $attendanceDate === '') {
        $message = 'Please select a subject and date.';
        $messageType = 'error';
    } elseif (!isset($_POST['status']) || !is_array($_POST['status'])) {
        $message = 'Please mark attendance status for students.';
        $messageType = 'error';
    } else {
        $statusList = $_POST['status'];

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare(
                'INSERT INTO attendance (student_id, subject_id, date, status) VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE status = VALUES(status)'
            );

            if (!$stmt) {
                throw new Exception($conn->error);
            }

            foreach ($statusList as $studentId => $status) {
                $studentId = (int) $studentId;
                $status = ($status === 'Present') ? 'Present' : 'Absent';
                $stmt->bind_param('iiss', $studentId, $selectedSubjectId, $attendanceDate, $status);
                $stmt->execute();
            }

            $stmt->close();
            $conn->commit();

            $message = 'Attendance saved successfully.';
            $messageType = 'success';
        } catch (Exception $e) {
            $conn->rollback();
            $message = 'Failed to save attendance: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>

<section class="page-heading">
    <p class="section-kicker">Attendance</p>
    <h2>Mark daily attendance</h2>
    <p class="section-copy">Select a subject, choose a date, and save each student as present or absent.</p>
</section>

<?php if ($message !== ''): ?>
    <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="row">
        <div>
            <label for="subject_id">Subject</label>
            <select id="subject_id" name="subject_id" required>
                <option value="">Select Subject</option>
                <?php if ($subjects && $subjects->num_rows > 0): ?>
                    <?php while ($subject = $subjects->fetch_assoc()): ?>
                        <option value="<?php echo $subject['subject_id']; ?>" <?php echo ($selectedSubjectId === (int) $subject['subject_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subject['subject_name'] . ' - ' . $subject['faculty_name']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>
        <div>
            <label for="attendance_date">Date</label>
            <input type="date" id="attendance_date" name="attendance_date" value="<?php echo htmlspecialchars($attendanceDate); ?>" required>
        </div>
    </div>

    <div class="table-shell">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>USN</th>
                    <th>Department</th>
                    <th>Semester</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students && $students->num_rows > 0): ?>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $student['student_id']; ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['usn']); ?></td>
                            <td><?php echo htmlspecialchars($student['department']); ?></td>
                            <td><?php echo $student['semester']; ?></td>
                            <td>
                                <select name="status[<?php echo $student['student_id']; ?>]">
                                    <option value="Present">Present</option>
                                    <option value="Absent">Absent</option>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No students found. Add students first.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <button type="submit" name="mark_attendance">Save Attendance</button>
</form>

<?php require_once 'footer.php'; ?>
