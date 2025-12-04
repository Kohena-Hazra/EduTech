<?php
session_start();

// ✅ Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// ✅ Database connection
$host = "localhost";
$dbname = "studentmanagement";
$username = "root"; 
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'post_routine':
        postRoutine();
        break;
    case 'post_exam':
        postExam();
        break;
    case 'post_course':
        postCourse();
        break;
    case 'post_material':
        postMaterial();
        break;
    case 'mark_attendance':
        markAttendance();
        break;
    case 'post_notice':
        postNotice();
        break;
    case 'get_students':
        getStudents();
        break;
    case 'get_student':
        getStudent();
        break;
    case 'add_student':
        addStudent();
        break;
    case 'update_student':
        updateStudent();
        break;
    case 'delete_student':
        deleteStudent();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function postRoutine() {
    global $conn;

    $title = $_POST['routine_title'] ?? '';
    $description = $_POST['routine_description'] ?? '';
    $date = $_POST['routine_date'] ?? '';

    // Fix date format (optional)
    if (!empty($date)) {
        $date = date('Y-m-d', strtotime($date));
    }

    $sql = "INSERT INTO routine (title, description, `date`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("sss", $title, $description, $date);

    echo $stmt->execute()
        ? json_encode(['success' => true, 'message' => 'Routine added successfully'])
        : json_encode(['success' => false, 'message' => 'Failed to add routine: ' . $stmt->error]);
}


// ✅ Post Exam
function postExam() {
    global $conn;
    $name = $_POST['exam_name'] ?? '';
    $subject = $_POST['exam_subject'] ?? '';
    $date = $_POST['exam_date'] ?? '';
    $time = $_POST['exam_time'] ?? '';

    $sql = "INSERT INTO exam (exam_name, subject, exam_date, exam_time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $subject, $date, $time);

    echo $stmt->execute()
        ? json_encode(['success' => true, 'message' => 'Exam posted successfully'])
        : json_encode(['success' => false, 'message' => 'Failed to post exam']);
}

// ✅ Post Course
function postCourse() {
    global $conn;
    $name = $_POST['course_name'] ?? '';
    $code = $_POST['course_code'] ?? '';
    $instructor = $_POST['course_instructor'] ?? '';
    $description = $_POST['course_description'] ?? '';

    $sql = "INSERT INTO course (course_name, course_code, instructor, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $code, $instructor, $description);

    echo $stmt->execute()
        ? json_encode(['success' => true, 'message' => 'Course added successfully'])
        : json_encode(['success' => false, 'message' => 'Failed to add course']);
}

// ✅ Post Study Material
function postMaterial() {
    global $conn;
    $title = $_POST['material_title'] ?? '';
    $course = $_POST['material_course'] ?? '';
    $link = $_POST['material_link'] ?? '';
    $description = $_POST['material_description'] ?? '';

    $sql = "INSERT INTO study_material (title,course,link_or_file,description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $title, $course, $link, $description);

    echo $stmt->execute()
        ? json_encode(['success' => true, 'message' => 'Material added successfully'])
        : json_encode(['success' => false, 'message' => 'Failed to add material']);
}

// ✅ Mark Attendance
function markAttendance() {
    global $conn;
    $email = $_POST['student_email'] ?? '';
    $date = $_POST['attendance_date'] ?? '';
    $status = $_POST['attendance_status'] ?? '';
    $subject = $_POST['attendance_subject'] ?? '';

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND role = 'student'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        return;
    }

    $student = $result->fetch_assoc();
    $student_id = $student['id'];

    $sql = "INSERT INTO attendance (student_email,attendance_date,status,subject)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status = VALUES(status), subject = VALUES(subject)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $student_id, $date, $status, $subject);

    echo $stmt->execute()
        ? json_encode(['success' => true, 'message' => 'Attendance marked successfully'])
        : json_encode(['success' => false, 'message' => 'Failed to mark attendance']);
}

// ✅ Post Notice
function postNotice() {
    global $conn;
    $title = $_POST['notice_title'] ?? '';
    $content = $_POST['notice_content'] ?? '';
    $date = date('Y-m-d');

    $sql = "INSERT INTO notice (title,content,priority) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $title, $content, $date);

    echo $stmt->execute()
        ? json_encode(['success' => true, 'message' => 'Notice posted successfully'])
        : json_encode(['success' => false, 'message' => 'Failed to post notice']);
}

// ✅ Get all students (with search support)
function getStudents() {
    global $conn;
    $search = $_GET['search'] ?? '';
    $search = "%$search%";

    $sql = "SELECT id, name, roll_no, class, email, created_at 
            FROM users 
            WHERE role = 'student' 
              AND (name LIKE ? OR email LIKE ? OR class LIKE ? OR roll_no LIKE ?)
            ORDER BY id DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $search, $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode(['success' => true, 'students' => $students]);
}

// ✅ Get single student
function getStudent() {
    global $conn;
    $id = (int)($_GET['id'] ?? 0);

    $sql = "SELECT id, name, roll_no, class, email FROM users WHERE id = ? AND role = 'student'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo $result->num_rows > 0
        ? json_encode(['success' => true, 'student' => $result->fetch_assoc()])
        : json_encode(['success' => false, 'message' => 'Student not found']);
}

// ✅ Add student
function addStudent() {
    global $conn;
    $name = $_POST['student_name'] ?? '';
    $email = $_POST['student_email'] ?? '';
    $roll = $_POST['student_roll'] ?? '';
    $class = $_POST['student_class'] ?? '';
    $password = password_hash($_POST['student_password'] ?? 'student123', PASSWORD_DEFAULT);
    $role = 'student';

    // Prevent duplicate emails
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }

    $sql = "INSERT INTO users (name, roll_no, class, email, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $roll, $class, $email, $password, $role);

    echo $stmt->execute()
        ? json_encode(['success' => true, 'message' => 'Student added successfully'])
        : json_encode(['success' => false, 'message' => 'Failed to add student']);
}

// ✅ Update student
function updateStudent() {
    global $conn;
    $id = (int)($_POST['student_id'] ?? 0);
    $name = $_POST['student_name'] ?? '';
    $email = $_POST['student_email'] ?? '';
    $roll = $_POST['student_roll'] ?? '';
    $class = $_POST['student_class'] ?? '';

    $sql = "UPDATE users SET name = ?, email = ?, roll_no = ?, class = ? WHERE id = ? AND role = 'student'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $roll, $class, $id);

    echo $stmt->execute()
        ? json_encode(['success' => true, 'message' => 'Student updated successfully'])
        : json_encode(['success' => false, 'message' => 'Failed to update student']);
}

// ✅ Delete student
function deleteStudent() {
    global $conn;
    $id = (int)($_POST['student_id'] ?? 0);

    // Delete related attendance records first
    $conn->query("DELETE FROM attendance WHERE student_id = $id");

    $sql = "DELETE FROM users WHERE id = ? AND role = 'student'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    echo $stmt->execute()
        ? json_encode(['success' => true, 'message' => 'Student deleted successfully'])
        : json_encode(['success' => false, 'message' => 'Failed to delete student']);
}
?>
