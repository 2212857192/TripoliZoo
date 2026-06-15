<style>
    /* مدير الحديقة: إخفاء إجراءات الكتابة — الإبقاء على عرض التفاصيل */
    .content-area button:not(.seg-tab):not(.btn-tbl):not(.modal-close):not(.modal-tab):not(.tab-btn),
    .content-area input[type="submit"],
    .content-area input[type="button"],
    .content-area .btn-add,
    .content-area .btn-premium,
    .content-area .btn-submit,
    .content-area .btn-save,
    .content-area .btn-edit,
    .content-area .btn-delete,
    .content-area .btn-cancel:not(.modal-close),
    .content-area .btn-action,
    .content-area .btn-modal,
    .content-area .btn-export,
    .content-area .btn-confirm-delete,
    .content-area .btn-confirm-slaughter,
    .content-area .btn-confirm-release,
    .content-area .btn-add-rule,
    .content-area .btn-submit-orange,
    .content-area .btn-submit-premium,
    .content-area .btn-save-premium,
    .content-area a.btn-add,
    .content-area a.btn-premium,
    .content-area a.btn-action,
    .content-area a.btn-export,
    .content-area a[class*="btn-"]:not(.btn-tbl):not(.stat-card),
    .content-area a[href*="/create"],
    .content-area a[href*="/edit"],
    .content-area .actions-bar,
    .content-area .page-header-actions,
    .content-area .tab-actions,
    .content-area .header-actions,
    .content-area .view-all-link,
    .content-area .btn-tbl.edit,
    .content-area .btn-tbl.end,
    .content-area a.btn-tbl.edit,
    .content-area a.btn-tbl.end {
        display: none !important;
    }

    /* أزرار وروابط عرض التفاصيل */
    .content-area .action-btn,
    .content-area button.action-btn,
    .content-area .user-menu-chevron,
    .content-area .topbar-user-menu,
    .content-area .user-menu-dropdown a {
        display: inline-flex !important;
        pointer-events: auto;
    }

    .content-area .notification-dropdown-wrapper {
        display: block !important;
    }

    .content-area button.btn-tbl.view,
    .content-area a.btn-tbl.view {
        display: inline-flex !important;
        pointer-events: auto;
        cursor: pointer;
    }

    /* إخفاء إجراءات الكتابة داخل النماذج */
    .content-area .modal-footer .btn-approve,
    .content-area .modal-footer .btn-reject,
    .content-area .modal-footer .btn-submit,
    .content-area .modal-footer .btn-confirm,
    .content-area .modal-footer .btn-confirm-danger,
    .content-area .confirm-backdrop,
    .content-area .btn-approve,
    .content-area .btn-reject,
    .content-area .btn-confirm,
    .content-area .btn-confirm-danger {
        display: none !important;
    }

    .content-area .modal-footer .btn-cancel,
    .content-area .modal-footer .btn-secondary,
    .content-area .modal-footer a.btn-secondary {
        display: inline-flex !important;
        pointer-events: auto;
    }

    /* بطاقات التنقل في لوحة التحكم — مسموح بالضغط */
    .content-area a.stat-card,
    .content-area a.alert-item-row,
    .content-area .seg-tab {
        pointer-events: auto;
        cursor: pointer;
    }

    .content-area .dashboard-tabs-card,
    .content-area .dashboard-tabs-card .seg-tab {
        display: inline-flex !important;
    }
    .content-area .dashboard-tabs-card {
        display: block !important;
    }

    .content-area a.unit-card,
    .content-area a.alert-item,
    .content-area a.quick-link-item {
        pointer-events: none;
        cursor: default;
    }

    /* بطاقات الملاحظات — عرض بالنقر */
    .content-area .note-card {
        pointer-events: auto;
        cursor: pointer;
    }

    /* النماذج المنبثقة للعرض */
    .content-area .modal-backdrop.open {
        display: flex !important;
    }
    .content-area button.tab-btn,
    .content-area button.modal-tab,
    .content-area button.modal-close {
        display: inline-flex !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var area = document.querySelector('.content-area');
        if (!area) return;

        /* إزالة onclick من عناصر الكتابة فقط */
        area.querySelectorAll('[onclick]').forEach(function (el) {
            if (el.classList.contains('seg-tab')) return;
            if (el.classList.contains('tab-btn')) return;
            if (el.classList.contains('action-btn')) return;
            if (el.classList.contains('user-menu-chevron')) return;
            if ((el.getAttribute('onclick') || '').indexOf('toggleUserMenu') !== -1) return;
            if ((el.getAttribute('onclick') || '').indexOf('toggleNotifications') !== -1) return;
            if (el.classList.contains('modal-tab')) return;
            if (el.classList.contains('btn-tbl') && el.classList.contains('view')) return;
            if ((el.getAttribute('onclick') || '').indexOf('openModal') !== -1) return;
            if ((el.getAttribute('onclick') || '').indexOf('openView') !== -1) return;
            if ((el.getAttribute('onclick') || '').indexOf('switchTab') !== -1) return;
            if (el.classList.contains('note-card')) return;
            el.removeAttribute('onclick');
        });

        /* إعادة توجيه روابط الأقسام إلى مسار director */
        area.addEventListener('click', function (e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            var href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
            if (href.startsWith('/director/')) return;

            var match = href.match(/^\/(admin|vet|care|records)(\/.*)?$/);
            if (!match) return;

            if (/\/(create|edit)(\/|$)/.test(href)) {
                e.preventDefault();
                return;
            }

            e.preventDefault();
            window.location.href = '/director' + href;
        }, true);

        /* منع أزرار الكتابة — السماح بعرض التفاصيل */
        area.addEventListener('click', function (e) {
            if (e.target.closest('.seg-tab')) return;
            if (e.target.closest('.tab-btn')) return;
            if (e.target.closest('.action-btn')) return;
            if (e.target.closest('.user-menu-chevron')) return;
            if (e.target.closest('.topbar-user-menu')) return;
            if (e.target.closest('.modal-tab')) return;
            if (e.target.closest('.btn-tbl.view')) return;
            if (e.target.closest('.modal-close')) return;
            if (e.target.closest('.modal-footer .btn-cancel')) return;
            if (e.target.closest('.modal-footer .btn-secondary')) return;
            if (e.target.closest('.modal-footer a.btn-secondary')) return;
            if (e.target.closest('.note-card')) return;
            if (e.target.closest('a.stat-card, a.alert-item-row')) return;
            if (e.target.closest('.confirm-backdrop')) return;

            var blocked = e.target.closest(
                'button:not(.modal-close):not(.btn-tbl):not(.tab-btn):not(.modal-tab), input[type="submit"], input[type="button"], ' +
                '.btn-add, .btn-action, .btn-edit, .btn-tbl.edit, .btn-tbl.end, ' +
                'a.btn-add, a.btn-action, a[href*="/create"], a[href*="/edit"]'
            );
            if (blocked) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
    });
</script>
