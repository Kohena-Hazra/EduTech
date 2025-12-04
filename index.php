<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'studentmanagement';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error_message = '';

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if (empty($email) || empty($password) || empty($role)) {
        $error_message = "All fields are required!";
    } else {
        $stmt = $pdo->prepare("SELECT id, email, password, role FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            if ($role == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit();
        } else {
            $error_message = "Invalid email, password, or role!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech</title>
    <link rel="stylesheet" href="View/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>Edutech</h1>
                <p>Welcome back! Please sign in to your account</p>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="" id="loginForm">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="role-selection">
                    <label class="role-label">Select Your Role</label>
                    <div class="role-options">
                        <div class="role-option">
                            <input type="radio" id="admin" name="role" value="admin" required>
                            <label for="admin" class="role-card">
                                <div class="role-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="role-info">
                                    <h3>Admin</h3>
                                    <p>System Administrator</p>
                                </div>
                            </label>
                        </div>
                        
                        <div class="role-option">
                            <input type="radio" id="student" name="role" value="student" required>
                            <label for="student" class="role-card">
                                <div class="role-icon">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div class="role-info">
                                    <h3>Student</h3>
                                    <p>Student Account</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>

            <div class="login-footer">
                <p>Don't have an account? 
                    <a href="registration.php" class="register-link">
                        <i class="fas fa-user-plus"></i>
                        Register here
                    </a>
                </p>
            </div>
        </div>

        <div class="info-panel">
            <div class="info-content">
                <h2>About Us</h2>
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-chart-line"></i>
                        <h4>Track Performance</h4>
                        <p>Monitor student progress and academic performance</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-calendar-alt"></i>
                        <h4>Manage Schedule</h4>
                        <p>Organize classes, assignments, and events efficiently</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-users"></i>
                        <h4>Connect & Collaborate</h4>
                        <p>Seamless communication between students and faculty</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="View/script.js"></script>
</body>
</html>