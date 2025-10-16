document.addEventListener('DOMContentLoaded', function () {
    const badge = document.getElementById('vendorNotificationBadge');
    const menu = document.getElementById('vendorNotificationsMenu');
    const placeholder = menu ? menu.querySelector('.notifications-container') ? .parentElement || document.createElement('li') : null;
    if (!badge || !menu || !placeholder) {
        return; // nothing to do
    }

    function showBadge(count)
    {
        if (count > 0) {
            const txt = count > 99 ? '99+' : count;
            badge.textContent = txt;
            badge.style.display = 'inline-block';
            badge.classList.remove('envato-hidden');
            badge.setAttribute('aria-label', txt + ' ' + (window.__t ? (window.__t('notifications.title') || 'Notifications') : 'Notifications'));
        } else {
            badge.textContent = '';
            badge.style.display = 'none';
            badge.removeAttribute('aria-label');
        }
    }

    const t = (k, fallback) => (window.__t ? (window.__t(k) || window.__t(fallback) || fallback) : (fallback || k));

    async function preflightUnread()
    {
        try {
            const res = await fetch('/vendor/notifications/unread-count',{credentials:'same-origin'});
            const j = await res.json().catch(() => ({}));
            if (j.ok && typeof j.unread === 'number') {
                showBadge(j.unread);
            }
        } catch (e) {
        }
    }

    async function loadNotifications()
    {
        try {
            const res = await fetch('/vendor/notifications/latest', { credentials: 'same-origin' });
            if (!res.ok) {
                (placeholder.querySelector('.notifications-container') || placeholder).innerHTML = '<div class="px-3 py-2 text-muted">' + t('notifications.failed','Failed') + '</div>';
                return;
            }
            const json = await res.json().catch(() => ({ ok:false }));
            if (!json.ok) {
                (placeholder.querySelector('.notifications-container') || placeholder).innerHTML = '<div class="px-3 py-2 text-muted">' + t('notifications.none','No notifications') + '</div>';
                badge.style.display = 'none'; badge.textContent = '';
                return;
            }
            const items = json.notifications || [];
            const unreadTotal = (typeof json.unread === 'number') ? json.unread : items.filter(i => !i.read_at).length;
            showBadge(unreadTotal);
            const containerLi = placeholder; // li element
            containerLi.innerHTML = '';
            if (items.length === 0) {
                containerLi.innerHTML = '<div class="notifications-container px-3 py-2 text-muted">' + t('notifications.none','No notifications') + '</div>';
                return;
            }
            items.forEach(n => {
                const a = document.createElement('a');
                a.className = 'dropdown-item d-flex align-items-start';
                a.href = n.data ? .url || '#';
                a.dataset.notificationId = n.id;
                const icon = document.createElement('div'); icon.className = 'me-2'; icon.innerHTML = '<i class="fas fa-' + (n.data ? .icon || 'bell') + ' fa-lg text-primary"></i>';
                const body = document.createElement('div'); body.style.flex = '1';
                const title = document.createElement('div'); title.className = 'fw-semibold';
                function humanize(t)
                {
                    return (t || '').replace(/_/g,' ').replace(/\b\w/g,c => c.toUpperCase()); }
                title.textContent = n.data ? .title || humanize(n.data ? .type) || 'Notification';
                const subtitle = document.createElement('div'); subtitle.className = 'small text-muted'; subtitle.textContent = n.data ? .message || n.data ? .text || '';
                const ts = document.createElement('div'); ts.className = 'small text-muted ms-2'; ts.textContent = n.created_at;
                body.appendChild(title); body.appendChild(subtitle);
                a.appendChild(icon); a.appendChild(body); a.appendChild(ts);
                a.addEventListener('click', async function (ev) {
                    ev.preventDefault();
                    const id = this.dataset.notificationId;
                    try {
                        await fetch('/vendor/notifications/' + encodeURIComponent(id) + '/read',{method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content') || ''}});
                        if (window.notify ? .success) {
                            window.notify.success(t('notifications.marked_read','Marked read'));
                        }
                    } catch (e) {
                        if (window.notify ? .error) {
                            window.notify.error(t('notifications.failed','Failed'));
                        } }
                    // optimistic update
                    if (!this.classList.contains('text-muted')) {
                        this.classList.add('text-muted');
                    }
                    const current = parseInt((badge.textContent || '0').replace('+',''),10) || 0; const next = Math.max(0,current - 1); showBadge(next);
                    const url = this.getAttribute('href'); if (url && url !== '#') {
                        window.location.href = url;
                    }
                });
                containerLi.appendChild(a);
            });
        } catch (e) {
            (placeholder.querySelector('.notifications-container') || placeholder).innerHTML = '<div class="px-3 py-2 text-muted">' + t('notifications.failed','Failed') + '</div>';
        }
    }

    window.refreshVendorNotifications = loadNotifications;
    const markAllBtn = document.getElementById('vendorMarkAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', async function (ev) {
            ev.preventDefault();
            try {
                const res = await fetch('/vendor/notifications/mark-all-read',{method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content') || ''}});
                if (res.ok) {
                    showBadge(0); if (window.notify ? .success) {
                        window.notify.success(t('notifications.all_marked_read','All marked read'));
                    } }
            } catch (e) {
            }
            loadNotifications();
        });
    }
    preflightUnread().finally(() => loadNotifications());
    let serverSuggested = null; try {
        serverSuggested = parseInt(window.NOTIFICATIONS_POLL_INTERVAL_MS,10);} catch (e) {
        }
    // Automatic notifications polling removed by policy. Only initial load will occur.
});
