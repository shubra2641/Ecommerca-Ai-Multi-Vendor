document.addEventListener('DOMContentLoaded', function () {
    const badge = document.getElementById('adminNotificationBadge');
    const menu = document.getElementById('adminNotificationsMenu');
    const placeholder = document.getElementById('adminNotificationsPlaceholder');
    if (!badge || !menu || !placeholder) {
        return; // markup missing
    }

    function showBadge(count) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'inline-block';
            badge.classList.remove('envato-hidden');
        } else {
            badge.textContent = '';
            badge.style.display = 'none';
        }
    }

    // build baseUrl from body[data-admin-base] to support subdirectory installs
    const _baseEl = document.querySelector('body') || document.documentElement;
    const _rawBase = (_baseEl && _baseEl.getAttribute) ? (_baseEl.getAttribute('data-admin-base') || '') : '';
    let _baseUrl = _rawBase.replace(/\/$/, '');
    if (!_baseUrl) {
        try {
            const l = window.location;
            const i = l.pathname.indexOf('/admin');
            const pre = i !== -1 ? l.pathname.slice(0, i) : '';
            _baseUrl = l.origin + pre;
        } catch (e) {
            _baseUrl = '';
        }
    }

    async function preflightUnread() {
        try {
            const url = (_baseUrl ? (_baseUrl + '/admin/notifications/unread-count') : '/admin/notifications/unread-count');
            const res = await fetch(url, { credentials: 'same-origin' });
            if (!res.ok) {
                return;
            }
            const j = await res.json().catch(() => ({}));
            if (j && typeof j.unread === 'number') {
                showBadge(j.unread);
            }
        } catch (e) {
            /* ignore network error */
        }
    }

    async function loadNotifications() {
        try {
            const res = await fetch((_baseUrl ? (_baseUrl + '/admin/notifications/latest') : '/admin/notifications/latest'), { credentials: 'same-origin' });
            if (!res.ok) {
                // don't try to parse non-OK responses as JSON (could be HTML error page)
                placeholder.innerHTML = '<div class="px-3 py-2 text-muted">Could not load (status: ' + res.status + ')</div>';
                return;
            }
            let json;
            try {
                json = await res.json();
            } catch (e) {
                // invalid JSON (e.g., HTML error page) — show friendly message and abort
                placeholder.innerHTML = '<div class="px-3 py-2 text-muted">Could not parse response</div>';
                return;
            }
            if (!json.ok) {
                placeholder.innerHTML = '<div class="px-3 py-2 text-muted">No notifications</div>';
                return;
            }
            const items = json.notifications || [];
            const unread = (json.unread ?? items.filter(i => !i.read_at).length) || 0;
            showBadge(unread);
            if (items.length === 0) {
                placeholder.innerHTML = '<div class="px-3 py-2 text-muted">' + ((window.__t && typeof window.__t === 'function') ? window.__t('No notifications') : 'No notifications') + '</div>';
                return;
            }
            placeholder.innerHTML = '';
            items.forEach(n => {
                const a = document.createElement('a');
                a.className = 'dropdown-item d-flex align-items-start';
                a.href = n.data.url || '#';
                a.dataset.notificationId = n.id;
                // structured content: icon, title + subtitle, timestamp
                const icon = document.createElement('div');
                icon.className = 'me-2';
                icon.innerHTML = ` < i class = "fas fa-${n.data.icon ?? 'bell'} fa-lg text-primary" > < / i > `;
                const body = document.createElement('div');
                body.style.flex = '1';
                const title = document.createElement('div');
                title.className = 'fw-semibold';
                // fallback: server may not have provided title for older notifications
                function humanizeType(t) {
                    if (!t) {
                        return '';
                    }
                    return t.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                }
                const titleText = (n.data?.title) || ((typeof window.__t === 'function') ? (window.__t(n.data?.type || '') || '') : '') || humanizeType(n.data?.type) || (n.data?.type || 'Notification');
                title.textContent = titleText;
                const subtitle = document.createElement('div');
                subtitle.className = 'small text-muted';
                const subtitleText = n.data?.message || n.data?.text || '';
                subtitle.textContent = subtitleText;
                const ts = document.createElement('div');
                ts.className = 'small text-muted ms-2';
                ts.textContent = n.created_at;

                body.appendChild(title);
                body.appendChild(subtitle);
                a.appendChild(icon);
                a.appendChild(body);
                a.appendChild(ts);

                // mark-as-read on click, then follow link
                a.addEventListener('click', async function (ev) {
                    ev.preventDefault();
                    const id = this.dataset.notificationId;
                    let ok = false; let msg = '';
                    try {
                        const res = await fetch((_baseUrl ? (_baseUrl + '/admin/notifications/' + encodeURIComponent(id) + '/read') : ('/admin/notifications/' + encodeURIComponent(id) + '/read')), {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || '', 'Accept': 'application/json' },
                        });
                        const j = await res.json().catch(() => ({}));
                        ok = j.ok || res.ok;
                        msg = j.message || (ok ? ((typeof window.__t === 'function' && window.__t('Marked read')) || 'Marked read') : (j.error || ((typeof window.__t === 'function' && window.__t('Failed')) || 'Failed')));
                    } catch (e) {
                        msg = ((typeof window.__t === 'function') ? window.__t('Network error') : 'Network error');
                    }

                    // show toast feedback
                    try {
                        if (window.notify && typeof window.notify.success === 'function') {
                            if (ok) {
                                window.notify.success(msg);
                            } else {
                                window.notify.error(msg);
                            }
                        } else if (window.showToast) {
                            window.showToast(msg, ok ? 'success' : 'error');
                        } else {
                            try {
                                alert(msg);
                            } catch (e) {
                                /* ignore */
}
                        }
                    } catch (e) {
                        /* ignore */
}

                    // optimistically mark this item as read and update badge
                    try {
                        if (!this.classList.contains('text-muted')) {
                            this.classList.add('text-muted');
                        }
                        // decrement badge
                        try {
                            const current = parseInt((badge.textContent || '0').replace('+', ''), 10) || 0;
                            const next = Math.max(0, current - 1);
                            showBadge(next);
                        } catch (e) {
                            /* ignore */
}
                    } catch (e) {
                    }

                    // refresh list in background to ensure consistency, then navigate
                    loadNotifications().catch(() => { });
                    const url = this.getAttribute('href');
                    if (url && url !== '#') {
                        window.location.href = url;
                    }
                });

                placeholder.appendChild(a);
            });
        } catch (e) {
            placeholder.innerHTML = '<div class="px-3 py-2 text-muted">Could not load</div>';
        }
    }

    // expose refresh for other scripts to call after an action
    window.refreshAdminNotifications = loadNotifications;

    // mark all read button handler (if present)
    document.addEventListener('DOMContentLoaded', function () {
        const markAllBtn = document.getElementById('adminMarkAllReadBtn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', async function (ev) {
                ev.preventDefault();
                try {
                    const res = await fetch((_baseUrl ? (_baseUrl + '/admin/notifications/mark-all-read') : '/admin/notifications/mark-all-read'), {
                        method: 'POST', credentials: 'same-origin',
                        headers: { 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || '' }
                    });
                    const j = await res.json().catch(() => ({}));
                    if (j.ok || res.ok) {
                        if (window.notify && window.notify.success) {
                            window.notify.success((typeof window.__t === 'function') ? window.__t('All marked read') : 'All marked read');
                        } else if (window.showToast) {
                            window.showToast((typeof window.__t === 'function') ? window.__t('All marked read') : 'All marked read', 'success');
                        }
                        // hide badge and refresh
                        try {
                            showBadge(0);
                        } catch (e) {
                        }
                        loadNotifications().catch(() => { });
                    } else {
                        if (window.notify && window.notify.error) {
                            window.notify.error(j.message || ((typeof window.__t === 'function') ? window.__t('Failed') : 'Failed'));
                        } else if (window.showToast) {
                            window.showToast(j.message || ((typeof window.__t === 'function') ? window.__t('Failed') : 'Failed'), 'error');
                        }
                    }
                } catch (e) {
                    if (window.notify && window.notify.error) {
                        window.notify.error((typeof window.__t === 'function') ? window.__t('Network error') : 'Network error');
                    } else if (window.showToast) {
                        window.showToast((typeof window.__t === 'function') ? window.__t('Network error') : 'Network error', 'error');
                    }
                }
            });
        }
    });

    // initial unread preflight then load full list
    preflightUnread().finally(loadNotifications);
    let pollInterval = 30000;
    // allow server to provide interval on first successful load
    (async () => {
        try {
            const res = await fetch((_baseUrl ? (_baseUrl + '/admin/notifications/latest') : '/admin/notifications/latest'), { credentials: 'same-origin' });
            if (res.ok) {
                const j = await res.json().catch(() => null);
                if (j && j.poll_interval_ms) {
                    pollInterval = j.poll_interval_ms;
                }
            }
        } catch (e) {
        }
        // Automatic notifications polling removed by policy. Keeping only the initial load.
    })();
});
