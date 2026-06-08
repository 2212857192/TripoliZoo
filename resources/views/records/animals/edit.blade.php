@extends($__layout ?? 'records.layout')
@section('title', 'تعديل بيانات الحيوان | السجلات والتوثيق')
@section('page_title', 'تعديل بيانات الحيوان الرسمية')

@section('styles')
<style>
    /* ── Page Layout ── */
    .form-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.75rem;
    }

    .form-page-header h2 {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .form-page-header p {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        margin: 4px 0 0;
    }

    /* ── Identity Banner ── */
    .identity-banner {
        background: linear-gradient(135deg, #1a4a2e 0%, #2d7a47 100%);
        border-radius: 16px;
        padding: 1.25rem 1.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 20px;
        color: white;
    }

    .identity-avatar {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        flex-shrink: 0;
    }

    .identity-info h3 {
        font-size: 1.15rem;
        font-weight: 800;
        margin: 0 0 4px;
    }

    .identity-info p {
        font-size: 0.82rem;
        font-weight: 600;
        opacity: 0.8;
        margin: 0;
    }

    .identity-tag {
        margin-right: auto;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 10px;
        padding: 8px 16px;
        text-align: center;
    }

    .identity-tag .tag-label {
        font-size: 0.7rem;
        font-weight: 700;
        opacity: 0.7;
        display: block;
    }

    .identity-tag .tag-value {
        font-size: 1rem;
        font-weight: 900;
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
    }

    .notice-readonly {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-right: 4px solid #ef4444;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #7f1d1d;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    /* ── Section Cards ── */
    .form-section {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }

    .form-section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: #FAFBFC;
    }

    .section-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #e6f4ea;
        color: #1a4a2e;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .form-section-header h3 {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .form-section-header p {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        margin: 2px 0 0;
    }

    .form-section-body {
        padding: 1.5rem;
    }

    /* ── Form Grid ── */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    .form-grid.col-3 {
        grid-template-columns: 1fr 1fr 1fr;
    }

    .field-span-2  { grid-column: span 2; }
    .field-span-full { grid-column: 1 / -1; }

    /* ── Field ── */
    .field-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .field-label {
        font-size: 0.85rem;
        font-weight: 800;
        color: #0f172a;
    }

    .field-label .required { color: #ef4444; margin-right: 3px; }
    .field-label .optional  { font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-right: 4px; }

    .form-control {
        padding: 12px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
        outline: none;
        background: #fff;
        transition: all 0.2s;
        width: 100%;
    }

    .form-control:focus {
        border-color: #1a4a2e;
        box-shadow: 0 0 0 3px rgba(26,74,46,0.1);
    }

    .form-control:disabled {
        background: #f8fafc;
        color: #475569;
        cursor: not-allowed;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* ── Age Method Toggle ── */
    .age-method-toggle {
        display: flex;
        gap: 0;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .age-method-btn {
        flex: 1;
        padding: 10px 14px;
        text-align: center;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        background: #f8fafc;
        border: none;
        border-left: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .age-method-btn:last-child { border-left: none; }

    .age-method-btn.active {
        background: #1a4a2e;
        color: white;
    }

    /* ── Upload ── */
    .upload-area {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafbfc;
    }

    .upload-area:hover {
        border-color: #1a4a2e;
        background: #f0fdf4;
    }

    .upload-area p {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        margin: 8px 0 0;
    }

    /* ── Current photo preview ── */
    .current-photo-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 1rem;
    }

    .current-photo {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid var(--border);
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 2rem;
    }

    .current-photo-info p {
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        margin: 0 0 4px;
    }

    .btn-remove-photo {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 5px 12px;
        border-radius: 6px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
    }

    .btn-remove-photo:hover { background: #fee2e2; }

    /* ── Conditional block ── */
    .conditional-block { display: none; flex-direction: column; gap: 1.25rem; }
    .conditional-block.visible { display: flex; }

    /* ── Readonly NOT-EDITABLE section ── */
    .readonly-section {
        background: #f8fafc;
        border-radius: 16px;
        border: 1.5px dashed #e2e8f0;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .readonly-section-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #94a3b8;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .readonly-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .readonly-chip {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .readonly-chip svg { color: #cbd5e1; }

    /* ── Buttons ── */
    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        margin-top: 2rem;
    }

    .btn-save {
        background: linear-gradient(135deg, #1a4a2e, #2d7a47);
        color: white;
        border: none;
        padding: 13px 30px;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(26,74,46,0.25);
    }

    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(26,74,46,0.35); }

    .btn-cancel-form {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 13px 24px;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-cancel-form:hover { background: #e2e8f0; color: #0f172a; }

    /* ═══ DIALOG ═══ */
    .dialog-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(4px); z-index: 1100; align-items: center; justify-content: center; }
    .dialog-backdrop.open { display: flex; }
    .dialog-box { background: #fff; border-radius: 20px; width: 100%; max-width: 440px; box-shadow: 0 30px 80px rgba(0,0,0,0.2); animation: modalIn 0.25s cubic-bezier(0.34,1.56,0.64,1); overflow: hidden; }
    @keyframes modalIn { from { transform: translateY(20px) scale(0.95); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
    .dialog-icon-wrap { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem; }
    .dialog-body { padding: 2.2rem 2rem 1.5rem; text-align: center; }
    .dialog-body h4 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
    .dialog-body p { font-size: 0.88rem; color: #64748b; font-weight: 600; line-height: 1.7; margin-bottom: 0; }
    .dialog-footer { padding: 1.2rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: center; }
    .btn-submit { padding: 11px 26px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-submit:hover { transform: translateY(-1px); }
    .btn-dialog-cancel { padding: 11px 22px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s; }
    .btn-dialog-cancel:hover { background: #e2e8f0; }

    /* ── Toast ── */
    .toast { position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(20px); background: #0f172a; color: #fff; padding: 14px 24px; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); z-index: 2000; opacity: 0; transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1); pointer-events: none; }
    .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    .toast.green { background: linear-gradient(135deg, #1a4a2e, #2d7a47); }
</style>
@endsection

@section('content')

<div class="form-page-header">
    <div>
        <h2>تعديل بيانات الحيوان الرسمية</h2>
        <p>يمكنك تعديل البيانات الأساسية الرسمية فقط — التشخيصات والسجلات الطبية غير قابلة للتعديل من هنا.</p>
    </div>
    <a href="/records/animals" class="btn-cancel-form">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        العودة للقائمة
    </a>
</div>

{{-- Identity Banner --}}
<div class="identity-banner">
    <div class="identity-avatar">🦁</div>
    <div class="identity-info">
        <h3>سيمبا</h3>
        <p>أسد أفريقي &nbsp;•&nbsp; القططية &nbsp;•&nbsp; ذكر</p>
    </div>
    <div class="identity-tag">
        <span class="tag-label">رقم الحيوان</span>
        <span class="tag-value">#ANM-0012</span>
    </div>
</div>

{{-- Readonly Notice --}}
<div class="notice-readonly">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        <strong>تنبيه:</strong> لا يسمح بتعديل التشخيصات أو العلاجات أو السجلات الرسمية الناتجة عن المسارات (النفوق، القرارات الطبية، الذبح الاضطراري...) من هذه الواجهة.
    </div>
</div>

<form id="editAnimalForm" onsubmit="return false;">

    {{-- ══════════════════ SECTION 1: BASIC DATA ══════════════════ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <h3>البيانات الأساسية</h3>
                <p>الهوية الرسمية للحيوان</p>
            </div>
        </div>
        <div class="form-section-body">
            <div class="form-grid">

                {{-- Animal Name --}}
                <div class="field-group">
                    <label class="field-label">اسم الحيوان <span class="optional">(اختياري — إن وجد)</span></label>
                    <input type="text" class="form-control" value="سيمبا" placeholder="مثال: سيمبا، صخر، لونا...">
                </div>

                {{-- Species/Type --}}
                <div class="field-group">
                    <label class="field-label"><span class="required">*</span> النوع</label>
                    <input type="text" class="form-control" value="أسد أفريقي" placeholder="مثال: أسد أفريقي، غزال الريم...">
                </div>

                {{-- Group --}}
                <div class="field-group">
                    <label class="field-label"><span class="required">*</span> المجموعة</label>
                    <select class="form-control">
                        <option>القططية</option>
                        <option>الطيور</option>
                        <option>الزواحف</option>
                        <option>الغزلان</option>
                        <option>القرود</option>
                        <option>الثدييات الصغيرة</option>
                        <option>الثدييات الكبيرة</option>
                        <option>الدب واللامة</option>
                    </select>
                </div>

                {{-- Gender --}}
                <div class="field-group">
                    <label class="field-label"><span class="required">*</span> الجنس</label>
                    <select class="form-control">
                        <option selected>ذكر</option>
                        <option>أنثى</option>
                    </select>
                </div>

                {{-- Distinguishing Marks --}}
                <div class="field-group field-span-2">
                    <label class="field-label">العلامات المميزة <span class="optional">(اختياري)</span></label>
                    <input type="text" class="form-control" value="وشم على الأذن اليسرى برقم 012" placeholder="مثال: لون مميز، ندبة، علامة طبيعية...">
                </div>

                {{-- Animal Photo --}}
                <div class="field-group field-span-2">
                    <label class="field-label">صورة الحيوان <span class="optional">(اختياري)</span></label>
                    {{-- Current photo preview (simulated) --}}
                    <div class="current-photo-wrap">
                        <div class="current-photo" style="font-size:2rem;">🦁</div>
                        <div class="current-photo-info">
                            <p>الصورة الحالية: simba_photo.jpg</p>
                            <button type="button" class="btn-remove-photo">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                إزالة الصورة
                            </button>
                        </div>
                    </div>
                    <label class="upload-area" for="animalPhoto">
                        <div style="color: #94a3b8;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <p>استبدال الصورة — اضغط لرفع صورة جديدة<br><span style="font-size:0.75rem; color:#94a3b8;">PNG, JPG حتى 5 ميجابايت</span></p>
                        <input type="file" id="animalPhoto" accept="image/*" style="display:none;" onchange="showFileName(this, 'photoName')">
                        <p id="photoName" style="color:#1a4a2e; font-weight:700; margin-top:6px;"></p>
                    </label>
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════ SECTION 2: AGE DATA ══════════════════ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <h3>تاريخ الميلاد أو العمر التقريبي</h3>
                <p>يمكنك تعديل طريقة احتساب العمر أو تصحيح القيم</p>
            </div>
        </div>
        <div class="form-section-body">
            <div class="field-group" style="margin-bottom: 1.5rem;">
                <label class="field-label"><span class="required">*</span> طريقة تحديد العمر</label>
                <div class="age-method-toggle">
                    <button type="button" class="age-method-btn active" id="btnBirthdate" onclick="setAgeMethod('birthdate')">
                        📅 تاريخ ميلاد معروف
                    </button>
                    <button type="button" class="age-method-btn" id="btnApprox" onclick="setAgeMethod('approx')">
                        🔢 عمر تقريبي عند التسجيل
                    </button>
                </div>
            </div>

            <div class="conditional-block visible" id="blockBirthdate">
                <div class="form-grid">
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> تاريخ الميلاد</label>
                        <input type="date" class="form-control" value="2018-02-14">
                    </div>
                    <div class="field-group" style="align-self: end;">
                        <label class="field-label" style="color:#64748b;">العمر المحسوب</label>
                        <input type="text" class="form-control" value="8 سنوات و 3 أشهر" disabled>
                    </div>
                </div>
            </div>

            <div class="conditional-block" id="blockApprox">
                <div class="form-grid col-3">
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> العمر التقريبي عند التسجيل</label>
                        <input type="number" class="form-control" placeholder="مثال: 4" min="1">
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> وحدة العمر</label>
                        <select class="form-control">
                            <option>أيام</option>
                            <option>أشهر</option>
                            <option selected>سنوات</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label" style="color:#64748b;">العمر الحالي التقريبي</label>
                        <input type="text" class="form-control" placeholder="سيُحسب تلقائياً..." disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ SECTION 3: ORIGIN & SOURCE ══════════════════ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <div>
                <h3>الأصل والمصدر</h3>
                <p>تصنيف أصل الحيوان وبيانات جهة مصدره</p>
            </div>
        </div>
        <div class="form-section-body">
            <div class="form-grid">
                <div class="field-group">
                    <label class="field-label"><span class="required">*</span> أصل الحيوان</label>
                    <select class="form-control">
                        <option selected>مولود داخل الحديقة</option>
                        <option>وارد من خارج الحديقة</option>
                    </select>
                </div>
                <div class="field-group">
                    <label class="field-label"><span class="required">*</span> مصدر الحيوان</label>
                    <input type="text" class="form-control" value="مولود داخل الحديقة حسب السجلات الورقية" placeholder="مثال: وارد من جهة رسمية، إهداء من جهة خاصة...">
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ SECTION 4: HISTORY ══════════════════ --}}
    <div class="form-section">
        <div class="form-section-header">
            <div class="section-icon" style="background:#fef3c7; color:#d97706;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div>
                <h3>التاريخ السابق قبل تشغيل النظام</h3>
                <p>اختياري — يمكن تحديث الملخص أو استبدال المرفق</p>
            </div>
        </div>
        <div class="form-section-body">
            <div class="form-grid">
                <div class="field-group field-span-2">
                    <label class="field-label">ملخص التاريخ السابق <span class="optional">(اختياري)</span></label>
                    <textarea class="form-control" rows="4" placeholder="اكتب ملخصًا عن التاريخ الطبي أو البيئي للحيوان قبل إدخاله في النظام...">لا توجد سجلات طبية موثقة قبل تشغيل النظام، الحيوان مولود في الحديقة منذ عام 2018.</textarea>
                </div>
                <div class="field-group field-span-2">
                    <label class="field-label">مرفق التاريخ السابق <span class="optional">(اختياري)</span></label>
                    <label class="upload-area" for="historyFile">
                        <div style="color: #94a3b8;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <p>رفع مرفق جديد أو استبدال المرفق الحالي<br><span style="font-size:0.75rem; color:#94a3b8;">PDF, PNG, JPG حتى 10 ميجابايت</span></p>
                        <input type="file" id="historyFile" accept=".pdf,image/*" style="display:none;" onchange="showFileName(this, 'historyFileName')">
                        <p id="historyFileName" style="color:#1a4a2e; font-weight:700; margin-top:6px;"></p>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ NOT EDITABLE ══════════════════ --}}
    <div class="readonly-section">
        <div class="readonly-section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            البيانات غير القابلة للتعديل من هذه الواجهة
        </div>
        <div class="readonly-chips">
            <span class="readonly-chip"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>التشخيصات</span>
            <span class="readonly-chip"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>العلاجات والجرعات</span>
            <span class="readonly-chip"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>القرارات الطبية</span>
            <span class="readonly-chip"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>قرار الذبح الاضطراري</span>
            <span class="readonly-chip"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>نتائج التشريح</span>
            <span class="readonly-chip"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>قرارات الإفراج الصحي</span>
            <span class="readonly-chip"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>حالة النفوق</span>
            <span class="readonly-chip"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>التوصيات الغذائية العلاجية</span>
        </div>
    </div>

    {{-- ══════════════════ FORM ACTIONS ══════════════════ --}}
    <div class="form-actions">
        <button type="button" class="btn-save" onclick="saveEdits()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            حفظ التعديلات
        </button>
        <a href="/records/animals" class="btn-cancel-form">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            إلغاء
        </a>
    </div>

</form>

{{-- ══════════════════ SUCCESS TOAST ══════════════════ --}}
<div class="toast green" id="toastMsg">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastText">تم تعديل بيانات الحيوان بنجاح.</span>
</div>

@endsection

@section('scripts')
<script>
    // ── Age Method Toggle ──
    function setAgeMethod(method) {
        document.querySelectorAll('.age-method-btn').forEach(b => b.classList.remove('active'));
        ['blockBirthdate', 'blockApprox'].forEach(id => {
            const el = document.getElementById(id);
            el.classList.remove('visible');
            el.style.display = 'none';
        });

        if (method === 'birthdate') {
            document.getElementById('btnBirthdate').classList.add('active');
            const b = document.getElementById('blockBirthdate');
            b.classList.add('visible'); b.style.display = 'flex';
        } else {
            document.getElementById('btnApprox').classList.add('active');
            const b = document.getElementById('blockApprox');
            b.classList.add('visible'); b.style.display = 'flex';
        }
    }

    setAgeMethod('birthdate');

    // ── File Upload ──
    function showFileName(input, targetId) {
        const t = document.getElementById(targetId);
        if (input.files && input.files[0]) t.innerText = '📎 ' + input.files[0].name;
    }

    // ── Save & Toast ──
    function saveEdits() {
        showToast('✅ تم تعديل بيانات الحيوان بنجاح.');
    }

    function showToast(msg) {
        const t = document.getElementById('toastMsg');
        document.getElementById('toastText').innerText = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }
</script>
@endsection
