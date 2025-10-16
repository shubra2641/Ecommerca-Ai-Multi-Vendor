/* Enhanced App Loader & Landing Animations Integration */
(function () {
    'use strict';
    const loader = document.getElementById('app-loader');
    if (!loader) {
        return;
    }

  // Inject progress bar element if missing
    let progress = loader.querySelector('.loader-progress');
    if (!progress) {
        progress = document.createElement('div');
        progress.className = 'loader-progress';
        progress.innerHTML = '<div class="loader-progress-bar" aria-hidden="true"></div>';
        loader.querySelector('.loader-core')?.appendChild(progress);
    }
    const bar = progress.querySelector('.loader-progress-bar');

  // Rough staged progress (simulated until window load) for better perceived performance
    let current = 0; let target = 55; // initial target before real load
    function tick()
    {
        current += (target - current) * 0.08;
        if (bar) {
            bar.style.width = current.toFixed(2) + '%'; }
        if (!window.__APP_FULLY_LOADED__) {
            requestAnimationFrame(tick); }
    }
    requestAnimationFrame(tick);

  // Escalate target values based on key milestones
    document.addEventListener('DOMContentLoaded', () => { target = 75; });
    window.addEventListener('load', () => {
        target = 100; window.__APP_FULLY_LOADED__ = true;
        if (bar) {
            bar.style.width = '100%'; }
        setTimeout(() => hideLoader(), 250); // slight delay to show completion
    });

    function hideLoader()
    {
        if (!loader || loader.classList.contains('hidden')) {
            return;
        }
        loader.classList.add('hidden'); loader.setAttribute('aria-hidden','true');
        setTimeout(() => { loader.remove(); }, 800);
    }

  // Fallback timeout (if load event delayed / blocked)
    setTimeout(() => { if (!window.__APP_FULLY_LOADED__) {
            target = 97; } }, 3500);
    setTimeout(() => { if (!window.__APP_FULLY_LOADED__) {
            hideLoader(); } }, 7000);

  // Image skeleton handling (products + blog thumbs)
    function markLoaded(img)
    {
        const wrapper = img.closest('.thumb-wrapper, .product-image');
        if (wrapper) {
            wrapper.classList.add('is-loaded'); }
    }
    document.addEventListener('load', (e) => {
        const t = e.target; if (t && t.tagName === 'IMG') {
            markLoaded(t); }
    }, true);

  // Intersection boost: apply animate-visible earlier when loader hides to avoid double fade
    const animated = Array.from(document.querySelectorAll('.animate-fade-in-up, .animate-fade-in-left, .animate-fade-in-right'));
    const earlyReveal = new IntersectionObserver(entries => {
        entries.forEach(entry => { if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible'); earlyReveal.unobserve(entry.target); } });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
    animated.forEach(el => earlyReveal.observe(el));

})();

