<?php
session_start();

// Security: Only allow if logged in as student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$dbname = "studentmanagement";
$username = "root"; 
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $student_id = $_SESSION['user_id'];

    // Fetch student info
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();

    // Fetch upcoming exams
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE class = ?");
    $stmt->execute([$student['class']]);
    $exams = $stmt->fetchAll();

    // Fetch class routine
    $stmt = $pdo->prepare("SELECT * FROM class_routine WHERE class = ?");
    $stmt->execute([$student['class']]);
    $routine = $stmt->fetchAll();

    // Fetch attendance
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $attendance = $stmt->fetchAll();

    // Fetch downloadable materials
    $stmt = $pdo->prepare("SELECT * FROM materials WHERE class = ?");
    $stmt->execute([$student['class']]);
    $materials = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="student_dashboard.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">EduTech</div>
    <ul class="nav-links">
      <li><a href="#">Dashboard</a></li>
      <li><a href="#">Courses</a></li>
      <li><a href="#">Exams</a></li>
      <li><a href="#">Profile</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <!-- Dashboard Container -->
  <div class="dashboard">
    <h2>Welcome, <?php echo htmlspecialchars($student['name']); ?> 👋</h2>
    <div class="cards">
      
      <!-- Routine -->
      <div class="card routine">
        <h3>📅 Routine</h3>
        <?php if ($routine): ?>
          <ul>
            <?php foreach ($routine as $r): ?>
              <li><?php echo htmlspecialchars($r['day']) . ": " . htmlspecialchars($r['subject']) . " (" . htmlspecialchars($r['time']) . ")"; ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No routine available</p>
        <?php endif; ?>
      </div>

      <!-- Exams -->
      <div class="card exams">
        <h3>📝 Upcoming Exams</h3>
        <?php if ($exams): ?>
          <ul>
            <?php foreach ($exams as $exam): ?>
              <li><?php echo htmlspecialchars($exam['subject']) . " - " . htmlspecialchars($exam['date']); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No upcoming exams</p>
        <?php endif; ?>
      </div>

      <!-- Attendance -->
      <div class="card attendance">
        <h3>📊 Attendance</h3>
        <?php if ($attendance): ?>
          <p>Total Classes: <?php echo count($attendance); ?></p>
          <p>Present: <?php echo count(array_filter($attendance, fn($a) => $a['status'] === 'Present')); ?></p>
          <p>Absent: <?php echo count(array_filter($attendance, fn($a) => $a['status'] === 'Absent')); ?></p>
        <?php else: ?>
          <p>No attendance record found</p>
        <?php endif; ?>
      </div>

      <!-- Materials -->
      <div class="card materials">
        <h3>📂 Study Materials</h3>
        <?php if ($materials): ?>
          <ul>
            <?php foreach ($materials as $m): ?>
              <li><a href="uploads/<?php echo htmlspecialchars($m['file']); ?>" download><?php echo htmlspecialchars($m['title']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No materials available</p>
        <?php endif; ?>
      </div>

      <!-- Profile -->
      <div class="card profile">
        <h3>👤 Profile</h3>
        <p>Email: <?php echo htmlspecialchars($student['email']); ?><br>
           Class: <?php echo htmlspecialchars($student['class']); ?>
        </p>
      </div>

    </div>
  </div>

  <script src="student_dashboard.js"></script>
</body>
</html>
