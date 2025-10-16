// pb-style-consolidate.js
// Consolidate duplicate dynamically generated page-builder row <style> tags.
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const styles = Array.from(document.querySelectorAll('style[data-generated="pb-row-dynamic"]'));
        if (styles.length <= 1) {
            return;
        }
        const seen = new Set();
        let combined = '';
        styles.forEach(s => {
            const css = s.textContent || '';
            if (!seen.has(css)) {
                combined += css; seen.add(css); }
        });
        const first = styles[0];
        first.textContent = combined;
        styles.slice(1).forEach(s => s.remove());
    });
})();

