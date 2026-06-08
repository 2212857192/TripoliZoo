<style>
    /* مدير الحديقة: جداول للعرض فقط — بدون أي إجراء */
    .content-area button:not(.seg-tab),
    .content-area input[type="submit"],
    .content-area input[type="button"],
    .content-area .btn-add,
    .content-area .btn-premium,
    .content-area .btn-submit,
    .content-area .btn-save,
    .content-area .btn-edit,
    .content-area .btn-delete,
    .content-area .btn-cancel,
    .content-area .btn-action,
    .content-area .btn-tbl,
    .content-area .btn-modal,
    .content-area .btn-view,
    .content-area .btn-back,
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
    .content-area a.btn-tbl,
    .content-area a.btn-back,
    .content-area a.btn-view,
    .content-area a.btn-export,
    .content-area a[class*="btn-"],
    .content-area a[href*="/create"],
    .content-area a[href*="/edit"],
    .content-area .actions-bar,
    .content-area .actions-cell,
    .content-area .page-header-actions,
    .content-area .modal-backdrop,
    .content-area .modal-footer,
    .content-area .tab-actions,
    .content-area .header-actions,
    .content-area .view-all-link {
        display: none !important;
    }

    /* بطاقات التنقل في لوحة التحكم — مسموح بالضغط */
    .content-area a.stat-card,
    .content-area a.alert-item-row,
    .content-area .seg-tab {
        pointer-events: auto;
        cursor: pointer;
    }

    /* تبويبات لوحة التحكم — ظاهرة وقابلة للضغط */
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

    /* صفوف/بطاقات تفتح تفاصيل بالنقر — ما عدا تبويبات اللوحة */
    .content-area [onclick]:not(.seg-tab),
    .content-area .note-card {
        pointer-events: none;
        cursor: default;
    }

    .content-area .note-card:hover {
        transform: none;
        box-shadow: inherit;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var area = document.querySelector('.content-area');
        if (!area) return;

        /* إخفاء عمود الإجراءات في الجداول */
        area.querySelectorAll('table thead tr').forEach(function (row) {
            Array.from(row.children).forEach(function (th, idx) {
                var label = (th.textContent || '').trim();
                if (/إجراء/i.test(label)) {
                    th.style.display = 'none';
                    var table = th.closest('table');
                    if (!table) return;
                    table.querySelectorAll('tbody tr').forEach(function (tr) {
                        if (tr.children[idx]) tr.children[idx].style.display = 'none';
                    });
                }
            });
        });

        /* إزالة onclick من أي عنصر متبقي — ما عدا تبويبات اللوحة */
        area.querySelectorAll('[onclick]:not(.seg-tab)').forEach(function (el) {
            el.removeAttribute('onclick');
        });

        /* منع فتح النماذج المنبثقة */
        area.querySelectorAll('.modal-backdrop').forEach(function (modal) {
            modal.classList.remove('open');
        });

        /* إعادة توجيه روابط الأقسام إلى مسار director */
        area.addEventListener('click', function (e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            var href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
            if (href.startsWith('/director/')) return;

            var match = href.match(/^\/(admin|vet|care|records)(\/.*)?$/);
            if (match) {
                e.preventDefault();
                if (/\/(create|edit|show)(\/|$)/.test(href)) return;
                /* منع صفحات التفاصيل — أرقام أو معرّفات في آخر المسار */
                if (/\/[^/]+\/[^/]+$/.test(href) && !/\/logs\//.test(href)) return;
                window.location.href = '/director' + href;
            }
        }, true);

        /* منع أي ضغط على أزرار متبقية — ما عدا تبويبات اللوحة */
        area.addEventListener('click', function (e) {
            if (e.target.closest('.seg-tab')) return;
            if (e.target.closest('button, input[type="submit"], input[type="button"], .btn-tbl, .btn-action, [class*="btn-"]')) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
    });
</script>
