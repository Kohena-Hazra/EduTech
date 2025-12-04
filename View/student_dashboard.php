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

    // ✅ Fetch student details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ Fetch class routine
    $stmt = $pdo->query("SELECT title, description, `date` FROM routine ORDER BY date DESC LIMIT 5");
    $routine = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Fetch exams
    $stmt = $pdo->query("SELECT exam_name, subject, exam_date, exam_time FROM exam ORDER BY exam_date ASC LIMIT 5");
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Fetch attendance for this student
$student_id = $student['id'];
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE student_email = ?");
$stmt->execute([$student_id]);
$attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate attendance percentage
$totalClasses = count($attendance);
$presentClasses = count(array_filter($attendance, fn($a) => strtolower($a['status']) === 'present'));
$attendancePercentage = $totalClasses > 0 ? round(($presentClasses / $totalClasses) * 100) : 0;

    // ✅ Fetch study materials
    $stmt = $pdo->query("SELECT title, course, link_or_file, description FROM study_material ORDER BY id DESC LIMIT 5");
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // ✅ Fetch latest notices (limit 5)
    $stmt = $pdo->query("SELECT id, title, content, priority, created_at FROM notice ORDER BY created_at DESC LIMIT 5");
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);



    // ✅ Create notifications array
    $notifications = [];
    
    // Add exam notifications
    foreach ($exams as $exam) {
        $examDate = strtotime($exam['exam_date']);
        $today = strtotime(date('Y-m-d'));
        $daysUntil = floor(($examDate - $today) / (60 * 60 * 24));
        
        if ($daysUntil >= 0 && $daysUntil <= 7) {
            $notifications[] = [
                'type' => 'exam',
                'icon' => '📝',
                'title' => 'Upcoming Exam',
                'message' => $exam['subject'] . ' exam on ' . date('M d', $examDate),
                'time' => $daysUntil == 0 ? 'Today' : $daysUntil . ' days left',
                'priority' => $daysUntil <= 2 ? 'high' : 'medium'
            ];
        }
    }
    
    // Add routine notifications
    foreach ($routine as $r) {
        $routineDate = strtotime($r['date']);
        $today = strtotime(date('Y-m-d'));
        $daysUntil = floor(($routineDate - $today) / (60 * 60 * 24));
        
        if ($daysUntil >= 0 && $daysUntil <= 3) {
            $notifications[] = [
                'type' => 'routine',
                'icon' => '📅',
                'title' => 'Class Schedule',
                'message' => $r['title'] . ' - ' . $r['description'],
                'time' => $daysUntil == 0 ? 'Today' : $daysUntil . ' days',
                'priority' => 'low'
            ];
        }
    }
    
    // Add attendance warning if below 75%
    if ($attendancePercentage < 75 && $totalClasses > 0) {
        $notifications[] = [
            'type' => 'attendance',
            'icon' => '⚠️',
            'title' => 'Attendance Alert',
            'message' => 'Your attendance is ' . $attendancePercentage . '%. Maintain 75% minimum.',
            'time' => 'Important',
            'priority' => 'high'
        ];
    }
    
    // Add new study material notification
    if (count($materials) > 0) {
        $notifications[] = [
            'type' => 'material',
            'icon' => '📚',
            'title' => 'New Study Material',
            'message' => 'New materials available for ' . $materials[0]['course'],
            'time' => 'Recent',
            'priority' => 'medium'
        ];
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
    <title>Student Dashboard - EduTech</title>
    <link rel="stylesheet" href="css/studentdashboard2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>EduTech</span>
        </div>
        <ul class="nav-menu">
            <li class="active"><a href="#dashboard"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
           <li><a href="course_details.php"><i class="fas fa-book"></i> <span>Course</span></a></li>
            <li><a href="#routine"><i class="fas fa-calendar-alt"></i> <span>Routine</span></a></li>
            <li><a href="#exams"><i class="fas fa-clipboard-list"></i> <span>Exams</span></a></li>
            <li><a href="#attendance"><i class="fas fa-check-circle"></i> <span>Attendance</span></a></li>
            <li><a href="#materials"><i class="fas fa-folder-open"></i> <span>Materials</span></a></li>
            <li><a href="#profile"><i class="fas fa-user"></i> <span>Profile</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="index.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="top-nav">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search courses, exams, materials...">
            </div>
            <div class="nav-right">
                <button class="notification-btn" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    <span class="badge"><?php echo count($notifications); ?></span>
                </button>
                <div class="profile-menu">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['name']); ?>&background=4F46E5&color=fff" alt="Profile">
                    <span><?php echo htmlspecialchars($student['name']); ?></span>
                </div>
            </div>
        </nav>

        <!-- Notification Panel -->
        <div class="notification-panel" id="notificationPanel">
            <div class="notification-header">
                <h3>Notifications</h3>
                <button class="close-btn" id="closeNotifications"><i class="fas fa-times"></i></button>
            </div>
            <div class="notification-list">
                <?php if (count($notifications) > 0): ?>
                    <?php foreach ($notifications as $notif): ?>
                        <div class="notification-item <?php echo $notif['priority']; ?>">
                            <span class="notif-icon"><?php echo $notif['icon']; ?></span>
                            <div class="notif-content">
                                <h4><?php echo $notif['title']; ?></h4>
                                <p><?php echo $notif['message']; ?></p>
                                <span class="notif-time"><?php echo $notif['time']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-notifications">
                        <i class="fas fa-bell-slash"></i>
                        <p>No new notifications</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <div class="welcome-section">
                <div>
                    <h1>Welcome back, <?php echo htmlspecialchars(explode(' ', $student['name'])[0]); ?>! 👋</h1>
                    <p>Here's what's happening with your courses today</p>
                </div>
                <div class="date-display">
                    <i class="fas fa-calendar"></i>
                    <?php echo date('l, F d, Y'); ?>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                    <div class="stat-info">
                        <h3><?php echo count($materials); ?></h3>
                        <p>Study Materials</p>
                    </div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $attendancePercentage; ?>%</h3>
                        <p>Attendance Rate</p>
                    </div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="stat-info">
                        <h3><?php echo count($exams); ?></h3>
                        <p>Upcoming Exams</p>
                    </div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-info">
                        <h3><?php echo count($routine); ?></h3>
                        <p>Classes Scheduled</p>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="content-grid">
                <!-- Attendance Section -->
                <div class="card attendance-card" id="attendance">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-pie"></i> Attendance Overview</h3>
                    </div>
                    <div class="card-body">
                        <div class="attendance-chart">
                            <div class="circular-progress" data-percentage="<?php echo $attendancePercentage; ?>">
                                <svg width="160" height="160">
                                    <circle class="bg" cx="80" cy="80" r="70"></circle>
                                    <circle class="progress" cx="80" cy="80" r="70" 
                                            style="stroke-dasharray: 440; stroke-dashoffset: <?php echo 440 - (440 * $attendancePercentage / 100); ?>;">
                                    </circle>
                                </svg>
                                <div class="percentage"><?php echo $attendancePercentage; ?>%</div>
                            </div>
                        </div>
                        <div class="attendance-stats">
                            <div class="stat-item">
                                <span class="label">Total Classes</span>
                                <span class="value"><?php echo $totalClasses; ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Present</span>
                                <span class="value present"><?php echo $presentClasses; ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="label">Absent</span>
                                <span class="value absent"><?php echo $totalClasses - $presentClasses; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Exams -->
                <div class="card exams-card" id="exams">
                    <div class="card-header">
                        <h3><i class="fas fa-file-alt"></i> Upcoming Exams</h3>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if ($exams): ?>
                            <div class="exam-list">
                                <?php foreach ($exams as $exam): ?>
                                    <div class="exam-item">
                                        <div class="exam-icon">📝</div>
                                        <div class="exam-info">
                                            <h4><?php echo htmlspecialchars($exam['subject']); ?></h4>
                                            <p><?php echo htmlspecialchars($exam['exam_name']); ?></p>
                                        </div>
                                        <div class="exam-date">
                                            <span class="date"><?php echo date('d', strtotime($exam['exam_date'])); ?></span>
                                            <span class="month"><?php echo date('M', strtotime($exam['exam_date'])); ?></span>
                                            <span class="time"><?php echo $exam['exam_time']; ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-clipboard-check"></i>
                                <p>No upcoming exams</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Class Routine -->
                <div class="card routine-card" id="routine">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-alt"></i> Class Routine</h3>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if ($routine): ?>
                            <div class="routine-list">
                                <?php foreach ($routine as $r): ?>
                                    <div class="routine-item">
                                        <div class="routine-date">
                                            <span class="day"><?php echo date('d', strtotime($r['date'])); ?></span>
                                            <span class="month"><?php echo date('M', strtotime($r['date'])); ?></span>
                                        </div>
                                        <div class="routine-info">
                                            <h4><?php echo htmlspecialchars($r['title']); ?></h4>
                                            <p><?php echo htmlspecialchars($r['description']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>No classes scheduled</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Study Materials -->
                <div class="card materials-card" id="materials">
                    <div class="card-header">
                        <h3><i class="fas fa-folder-open"></i> Study Materials</h3>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if ($materials): ?>
                            <div class="materials-grid">
                                <?php foreach ($materials as $m): ?>
                                    <div class="material-item">
                                        <div class="material-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="material-info">
                                            <h4><?php echo htmlspecialchars($m['title']); ?></h4>
                                            <p><?php echo htmlspecialchars($m['course']); ?></p>
                                            <a href="<?php echo htmlspecialchars($m['link_or_file']); ?>" class="download-btn" download>
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <p>No study materials available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Notice Section -->
<div class="card notice-card" id="notice">
    <div class="card-header">
        <h3><i class="fas fa-bullhorn"></i> Notices</h3>
        <a href="#" class="view-all">View All</a>
    </div>
    <div class="card-body">
        <?php if ($notices): ?>
            <div class="notice-list">
                <?php foreach ($notices as $n): ?>
                    <div class="notice-item">
                        <div class="notice-date">
                            <span class="day"><?php echo date('d', strtotime($n['created_at'])); ?></span>
                            <span class="month"><?php echo date('M', strtotime($n['created_at'])); ?></span>
                        </div>
                        <div class="notice-info">
                            <h4><?php echo htmlspecialchars($n['title']); ?></h4>
                            <p><?php echo htmlspecialchars($n['content']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bullhorn"></i>
                <p>No new notices</p>
            </div>
        <?php endif; ?>
    </div>
</div>


                <!-- Profile Section -->
                <div class="card profile-card" id="profile">
                    <div class="card-header">
                        <h3><i class="fas fa-user"></i> Profile Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="profile-content">
                            <div class="profile-avatar">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['name']); ?>&background=4F46E5&color=fff&size=100" alt="Profile">
                            </div>
                            <div class="profile-details">
                                <h3><?php echo htmlspecialchars($student['name']); ?></h3>
                                <p class="student-class"><?php echo htmlspecialchars($student['class']); ?></p>
                                <div class="profile-info">
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo htmlspecialchars($student['email']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-id-card"></i>
                                        <span>ID: <?php echo $student['id']; ?></span>
                                    </div>
                                </div>
                                <button class="edit-profile-btn"><i class="fas fa-edit"></i> Edit Profile</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="studentdashboard.js"></script>
</body>
</html>