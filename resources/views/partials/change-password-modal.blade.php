<div class="portal-modal-backdrop" id="changePasswordModal" aria-hidden="true">
    <div class="portal-modal-box" role="dialog" aria-labelledby="changePasswordTitle" aria-modal="true">
        <div class="portal-modal-header">
            <h3 id="changePasswordTitle">تغيير كلمة المرور</h3>
            <button type="button" class="portal-modal-close" onclick="closeChangePasswordModal()" aria-label="إغلاق">✕</button>
        </div>
        <form id="changePasswordForm" class="portal-modal-body" novalidate>
            <p class="portal-modal-hint">أدخل كلمة المرور الحالية ثم اختر كلمة مرور جديدة لا تقل عن 8 أحرف.</p>
            <div class="portal-field">
                <label for="currentPasswordInput">كلمة المرور الحالية</label>
                <input type="password" id="currentPasswordInput" name="current_password" autocomplete="current-password" required>
            </div>
            <div class="portal-field">
                <label for="newPasswordInput">كلمة المرور الجديدة</label>
                <input type="password" id="newPasswordInput" name="password" autocomplete="new-password" minlength="8" required>
            </div>
            <div class="portal-field">
                <label for="confirmPasswordInput">تأكيد كلمة المرور الجديدة</label>
                <input type="password" id="confirmPasswordInput" name="password_confirmation" autocomplete="new-password" minlength="8" required>
            </div>
            <div class="portal-modal-error" id="changePasswordError" hidden></div>
        </form>
        <div class="portal-modal-footer">
            <button type="button" class="portal-modal-btn secondary" onclick="closeChangePasswordModal()">إلغاء</button>
            <button type="submit" form="changePasswordForm" class="portal-modal-btn primary" id="changePasswordSubmit">حفظ كلمة المرور</button>
        </div>
    </div>
</div>
