// Minimal JS for core interactions: mobile nav only (dark mode removed)
(function () {
    function $(sel)
    {
        return document.querySelector(sel) }
    function on(el, ev, fn)
    {
        if (el) {
            el.addEventListener(ev, fn) }
    }

    function initMobileNav()
    {
        const toggle = $('.nav-toggle');
        const mobile = document.getElementById('mobileNav');
        if (!toggle || !mobile) {
            return;
        }
        on(toggle, 'click', () => {
            const open = mobile.classList.toggle('show');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            document.body.style.overflow = open ? 'hidden' : '';
        });
        // close on escape
        on(document, 'keydown', (e) => { if (e.key === 'Escape') {
                mobile.classList.remove('show'); toggle.setAttribute('aria-expanded', 'false'); document.body.style.overflow = ''; } });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { initMobileNav(); }); } else {
        initMobileNav(); }
})();

// Simple Tabs controller: switch .tab-btn and .tab-pane, support hash deep-link and keyboard navigation
(function () {
    function qs(sel, ctx)
    {
        return (ctx || document).querySelector(sel) }
    function qsa(sel, ctx)
    {
        return Array.from((ctx || document).querySelectorAll(sel)) }

    function activateTab(name, updateHash = true)
    {
        qsa('.tab-btn').forEach(b => b.classList.toggle('active', b.dataset.tab === name));
        qsa('.tab-pane').forEach(p => p.classList.toggle('active', p.id === name));
        // update hash without scrolling (only when triggered by user interaction)
        if (updateHash && history.replaceState) {
            history.replaceState(null, '', '#' + name);
        }
        // lazy-load reviews (if panel contains data-src or is server-rendered but heavy)
        const pane = qs('#' + name);
        if (pane && pane.dataset.lazy === 'reviews' && !pane.dataset.loaded) {
            // fetch reviews fragment via AJAX as progressive enhancement
            const url = pane.dataset.src;
            if (url) {
                fetch(url, { credentials: 'same-origin' }).then(r => r.text()).then(html => { pane.innerHTML = html; pane.dataset.loaded = '1'; });
            }
        }
    }

    function bindTabs(container)
    {
        const btns = qsa('.tab-btn', container);
        btns.forEach(b => { b.addEventListener('click', () => activateTab(b.dataset.tab, true)); b.setAttribute('role', 'tab'); b.setAttribute('aria-controls', b.dataset.tab); b.setAttribute('tabindex', 0); });
        // keyboard support
        container.addEventListener('keydown', (e) => {
            if (!e.target.classList.contains('tab-btn')) {
                return;
            }
            const all = qsa('.tab-btn', container);
            const idx = all.indexOf(e.target);
            if (e.key === 'ArrowRight') {
                all[(idx + 1) % all.length].focus();
            }
            if (e.key === 'ArrowLeft') {
                all[(idx - 1 + all.length) % all.length].focus();
            }
            if (e.key === 'Home') {
                all[0].focus();
            }
            if (e.key === 'End') {
                all[all.length - 1].focus();
            }
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault(); e.target.click(); }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const nav = qs('.tab-nav');
        if (!nav) {
            return;
        }
        bindTabs(nav);
        // initial activation from hash or first
        const hash = (location.hash || '').replace('#', '');
        const initial = hash && qs('#' + hash) ? hash : (qs('.tab-btn') ? qs('.tab-btn').dataset.tab : null);
        if (initial) {
            activateTab(initial, false);
        }
    });
})();

// Reviews interactions: star rating selection
(function () {
    function qs(sel)
    {
        return document.querySelector(sel) }
    function qsa(sel)
    {
        return Array.from(document.querySelectorAll(sel)) }

    document.addEventListener('DOMContentLoaded', function () {
        // Star rating (if present)
        const stars = qsa('.star-btn');
        const ratingInput = qs('#ratingValue');
        if (stars.length && ratingInput) {
            stars.forEach(s => s.addEventListener('click', () => {
                const val = parseInt(s.dataset.rating, 10);
                ratingInput.value = val;
                stars.forEach(x => {
                    const rv = parseInt(x.dataset.rating, 10);
                    x.classList.toggle('active', rv <= val);
                });
            }));
        }

        // Gallery: scope thumbnails to each .thumbnail-gallery so other pages with .thumbnail are unaffected
        const galleries = Array.from(document.querySelectorAll('.thumbnail-gallery'));
        galleries.forEach(gallery => {
            const thumbs = Array.from(gallery.querySelectorAll('.thumbnail'));
            // try to find the main image within the same product-gallery container
            const productGallery = gallery.closest('.product-gallery') || document;
            const main = productGallery.querySelector('#mainImage') || productGallery.querySelector('.main-image');
            if (!thumbs.length || !main) {
                return;
            }
            thumbs.forEach(t => t.addEventListener('click', function (e) {
                e.preventDefault();
                const src = t.dataset.image;
                if (!src) {
                    return;
                }
                main.classList.add('image-placeholder');
                const img = new Image();
                img.onload = function () {
                    main.src = src; main.classList.remove('image-placeholder'); };
                img.src = src;
                // update active class only inside this gallery
                thumbs.forEach(x => x.classList.toggle('active', x === t));
            }));
        });

        // Lightbox: initialize once and reuse for any product gallery main image
        (function initLightbox()
        {
            let modal = document.querySelector('.lightbox-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.className = 'lightbox-modal';
                modal.innerHTML = ` < div class = "lightbox-inner" > < button class = "lightbox-close" aria - label = "Close" > ✕ < / button > < img src = "" alt = "Zoomed image" > < / div > `;
                document.body.appendChild(modal);
            }
            const modalImg = modal.querySelector('img');
            const closeBtn = modal.querySelector('.lightbox-close');

            function openLightbox(src)
            {
                modalImg.src = src; modal.classList.add('show'); document.body.style.overflow = 'hidden'; }
            function closeLightbox()
            {
                modal.classList.remove('show'); document.body.style.overflow = ''; }

            // attach click handler to every main image inside product-gallery
            const mains = Array.from(document.querySelectorAll('.product-gallery #mainImage, .product-gallery .main-image'));
            mains.forEach(main => {
                if (!main) {
                    return;
                }
                main.addEventListener('click', function () {
                    openLightbox(main.src || main.getAttribute('src') || '') });
                const zoomBtn = main.closest('.product-gallery') ? main.closest('.product-gallery').querySelector('#zoomBtn') : null;
            if (zoomBtn) {
                zoomBtn.addEventListener('click', function (e) {
                                e.preventDefault(); openLightbox(main.src || main.getAttribute('src') || ''); });
            }
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', closeLightbox);
            }
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeLightbox();
                } });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeLightbox();
                } });
        })();

        // File upload removed from review form; no client-side preview necessary.
    });
})();

