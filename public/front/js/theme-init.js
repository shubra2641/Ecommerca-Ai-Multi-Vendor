// Advanced theme initialization: sync html & body data-theme early.
(function () {
    try {
        var stored = localStorage.getItem('theme');
        var systemPref = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        var theme = stored || systemPref;
        document.documentElement.setAttribute('data-theme', theme);
        function applyBody()
        {
            document.body && document.body.setAttribute('data-theme', theme); }
        if (document.body) {
            applyBody(); } else {
            document.addEventListener('DOMContentLoaded', applyBody);
            }
            window.__applyTheme = function (next) {
                theme = next || (document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
                document.documentElement.setAttribute('data-theme', theme);
                if (document.body) {
                    document.body.setAttribute('data-theme', theme);
                }
                try {
                    localStorage.setItem('theme', theme); } catch (e) {
                    }
                    var ev; try {
                        ev = new CustomEvent('themechange',{detail:{theme}}); } catch (e) {
                              ev = document.createEvent('CustomEvent'); ev.initCustomEvent('themechange',true,true,{theme}); }
                        window.dispatchEvent(ev);
            };
    } catch (e) {
/* silent */ }
})();

