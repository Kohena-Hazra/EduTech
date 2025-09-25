<?php
require_once(__DIR__ . "/../Model/db.php"); // $pdo is defined here

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role     = $_POST['role'];

    // Optional fields for students
    $roll_no = !empty($_POST['roll_no']) ? $_POST['roll_no'] : null;
    $class   = !empty($_POST['class']) ? $_POST['class'] : null;

    try {
        if ($role === "student") {
            $sql = "INSERT INTO users (name, email, password, role, roll_no, class, created_at) 
                    VALUES (:name, :email, :password, :role, :roll_no, :class, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name'     => $name,
                ':email'    => $email,
                ':password' => $password,
                ':role'     => $role,
                ':roll_no'  => $roll_no,
                ':class'    => $class
            ]);
        } else {
            // Admin case (no roll_no/class)
            $sql = "INSERT INTO users (name, email, password, role, created_at) 
                    VALUES (:name, :email, :password, :role, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name'     => $name,
                ':email'    => $email,
                ':password' => $password,
                ':role'     => $role
            ]);
        }

        echo "<script>
                alert('Registration successful! Please login.');
                window.location.href='../View/index.php';
              </script>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
