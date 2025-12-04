<?php
// include database
require_once(__DIR__ . "/../Model/db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduTech - Register</title>
  <link rel="stylesheet" href="css/registration.css">
  <script src="registration.js" defer></script>
</head>
<body>
  <div class="form-container">
    <h1 class="brand">EduTech</h1>
    <h2 class="form-title">Create Your Account</h2>

    <form action="../Controller/register_submit.php" method="POST" onsubmit="return validateForm()">
        
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" placeholder="Enter your name" required>

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" required>

        <label for="role">Select Role</label>
        <select name="role" id="role" required>
          <option value="">-- Select Role --</option>
          <option value="admin">Admin</option>
          <option value="student">Student</option>
        </select>
         <!-- Student-only fields -->
    <div id="studentFields" style="display:none;">
        <label for="roll_no">Roll No</label>
        <input type="text" name="roll_no" id="roll_no" placeholder="Enter Roll No">
        <label for="class">Class</label>
        <input type="text" name="class" id="class" placeholder="Enter Class">
    </div>
        <button type="submit" class="btn">Register</button>
        <p class="login-link">Already have an account? <a href="index.php">Login here</a></p>
    </form>
  </div>

</body>
</html>
