<script>
    function toggleUserMenu(e) {
        if (e) e.stopPropagation();
        var dropdown = document.getElementById('userMenuDropdown');
        var trigger = document.getElementById('userMenuTrigger');
        if (!dropdown) return;
        var isOpen = dropdown.classList.contains('open');
        dropdown.classList.toggle('open', !isOpen);
        if (trigger) {
            trigger.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
        }
        var menu = document.getElementById('notificationMenu');
        if (menu) menu.style.display = 'none';
    }

    function toggleNotifications(e) {
        if (e) e.stopPropagation();
        var menu = document.getElementById('notificationMenu');
        if (!menu) return;
        var isOpen = menu.classList.contains('open');
        menu.classList.toggle('open', !isOpen);
        menu.style.display = isOpen ? 'none' : 'block';
        var dropdown = document.getElementById('userMenuDropdown');
        var trigger = document.getElementById('userMenuTrigger');
        if (dropdown) dropdown.classList.remove('open');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    }

    function openChangePasswordModal(event) {
        if (event) event.stopPropagation();
        var dropdown = document.getElementById('userMenuDropdown');
        var trigger = document.getElementById('userMenuTrigger');
        if (dropdown) dropdown.classList.remove('open');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');

        var modal = document.getElementById('changePasswordModal');
        var form = document.getElementById('changePasswordForm');
        var errorBox = document.getElementById('changePasswordError');
        if (!modal || !form) return;

        form.reset();
        if (errorBox) {
            errorBox.hidden = true;
            errorBox.textContent = '';
        }
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        setTimeout(function () {
            var input = document.getElementById('currentPasswordInput');
            if (input) input.focus();
        }, 50);
    }

    function closeChangePasswordModal() {
        var modal = document.getElementById('changePasswordModal');
        if (!modal) return;
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }

    (function initChangePasswordModal() {
        var form = document.getElementById('changePasswordForm');
        var modal = document.getElementById('changePasswordModal');
        var submitBtn = document.getElementById('changePasswordSubmit');
        if (!form || !modal) return;

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeChangePasswordModal();
            }
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            var errorBox = document.getElementById('changePasswordError');
            if (errorBox) {
                errorBox.hidden = true;
                errorBox.textContent = '';
            }

            if (submitBtn) submitBtn.disabled = true;

            fetch(@json(route('account.password.update')), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    current_password: form.current_password.value,
                    password: form.password.value,
                    password_confirmation: form.password_confirmation.value,
                }),
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        return { ok: response.ok, status: response.status, payload: payload };
                    });
                })
                .then(function (result) {
                    if (result.ok) {
                        closeChangePasswordModal();
                        window.alert(result.payload.message || 'تم تغيير كلمة المرور بنجاح.');
                        return;
                    }

                    var message = 'تعذر تغيير كلمة المرور.';
                    if (result.payload && result.payload.message) {
                        message = result.payload.message;
                    }
                    if (result.payload && result.payload.errors) {
                        var errors = result.payload.errors;
                        var firstKey = Object.keys(errors)[0];
                        if (firstKey && errors[firstKey] && errors[firstKey][0]) {
                            message = errors[firstKey][0];
                        }
                    }
                    if (errorBox) {
                        errorBox.textContent = message;
                        errorBox.hidden = false;
                    }
                })
                .catch(function () {
                    if (errorBox) {
                        errorBox.textContent = 'تعذر الاتصال بالخادم. حاول مرة أخرى.';
                        errorBox.hidden = false;
                    }
                })
                .finally(function () {
                    if (submitBtn) submitBtn.disabled = false;
                });
        });
    })();

    window.addEventListener('click', function (e) {
        var notifBtn = document.getElementById('notificationBtn');
        var notifMenu = document.getElementById('notificationMenu');
        if (notifBtn && notifMenu && !notifBtn.contains(e.target) && !notifMenu.contains(e.target)) {
            notifMenu.classList.remove('open');
            notifMenu.style.display = 'none';
        }

        var userMenu = document.getElementById('topbarUserMenu');
        var userDropdown = document.getElementById('userMenuDropdown');
        if (userMenu && userDropdown && !userMenu.contains(e.target)) {
            userDropdown.classList.remove('open');
            var trigger = document.getElementById('userMenuTrigger');
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        }
    });

    function toggleCustomDateFilter(filterId) {
        const select = document.getElementById(filterId);
        const wrap = document.getElementById(filterId + 'PickerWrap');
        if (!select || !wrap) return;
        wrap.classList.toggle('show', select.value === 'custom');
    }

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta && meta.content) {
            return meta.content;
        }
        var match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
        return match ? decodeURIComponent(match[1]) : '';
    }

    function closeNotificationMenu() {
        var menu = document.getElementById('notificationMenu');
        if (!menu) return;
        menu.classList.remove('open');
        menu.style.display = 'none';
    }

    function markPortalNotificationReadLocally(selector, value) {
        if (!value) return;
        document.querySelectorAll(selector).forEach(function (el) {
            if (el.dataset.unread !== '1') return;
            el.classList.remove('is-unread');
            el.classList.add('is-read');
            el.dataset.unread = '0';
            var badge = el.querySelector('.notification-new-badge');
            if (badge) badge.remove();
        });
        refreshNotificationUi();
    }

    function markPortalNotificationReadLocallyByElement(el) {
        if (!el || el.dataset.unread !== '1') return;
        el.classList.remove('is-unread');
        el.classList.add('is-read');
        el.dataset.unread = '0';
        var badge = el.querySelector('.notification-new-badge');
        if (badge) badge.remove();
        var markBtn = el.querySelector('.notification-mark-read-btn');
        if (markBtn) {
            var readBadge = document.createElement('span');
            readBadge.className = 'notification-read-badge';
            readBadge.textContent = 'مقروء';
            markBtn.replaceWith(readBadge);
        }
        refreshNotificationUi();
    }

    function portalNotificationMarkConfig(item) {
        if (!item) return null;
        var kind = item.dataset.notificationKind || '';
        var reference = item.dataset.reference || '';
        if (!reference) return null;

        if (kind === 'receiving') {
            var isVet = item.classList.contains('vet-notification-feed-item');
            return {
                markReadUrl: isVet
                    ? (window.vetReceivingNotificationReadUrl || '/vet/notifications/read')
                    : (window.careNotificationReadUrl || '/care/notifications/read'),
                markReadBody: { task_number: reference },
            };
        }
        if (kind === 'quarantine') {
            return {
                markReadUrl: window.quarantineNotificationReadUrl || '/vet/quarantine/notifications/read',
                markReadBody: { case_number: reference },
            };
        }
        if (kind === 'treatment_referral') {
            return {
                markReadUrl: window.vetTreatmentReferralNotificationReadUrl || '/vet/notifications/treatment-referral/read',
                markReadBody: { referral_number: reference },
            };
        }
        if (kind === 'autopsy_referral') {
            return {
                markReadUrl: window.vetAutopsyReferralNotificationReadUrl || '/vet/notifications/autopsy-referral/read',
                markReadBody: { referral_number: reference },
            };
        }
        if (kind === 'hospital_case') {
            var hospitalReadUrl = typeof window.vetHospitalNotificationReadUrl === 'function'
                ? window.vetHospitalNotificationReadUrl(reference)
                : '/vet/notifications/hospital/' + encodeURIComponent(reference) + '/read';
            return { markReadUrl: hospitalReadUrl, markReadBody: null };
        }
        if (kind === 'health') {
            var healthReadUrl = typeof window.careHealthNotificationReadUrl === 'function'
                ? window.careHealthNotificationReadUrl(reference)
                : '/care/notifications/health/' + encodeURIComponent(reference) + '/read';
            return { markReadUrl: healthReadUrl, markReadBody: null };
        }
        if (kind === 'operational_note') {
            var noteReadUrl = typeof window.careOperationalNoteNotificationReadUrl === 'function'
                ? window.careOperationalNoteNotificationReadUrl(reference)
                : '/care/notifications/operational-note/' + encodeURIComponent(reference) + '/read';
            return { markReadUrl: noteReadUrl, markReadBody: null };
        }

        return null;
    }

    function markPortalNotificationItemRead(event, btn) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        var item = btn ? btn.closest('.portal-notification-item') : null;
        if (!item || item.dataset.unread !== '1') return false;

        var config = portalNotificationMarkConfig(item);
        if (!config) return false;

        markPortalNotificationReadLocallyByElement(item);
        portalNotificationPost(config.markReadUrl, config.markReadBody, false).catch(function () {});

        return false;
    }

    function notificationDayBounds() {
        var now = new Date();
        var startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        var startOfYesterday = new Date(startOfToday);
        startOfYesterday.setDate(startOfYesterday.getDate() - 1);
        var startOfWeek = new Date(startOfToday);
        startOfWeek.setDate(startOfWeek.getDate() - 6);

        return {
            now: now,
            startOfToday: startOfToday,
            startOfYesterday: startOfYesterday,
            startOfWeek: startOfWeek,
        };
    }

    function notificationMatchesFilter(createdAtSeconds, filter) {
        if (!filter || filter === 'all') return true;
        if (!createdAtSeconds) return filter === 'older';

        var bounds = notificationDayBounds();
        var createdAt = new Date(createdAtSeconds * 1000);

        if (filter === 'today') {
            return createdAt >= bounds.startOfToday;
        }
        if (filter === 'yesterday') {
            return createdAt >= bounds.startOfYesterday && createdAt < bounds.startOfToday;
        }
        if (filter === 'week') {
            return createdAt >= bounds.startOfWeek;
        }
        if (filter === 'older') {
            return createdAt < bounds.startOfWeek;
        }

        return true;
    }

    function applyNotificationFilter(filter) {
        var list = document.getElementById('notificationItemsList');
        var emptyState = document.getElementById('notificationFilterEmpty');
        if (!list) return;

        var visibleCount = 0;
        list.querySelectorAll('.portal-notification-item').forEach(function (item) {
            var createdAt = parseInt(item.dataset.createdAt || '0', 10);
            var visible = notificationMatchesFilter(createdAt, filter);
            item.style.display = visible ? '' : 'none';
            if (visible) visibleCount += 1;
        });

        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        document.querySelectorAll('.notification-filter-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.filter === filter);
        });
    }

    function initNotificationFilters() {
        var filters = document.getElementById('notificationFilters');
        if (!filters || filters.dataset.bound === '1') return;
        filters.dataset.bound = '1';

        filters.querySelectorAll('.notification-filter-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                applyNotificationFilter(btn.dataset.filter || 'all');
            });
        });

        applyNotificationFilter('all');
    }

    document.addEventListener('DOMContentLoaded', initNotificationFilters);

    function refreshNotificationUi() {
        var items = document.querySelectorAll('.portal-notification-item');
        var unreadCount = 0;
        items.forEach(function (item) {
            if (item.dataset.unread === '1') unreadCount += 1;
        });

        var badge = document.getElementById('notificationBadge');
        var countBadge = document.getElementById('notificationCountBadge');
        var list = document.getElementById('notificationItemsList');
        var emptyState = document.getElementById('notificationFilterEmpty');
        var btn = document.getElementById('notificationBtn');

        if (!badge && btn) {
            badge = document.createElement('span');
            badge.className = 'notification-badge';
            badge.id = 'notificationBadge';
            btn.appendChild(badge);
        }

        if (!countBadge) {
            var title = document.querySelector('.notification-dropdown-title');
            if (title) {
                countBadge = document.createElement('span');
                countBadge.id = 'notificationCountBadge';
                countBadge.className = 'notification-count-pill';
                countBadge.style.display = 'none';
                title.appendChild(countBadge);
            }
        }

        if (countBadge) {
            if (unreadCount > 0) {
                countBadge.textContent = unreadCount + ' جديدة';
                countBadge.style.display = '';
            } else {
                countBadge.textContent = '';
                countBadge.style.display = 'none';
            }
        }

        if (badge) {
            badge.style.display = unreadCount > 0 ? '' : 'none';
        }

        if (list && items.length === 0) {
            list.innerHTML = '<div class="notification-empty-state">لا توجد إشعارات</div>';
            if (emptyState) emptyState.style.display = 'none';
        }

        var activeFilter = document.querySelector('.notification-filter-btn.active');
        if (activeFilter) {
            applyNotificationFilter(activeFilter.dataset.filter || 'all');
        }
    }

    function applyPortalNotificationFeed(payload) {
        var list = document.getElementById('notificationItemsList');
        if (!list || !payload) return;

        var html = payload.html || '';
        if (!html.trim()) {
            list.innerHTML = '<div class="notification-empty-state">لا توجد إشعارات</div>';
        } else {
            list.innerHTML = html;
        }

        refreshNotificationUi();
    }

    function portalNotificationFeedFingerprint(payload) {
        if (!payload) return '';
        return String(payload.version || '') + ':' + String(payload.unread_count || 0);
    }

    function fetchPortalNotificationFeed() {
        var feedUrl = window.portalNotificationsFeedUrl;
        if (!feedUrl) {
            return Promise.resolve(null);
        }

        return fetch(feedUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        }).then(function (res) {
            if (!res.ok) {
                throw new Error('feed fetch failed');
            }
            return res.json();
        });
    }

    (function initPortalNotificationRealtime() {
        var feedUrl = window.portalNotificationsFeedUrl;
        if (!feedUrl) return;

        var pollMs = Number(window.portalNotificationsPollMs || 15000);
        var lastFingerprint = '';
        var pollTimer = null;
        var inFlight = false;

        function syncFeed(forceUpdate) {
            if (inFlight || document.visibilityState === 'hidden') {
                return;
            }

            inFlight = true;
            fetchPortalNotificationFeed()
                .then(function (payload) {
                    if (!payload) return;

                    var fingerprint = portalNotificationFeedFingerprint(payload);
                    if (forceUpdate || (fingerprint && fingerprint !== lastFingerprint)) {
                        lastFingerprint = fingerprint;
                        applyPortalNotificationFeed(payload);
                    }
                })
                .catch(function () {})
                .finally(function () {
                    inFlight = false;
                });
        }

        function schedulePoll() {
            if (pollTimer) {
                clearInterval(pollTimer);
            }
            pollTimer = setInterval(function () {
                syncFeed(false);
            }, pollMs);
        }

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
                syncFeed(false);
                schedulePoll();
            } else if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        });

        schedulePoll();
        setTimeout(function () {
            syncFeed(false);
        }, 1200);
    })();

    function portalNormalizePath(path) {
        return (path || '').replace(/\/+$/, '') || '/';
    }

    function portalPathIs(prefix) {
        var current = portalNormalizePath(window.location.pathname);
        var base = portalNormalizePath(prefix);
        return current === base || current.indexOf(base + '/') === 0;
    }

    function portalNotificationPost(url, body, keepalive) {
        if (!url) {
            return Promise.reject(new Error('missing mark read url'));
        }

        var options = {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        };

        if (body !== undefined && body !== null) {
            options.body = JSON.stringify(body);
        }

        if (keepalive) {
            options.keepalive = true;
        }

        return fetch(url, options).then(function (res) {
            if (!res.ok) {
                throw new Error('mark read failed');
            }
            return res.json();
        });
    }

    function portalNotificationActivate(event, config) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        closeNotificationMenu();

        var item = event && event.currentTarget ? event.currentTarget : null;
        var wasUnread = !!(item && item.dataset.unread === '1');
        var openedInPlace = false;

        if (typeof config.tryOpenInPlace === 'function') {
            openedInPlace = config.tryOpenInPlace() === true;
        }

        if (openedInPlace) {
            if (wasUnread) {
                markPortalNotificationReadLocallyByElement(item);
                portalNotificationPost(config.markReadUrl, config.markReadBody, false).catch(function () {});
            }
            return false;
        }

        if (config.fallbackUrl) {
            if (wasUnread) {
                markPortalNotificationReadLocallyByElement(item);
                portalNotificationPost(config.markReadUrl, config.markReadBody, true).catch(function () {});
            }
            window.location.href = config.fallbackUrl;
            return false;
        }

        return false;
    }

    function handleCareNotificationClick(el, event) {
        if (!el) return false;

        var kind = el.dataset.notificationKind || '';
        var reference = el.dataset.reference || '';
        if (!reference) return false;

        if (kind === 'receiving') {
            return openDecisionFromNotification(reference, event);
        }
        if (kind === 'health') {
            return openHealthCaseFromNotification(reference, event);
        }
        if (kind === 'operational_note') {
            return openOperationalNoteFromNotification(reference, event);
        }

        return false;
    }

    function handleVetNotificationClick(el, event) {
        if (!el) return false;

        var kind = el.dataset.notificationKind || '';
        var reference = el.dataset.reference || '';
        if (!reference) return false;

        if (kind === 'quarantine') {
            return openQuarantineFromNotification(reference, event);
        }
        if (kind === 'receiving') {
            return openVetDecisionFromNotification(reference, event);
        }
        if (kind === 'treatment_referral') {
            return openTreatmentReferralFromNotification(reference, event);
        }
        if (kind === 'autopsy_referral') {
            return openAutopsyReferralFromNotification(reference, event);
        }
        if (kind === 'hospital_case') {
            return openHospitalCaseFromNotification(reference, event);
        }

        return false;
    }

    function openHospitalCaseFromNotification(caseNumber, event) {
        var base = window.vetHospitalCasesUrl || '/vet/cases/hospital';
        var readUrl = typeof window.vetHospitalNotificationReadUrl === 'function'
            ? window.vetHospitalNotificationReadUrl(caseNumber)
            : '/vet/notifications/hospital/' + encodeURIComponent(caseNumber) + '/read';

        return portalNotificationActivate(event, {
            fallbackUrl: base + '/' + encodeURIComponent(caseNumber),
            markReadUrl: readUrl,
        });
    }

    function openQuarantineFromNotification(caseNumber, event) {
        return portalNotificationActivate(event, {
            tryOpenInPlace: function () {
                if (portalPathIs('/vet/quarantine') && typeof window.openQuarantineModal === 'function') {
                    window.openQuarantineModal(caseNumber);
                    return true;
                }
                return false;
            },
            fallbackUrl: (window.quarantineListUrl || '/vet/quarantine') + '?open=' + encodeURIComponent(caseNumber),
            markReadUrl: window.quarantineNotificationReadUrl || '/vet/quarantine/notifications/read',
            markReadBody: { case_number: caseNumber },
        });
    }

    function openDecisionFromNotification(taskNumber, event) {
        var base = window.careDecisionsUrl || '/care/decisions';
        return portalNotificationActivate(event, {
            fallbackUrl: base + '/' + encodeURIComponent(taskNumber),
            markReadUrl: window.careNotificationReadUrl || '/care/notifications/read',
            markReadBody: { task_number: taskNumber },
        });
    }

    function openVetDecisionFromNotification(taskNumber, event) {
        var base = window.vetDecisionsUrl || '/vet/decisions';
        return portalNotificationActivate(event, {
            fallbackUrl: base + '/' + encodeURIComponent(taskNumber),
            markReadUrl: window.vetReceivingNotificationReadUrl || '/vet/notifications/read',
            markReadBody: { task_number: taskNumber },
        });
    }

    function openTreatmentReferralFromNotification(referralNumber, event) {
        var base = window.vetTreatmentReferralsUrl || '/vet/referrals/treatment';
        return portalNotificationActivate(event, {
            tryOpenInPlace: function () {
                if (portalPathIs('/vet/referrals/treatment') && typeof window.openTreatmentReferralModal === 'function') {
                    window.openTreatmentReferralModal(referralNumber);
                    return true;
                }
                return false;
            },
            fallbackUrl: base + '?referral=' + encodeURIComponent(referralNumber),
            markReadUrl: window.vetTreatmentReferralNotificationReadUrl || '/vet/notifications/treatment-referral/read',
            markReadBody: { referral_number: referralNumber },
        });
    }

    function openAutopsyReferralFromNotification(referralNumber, event) {
        var base = window.vetAutopsyReferralsUrl || '/vet/referrals/autopsy';
        return portalNotificationActivate(event, {
            fallbackUrl: base + '/' + encodeURIComponent(referralNumber),
            markReadUrl: window.vetAutopsyReferralNotificationReadUrl || '/vet/notifications/autopsy-referral/read',
            markReadBody: { referral_number: referralNumber },
        });
    }

    function openHealthCaseFromNotification(caseNumber, event) {
        var base = window.careHealthCasesUrl || '/care/health';
        var readUrl = typeof window.careHealthNotificationReadUrl === 'function'
            ? window.careHealthNotificationReadUrl(caseNumber)
            : '/care/notifications/health/' + encodeURIComponent(caseNumber) + '/read';

        return portalNotificationActivate(event, {
            tryOpenInPlace: function () {
                if (portalPathIs('/care/health') && typeof window.openHealthCaseModal === 'function') {
                    window.openHealthCaseModal(caseNumber);
                    return true;
                }
                return false;
            },
            fallbackUrl: base + '?case=' + encodeURIComponent(caseNumber),
            markReadUrl: readUrl,
        });
    }

    function openOperationalNoteFromNotification(noteNumber, event) {
        var base = window.careNotesUrl || '/care/notes';
        var readUrl = typeof window.careOperationalNoteNotificationReadUrl === 'function'
            ? window.careOperationalNoteNotificationReadUrl(noteNumber)
            : '/care/notifications/operational-note/' + encodeURIComponent(noteNumber) + '/read';

        return portalNotificationActivate(event, {
            tryOpenInPlace: function () {
                if (portalPathIs('/care/notes') && typeof window.openOperationalNoteModal === 'function') {
                    window.openOperationalNoteModal(noteNumber);
                    return true;
                }
                return false;
            },
            fallbackUrl: base + '?note=' + encodeURIComponent(noteNumber),
            markReadUrl: readUrl,
        });
    }
</script>
