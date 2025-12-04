<?php
session_start();

// Database connection
$host = "localhost";
$dbname = "studentmanagement";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle course submission
if(isset($_POST['add_course'])){
    $stmt = $pdo->prepare("INSERT INTO courses (name, duration, mode, language, certificate, level, description, instructor, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['duration'],
        $_POST['mode'],
        $_POST['language'],
        $_POST['certificate'],
        $_POST['level'],
        $_POST['description'],
        $_POST['instructor'],
        $_POST['image']
    ]);
    $message = "Course added successfully!";
}

// Fetch courses
$courses = $pdo->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);

// Fetch registrations
$registrations = $pdo->query("SELECT r.*, c.name as course_name FROM course_registrations r JOIN courses c ON r.course_id = c.id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - EduTech</title>
<link rel="stylesheet" href="css/admin.css">
<script src="admin.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="admin-navbar">
    <div class="logo">EduTech Admin</div>
    <ul>
        <li><a href="#add-course" class="active-link">Add Course</a></li>
        <li><a href="#view-registrations">Registrations</a></li>
    </ul>
</nav>

<div class="container">

    <!-- Flash Message -->
    <?php if(isset($message)) echo "<div class='flash-message'>$message</div>"; ?>

    <!-- Add Course Section -->
    <section id="add-course">
        <h2><i class="fas fa-plus-circle"></i> Add New Course</h2>
        <form method="POST" class="course-form">
            <input type="text" name="name" placeholder="Course Name" required>
            <input type="text" name="duration" placeholder="Duration (e.g. 6 Months)" required>
            <input type="text" name="mode" placeholder="Mode (Online/Offline)" required>
            <input type="text" name="language" placeholder="Language (comma-separated)" required>
            <input type="text" name="certificate" placeholder="Certificate (Yes/No)" required>
            <input type="text" name="level" placeholder="Level (Beginner/Intermediate/Advanced)" required>
            <textarea name="description" placeholder="Course Description" required></textarea>
            <input type="text" name="instructor" placeholder="Instructor Name" required>
            <input type="text" name="image" placeholder="Course Image URL" required>
            <button type="submit" name="add_course"><i class="fas fa-plus"></i> Add Course</button>
        </form>
    </section>
    <!-- View Registrations Section -->
    <section id="view-registrations">
        <h2><i class="fas fa-users"></i> Registered Students</h2>
        <div class="registrations-list">
            <?php if($registrations): ?>
                <?php foreach($registrations as $reg): ?>
                    <div class="student-card">
                        <h3><?php echo htmlspecialchars($reg['student_name']); ?></h3>
                        <p><strong>Course:</strong> <?php echo htmlspecialchars($reg['course_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($reg['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($reg['phone']); ?></p>
                        <p><strong>Roll No:</strong> <?php echo htmlspecialchars($reg['roll_number']); ?></p>
                        <p><strong>DOB:</strong> <?php echo htmlspecialchars($reg['dob']); ?></p>
                        <p><strong>Batch:</strong> <?php echo htmlspecialchars($reg['preferred_batch']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No registrations yet.</p>
            <?php endif; ?>
        </div>
    </section>

</div>
</body>
</html>
