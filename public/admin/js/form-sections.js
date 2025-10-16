// Unified form sections controller with localStorage persistence & animation
(function () {
    const STORAGE_KEY_PREFIX = 'formSectionsState:';
    function getStorageKey() {
        return STORAGE_KEY_PREFIX + (window.location.pathname || '');
    }
    function loadState() {
        try {
            return JSON.parse(localStorage.getItem(getStorageKey()) || '{}');
        } catch (e) {
            return {};
        }
    }
    function saveState(state) {
        try {
            localStorage.setItem(getStorageKey(), JSON.stringify(state));
        } catch (e) {
        }
    }
    function updateAria(section, collapsed) {
        const headerBtn = section.querySelector('[data-toggle-section]');
        const body = section.querySelector('.inner-section-body');
        if (headerBtn) {
            headerBtn.setAttribute('aria-expanded', (!collapsed).toString());
        }
        if (body) {
            body.setAttribute('aria-hidden', collapsed.toString());
        }
    }
    function toggleSection(section, toState) {
        const body = section.querySelector('.inner-section-body');
        if (!body) {
            return;
        }
        const willCollapse = (typeof toState === 'boolean') ? toState : !section.classList.contains('collapsed');
        if (willCollapse) {
            const h = body.scrollHeight;
            body.style.maxHeight = h + 'px';
            requestAnimationFrame(() => {
                body.style.maxHeight = '0px';
                section.classList.add('collapsing-out');
                section.classList.add('collapsed');
                updateAria(section, true);
            });
        } else {
            section.classList.remove('collapsed');
            const h = body.scrollHeight;
            body.style.maxHeight = '0px';
            requestAnimationFrame(() => {
                body.style.maxHeight = h + 'px';
                section.classList.add('collapsing-in');
                updateAria(section, false);
            });
        }
    }
    function finalizeTransition(e) {
        if (e.propertyName !== 'max-height') {
            return;
        }
        const body = e.target;
        const section = body.closest('.inner-section');
        body.style.maxHeight = section.classList.contains('collapsed') ? '0px' : body.scrollHeight + 'px';
        section.classList.remove('collapsing-out', 'collapsing-in');
        if (!section.classList.contains('collapsed')) {
            setTimeout(() => { body.style.maxHeight = 'none'; }, 150);
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        const sections = Array.from(document.querySelectorAll('.inner-section[data-section]'));
        if (!sections.length) {
            return;
        }
        const state = loadState();
        sections.forEach((sec, idx) => {
            const id = sec.dataset.sectionId || idx;
            sec.dataset.sectionId = id;
            const body = sec.querySelector('.inner-section-body');
            if (body) {
                body.addEventListener('transitionend', finalizeTransition);
                if (!body.id) {
                    body.id = 'innerSectionBody_' + id;
                }
                body.setAttribute('role', 'region');
                body.setAttribute('aria-labelledby', 'innerSectionHeader_' + id);
            }
            const header = sec.querySelector('[data-toggle-section]');
            if (header) {
                if (!header.id) {
                    header.id = 'innerSectionHeader_' + id;
                }
                header.setAttribute('role', 'button');
                header.setAttribute('tabindex', '0');
                header.setAttribute('aria-controls', body ? body.id : '');
            }
            if (state[id] === 'collapsed') {
                sec.classList.add('collapsed');
                if (body) {
                    body.style.maxHeight = '0px';
                }
                sec.querySelector('.section-caret')?.classList.add('fa-rotate-180');
                updateAria(sec, true);
            } else {
                if (body) {
                    body.style.maxHeight = 'none';
                }
                updateAria(sec, false);
            }
            function activateToggle(evt) {
                if (evt.type === 'keydown' && !['Enter', ' '].includes(evt.key)) {
                    return;
                }
                evt.preventDefault();
                const collapsing = !sec.classList.contains('collapsed');
                toggleSection(sec, collapsing);
                const caret = sec.querySelector('.section-caret');
                caret && caret.classList.toggle('fa-rotate-180');
                state[id] = collapsing ? 'collapsed' : 'expanded';
                saveState(state);
            }
            header && header.addEventListener('click', activateToggle);
            header && header.addEventListener('keydown', activateToggle);
        });
        const collapseAllBtn = document.querySelector('[data-collapse-all]');
        const expandAllBtn = document.querySelector('[data-expand-all]');
        collapseAllBtn && collapseAllBtn.addEventListener('click', () => {
            sections.forEach(sec => {
                if (!sec.classList.contains('collapsed')) {
                    toggleSection(sec, true);
                }
                sec.querySelector('.section-caret')?.classList.add('fa-rotate-180');
                state[sec.dataset.sectionId] = 'collapsed';
            });
            saveState(state);
        });
        expandAllBtn && expandAllBtn.addEventListener('click', () => {
            sections.forEach(sec => {
                if (sec.classList.contains('collapsed')) {
                    toggleSection(sec, false);
                }
                sec.querySelector('.section-caret')?.classList.remove('fa-rotate-180');
                state[sec.dataset.sectionId] = 'expanded';
            });
            saveState(state);
        });
        window.addEventListener('resize', () => {
            sections.forEach(sec => {
                if (!sec.classList.contains('collapsed')) {
                    const body = sec.querySelector('.inner-section-body');
                    if (body && body.style.maxHeight !== 'none') {
                        body.style.maxHeight = body.scrollHeight + 'px';
                    }
                }
            });
        });
    });
})();
