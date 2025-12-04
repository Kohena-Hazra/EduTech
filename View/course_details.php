<?php
session_start();
// ✅ Ensure only logged-in students can access
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

    // Fetch student details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Sample courses data
    // Fetch all courses from database
$sql = "SELECT * FROM courses";
$stmt = $pdo->query($sql);
$courses = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // Convert stored JSON/comma fields safely
    $row['language'] = !empty($row['language']) ? explode(',', $row['language']) : [];
    $row['what_you_learn'] = !empty($row['what_you_learn']) ? json_decode($row['what_you_learn'], true) : [];
    $row['requirements'] = !empty($row['requirements']) ? json_decode($row['requirements'], true) : [];
    $row['curriculum'] = !empty($row['curriculum']) ? json_decode($row['curriculum'], true) : [];

    $courses[] = $row;
}


} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details - EduTech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/course_details3.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <span>EduTech</span>
            </div>
            <ul class="nav-menu">
                <li><a href="student_dashboard.php">Dashboard</a></li>
                <li><a href="course_details.php" class="active">Courses</a></li>
                <li><a href="student_dashboard.php">Logout</a></li>
            </ul>
            <div class="nav-user">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['name']); ?>&background=4F46E5&color=fff" alt="Profile">
                <span><?php echo htmlspecialchars($student['name']); ?></span>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <?php foreach ($courses as $course): 
            // Safe defaults
            $description = $course['description'] ?? "No description available";
            $instructor = $course['instructor'] ?? "Not assigned";
            $language = isset($course['language']) && is_array($course['language']) ? implode(', ', $course['language']) : "N/A";
            $certificate = $course['certificate'] ?? "No";
            $mode = $course['mode'] ?? "N/A";
            $rating = $course['rating'] ?? 0;
            $students_enrolled = $course['students_enrolled'] ?? 0;
            $duration = $course['duration'] ?? "N/A";
            $level = $course['level'] ?? "N/A";
            $image = $course['image'] ?? "https://via.placeholder.com/800x400";
        ?>
        <!-- Course Header -->
        <div class="course-header" style="background: linear-gradient(rgba(79, 70, 229, 0.9), rgba(99, 102, 241, 0.9)), url('<?php echo $image; ?>') center/cover;">
            <div class="container">
                <h1><?php echo $course['name'] ?? "Unnamed Course"; ?></h1>
                <div class="course-meta">
                    <div class="meta-item"><i class="fas fa-users"></i> <?php echo $students_enrolled; ?> Students</div>
                    <div class="meta-item"><i class="fas fa-clock"></i> <?php echo $duration; ?></div>
                    <div class="meta-item"><i class="fas fa-signal"></i> <?php echo $level; ?></div>
                </div>
            </div>
        </div>

        <!-- Course Content Wrapper -->
        <div class="content-wrapper">
            <div class="course-content">
                <!-- Overview -->
                <section class="content-section">
                    <h2><i class="fas fa-info-circle"></i> Course Overview</h2>
                    <p><?php echo $description; ?></p>
                    <div class="course-highlights">
                        <div class="highlight-item">
                            <i class="fas fa-graduation-cap"></i>
                            <div><h4>Instructor</h4><p><?php echo $instructor; ?></p></div>
                        </div>
                        <div class="highlight-item">
                            <i class="fas fa-language"></i>
                            <div><h4>Languages</h4><p><?php echo $language; ?></p></div>
                        </div>
                        <div class="highlight-item">
                            <i class="fas fa-certificate"></i>
                            <div><h4>Certificate</h4><p><?php echo $certificate; ?></p></div>
                        </div>
                        <div class="highlight-item">
                            <i class="fas fa-laptop-house"></i>
                            <div><h4>Mode</h4><p><?php echo $mode; ?></p></div>
                        </div>
                    </div>
                </section>


            <!-- Sidebar -->
<!-- Sidebar -->
<div class="course-sidebar">
    <div class="sidebar-card">
        <div class="course-thumbnail">
            <img src="<?php echo $image; ?>" alt="<?php echo $course['name'] ?? "Course"; ?>">
        </div>
        <!-- Pass course_id to registration page -->
        <a href="course_registration.php?course_id=<?php echo $course['id']; ?>" class="btn-register">
            <i class="fas fa-user-plus"></i> Register Now
        </a>
        <div class="course-includes">
            <h4>This course includes:</h4>
            <ul>
                <li><i class="fas fa-file-alt"></i> Study Materials</li>
                <li><i class="fas fa-infinity"></i> Lifetime Access</li>
                <li><i class="fas fa-certificate"></i> Certificate of Completion</li>
                <li><i class="fas fa-headset"></i> 24/7 Support</li>
                <li><i class="fas fa-mobile-alt"></i> Mobile & Desktop Access</li>
            </ul>
        </div>
    </div>
</div>
        </div>
        <hr style="margin:40px 0;">
        <?php endforeach; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 EduTech. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>
</body>
</html>
