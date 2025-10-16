// Lightweight slider enhancement (no dependency). Degrades to native horizontal scroll.
(function () {
    const track = document.querySelector('[data-hero-slider-track]');
    if (!track) {
        return;
    }
    const slides = Array.from(track.children);
    if (slides.length <= 1) {
        return; // no need for controls
    }
    const prevBtn = document.querySelector('[data-hero-prev]');
    const nextBtn = document.querySelector('[data-hero-next]');
    const dotsWrap = document.querySelector('[data-hero-dots]');
    let index = 0; let autoplayTimer; const interval = 6000; let userInteracted = false;
    function apply()
    {
        const offset = slides[index].offsetLeft; track.scrollTo({left: offset, behavior:'smooth'});
        dotsWrap.querySelectorAll('button').forEach((b,i) => b.setAttribute('aria-current', i === index ? 'true' : 'false'));
        prevBtn.disabled = index === 0; nextBtn.disabled = index === slides.length - 1;
    }
    function go(dir)
    {
        index = (index + dir + slides.length) % slides.length; apply(); }
    function goTo(i)
    {
        index = i; apply(); }
    function startAutoplay()
    {
        if (userInteracted) {
            return;
        } stopAutoplay(); autoplayTimer = setInterval(() => { go(1); }, interval); }
    function stopAutoplay()
    {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
        } }
  // Build dots
    dotsWrap.hidden = false; prevBtn.hidden = false; nextBtn.hidden = false;
    slides.forEach((_,i) => { const b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', (i + 1) + ' / ' + slides.length); b.addEventListener('click', () => { userInteracted = true; goTo(i);}); dotsWrap.appendChild(b); });
    prevBtn.addEventListener('click', () => { userInteracted = true; go(-1); });
    nextBtn.addEventListener('click', () => { userInteracted = true; go(1); });
    track.addEventListener('pointerdown', () => { userInteracted = true; stopAutoplay(); });
  // Update index on manual scroll (snap detection)
    let scrollDebounce; track.addEventListener('scroll', () => { if (scrollDebounce) {
            cancelAnimationFrame(scrollDebounce);
    } scrollDebounce = requestAnimationFrame(() => { const pos = track.scrollLeft; let nearest = 0; let min = Infinity; slides.forEach((s,i) => { const d = Math.abs(s.offsetLeft - pos); if (d < min) {
            min = d; nearest = i; } }); index = nearest; apply(); }); });
    apply(); startAutoplay();
    document.addEventListener('visibilitychange', () => { if (document.hidden) {
            stopAutoplay(); } else {
        startAutoplay();
            } });
})();

