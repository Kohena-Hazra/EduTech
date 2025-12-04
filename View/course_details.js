// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Play button animation
const playButton = document.querySelector('.play-button');
if (playButton) {
    playButton.addEventListener('click', function() {
        this.style.transform = 'translate(-50%, -50%) scale(0.9)';
        setTimeout(() => {
            this.style.transform = 'translate(-50%, -50%) scale(1)';
            alert('Preview video would play here!');
        }, 200);
    });
}


// Curriculum items expand animation
const curriculumItems = document.querySelectorAll('.curriculum-item');
curriculumItems.forEach((item, index) => {
    item.style.opacity = '0';
    item.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        item.style.transition = 'all 0.5s ease';
        item.style.opacity = '1';
        item.style.transform = 'translateY(0)';
    }, index * 100);
});

// Register button click tracking
const registerBtn = document.querySelector('.btn-register');
if (registerBtn) {
    registerBtn.addEventListener('click', function(e) {
        // Add a loading animation
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirecting...';
        this.style.pointerEvents = 'none';
        
        // Log the action (in production, send to analytics)
        console.log('User clicked register for course');
        
        // Allow the link to proceed after a brief delay
        setTimeout(() => {
            this.innerHTML = originalText;
            this.style.pointerEvents = 'auto';
        }, 1000);
    });
}

// Sticky sidebar behavior
function checkSidebarSticky() {
    const sidebar = document.querySelector('.course-sidebar');
    const content = document.querySelector('.course-content');
    
    if (sidebar && content && window.innerWidth > 1024) {
        const sidebarHeight = sidebar.offsetHeight;
        const contentHeight = content.offsetHeight;
        
        if (sidebarHeight > contentHeight) {
            sidebar.style.position = 'relative';
            sidebar.style.top = '0';
        }
    }
}

window.addEventListener('load', checkSidebarSticky);
window.addEventListener('resize', checkSidebarSticky);

// Add animation to outcome items on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '0';
            entry.target.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                entry.target.style.transition = 'all 0.5s ease';
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateX(0)';
            }, 100);
            
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('.outcome-item').forEach(item => {
    observer.observe(item);
});

// Highlight active section in navigation (if you add internal navigation)
window.addEventListener('scroll', () => {
    const sections = document.querySelectorAll('.content-section');
    const scrollPos = window.scrollY + 150;
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        
        if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
            section.style.borderLeft = '4px solid var(--primary)';
            section.style.paddingLeft = '36px';
        } else {
            section.style.borderLeft = 'none';
            section.style.paddingLeft = '40px';
        }
    });
});

// Add hover effect to curriculum items
curriculumItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.querySelector('.module-number').style.transform = 'scale(1.1) rotate(5deg)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.querySelector('.module-number').style.transform = 'scale(1) rotate(0deg)';
    });
});

// Price section animation
const priceSection = document.querySelector('.price-section');
if (priceSection) {
    const priceValue = priceSection.querySelector('h2');
    if (priceValue) {
        priceValue.style.opacity = '0';
        priceValue.style.transform = 'scale(0.5)';
        
        setTimeout(() => {
            priceValue.style.transition = 'all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            priceValue.style.opacity = '1';
            priceValue.style.transform = 'scale(1)';
        }, 300);
    }
}

// Course includes items animation
const includesItems = document.querySelectorAll('.course-includes li');
includesItems.forEach((item, index) => {
    item.style.opacity = '0';
    item.style.transform = 'translateX(-10px)';
    
    setTimeout(() => {
        item.style.transition = 'all 0.3s ease';
        item.style.opacity = '1';
        item.style.transform = 'translateX(0)';
    }, 500 + (index * 50));
});

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

// Add ripple CSS
const style = document.createElement('style');
style.textContent = `
    .btn-register, .share-btn {
        position: relative;
        overflow: hidden;
    }
    
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
    
    .module-number {
        transition: all 0.3s ease;
    }
`;
document.head.appendChild(style);

registerBtn?.addEventListener('click', createRipple);
shareButtons.forEach(btn => btn.addEventListener('click', createRipple));

// Print course details
function printCourse() {
    window.print();
}

// Add keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + P for printing
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printCourse();
    }
    
    // Escape to scroll to top
    if (e.key === 'Escape') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

// Track time spent on page (for analytics)
let timeOnPage = 0;
const trackingInterval = setInterval(() => {
    timeOnPage++;
    // In production, send this data to your analytics service
    if (timeOnPage % 30 === 0) { // Log every 30 seconds
        console.log(`User has been on page for ${timeOnPage} seconds`);
    }
}, 1000);

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    clearInterval(trackingInterval);
    console.log(`Total time on page: ${timeOnPage} seconds`);
});

// Mobile menu toggle (if needed)
const createMobileMenu = () => {
    if (window.innerWidth <= 768) {
        const navMenu = document.querySelector('.nav-menu');
        if (navMenu && navMenu.style.display !== 'none') {
            const menuToggle = document.createElement('button');
            menuToggle.className = 'mobile-menu-toggle';
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            menuToggle.style.cssText = 'display: block; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--primary);';
            
            const navContainer = document.querySelector('.nav-container');
            navContainer.insertBefore(menuToggle, navMenu);
            
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
            });
        }
    }
};

window.addEventListener('load', createMobileMenu);
window.addEventListener('resize', createMobileMenu);

console.log('Course Details page loaded successfully! ✨');