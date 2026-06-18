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

    function removeNotificationItem(caseNumber) {
        if (!caseNumber) return;
        document.querySelectorAll('.vet-notification-item[data-case="' + caseNumber + '"]').forEach(function (el) {
            el.remove();
        });
        refreshNotificationUi();
    }

    function openQuarantineFromNotification(caseNumber, event) {
        if (event) event.preventDefault();
        var menu = document.getElementById('notificationMenu');
        if (menu) {
            menu.classList.remove('open');
            menu.style.display = 'none';
        }

        var base = window.quarantineListUrl || '/vet/quarantine';
        var openDetails = function () {
            if (typeof window.openQuarantineModal === 'function') {
                window.openQuarantineModal(caseNumber);
                return;
            }
            window.location.href = base + '?open=' + encodeURIComponent(caseNumber);
        };

        markQuarantineNotificationRead(caseNumber)
            .then(function () {
                removeNotificationItem(caseNumber);
                openDetails();
            })
            .catch(function () {
                window.location.href = base + '?open=' + encodeURIComponent(caseNumber);
            });

        return false;
    }

    function markQuarantineNotificationRead(caseNumber) {
        var url = window.quarantineNotificationReadUrl || '/vet/quarantine/notifications/read';
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ case_number: caseNumber }),
        }).then(function (res) {
            if (!res.ok) {
                throw new Error('mark read failed');
            }
            return res.json();
        });
    }

    function refreshNotificationUi() {
        var items = document.querySelectorAll('.vet-notification-item, .vet-receiving-notification-item, .care-notification-item');
        var badge = document.getElementById('notificationBadge');
        var countBadge = document.getElementById('notificationCountBadge');
        var list = document.getElementById('notificationItemsList');
        var count = items.length;

        if (countBadge) {
            countBadge.textContent = count > 0 ? (count + ' جديدة') : '';
            countBadge.style.display = count > 0 ? '' : 'none';
        }
        if (badge) badge.style.display = count > 0 ? '' : 'none';
        if (list && count === 0) {
            list.innerHTML = '<div style="font-size:0.8rem;text-align:center;padding:20px 0;color:var(--text-muted);">لا توجد إشعارات جديدة</div>';
        }
    }

    function removeCareNotificationItem(taskNumber) {
        if (!taskNumber) return;
        document.querySelectorAll('.care-notification-item[data-task="' + taskNumber + '"]').forEach(function (el) {
            el.remove();
        });
        refreshNotificationUi();
    }

    function openDecisionFromNotification(taskNumber, event) {
        if (event) event.preventDefault();
        var menu = document.getElementById('notificationMenu');
        if (menu) {
            menu.classList.remove('open');
            menu.style.display = 'none';
        }

        var base = window.careDecisionsUrl || '/care/decisions';
        markCareNotificationRead(taskNumber)
            .then(function () {
                removeCareNotificationItem(taskNumber);
                window.location.href = base + '/' + encodeURIComponent(taskNumber);
            })
            .catch(function () {
                window.location.href = base + '/' + encodeURIComponent(taskNumber);
            });

        return false;
    }

    function markCareNotificationRead(taskNumber) {
        var url = window.careNotificationReadUrl || '/care/notifications/read';
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ task_number: taskNumber }),
        }).then(function (res) {
            if (!res.ok) {
                throw new Error('mark read failed');
            }
            return res.json();
        });
    }

    function removeVetReceivingNotificationItem(taskNumber) {
        if (!taskNumber) return;
        document.querySelectorAll('.vet-receiving-notification-item[data-task="' + taskNumber + '"]').forEach(function (el) {
            el.remove();
        });
        refreshNotificationUi();
    }

    function openVetDecisionFromNotification(taskNumber, event) {
        if (event) event.preventDefault();
        var menu = document.getElementById('notificationMenu');
        if (menu) {
            menu.classList.remove('open');
            menu.style.display = 'none';
        }

        var base = window.vetDecisionsUrl || '/vet/decisions';
        markVetReceivingNotificationRead(taskNumber)
            .then(function () {
                removeVetReceivingNotificationItem(taskNumber);
                window.location.href = base + '/' + encodeURIComponent(taskNumber);
            })
            .catch(function () {
                window.location.href = base + '/' + encodeURIComponent(taskNumber);
            });

        return false;
    }

    function markVetReceivingNotificationRead(taskNumber) {
        var url = window.vetReceivingNotificationReadUrl || '/vet/notifications/read';
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ task_number: taskNumber }),
        }).then(function (res) {
            if (!res.ok) {
                throw new Error('mark read failed');
            }
            return res.json();
        });
    }

    function openTreatmentReferralFromNotification(referralNumber, event) {
        if (event) event.preventDefault();
        var menu = document.getElementById('notificationMenu');
        if (menu) {
            menu.classList.remove('open');
            menu.style.display = 'none';
        }

        var base = window.vetTreatmentReferralsUrl || '/vet/referrals/treatment';
        markTreatmentReferralNotificationRead(referralNumber)
            .then(function () {
                document.querySelectorAll('.vet-treatment-referral-notification-item[data-referral="' + referralNumber + '"]').forEach(function (el) {
                    el.remove();
                });
                refreshNotificationUi();
                window.location.href = base + '?referral=' + encodeURIComponent(referralNumber);
            })
            .catch(function () {
                window.location.href = base + '?referral=' + encodeURIComponent(referralNumber);
            });

        return false;
    }

    function markTreatmentReferralNotificationRead(referralNumber) {
        var url = window.vetTreatmentReferralNotificationReadUrl || '/vet/notifications/treatment-referral/read';
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ referral_number: referralNumber }),
        }).then(function (res) {
            if (!res.ok) {
                throw new Error('mark read failed');
            }
            return res.json();
        });
    }

    function openAutopsyReferralFromNotification(referralNumber, event) {
        if (event) event.preventDefault();
        var menu = document.getElementById('notificationMenu');
        if (menu) {
            menu.classList.remove('open');
            menu.style.display = 'none';
        }

        var base = window.vetAutopsyReferralsUrl || '/vet/referrals/autopsy';
        markAutopsyReferralNotificationRead(referralNumber)
            .then(function () {
                document.querySelectorAll('.vet-autopsy-referral-notification-item[data-referral="' + referralNumber + '"]').forEach(function (el) {
                    el.remove();
                });
                refreshNotificationUi();
                window.location.href = base + '/' + encodeURIComponent(referralNumber);
            })
            .catch(function () {
                window.location.href = base + '/' + encodeURIComponent(referralNumber);
            });

        return false;
    }

    function markAutopsyReferralNotificationRead(referralNumber) {
        var url = window.vetAutopsyReferralNotificationReadUrl || '/vet/notifications/autopsy-referral/read';
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ referral_number: referralNumber }),
        }).then(function (res) {
            if (!res.ok) {
                throw new Error('mark read failed');
            }
            return res.json();
        });
    }
</script>
