<?php
$host = "localhost";
$dbname = "studentmanagement";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $course_id = $_GET['course_id'] ?? null;
    if (!$course_id) die("Course not selected.");

    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $roll_number = $_POST['roll_number'];
        $dob = $_POST['dob'];
        $preferred_batch = $_POST['preferred_batch'];

        $stmt = $pdo->prepare("INSERT INTO course_registrations 
            (course_id, student_name, email, phone, roll_number, dob, preferred_batch) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $name, $email, $phone, $roll_number, $dob, $preferred_batch]);

        $success = "Registration successful!";
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
    <title>Register for <?php echo $course['name']; ?></title>
    <link rel="stylesheet" href="css/course_registration.css">
</head>
<body>
    <div class="registration-container">
        <h1>Register for <span><?php echo $course['name']; ?></span></h1>
        <?php if(isset($success)) echo "<div class='success-msg'>$success</div>"; ?>
        <form method="POST" id="registrationForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" required>
            </div>

            <div class="form-group">
                <label for="roll_number">Roll Number</label>
                <input type="text" name="roll_number" id="roll_number">
            </div>

            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" name="dob" id="dob">
            </div>

            <div class="form-group">
                <label for="preferred_batch">Preferred Batch Timing</label>
                <select name="preferred_batch" id="preferred_batch" required>
                    <option value="">--Select--</option>
                    <option value="Morning">Morning</option>
                    <option value="Afternoon">Afternoon</option>
                    <option value="Evening">Evening</option>
                </select>
            </div>

            <button type="submit">Register Now</button>
        </form>
    </div>
    <script src="course_registration.js"></script>
</body>
</html>
