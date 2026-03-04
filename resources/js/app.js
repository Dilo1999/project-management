import './bootstrap';

// Lightweight sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('[data-sidebar]');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    const toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
    const closeButtons = document.querySelectorAll('[data-sidebar-close]');

    if (!sidebar) {
        return;
    }

    const openSidebar = () => {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        if (backdrop) {
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100');
        }
        toggleButtons.forEach(btn => btn.setAttribute('aria-expanded', 'true'));
        document.body.classList.add('overflow-hidden', 'md:overflow-auto');
    };

    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        if (backdrop) {
            backdrop.classList.add('opacity-0');
            backdrop.classList.add('pointer-events-none');
        }
        toggleButtons.forEach(btn => btn.setAttribute('aria-expanded', 'false'));
        document.body.classList.remove('overflow-hidden');
    };

    toggleButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const isOpen = sidebar.classList.contains('translate-x-0') && !sidebar.classList.contains('-translate-x-full');
            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    });

    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            closeSidebar();
        });
    });

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            closeSidebar();
        });
    }

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });
});
