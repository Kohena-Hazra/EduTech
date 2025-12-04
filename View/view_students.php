<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$dbname = "studentmanagement";
$username = "root";
$password = "";
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Get all courses
$courses = $pdo->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);

$students = [];
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    $stmt = $pdo->prepare("SELECT * FROM registrations WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Students - Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Registered Students</h2>

    <form method="GET">
        <label>Select Course</label>
        <select name="course_id" onchange="this.form.submit()">
            <option value="">-- Select Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>" <?= (isset($course_id) && $course_id == $course['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($course['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if (!empty($students)): ?>
        <h3>Total Students: <?= count($students) ?></h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Roll Number</th>
                <th>DOB</th>
                <th>Preferred Batch</th>
            </tr>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['student_name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars($s['phone']) ?></td>
                    <td><?= htmlspecialchars($s['roll_number']) ?></td>
                    <td><?= htmlspecialchars($s['dob']) ?></td>
                    <td><?= htmlspecialchars($s['preferred_batch']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif(isset($course_id)): ?>
        <p>No students registered for this course yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
