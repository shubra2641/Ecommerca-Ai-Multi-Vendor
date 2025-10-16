// checkout-pattern-sanitizer.js
// External sanitizer to validate data-pattern attributes and set the pattern
// attribute only if the regex compiles correctly. This avoids CSP issues with
// inline scripts and prevents browsers from compiling invalid patterns early.
(function () {
    'use strict';
    try {
        if (!document.querySelectorAll) {
            return;
        }

        // Helper: normalize a pattern string that may be in the form /.../flags
        function normalizePatternString(raw)
        {
            if (!raw || typeof raw !== 'string') {
                return null;
            }
            raw = raw.trim();
            if (raw.length > 1 && raw.charAt(0) === '/' && raw.lastIndexOf('/') > 0) {
                var last = raw.lastIndexOf('/');
                return raw.substring(1, last);
            }
            return raw;
        }

        // Validate a candidate pattern; return normalized pattern or null
        function validatePatternCandidate(candidate)
        {
            try {
                var raw = normalizePatternString(candidate);
                if (!raw) {
                    return null;
                }
                // Attempt to compile without flags to avoid invalid-flag errors
                new RegExp(raw);
                return raw;
            } catch (e) {
                return null;
            }
        }

        // 1) Handle inputs that used data-pattern (our preferred, CSP-safe approach)
        var dataInputs = document.querySelectorAll('input[data-pattern]');
        for (var i = 0; i < dataInputs.length; i++) {
            var inp = dataInputs[i];
            var p = inp.getAttribute('data-pattern');
            var ok = validatePatternCandidate(p);
            if (ok) {
                inp.setAttribute('pattern', ok);
            } else {
                // remove any pre-existing pattern if candidate invalid
                try {
                    inp.removeAttribute('pattern'); } catch (ee) {
                    }
                    if (window.console && console.warn) {
                        console.warn('checkout-pattern-sanitizer: removed invalid data-pattern on', inp, p);
                    }
            }
        }

        // 2) Defensive: also sanitize any existing input[pattern] attributes (other templates/plugins)
        var patternInputs = document.querySelectorAll('input[pattern]');
        for (var j = 0; j < patternInputs.length; j++) {
            var inp2 = patternInputs[j];
            var existing = inp2.getAttribute('pattern');
            var ok2 = validatePatternCandidate(existing);
            if (!ok2) {
                try {
                    inp2.removeAttribute('pattern'); } catch (ee) {
                    }
                    if (window.console && console.warn) {
                        console.warn('checkout-pattern-sanitizer: removed invalid pattern on', inp2, existing);
                    }
            } else if (ok2 !== existing) {
                // replace with normalized version (remove slashes/flags)
                inp2.setAttribute('pattern', ok2);
            }
        }
    } catch (e) {
/* no-op */ }
})();

