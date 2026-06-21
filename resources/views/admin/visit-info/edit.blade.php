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

    .status-edit-row {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .status-edit-row .form-input { flex: 1; }

    .btn-visibility-toggle {
        padding: 12px 18px;
        border-radius: 10px;
        border: 1.5px solid #86EFAC;
        background: #F0FDF4;
        color: #166534;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.82rem;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .btn-visibility-toggle.hidden-state {
        border-color: #FECACA;
        background: #FEF2F2;
        color: #991B1B;
    }

    @media (max-width: 900px) {
        .visit-container { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
        .status-edit-row { flex-direction: column; }
    }
</style>
@endsection

@section('content')
@php
    $settings = $settings ?? \App\Models\VisitSetting::current();
@endphp
<a href="{{ route('admin.visit-info.show') }}" class="page-back">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    إلغاء والعودة لصفحة المعلومات
</a>

<div class="visit-container">
    <form method="POST" action="{{ route('admin.visit-info.update') }}" id="visitInfoForm" class="main-panel" style="grid-column: span 2; display:flex; flex-direction:column; gap:1.8rem;">
        @csrf
        @method('PUT')
        <input type="hidden" name="status_visible" id="status_visible" value="{{ old('status_visible', $settings->status_visible ? '1' : '0') }}">

        <!-- Status & Announcements Form -->
        <div class="premium-card" style="border-right: 5px solid #eab308; background: rgba(254, 243, 199, 0.2); margin-bottom: 1.8rem;">
            <div class="card-accent-header" style="background: transparent; border-bottom: 1.5px solid var(--border);">
                <div class="icon-wrapper" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <h3 style="color: #854d0e;">تعديل حالة التشغيل والتنبيهات العاجلة</h3>
            </div>
            <div class="premium-card-body">
                <div class="form-group">
                    <label>نص حالة التشغيل</label>
                    <p style="margin: 0 0 8px; font-size: 0.82rem; color: #64748b; line-height: 1.6;">
                        استخدم هذا الحقل لعرض حالة التشغيل اليومية للزوار، مثل: «مفتوحة — أهلاً بزوارنا» أو «مغلقة اليوم لأعمال الصيانة».
                    </p>
                    <div class="status-edit-row">
                        <input type="text" name="status_text" id="status_text" class="form-input" value="{{ old('status_text', $settings->status_text) }}" style="font-weight:700;">
                        <button type="button" class="btn-visibility-toggle {{ old('status_visible', $settings->status_visible) ? '' : 'hidden-state' }}" id="statusVisToggle" onclick="toggleStatusVisEdit()">{{ old('status_visible', $settings->status_visible) ? '👁 ظاهر للزوار' : '🚫 مخفي عن الزوار' }}</button>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>نص التنبيه العاجل (يظهر مباشرة للزوار في التطبيق والواجهة)</label>
                    <p style="margin: 0 0 8px; font-size: 0.82rem; color: #64748b; line-height: 1.6;">
                        للإغلاقات المؤقتة أو الظروف الطارئة أو أي تغيير مؤقت في العمل دون تعديل أوقات الزيارة الأساسية.
                    </p>
                    <textarea name="urgent_alert" class="form-input" rows="2" style="resize: vertical; font-weight: 700; font-family: 'Cairo', sans-serif;">{{ old('urgent_alert', $settings->urgent_alert) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Emergency Contacts -->
        <div class="premium-card" style="margin-bottom: 1.8rem;">
            <div class="card-accent-header">
                <div class="icon-wrapper">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <h3>أرقام الطوارئ والأمن</h3>
            </div>
            <div class="premium-card-body">
                <div class="form-row">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>رقم الإسعاف</label>
                        <input type="text" name="ambulance_phone" id="ambulance_phone" class="form-input" value="{{ old('ambulance_phone', $settings->ambulance_phone) }}" dir="ltr">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>رقم الأمن</label>
                        <input type="text" name="security_phone" id="security_phone" class="form-input" value="{{ old('security_phone', $settings->security_phone) }}" dir="ltr">
                    </div>
                </div>
            </div>
        </div>

        <!-- Entry Instructions -->
        <div class="premium-card" style="margin-bottom: 1.8rem;">
            <div class="card-accent-header">
                <div class="icon-wrapper">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                </div>
                <h3>تعليمات الدخول</h3>
            </div>
            <div class="premium-card-body">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>التعليمات المعروضة للزوار</label>
                    <textarea id="entry_instructions" name="entry_instructions" class="form-input" rows="5" style="resize: vertical; line-height: 1.7;">{{ old('entry_instructions', $settings->entry_instructions) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Hours Form -->
        <div class="premium-card">
            <div class="card-accent-header">
                <div class="icon-wrapper">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <h3>أوقات ومواعيد العمل</h3>
            </div>
            <div class="premium-card-body">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label>نمط العمل</label>
                    <div class="form-input" style="background: #f8fafc; color: #166534; font-weight: 800; border-color: #bbf7d0;">
                        {{ \App\Models\VisitSetting::scheduleLabel() }}
                    </div>
                    <p style="margin: 8px 0 0; font-size: 0.82rem; color: #64748b; line-height: 1.6;">
                        الحديقة مفتوحة يومياً بشكل افتراضي. لإبلاغ الزوار بإغلاق مؤقت استخدم حالة التشغيل أو التنبيه العاجل أعلاه.
                    </p>
                </div>

                <div class="form-row">
                    <div class="form-group" style="margin: 0;">
                        <label>وقت الفتح (صباحاً)</label>
                        <input type="time" name="open_time" id="hours_open" class="form-input" value="{{ old('open_time', substr((string) $settings->open_time, 0, 5)) }}">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>وقت الإغلاق (مساءً)</label>
                        <input type="time" name="close_time" id="hours_close" class="form-input" value="{{ old('close_time', substr((string) $settings->close_time, 0, 5)) }}">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.3rem; margin-bottom: 0;">
                    <label>آخر موعد للدخول</label>
                    <input type="text" name="last_ticket_time_note" id="hours_last_ticket" class="form-input" value="{{ old('last_ticket_time_note', $settings->last_ticket_time_note) }}">
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 0.5rem;">
            <a href="{{ route('admin.visit-info.show') }}" class="btn-cancel-visit" style="width: auto; margin: 0; padding: 12px 30px;">
                إلغاء التعديلات
            </a>
            <button class="btn-submit-visit" type="submit" style="width: auto; padding: 12px 30px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                حفظ ونشر المعلومات
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let statusVisEdit = {{ old('status_visible', $settings->status_visible) ? 'true' : 'false' }};

    function toggleStatusVisEdit() {
        statusVisEdit = !statusVisEdit;
        document.getElementById('status_visible').value = statusVisEdit ? '1' : '0';
        const btn = document.getElementById('statusVisToggle');
        if (statusVisEdit) {
            btn.textContent = '👁 ظاهر للزوار';
            btn.classList.remove('hidden-state');
        } else {
            btn.textContent = '🚫 مخفي عن الزوار';
            btn.classList.add('hidden-state');
        }
    }

    document.getElementById('visitInfoForm').addEventListener('submit', async function (event) {
        if (this.dataset.confirmed === '1') {
            this.dataset.confirmed = '0';
            return;
        }

        event.preventDefault();

        const ok = await showAdminConfirm({
            title: 'تأكيد الحفظ',
            message: 'هل أنت متأكد من حفظ ونشر معلومات الزيارة؟',
            confirmLabel: 'حفظ',
        });

        if (!ok) return;

        this.dataset.confirmed = '1';
        this.submit();
    });
</script>
@endsection
