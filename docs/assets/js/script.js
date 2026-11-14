// Rainpedia - Global Script

document.addEventListener('DOMContentLoaded', () => {

    // =============================
    // Auto hide flash alerts
    // =============================
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';
            setTimeout(() => alert.remove(), 500);
        }, 4500);
    });

    // =============================
    // Mobile hamburger menu
    // =============================
    const hamburger = document.getElementById('navbar-hamburger');
    const mobileMenu = document.getElementById('navbar-mobile-menu');
    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            const isOpen = mobileMenu.classList.contains('open');
            hamburger.setAttribute('aria-expanded', isOpen);
        });
        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.remove('open');
            }
        });
    }

    // =============================
    // Confirm dialog on delete buttons
    // =============================
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm || 'Yakin?')) {
                e.preventDefault();
            }
        });
    });

    // =============================
    // Active nav link highlight
    // =============================
    const currentPath = window.location.pathname;
    document.querySelectorAll('.navbar-nav a').forEach(link => {
        if (link.getAttribute('href') && currentPath.endsWith(link.getAttribute('href').split('/').pop())) {
            link.classList.add('active');
        }
    });

});