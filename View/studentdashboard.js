// DOM Elements
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const notificationBtn = document.getElementById('notificationBtn');
const notificationPanel = document.getElementById('notificationPanel');
const closeNotifications = document.getElementById('closeNotifications');
const navLinks = document.querySelectorAll('.nav-menu li a');

// Toggle Sidebar (Mobile)
menuToggle?.addEventListener('click', () => {
    sidebar.classList.toggle('active');
});

// Toggle Notification Panel
notificationBtn?.addEventListener('click', () => {
    notificationPanel.classList.toggle('active');
});

closeNotifications?.addEventListener('click', () => {
    notificationPanel.classList.remove('active');
});

// Close notification panel when clicking outside
document.addEventListener('click', (e) => {
    if (!notificationPanel.contains(e.target) && 
        !notificationBtn.contains(e.target) && 
        notificationPanel.classList.contains('active')) {
        notificationPanel.classList.remove('active');
    }
});

// Close sidebar when clicking outside (mobile)
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && 
        !sidebar.contains(e.target) && 
        !menuToggle.contains(e.target) && 
        sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
    }
});

// Smooth Scroll Navigation
navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        
        // Only handle internal links (starting with #)
        if (href && href.startsWith('#') && href !== '#') {
            e.preventDefault();
            
            // Remove active class from all links
            navLinks.forEach(l => l.parentElement.classList.remove('active'));
            
            // Add active class to clicked link
            link.parentElement.classList.add('active');
            
            // Smooth scroll to section
            const targetId = href.substring(1);
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                targetSection.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
            
            // Close sidebar on mobile after clicking
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
            }
        }
    });
});

// Animate Circular Progress
function animateCircularProgress() {
    const progressCircle = document.querySelector('.circular-progress');
    if (!progressCircle) return;
    
    const percentage = progressCircle.getAttribute('data-percentage');
    const circle = progressCircle.querySelector('circle.progress');
    
    if (circle && percentage) {
        const circumference = 2 * Math.PI * 70; // radius = 70
        const offset = circumference - (percentage / 100) * circumference;
        
        // Add gradient definition to SVG
        const svg = circle.closest('svg');
        const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
        gradient.setAttribute('id', 'gradient');
        gradient.setAttribute('x1', '0%');
        gradient.setAttribute('y1', '0%');
        gradient.setAttribute('x2', '100%');
        gradient.setAttribute('y2', '100%');
        
        const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop1.setAttribute('offset', '0%');
        stop1.setAttribute('style', 'stop-color:#10B981;stop-opacity:1');
        
        const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
        stop2.setAttribute('offset', '100%');
        stop2.setAttribute('style', 'stop-color:#34D399;stop-opacity:1');
        
        gradient.appendChild(stop1);
        gradient.appendChild(stop2);
        defs.appendChild(gradient);
        svg.insertBefore(defs, svg.firstChild);
        
        // Animate the progress
        setTimeout(() => {
            circle.style.strokeDashoffset = offset;
        }, 100);
    }
}

// Animate stats on scroll
function animateStats() {
    const statCards = document.querySelectorAll('.stat-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    entry.target.style.transition = 'all 0.5s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    statCards.forEach(card => observer.observe(card));
}

// Highlight current date in routine
function highlightCurrentDate() {
    const routineItems = document.querySelectorAll('.routine-item');
    const today = new Date().toDateString();
    
    routineItems.forEach(item => {
        const dateElement = item.querySelector('.routine-date');
        if (dateElement) {
            const itemDate = new Date(dateElement.getAttribute('data-date'));
            if (itemDate.toDateString() === today) {
                item.style.background = '#EEF2FF';
                item.style.borderLeft = '4px solid #4F46E5';
            }
        }
    });
}

// Search functionality
const searchInput = document.querySelector('.search-bar input');
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        
        // Search through cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = searchTerm === '' ? 'block' : 'none';
            }
        });
    });
}

// Add loading animation to download buttons
const downloadButtons = document.querySelectorAll('.download-btn');
downloadButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
        this.style.pointerEvents = 'none';
        
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-check"></i> Downloaded';
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            }, 2000);
        }, 1500);
    });
});

// Notification interactions
const notificationItems = document.querySelectorAll('.notification-item');
notificationItems.forEach(item => {
    item.addEventListener('click', function() {
        this.style.opacity = '0.6';
        setTimeout(() => {
            this.style.display = 'none';
            updateNotificationBadge();
        }, 300);
    });
});

function updateNotificationBadge() {
    const visibleNotifications = document.querySelectorAll('.notification-item[style*="display: none"]');
    const badge = document.querySelector('.notification-btn .badge');
    const remainingCount = notificationItems.length - visibleNotifications.length;
    
    if (badge) {
        badge.textContent = remainingCount;
        if (remainingCount === 0) {
            badge.style.display = 'none';
        }
    }
}

// Edit profile button
const editProfileBtn = document.querySelector('.edit-profile-btn');
if (editProfileBtn) {
    editProfileBtn.addEventListener('click', () => {
        alert('Profile edit functionality will be implemented soon!');
    });
}

// Add hover effect to cards
const cards = document.querySelectorAll('.card');
cards.forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Format dates nicely
function formatDates() {
    const dateElements = document.querySelectorAll('[data-date]');
    dateElements.forEach(el => {
        const date = new Date(el.getAttribute('data-date'));
        const options = { month: 'short', day: 'numeric', year: 'numeric' };
        el.textContent = date.toLocaleDateString('en-US', options);
    });
}

// Add ripple effect to buttons
function createRipple(event) {
    const button = event.currentTarget;
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    
    button.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

const buttons = document.querySelectorAll('button, .download-btn');
buttons.forEach(button => {
    button.style.position = 'relative';
    button.style.overflow = 'hidden';
    button.addEventListener('click', createRipple);
});

// Add CSS for ripple effect dynamically
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Refresh data periodically (every 5 minutes)
function refreshData() {
    console.log('Refreshing dashboard data...');
    // In a real application, this would fetch new data from the server
    // For now, we'll just log it
}

setInterval(refreshData, 300000); // 5 minutes

// Show welcome toast notification
function showWelcomeToast() {
    const toast = document.createElement('div');
    toast.className = 'welcome-toast';
    toast.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>Welcome back! Your dashboard is ready.</span>
    `;
    
    toast.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #10B981;
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 9999;
        animation: slideIn 0.5s ease;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.5s ease';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

// Add slide animations
const slideStyle = document.createElement('style');
slideStyle.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(slideStyle);

// Initialize all animations and features
window.addEventListener('load', () => {
    animateCircularProgress();
    animateStats();
    highlightCurrentDate();
    formatDates();
    showWelcomeToast();
});

// Handle window resize
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        // Close sidebar on desktop view
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
        }
    }, 250);
});

// Prevent form submission on Enter in search
searchInput?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
    }
});

// Add keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Alt + N for notifications
    if (e.altKey && e.key === 'n') {
        e.preventDefault();
        notificationPanel.classList.toggle('active');
    }
    
    // Alt + S for search
    if (e.altKey && e.key === 's') {
        e.preventDefault();
        searchInput?.focus();
    }
    
    // Escape to close panels
    if (e.key === 'Escape') {
        notificationPanel.classList.remove('active');
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('active');
        }
    }
});

// Log activity (for analytics)
function logActivity(action) {
    console.log(`User action: ${action} at ${new Date().toISOString()}`);
    // In production, send this to analytics server
}

// Track user interactions
document.addEventListener('click', (e) => {
    if (e.target.closest('.download-btn')) {
        logActivity('Download material');
    } else if (e.target.closest('.exam-item')) {
        logActivity('View exam details');
    } else if (e.target.closest('.notification-item')) {
        logActivity('Read notification');
    }
});

console.log('Student Dashboard loaded successfully! ✨');