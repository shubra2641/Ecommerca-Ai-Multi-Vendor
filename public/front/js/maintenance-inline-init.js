// Extracted maintenance label assignment
(function () {
    var c = document.getElementById('countdown');
    if (c && c.dataset && !c.dataset.labelSoon && c.getAttribute('data-label-soon')) {
      // Already set by server; no action required
        return;
    }
})();

