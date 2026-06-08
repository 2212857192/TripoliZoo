@extends($__layout ?? 'admin.layout')
@section('title', 'تعديل معلومات الزيارة | Tripoli Zoo')
@section('page_title', 'تعديل معلومات الزيارة')

@section('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --primary-gradient: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        --accent-gradient: linear-gradient(135deg, #E8651A 0%, #f97316 100%);
        --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .page-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        transition: color 0.2s;
    }

    .page-back:hover { color: var(--orange); }

    .visit-container {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 2rem;
        align-items: start;
    }

    .main-panel {
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
    }

    /* Premium Form Card */
    .premium-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .card-accent-header {
        padding: 1.5rem 1.8rem;
        background: linear-gradient(to left, rgba(45, 90, 39, 0.03), transparent);
        border-bottom: 1.5px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-accent-header h3 {
        font-size: 1.15rem;
        font-weight: 900;
        color: #1e3a1e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .icon-wrapper {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(45, 90, 39, 0.1);
        color: #2d5a27;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .premium-card-body {
        padding: 1.8rem;
    }

    .form-group {
        margin-bottom: 1.3rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--text-main);
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.92rem;
        outline: none;
        transition: all 0.2s;
        background: white;
    }

    .form-input:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 3px rgba(232, 101, 26, 0.08);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.2rem;
    }

    /* Rules editor styles */
    .rules-editor {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .rule-input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn-remove-rule {
        background: #FEE2E2;
        color: #EF4444;
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        transition: all 0.2s;
    }

    .btn-remove-rule:hover {
        background: #FCA5A5;
    }

    .btn-add-rule {
        align-self: flex-start;
        background: rgba(45, 90, 39, 0.05);
        color: #2d5a27;
        border: 1.5px dashed rgba(45, 90, 39, 0.3);
        padding: 10px 20px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.88rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        margin-top: 5px;
    }

    .btn-add-rule:hover {
        background: rgba(45, 90, 39, 0.1);
        border-color: #2d5a27;
    }

    /* Sidebar actions */
    .side-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .btn-submit-visit {
        width: 100%;
        padding: 14px;
        background: var(--accent-gradient);
        color: white;
        border: none;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 6px 20px rgba(232, 101, 26, 0.3);
        transition: all 0.3s;
    }

    .btn-submit-visit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(232, 101, 26, 0.4);
    }

    .btn-cancel-visit {
        width: 100%;
        padding: 12px;
        background: var(--bg-color);
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        cursor: pointer;
        display: block;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
        margin-top: 10px;
    }

    .btn-cancel-visit:hover {
        background: #E2E8F0;
    }

    .facility-row-edit {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--bg-color);
    }

    .facility-row-edit:last-child {
        border-bottom: none;
    }

    .facility-name {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .facility-select {
        padding: 6px 12px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.82rem;
        font-weight: 800;
        outline: none;
        background: white;
        transition: border-color 0.2s;
    }

    .facility-select:focus {
        border-color: var(--orange);
    }

    .toast {
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
        z-index: 9999;
        transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
        white-space: nowrap;
    }

    .toast.show {
        transform: translateX(-50%) translateY(0);
    }

    /* Days Selector Styles */
    .days-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .day-checkbox {
        display: none;
    }

    .day-label {
        padding: 8px 18px;
        background: #f1f5f9;
        border: 1.5px solid #e2e8f0;
        border-radius: 30px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
        user-select: none;
    }

    .day-label:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
    }

    .day-checkbox:checked + .day-label {
        background: rgba(45, 90, 39, 0.1);
        border-color: #2d5a27;
        color: #2d5a27;
    }

    @media (max-width: 900px) {
        .visit-container { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<a href="/admin/visit-info" class="page-back">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    إلغاء والعودة لصفحة المعلومات
</a>

<div class="visit-container">

    <!-- Main Panel -->
    <div class="main-panel" style="grid-column: span 2;">
        
        <!-- Status & Announcements Form -->
        <div class="premium-card" style="border-right: 5px solid #eab308; background: rgba(254, 243, 199, 0.2); margin-bottom: 1.8rem;">
            <div class="card-accent-header" style="background: transparent; border-bottom: 1.5px solid var(--border);">
                <div class="icon-wrapper" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <h3 style="color: #854d0e;">تعديل حالة التشغيل والتنبيهات العاجلة</h3>
            </div>
            <div class="premium-card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>حالة التشغيل العامة للحديقة</label>
                        <select class="form-input" style="font-weight: 700;">
                            <option value="open">🟢 مفتوحة بالكامل للزوار</option>
                            <option value="partial" selected>🟡 مفتوحة جزئياً (توجد أقسام تحت الصيانة)</option>
                            <option value="closed">🔴 مغلقة مؤقتاً (لأعمال صيانة شاملة أو طوارئ)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem; margin-bottom: 0;">
                    <label>نص التنبيه العاجل (يظهر مباشرة للزوار في التطبيق والواجهة)</label>
                    <textarea class="form-input" rows="2" style="resize: vertical; font-weight: 700; font-family: 'Cairo', sans-serif;">⚠️ نود إحاطة زوارنا الكرام بأن "منطقة الطيور البرية" ومبنى "القبة الفلكية" مغلقان حالياً لأعمال الصيانة الدورية وتحديث المرافق.</textarea>
                </div>
            </div>
        </div>

        <!-- Hours Form -->
        <div class="premium-card">
            <div class="card-accent-header">
                <div class="icon-wrapper">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <h3>تعديل مواعيد العمل الرسمية</h3>
            </div>
            <div class="premium-card-body">
                <!-- Day Selection -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label>أيام العمل الأسبوعية (اختر الأيام التي تفتح فيها الحديقة)</label>
                    <div class="days-selector">
                        <input type="checkbox" id="day_sat" class="day-checkbox" checked>
                        <label for="day_sat" class="day-label">السبت</label>

                        <input type="checkbox" id="day_sun" class="day-checkbox" checked>
                        <label for="day_sun" class="day-label">الأحد</label>

                        <input type="checkbox" id="day_mon" class="day-checkbox" checked>
                        <label for="day_mon" class="day-label">الإثنين</label>

                        <input type="checkbox" id="day_tue" class="day-checkbox" checked>
                        <label for="day_tue" class="day-label">الثلاثاء</label>

                        <input type="checkbox" id="day_wed" class="day-checkbox" checked>
                        <label for="day_wed" class="day-label">الأربعاء</label>

                        <input type="checkbox" id="day_thu" class="day-checkbox" checked>
                        <label for="day_thu" class="day-label">الخميس</label>

                        <input type="checkbox" id="day_fri" class="day-checkbox">
                        <label for="day_fri" class="day-label">الجمعة (إغلاق)</label>
                    </div>
                </div>

                <!-- Fixed Hours Selection -->
                <div class="form-row">
                    <div class="form-group" style="margin: 0;">
                        <label>وقت الفتح (صباحاً)</label>
                        <input type="time" id="hours_open" class="form-input" value="09:00">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>وقت الإغلاق (مساءً)</label>
                        <input type="time" id="hours_close" class="form-input" value="18:00">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.3rem; margin-bottom: 0;">
                    <label>آخر موعد للدخول ومبيعات التذاكر</label>
                    <input type="text" id="hours_last_ticket" class="form-input" value="قبل ساعة واحدة من موعد الإغلاق">
                </div>
            </div>
        </div>

            <!-- Action Buttons at the bottom -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="/admin/visit-info" class="btn-cancel-visit" style="width: auto; margin: 0; padding: 12px 30px;">
                    إلغاء التعديلات
                </a>
                <button class="btn-submit-visit" onclick="submitForm()" style="width: auto; padding: 12px 30px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    حفظ ونشر المواعيد
                </button>
            </div>
        </div>
    </div>
</div>

</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function removeRule(btn) {
        const container = document.getElementById('rulesContainer');
        if (container.children.length > 1) {
            btn.parentElement.remove();
        } else {
            showToast('⚠️ يجب ترك بند واحد على الأقل للسلامة');
        }
    }

    function addRuleField() {
        const container = document.getElementById('rulesContainer');
        const div = document.createElement('div');
        div.className = 'rule-input-group';
        div.innerHTML = `
            <input type="text" class="form-input rule-item-val" placeholder="اكتب بند السلامة الجديد...">
            <button type="button" class="btn-remove-rule" onclick="removeRule(this)">&times;</button>
        `;
        container.appendChild(div);
    }

    function submitForm() {
        const btn = document.querySelector('.btn-submit-visit');
        btn.textContent = '⏳ جاري الحفظ والنشر...';
        btn.disabled = true;

        setTimeout(() => {
            showToast('✅ تم تحديث معلومات الزيارة بنجاح (محاكاة)');
            btn.textContent = '✅ تم النشر!';
            setTimeout(() => { window.location.href = '/admin/visit-info'; }, 1200);
        }, 800);
    }
</script>
@endsection
