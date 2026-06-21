<style>
    .admin-toast {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(80px);
        background: #1E293B;
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        z-index: 12000;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        max-width: min(92vw, 520px);
        text-align: center;
    }

    .admin-toast.show {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
    }

    .admin-toast.success { background: #166534; }
    .admin-toast.error { background: #991B1B; }
</style>

<div class="admin-toast" id="adminToast" role="status" aria-live="polite"></div>

<script>
    (function () {
        let toastTimer = null;
        let confirmResolver = null;

        window.showAdminToast = function (message, type = 'success', duration = 4000) {
            const toast = document.getElementById('adminToast');
            if (!toast || !message) return;

            if (toastTimer) {
                clearTimeout(toastTimer);
                toastTimer = null;
            }

            toast.textContent = message;
            toast.className = 'admin-toast show ' + (type === 'error' ? 'error' : 'success');

            toastTimer = setTimeout(() => {
                toast.classList.remove('show');
            }, duration);
        };

        window.showAdminConfirm = function ({ title = 'تأكيد العملية', message = 'هل أنت متأكد؟', confirmLabel = 'تأكيد', danger = false } = {}) {
            const overlay = document.getElementById('adminConfirmOverlay');
            const titleEl = document.getElementById('adminConfirmTitle');
            const messageEl = document.getElementById('adminConfirmMessage');
            const okBtn = document.getElementById('adminConfirmOk');
            const cancelBtn = document.getElementById('adminConfirmCancel');

            if (!overlay || !titleEl || !messageEl || !okBtn || !cancelBtn) {
                return Promise.resolve(window.confirm(message));
            }

            titleEl.textContent = title;
            messageEl.textContent = message;
            okBtn.textContent = confirmLabel;
            okBtn.classList.toggle('danger', !!danger);

            overlay.classList.add('show');
            overlay.setAttribute('aria-hidden', 'false');

            return new Promise((resolve) => {
                confirmResolver = resolve;

                const onConfirm = () => cleanup(true);
                const onCancel = () => cleanup(false);

                function cleanup(result) {
                    overlay.classList.remove('show');
                    overlay.setAttribute('aria-hidden', 'true');
                    okBtn.removeEventListener('click', onConfirm);
                    cancelBtn.removeEventListener('click', onCancel);
                    overlay.removeEventListener('click', onBackdrop);
                    document.removeEventListener('keydown', onEscape);
                    confirmResolver = null;
                    resolve(result);
                }

                function onBackdrop(event) {
                    if (event.target === overlay) onCancel();
                }

                function onEscape(event) {
                    if (event.key === 'Escape') onCancel();
                }

                okBtn.addEventListener('click', onConfirm);
                cancelBtn.addEventListener('click', onCancel);
                overlay.addEventListener('click', onBackdrop);
                document.addEventListener('keydown', onEscape);
            });
        };

        window.bindAdminConfirmForms = function (selector, messageFn) {
            document.querySelectorAll(selector).forEach((form) => {
                if (form.dataset.confirmBound === '1') return;
                form.dataset.confirmBound = '1';

                form.addEventListener('submit', async (event) => {
                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '0';
                        return;
                    }

                    event.preventDefault();

                    const message = typeof messageFn === 'function'
                        ? messageFn(form)
                        : (form.dataset.confirmMessage || 'هل أنت متأكد؟');

                    const ok = await showAdminConfirm({
                        title: form.dataset.confirmTitle || 'تأكيد العملية',
                        message,
                        confirmLabel: form.dataset.confirmLabel || 'تأكيد',
                        danger: form.dataset.confirmDanger === '1',
                    });
                    if (!ok) return;

                    form.dataset.confirmed = '1';
                    form.submit();
                });
            });
        };

        @if (session('success'))
            document.addEventListener('DOMContentLoaded', () => {
                if (window.__adminFlashShown) return;
                window.__adminFlashShown = true;
                showAdminToast(@json(session('success')), 'success');
            });
        @endif

        @if (session('error'))
            document.addEventListener('DOMContentLoaded', () => {
                if (window.__adminFlashShown) return;
                window.__adminFlashShown = true;
                showAdminToast(@json(session('error')), 'error');
            });
        @endif

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', () => {
                if (window.__adminFlashShown) return;
                window.__adminFlashShown = true;
                showAdminToast(@json($errors->first()), 'error');
            });
        @endif
    })();
</script>
