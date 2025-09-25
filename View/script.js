// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
    addEventListeners();
    addAnimations();
});

// Initialize Form
function initializeForm() {
    const form = document.getElementById('loginForm');
    if (!form) return;
    const inputs = form.querySelectorAll('input');
    
    // Focus first empty input
    const firstEmptyInput = Array.from(inputs).find(input => !input.value);
    if (firstEmptyInput) {
        firstEmptyInput.focus();
    }

    // Add loading state management
    form.addEventListener('submit', handleFormSubmit);
}

// Add Event Listeners
function addEventListeners() {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const roleInputs = document.querySelectorAll('input[name="role"]');

    if (emailInput) {
        emailInput.addEventListener('blur', validateEmail);
        emailInput.addEventListener('input', clearValidation);
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', validatePassword);
        passwordInput.addEventListener('input', clearValidation);
    }

    roleInputs.forEach(role => {
        role.addEventListener('change', handleRoleSelection);
    });

    document.addEventListener('keydown', handleKeyboardNavigation);

    // Input focus effects
    const allInputs = document.querySelectorAll('input');
    allInputs.forEach(input => {
        input.addEventListener('focus', handleInputFocus);
        input.addEventListener('blur', handleInputBlur);
    });
}

// Toggle Password Visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Email validation
function validateEmail() {
    const emailInput = document.getElementById('email');
    const email = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const formGroup = emailInput.closest('.form-group');

    if (email === '' || !emailRegex.test(email)) {
        setValidationState(formGroup, 'error');
        showTooltip(emailInput, 'Please enter a valid email');
        return false;
    } else {
        setValidationState(formGroup, 'success');
        hideTooltip(emailInput);
        return true;
    }
}

// Password validation
function validatePassword() {
    const passwordInput = document.getElementById('password');
    const password = passwordInput.value.trim();
    const formGroup = passwordInput.closest('.form-group');

    if (password.length < 6) {
        setValidationState(formGroup, 'error');
        showTooltip(passwordInput, 'Password must be at least 6 characters');
        return false;
    } else {
        setValidationState(formGroup, 'success');
        hideTooltip(passwordInput);
        return true;
    }
}

// Role validation
function validateRole() {
    const roleInputs = document.querySelectorAll('input[name="role"]');
    return Array.from(roleInputs).some(input => input.checked);
}

// Validation State
function setValidationState(formGroup, state) {
    formGroup.classList.remove('error', 'success');
    if (state) formGroup.classList.add(state);
}
function clearValidation() {
    const formGroup = this.closest('.form-group');
    setValidationState(formGroup, '');
    hideTooltip(this);
}

// Tooltip
function showTooltip(element, message) {
    hideTooltip(element);
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = message;
    element.parentElement.appendChild(tooltip);
}
function hideTooltip(element) {
    const tooltip = element.parentElement.querySelector('.tooltip');
    if (tooltip) tooltip.remove();
}

// Role Selection
function handleRoleSelection(e) {
    document.querySelectorAll('.role-card').forEach(card => card.style.transform = '');
    const selectedCard = e.target.nextElementSibling;
    selectedCard.style.transform = 'translateY(-3px) scale(1.02)';
}

// Input focus effect
function handleInputFocus(e) {
    e.target.closest('.form-group').style.transform = 'translateY(-2px)';
}
function handleInputBlur(e) {
    e.target.closest('.form-group').style.transform = '';
}

// Submit handler
function handleFormSubmit(e) {
    if (!validateEmail() || !validatePassword() || !validateRole()) {
        e.preventDefault();
        showNotification('Please fill all fields correctly', 'error');
        return false;
    }
}

// Keyboard Navigation
function handleKeyboardNavigation(e) {
    if (e.key === 'Escape') document.activeElement.blur();
}

// Animations
function addAnimations() {
    const elements = document.querySelectorAll('.form-group, .role-selection, .login-btn');
    elements.forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease';
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 100 + (i * 100));
    });
}

// Notification System
function showNotification(message, type = 'info') {
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => notification.remove(), 3000);
}
