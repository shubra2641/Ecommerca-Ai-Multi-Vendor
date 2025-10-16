// Global refinements JS (auto-upgrade tables & metrics)
(function () {
    const THEME_KEY = 'adminTheme';
    const SECTION_PREFIX = 'formSectionsState:'; // used in form-sections.js

    function applyStoredTheme() {
        try {
            const stored = localStorage.getItem(THEME_KEY);
            if (stored === 'dark') {
                document.body.classList.add('dark-mode');
            }
        } catch (e) {
        }
    }
    function toggleTheme() {
        document.body.classList.toggle('dark-mode');
        const mode = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
        try {
            localStorage.setItem(THEME_KEY, mode);
        } catch (e) {
        }
        updateThemeButton();
        // Dispatch a custom event so other modules (charts, etc.) can react
        document.dispatchEvent(new CustomEvent('admin:theme-changed', { detail: { mode } }));
    }
    function updateThemeButton() {
        const btn = document.getElementById('themeToggleBtn');
        if (!btn) {
            return;
        }
        const icon = btn.querySelector('i');
        if (document.body.classList.contains('dark-mode')) {
            icon.className = 'fas fa-sun';
            btn.title = btn.getAttribute('data-light-label') || 'Light Mode';
        } else {
            icon.className = 'fas fa-moon';
            btn.title = btn.getAttribute('data-dark-label') || 'Dark Mode';
        }
    }
    function injectHeaderButtons() {
        const headerRight = document.querySelector('.top-header .header-right');
        if (!headerRight) {
            return;
        }
        if (!document.getElementById('themeToggleBtn')) {
            const themeBtnWrapper = document.createElement('div');
            themeBtnWrapper.className = 'header-item';
            const themeBtn = document.createElement('button');
            themeBtn.className = 'header-btn';
            themeBtn.id = 'themeToggleBtn';
            themeBtn.setAttribute('data-dark-label', 'Dark Mode');
            themeBtn.setAttribute('data-light-label', 'Light Mode');
            const themeIcon = document.createElement('i');
            themeIcon.className = 'fas fa-moon';
            themeBtn.appendChild(themeIcon);
            themeBtnWrapper.appendChild(themeBtn);
            headerRight.prepend(themeBtnWrapper);
        }
        if (!document.getElementById('resetLayoutBtn')) {
            const resetWrapper = document.createElement('div');
            resetWrapper.className = 'header-item';
            const resetBtn = document.createElement('button');
            resetBtn.className = 'header-btn';
            resetBtn.id = 'resetLayoutBtn';
            resetBtn.title = 'Reset Layout';
            const resetIcon = document.createElement('i');
            resetIcon.className = 'fas fa-undo';
            resetBtn.appendChild(resetIcon);
            resetWrapper.appendChild(resetBtn);
            headerRight.prepend(resetWrapper);
        }
        document.getElementById('themeToggleBtn')?.addEventListener('click', toggleTheme);
        document.getElementById('resetLayoutBtn')?.addEventListener('click', () => {
            try {
                // Remove all section states
                Object.keys(localStorage).filter(k => k.startsWith(SECTION_PREFIX)).forEach(k => localStorage.removeItem(k));
                // Reset theme
                localStorage.removeItem(THEME_KEY);
            } catch (e) {
            }
            location.reload();
        });
        updateThemeButton();
    }
    function enhanceTables() {
        document.querySelectorAll('table.table').forEach(tbl => {
            if (tbl.querySelector('thead.table-light')) {
                tbl.classList.add('table-modern');
            }
            // Auto wrap for horizontal scroll if not already
            if (window.innerWidth < 992 && !tbl.closest('.rf-table-wrapper')) {
                const wrap = document.createElement('div');
                wrap.className = 'rf-table-wrapper';
                tbl.parentNode.insertBefore(wrap, tbl);
                wrap.appendChild(tbl);
            }
            // Phase 2: add stacking support if marked or small screen
            const stackingOptIn = tbl.dataset.stack === 'enable' || tbl.classList.contains('rf-stack-opt');
            if (window.innerWidth < 576 && stackingOptIn) {
                // Add rf-stack-table class
                if (!tbl.classList.contains('rf-stack-table')) {
                    tbl.classList.add('rf-stack-table');
                }
                const headers = Array.from(tbl.querySelectorAll('thead th')).map(th => th.innerText.trim());
                // Map header cells by index
                tbl.querySelectorAll('tbody tr').forEach(row => {
                    const cells = row.children;
                    Array.from(cells).forEach((td, idx) => {
                        if (!td.hasAttribute('data-label')) {
                            td.setAttribute('data-label', headers[idx] || '');
                        }
                    });
                });
            } else if (window.innerWidth >= 576) {
                // Remove stacking class on resize up
                tbl.classList.remove('rf-stack-table');
            }
            // Condensed style on very small screens
            if (window.innerWidth < 480) {
                tbl.classList.add('table-condensed-sm');
            } else {
                tbl.classList.remove('table-condensed-sm');
            }
            // Accessibility roles
            if (!tbl.hasAttribute('role')) {
                tbl.setAttribute('role', 'table');
            }
            const thead = tbl.querySelector('thead'); if (thead) {
                thead.setAttribute('role', 'rowgroup');
            }
            tbl.querySelectorAll('thead tr').forEach(tr => tr.setAttribute('role', 'row'));
            tbl.querySelectorAll('thead th').forEach(th => { th.setAttribute('role', 'columnheader'); th.setAttribute('scope', 'col'); });
            tbl.querySelectorAll('tbody tr').forEach(tr => tr.setAttribute('role', 'row'));
            tbl.querySelectorAll('tbody td').forEach(td => {
                if (!td.hasAttribute('role')) {
                    td.setAttribute('role', 'cell');
                }
            });
        });
        document.querySelectorAll('.modern-card table.table-modern tbody tr').forEach(tr => {
            tr.addEventListener('mouseenter', () => tr.classList.add('row-hover'));
            tr.addEventListener('mouseleave', () => tr.classList.remove('row-hover'));
        });
    }
    function normalizeHeaderButtons() {
        document.querySelectorAll('.row.align-items-center .btn').forEach(btn => btn.classList.add('align-middle'));
    }
    function rtlCaretAdjustment() {
        if (document.documentElement.getAttribute('dir') === 'rtl') {
            // Ensure existing collapsed carets respect custom rotation rule
            document.querySelectorAll('.inner-section.collapsed .section-caret').forEach(c => {
                // Our CSS handles rotation inversion; no extra JS needed beyond ensuring class is present
                if (!c.classList.contains('fa-rotate-180')) {
                    c.classList.add('fa-rotate-180');
                }
            });
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        applyStoredTheme();
        injectHeaderButtons();
        enhanceTables();
        normalizeHeaderButtons();
        rtlCaretAdjustment();

        // Mobile sidebar toggle logic reuse existing buttons if present
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mobileToggle = document.getElementById('mobileMenuToggle');
        function closeSidebar() {
            sidebar?.classList.remove('show-mobile'); overlay?.classList.remove('active'); document.body.classList.remove('sidebar-open');
        }
        function openSidebar() {
            sidebar?.classList.add('show-mobile'); overlay?.classList.add('active'); document.body.classList.add('sidebar-open');
        }
        mobileToggle?.addEventListener('click', () => {
            if (sidebar?.classList.contains('show-mobile')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
        overlay?.addEventListener('click', closeSidebar);
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });
        // Re-run table enhancements on resize debounced
        let __resizeTO; window.addEventListener('resize', () => { clearTimeout(__resizeTO); __resizeTO = setTimeout(enhanceTables, 200); });

        // Phase 4: dynamic header item visibility based on available width
        function adjustHeaderItems() {
            const headerRight = document.querySelector('.top-header .header-right');
            if (!headerRight) {
                return;
            }
            const items = Array.from(headerRight.querySelectorAll('.header-item'));
            // Show all first
            items.forEach(i => i.classList.remove('rf-hide'));
            let totalWidth = 0; const max = headerRight.clientWidth - 40; // reserve some space
            for (let i = 0; i < items.length; i++) {
                totalWidth += items[i].offsetWidth;
                if (totalWidth > max && window.innerWidth < 768) {
                    // hide remaining
                    for (let j = i; j < items.length; j++) {
                        items[j].classList.add('rf-hide');
                    }
                    break;
                }
            }
        }
        adjustHeaderItems();
        window.addEventListener('resize', () => adjustHeaderItems());
    });
})();
