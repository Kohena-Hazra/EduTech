<?php
session_start();
require_once __DIR__ . "/../model/db.php";
require_once __DIR__ . "/../model/User.php";

$userModel = new User($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action === 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $user = $userModel->login($username, $password);

        if ($user) {
            $_SESSION['user'] = $user;
            if ($user['role'] === 'admin') {
                header("Location: ../view/dashboard_admin.php");
            } else {
                header("Location: ../view/dashboard_student.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid login credentials";
            header("Location: ../view/index.php");
        }
    }

    if ($action === 'register') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        if ($userModel->register($username, $password, $role)) {
            $_SESSION['success'] = "Registration successful!";
            header("Location: ../view/index.php");
        } else {
            $_SESSION['error'] = "Registration failed!";
            header("Location: ../view/registration.php");
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../view/index.php");
    exit();
}
?>
