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
    <title>Manage Students - EduTech</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-brand">EduTech Admin</div>
        <div class="nav-menu">
            <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
            <a href="manage_students.php" class="nav-link active">Students</a>
            <a href="index.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <!-- Page Container -->
    <div class="container">
        <div class="welcome-section">
            <h1>Manage Students</h1>
            <p>View, edit, and manage student information</p>
        </div>

        <!-- Search and Add Section -->
        <div class="search-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by name, email, or class...">
                <button onclick="searchStudents()" class="btn btn-primary">Search</button>
            </div>
            <button onclick="showAddStudentModal()" class="btn btn-success">Add New Student</button>
        </div>

        <!-- Students Table -->
        <div class="table-container">
            <table class="students-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Class</th>
                        <th>Roll No</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    <!-- Loaded dynamically by manage_students.js -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Student Modal -->
    <div id="studentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeStudentModal()">&times;</span>
            <h2 id="modalTitle">Add New Student</h2>
            <form id="studentForm" class="admin-form">
                <input type="hidden" id="studentId" name="student_id">

                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" id="studentName" name="student_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="studentEmail" name="student_email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Class *</label>
                        <input type="text" id="studentClass" name="student_class" required>
                    </div>
                    <div class="form-group">
                        <label>Roll No *</label>
                        <input type="text" id="studentRoll" name="student_roll" required>
                    </div>
                </div>

                <div class="modal-buttons">
                    <button type="submit" class="btn btn-primary">Save Student</button>
                    <button type="button" onclick="closeStudentModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content modal-small">
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete this student? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button onclick="confirmDelete()" class="btn btn-danger">Delete</button>
                <button onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <!-- Scripts -->
    <script src="admin_script.js"></script>
    <script src="manage_students.js"></script>
</body>
</html>
