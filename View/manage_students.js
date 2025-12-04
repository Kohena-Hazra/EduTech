let currentDeleteId = null;

// Load all students on page load
document.addEventListener('DOMContentLoaded', () => {
    loadStudents();
});

// Load students from database
async function loadStudents() {
    try {
        const response = await fetch('admin_actions.php?action=get_students');
        const result = await response.json();

        if (result.success) {
            displayStudents(result.students);
        } else {
            showNotification('Failed to load students', 'error');
        }
    } catch (error) {
        showNotification('An error occurred while loading students', 'error');
    }
}

// Display students in table
function displayStudents(students) {
    const tbody = document.getElementById('studentsTableBody');

    if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem;">No students found</td></tr>';
        return;
    }

    tbody.innerHTML = students.map(student => `
        <tr>
            <td>${student.id}</td>
            <td>${escapeHtml(student.name)}</td>
            <td>${escapeHtml(student.email)}</td>
            <td>${escapeHtml(student.class || 'N/A')}</td>
            <td>${escapeHtml(student.roll_no || 'N/A')}</td>
            <td class="action-buttons">
                <button onclick="editStudent(${student.id})" class="btn btn-warning btn-sm">Edit</button>
                <button onclick="deleteStudent(${student.id})" class="btn btn-danger btn-sm">Delete</button>
            </td>
        </tr>
    `).join('');
}

// Search students
function searchStudents() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#studentsTableBody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Show add student modal
function showAddStudentModal() {
    document.getElementById('modalTitle').textContent = 'Add New Student';
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
    document.getElementById('studentModal').style.display = 'block';
}

// Edit student
async function editStudent(id) {
    try {
        const response = await fetch(`admin_actions.php?action=get_student&id=${id}`);
        const result = await response.json();

        if (result.success) {
            const student = result.student;
            document.getElementById('modalTitle').textContent = 'Edit Student';
            document.getElementById('studentId').value = student.id;
            document.getElementById('studentName').value = student.name;
            document.getElementById('studentEmail').value = student.email;
            document.getElementById('studentClass').value = student.class || '';
            document.getElementById('studentRoll').value = student.roll_no || '';
            document.getElementById('studentModal').style.display = 'block';
        } else {
            showNotification('Failed to load student data', 'error');
        }
    } catch (error) {
        showNotification('An error occurred', 'error');
    }
}

// Delete student
function deleteStudent(id) {
    currentDeleteId = id;
    document.getElementById('deleteModal').style.display = 'block';
}

// Confirm delete
async function confirmDelete() {
    if (!currentDeleteId) return;

    try {
        const formData = new FormData();
        formData.append('student_id', currentDeleteId);

        const response = await fetch('admin_actions.php?action=delete_student', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Student deleted successfully!', 'success');
            closeDeleteModal();
            loadStudents();
        } else {
            showNotification(result.message || 'Failed to delete student', 'error');
        }
    } catch (error) {
        showNotification('An error occurred', 'error');
    }
}

// Handle student form submission
document.getElementById('studentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const studentId = document.getElementById('studentId').value;
    const action = studentId ? 'update_student' : 'add_student';

    try {
        const response = await fetch(`admin_actions.php?action=${action}`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showNotification(studentId ? 'Student updated successfully!' : 'Student added successfully!', 'success');
            closeStudentModal();
            loadStudents();
        } else {
            showNotification(result.message || 'Failed to save student', 'error');
        }
    } catch (error) {
        showNotification('An error occurred', 'error');
    }
});

// Close modals
function closeStudentModal() {
    document.getElementById('studentModal').style.display = 'none';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    currentDeleteId = null;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const studentModal = document.getElementById('studentModal');
    const deleteModal = document.getElementById('deleteModal');

    if (event.target === studentModal) {
        closeStudentModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
}
