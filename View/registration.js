function validateForm() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const role = document.getElementById("role").value;

    // simple email check
    if (!email.includes("@")) {
        alert("Please enter a valid email");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters");
        return false;
    }

    if (role === "") {
        alert("Please select a role (Admin/Student)");
        return false;
    }

    // extra check: if student, roll_no and class must be filled
    if (role === "student") {
        const rollNo = document.getElementById("roll_no").value.trim();
        const className = document.getElementById("class").value.trim();

        if (rollNo === "" || className === "") {
            alert("Please enter both Roll No and Class for Student");
            return false;
        }
    }

    return true;
}

function toggleStudentFields() {
    const role = document.getElementById("role").value;
    const studentFields = document.getElementById("studentFields");

    if (role === "student") {
        studentFields.style.display = "block";
        document.getElementById("roll_no").required = true;
        document.getElementById("class").required = true;
    } else {
        studentFields.style.display = "none";
        document.getElementById("roll_no").required = false;
        document.getElementById("class").required = false;
    }
}

// ✅ Attach event listener after page loads
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("role").addEventListener("change", toggleStudentFields);
});
