<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
$host = "localhost";
$dbname = "studentmanagement";
$username = "root"; 
$password = "";

$admin_name = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EduTech</title>
    <link rel="stylesheet" href="css/admin_style1.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">EduTech Admin</div>
        <div class="nav-menu">
            <a href="admin_dashboard.php" class="nav-link active">Dashboard</a>
            <a href="add_course.php" class="nav-link">Post Courses</a>
            <a href="manage_students.php" class="nav-link">Students</a>
            <a href="index.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?> 👋</h1>
            <p>Manage your educational platform from here</p>
        </div>

        <div class="dashboard-grid">
            <!-- Post Routine Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>📅 Post Routine</h2>
                </div>
                <div class="card-body">
                    <form id="routineForm" class="admin-form">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="routine_title" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="routine_description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="routine_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Routine</button>
                    </form>
                </div>
            </div>

            <!-- Post Exam Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>🎯 Post Exam</h2>
                </div>
                <div class="card-body">
                    <form id="examForm" class="admin-form">
                        <div class="form-group">
                            <label>Exam Name</label>
                            <input type="text" name="exam_name" required>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="exam_subject" required>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="exam_date" required>
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <input type="time" name="exam_time" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Exam</button>
                    </form>
                </div>
            </div>

            <!-- Post Study Material Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>📖 Post Study Material</h2>
                </div>
                <div class="card-body">
                    <form id="materialForm" class="admin-form" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Material Title</label>
                            <input type="text" name="material_title" required>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <input type="text" name="material_course" required>
                        </div>
                        <div class="form-group">
                            <label>File/Link</label>
                            <input type="text" name="material_link" placeholder="Enter URL or upload file">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="material_description" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Material</button>
                    </form>
                </div>
            </div>

            <!-- Post Attendance Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>✅ Mark Attendance</h2>
                </div>
                <div class="card-body">
                    <form id="attendanceForm" class="admin-form">
                        <div class="form-group">
                            <label>Student Email</label>
                            <input type="email" name="student_email" required>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="attendance_date" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="attendance_status" required>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="attendance_subject" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Mark Attendance</button>
                    </form>
                </div>
            </div>

            <!-- Post Notice Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>📢 Post Notice</h2>
                </div>
                <div class="card-body">
                    <form id="noticeForm" class="admin-form">
                        <div class="form-group">
                            <label>Notice Title</label>
                            <input type="text" name="notice_title" required>
                        </div>
                        <div class="form-group">
                            <label>Notice Content</label>
                            <textarea name="notice_content" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Priority</label>
                            <select name="notice_priority">
                                <option value="normal">Normal</option>
                                <option value="important">Important</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Notice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="notification" class="notification"></div>

    <script src="admin_script.js"></script>
</body>
</html>