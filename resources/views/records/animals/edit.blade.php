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
@php
    $portalBase = $portalBase ?? '/records';
    $ageMethod = old('age_method', $animal->age_method ?? 'birth');
@endphp

@if($errors->any())
<div class="notice-readonly" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;margin-bottom:1rem;">
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

@if(session('success'))
<div class="notice-readonly" style="background:#f0fdf4;border-color:#bbf7d0;color:#166534;margin-bottom:1rem;">{{ session('success') }}</div>
@endif

<div class="form-page-header" style="justify-content:flex-end;">
    <a href="{{ $portalBase }}/animals/{{ $animal->code }}" class="btn-cancel-form">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        العودة لملف الحيوان
    </a>
</div>

<div class="identity-banner">
    <div class="identity-avatar">🦁</div>
    <div class="identity-info">
        <h3>{{ $animal->name ?: $animal->species }}</h3>
        <p>{{ $animal->species }} &nbsp;•&nbsp; {{ $animal->group }} &nbsp;•&nbsp; {{ $animal->gender }}</p>
    </div>
    <div class="identity-tag">
        <span class="tag-label">رقم الحيوان</span>
        <span class="tag-value">#{{ $animal->code }}</span>
    </div>
</div>

<div class="notice-readonly">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div><strong>تنبيه:</strong> يمكن تعديل البيانات الأساسية فقط. السجلات الطبية والقرارات الرسمية لا تُعدّل من هنا.</div>
</div>

<form id="editAnimalForm" method="POST" action="{{ route('records.animals.update', $animal) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="age_method" id="ageMethodField" value="{{ $ageMethod }}">

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

                <div class="field-group">
                    <label class="field-label">رقم الحيوان</label>
                    <input type="text" class="form-control readonly" value="#{{ $animal->code }}" readonly>
                </div>
                <div class="field-group">
                    <label class="field-label">المجموعة</label>
                    <input type="text" class="form-control readonly" value="{{ $animal->group }}" readonly>
                </div>
                <div class="field-group">
                    <label class="field-label">اسم الحيوان <span class="optional">(اختياري)</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $animal->name) }}">
                </div>
                <div class="field-group">
                    <label class="field-label"><span class="required">*</span> النوع</label>
                    <input type="text" name="species" class="form-control" value="{{ old('species', $animal->species) }}" required>
                </div>
                <div class="field-group">
                    <label class="field-label"><span class="required">*</span> الجنس</label>
                    <select name="gender" class="form-control" required>
                        <option value="ذكر" @selected(old('gender', $animal->gender) === 'ذكر')>ذكر</option>
                        <option value="أنثى" @selected(old('gender', $animal->gender) === 'أنثى')>أنثى</option>
                    </select>
                </div>
                <div class="field-group field-span-2">
                    <label class="field-label">العلامات المميزة <span class="optional">(اختياري)</span></label>
                    <input type="text" name="distinguishing_marks" class="form-control" value="{{ old('distinguishing_marks', $animal->distinguishing_marks) }}">
                </div>
                <div class="field-group field-span-2">
                    <label class="field-label">صورة الحيوان <span class="optional">(اختياري)</span></label>
                    @if($animal->displayPhotoUrl())
                    <div style="margin-bottom:10px;">
                        <img src="{{ $animal->displayPhotoUrl() }}" alt="" style="width:72px;height:72px;border-radius:12px;object-fit:cover;">
                        <label style="display:flex;align-items:center;gap:8px;margin-top:8px;font-size:0.85rem;font-weight:700;">
                            <input type="checkbox" name="remove_photo" value="1"> إزالة الصورة الحالية
                        </label>
                    </div>
                    @endif
                    <label class="upload-area" for="animalPhoto">
                        <input type="file" id="animalPhoto" name="photo" accept="image/*" style="display:none;" onchange="showFileName(this, 'photoName')">
                        <span>رفع صورة جديدة</span>
                        <p id="photoName" style="color:#1a4a2e;font-weight:700;margin-top:6px;"></p>
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
                    <button type="button" class="age-method-btn {{ $ageMethod === 'birth' ? 'active' : '' }}" id="btnBirthdate" onclick="setAgeMethod('birthdate')">📅 تاريخ ميلاد معروف</button>
                    <button type="button" class="age-method-btn {{ $ageMethod === 'approx' ? 'active' : '' }}" id="btnApprox" onclick="setAgeMethod('approx')">🔢 عمر تقريبي</button>
                </div>
            </div>

            <div class="conditional-block {{ $ageMethod === 'birth' ? 'visible' : '' }}" id="blockBirthdate" style="{{ $ageMethod === 'birth' ? 'display:flex;' : 'display:none;' }}">
                <div class="form-grid">
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> تاريخ الميلاد</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $animal->birth_date?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
            <div class="conditional-block {{ $ageMethod === 'approx' ? 'visible' : '' }}" id="blockApprox" style="{{ $ageMethod === 'approx' ? 'display:flex;' : 'display:none;' }}">
                <div class="form-grid col-3">
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> العمر التقريبي</label>
                        <input type="number" name="approx_age_value" class="form-control" value="{{ old('approx_age_value', $animal->approx_age_value) }}" min="1">
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> وحدة العمر</label>
                        <select name="approx_age_unit" class="form-control">
                            <option value="أيام" @selected(old('approx_age_unit', $animal->approx_age_unit) === 'أيام')>أيام</option>
                            <option value="أشهر" @selected(old('approx_age_unit', $animal->approx_age_unit) === 'أشهر')>أشهر</option>
                            <option value="سنوات" @selected(old('approx_age_unit', $animal->approx_age_unit ?: 'سنوات') === 'سنوات')>سنوات</option>
                        </select>
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
                    <select name="origin" class="form-control" required>
                        <option value="مولود داخل الحديقة" @selected(old('origin', $animal->origin) === 'مولود داخل الحديقة')>مولود داخل الحديقة</option>
                        <option value="وارد من خارج الحديقة" @selected(old('origin', $animal->origin) === 'وارد من خارج الحديقة')>وارد من خارج الحديقة</option>
                    </select>
                </div>
                <div class="field-group">
                    <label class="field-label"><span class="required">*</span> مصدر الحيوان</label>
                    <input type="text" name="animal_source" class="form-control" value="{{ old('animal_source', $animal->registration_note) }}" required>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════ FORM ACTIONS ══════════════════ --}}
    <div class="form-actions">
        <button type="submit" class="btn-save">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            حفظ التعديلات
        </button>
        <a href="{{ $portalBase }}/animals/{{ $animal->code }}" class="btn-cancel-form">إلغاء</a>
    </div>

</form>

@endsection

@section('scripts')
<script>
    function setAgeMethod(method) {
        document.getElementById('ageMethodField').value = method === 'birthdate' ? 'birth' : 'approx';
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

    function showFileName(input, targetId) {
        const t = document.getElementById(targetId);
        if (input.files && input.files[0]) t.innerText = '📎 ' + input.files[0].name;
    }
</script>
@endsection
