<script>
    function bindMapLocationForms() {
        bindAdminConfirmForms('.js-map-toggle-form', (form) => {
            return form.dataset.active === '1'
                ? 'هل أنت متأكد من إخفاء هذا الموقع عن الزوار؟'
                : 'هل أنت متأكد من إظهار هذا الموقع للزوار؟';
        });

        bindAdminConfirmForms('.js-map-delete-form', () => 'هل أنت متأكد من حذف هذا الموقع من الخريطة؟');

        document.querySelectorAll('#mapLocationForm').forEach((form) => {
            if (form.dataset.confirmBound === '1') return;
            form.dataset.confirmBound = '1';

            form.addEventListener('submit', async (event) => {
                const lat = document.getElementById('latitude')?.value;
                const lng = document.getElementById('longitude')?.value;

                if (!lat || !lng) {
                    event.preventDefault();
                    showAdminToast('يرجى تحديد موقع على الخريطة', 'error');
                    return;
                }

                if (form.dataset.confirmed === '1') {
                    form.dataset.confirmed = '0';
                    return;
                }

                event.preventDefault();

                const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
                const ok = await showAdminConfirm({
                    title: 'تأكيد الحفظ',
                    message: isEdit
                        ? 'هل أنت متأكد من حفظ تعديلات هذا الموقع؟'
                        : 'هل أنت متأكد من حفظ هذا الموقع على الخريطة؟',
                    confirmLabel: 'حفظ',
                });

                if (!ok) return;

                form.dataset.confirmed = '1';
                form.submit();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', bindMapLocationForms);
</script>
