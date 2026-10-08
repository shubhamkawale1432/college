/**
 * College Management System - Interactive Front-End Engine
 * Modern, Accessible, Mobile-First
 */

// Global Modal Handlers
window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('open');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Focus first input or close button
        const focusable = modal.querySelector('input:not([type=hidden]), select, textarea, button');
        if (focusable) {
            setTimeout(() => focusable.focus(), 50);
        }
    }
};

window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('open');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
};

// Global Toast System
window.showToast = function(message, type = 'success', duration = 4000) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.position = 'fixed';
        container.style.bottom = '24px';
        container.style.right = '24px';
        container.style.zIndex = '9999';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '10px';
        container.style.maxWidth = '360px';
        container.style.pointerEvents = 'none';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.margin = '0';
    toast.style.pointerEvents = 'auto';
    toast.style.boxShadow = 'var(--shadow-lg)';
    toast.style.borderRadius = 'var(--radius-md)';
    toast.style.animation = 'slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1)';

    const iconClass = type === 'success' ? 'fa-circle-check' :
                      type === 'error' || type === 'danger' ? 'fa-triangle-exclamation' :
                      type === 'warning' ? 'fa-circle-exclamation' : 'fa-circle-info';

    toast.innerHTML = `<i class="fa-solid ${iconClass}"></i><div>${message}</div>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, duration);
};

document.addEventListener('DOMContentLoaded', () => {
    // 1. Theme Management (Dark / Light Mode)
    const storedTheme = localStorage.getItem('cms_theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const activeTheme = storedTheme || (prefersDark ? 'dark' : 'light');

    document.documentElement.setAttribute('data-theme', activeTheme);

    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const updateThemeIcon = (theme) => {
        if (!themeToggleBtn) return;
        const icon = themeToggleBtn.querySelector('i');
        if (icon) {
            if (theme === 'dark') {
                icon.className = 'fa-solid fa-sun';
                themeToggleBtn.setAttribute('title', 'Switch to Light Mode');
                themeToggleBtn.setAttribute('aria-label', 'Switch to Light Mode');
            } else {
                icon.className = 'fa-solid fa-moon';
                themeToggleBtn.setAttribute('title', 'Switch to Dark Mode');
                themeToggleBtn.setAttribute('aria-label', 'Switch to Dark Mode');
            }
        }
    };

    updateThemeIcon(activeTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-theme') || 'light';
            const nextTheme = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', nextTheme);
            localStorage.setItem('cms_theme', nextTheme);
            updateThemeIcon(nextTheme);
        });
    }

    // 2. Mobile Sidebar & Backdrop Toggle
    const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
    const sidebar = document.querySelector('.app-sidebar');
    let backdrop = document.getElementById('sidebar-backdrop');

    if (!backdrop && sidebar) {
        backdrop = document.createElement('div');
        backdrop.id = 'sidebar-backdrop';
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
    }

    const closeSidebar = () => {
        if (sidebar) sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('active');
    };

    const openSidebar = () => {
        if (sidebar) sidebar.classList.add('open');
        if (backdrop) backdrop.classList.add('active');
    };

    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    // Close mobile sidebar on nav link click
    if (sidebar) {
        sidebar.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    closeSidebar();
                }
            });
        });
    }

    // 3. Declarative Modal Triggers & Esc Key
    document.querySelectorAll('[data-modal-target]').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = trigger.getAttribute('data-modal-target');
            window.openModal(targetId);
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal-overlay');
            if (modal) {
                window.closeModal(modal.id);
            }
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                window.closeModal(overlay.id);
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(m => {
                window.closeModal(m.id);
            });
            closeSidebar();
        }
    });

    // 4. Destructive Action Confirmation
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            const msg = el.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
            if (!confirm(msg)) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        });
    });

    // 5. Anti-Double-Submit on Forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (form.getAttribute('data-submitted') === 'true') {
                e.preventDefault();
                return;
            }
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !form.hasAttribute('data-no-disable')) {
                // Delay disabling slightly so FormData captures button value if needed
                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.7';
                }, 20);
            }
        });
    });
});
