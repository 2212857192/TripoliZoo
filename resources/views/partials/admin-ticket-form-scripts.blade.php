<script>
    function bindTicketAdminForms() {
        bindAdminConfirmForms('.js-ticket-toggle-form', (form) => {
            return form.dataset.active === '1'
                ? 'هل أنت متأكد من إيقاف هذه التذكرة؟'
                : 'هل أنت متأكد من التفعيل؟';
        });
    }

    document.addEventListener('DOMContentLoaded', bindTicketAdminForms);
</script>
