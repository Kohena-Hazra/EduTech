// Notification function
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.display = 'none';
    }, 3000);
}

// Handle Routine Form Submission
const routineForm = document.getElementById('routineForm');
if (routineForm) {
    routineForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(routineForm);
        
        try {
            const response = await fetch('admin_actions.php?action=post_routine', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Routine posted successfully!', 'success');
                routineForm.reset();
            } else {
                showNotification(result.message || 'Failed to post routine', 'error');
            }
        } catch (error) {
            showNotification('An error occurred. Please try again.', 'error');
        }
    });
}

// Handle Exam Form Submission
const examForm = document.getElementById('examForm');
if (examForm) {
    examForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(examForm);
        
        try {
            const response = await fetch('admin_actions.php?action=post_exam', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Exam posted successfully!', 'success');
                examForm.reset();
            } else {
                showNotification(result.message || 'Failed to post exam', 'error');
            }
        } catch (error) {
            showNotification('An error occurred. Please try again.', 'error');
        }
    });
}

// Handle Course Form Submission
const courseForm = document.getElementById('courseForm');
if (courseForm) {
    courseForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(courseForm);
        
        try {
            const response = await fetch('admin_actions.php?action=post_course', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Course posted successfully!', 'success');
                courseForm.reset();
            } else {
                showNotification(result.message || 'Failed to post course', 'error');
            }
        } catch (error) {
            showNotification('An error occurred. Please try again.', 'error');
        }
    });
}

// Handle Study Material Form Submission
const materialForm = document.getElementById('materialForm');
if (materialForm) {
    materialForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(materialForm);
        
        try {
            const response = await fetch('admin_actions.php?action=post_material', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Study material posted successfully!', 'success');
                materialForm.reset();
            } else {
                showNotification(result.message || 'Failed to post material', 'error');
            }
        } catch (error) {
            showNotification('An error occurred. Please try again.', 'error');
        }
    });
}

// Handle Attendance Form Submission
const attendanceForm = document.getElementById('attendanceForm');
if (attendanceForm) {
    attendanceForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(attendanceForm);
        
        try {
            const response = await fetch('admin_actions.php?action=mark_attendance', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Attendance marked successfully!', 'success');
                attendanceForm.reset();
            } else {
                showNotification(result.message || 'Failed to mark attendance', 'error');
            }
        } catch (error) {
            showNotification('An error occurred. Please try again.', 'error');
        }
    });
}

// Handle Notice Form Submission
const noticeForm = document.getElementById('noticeForm');
if (noticeForm) {
    noticeForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(noticeForm);
        
        try {
            const response = await fetch('admin_actions.php?action=post_notice', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Notice posted successfully!', 'success');
                noticeForm.reset();
            } else {
                showNotification(result.message || 'Failed to post notice', 'error');
            }
        } catch (error) {
            showNotification('An error occurred. Please try again.', 'error');
        }
    });
}