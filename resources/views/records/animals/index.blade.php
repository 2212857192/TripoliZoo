@extends($__layout ?? 'records.layout')
@section('title', 'قائمة الحيوانات | السجلات والتوثيق')
@section('page_title', 'قائمة الحيوانات داخل الحديقة')

@section('styles')
@include('records.logs.partials.vet-log-styles')
<style>
    .legend-wrap { display:flex; align-items:center; gap:6px; margin-right:auto; }
    .legend-dot { width:10px; height:10px; border-radius:50%; background:#15803d; }
    .legend-text { font-size:0.8rem; font-weight:700; color:#64748b; }

    .animal-id { font-family:'Courier New',monospace; font-size:0.8rem; background:#f8fafc; padding:3px 8px; border-radius:6px; color:#334155; font-weight:800; display:inline-block; border:1px solid #e2e8f0; }
    .animal-id.monitoring { color:#15803d; background:#f0fdf4; border-color:#bbf7d0; }

    .btn-tbl.edit:hover { color:#16a34a; background:#f0fdf4; border-color:#bbf7d0; }
    .btn-tbl.pdf:hover { color:#dc2626; background:#fef2f2; border-color:#fecaca; }

    .page-header-actions { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .btn-add {
        display:inline-flex; align-items:center; gap:8px;
        padding:8px 16px; background:#16a34a; border:none; border-radius:8px;
        font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; color:#fff;
        cursor:pointer; transition:all 0.2s; box-shadow:0 2px 4px rgba(22,163,74,0.2);
    }
    .btn-add:hover { background:#15803d; box-shadow:0 4px 8px rgba(22,163,74,0.3); }

    /* ── MODAL – VET HOSPITAL STYLE ── */
    .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); backdrop-filter:blur(5px); z-index:1000; align-items:center; justify-content:center; }
    .modal-backdrop.open { display:flex; }
    .modal-box { background:#fff; border-radius:20px; width:100%; max-width:720px; max-height:92vh; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 25px 50px rgba(0,0,0,.15); animation:modalIn 0.3s cubic-bezier(.4,0,.2,1); }
    @keyframes modalIn { from { transform:translateY(24px) scale(.97); opacity:0; } to { transform:translateY(0) scale(1); opacity:1; } }

    .modal-header { background:#fff; border-bottom:1px solid #e2e8f0; padding:1.2rem 1.5rem 0; display:flex; justify-content:space-between; align-items:flex-end; }
    .modal-title-wrap { padding-bottom:.8rem; }
    .modal-title-wrap h3 { margin:0; font-size:1.1rem; font-weight:800; color:#0f172a; }
    .modal-title-wrap span { font-size:0.8rem; color:#64748b; font-weight:600; }
    .modal-close { width:32px; height:32px; border-radius:8px; background:#fff; border:1px solid #e2e8f0; color:#64748b; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.1rem; font-weight:700; transition:all 0.2s; margin-bottom:10px; }
    .modal-close:hover { background:#f8fafc; color:#0f172a; }

    .modal-body { padding:1.5rem; overflow-y:auto; max-height:68vh; }

    /* Form inside modal */
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
    .form-grid.col-3 { grid-template-columns:1fr 1fr 1fr; }
    .field-span-2 { grid-column:span 2; }
    .field-group { display:flex; flex-direction:column; gap:6px; }
    .field-label { font-size:0.82rem; font-weight:800; color:#0f172a; }
    .required { color:#ef4444; }
    .optional { font-size:0.72rem; font-weight:600; color:#94a3b8; margin-right:3px; }
    .form-control { padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.88rem; font-weight:600; color:#0f172a; outline:none; background:#fff; transition:all 0.2s; width:100%; }
    .form-control:focus { border-color:#1a4a2e; box-shadow:0 0 0 3px rgba(26,74,46,.1); }
    .form-control:disabled { background:#f8fafc; color:#64748b; cursor:default; }
    .form-control.generated { background:#f0fdf4; color:#16a34a; font-weight:800; font-family:'Courier New',monospace; letter-spacing:1px; border-color:#bbf7d0; }
    textarea.form-control { resize:vertical; min-height:90px; }

    /* Section divider inside modal */
    .modal-section { margin-bottom:1.5rem; }
    .modal-section-title { display:flex; align-items:center; gap:8px; font-size:0.88rem; font-weight:800; color:#0f172a; margin-bottom:1rem; padding-bottom:8px; border-bottom:2px solid #f1f5f9; }
    .modal-section-title .sec-icon { width:30px; height:30px; border-radius:8px; background:#e6f4ea; color:#1a4a2e; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .modal-section-title.orange .sec-icon { background:#fef3c7; color:#d97706; }

    /* Age Toggle */
    .age-toggle { display:flex; border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden; }
    .age-toggle-btn { flex:1; padding:9px 12px; text-align:center; cursor:pointer; font-family:'Cairo',sans-serif; font-size:0.82rem; font-weight:700; color:#64748b; background:#f8fafc; border:none; border-left:1px solid #e2e8f0; transition:all 0.2s; }
    .age-toggle-btn:last-child { border-left:none; }
    .age-toggle-btn.active { background:#1a4a2e; color:#fff; }

    .cond-block { display:none; flex-direction:column; gap:1rem; margin-top:1rem; }
    .cond-block.visible { display:flex; }

    /* Upload */
    .upload-area { border:2px dashed #e2e8f0; border-radius:10px; padding:18px; text-align:center; cursor:pointer; transition:all 0.2s; background:#fafbfc; }
    .upload-area:hover { border-color:#1a4a2e; background:#f0fdf4; }
    .upload-area p { font-size:0.8rem; color:#64748b; font-weight:600; margin:6px 0 0; }

    /* Notice */
    .notice-yellow { background:#fefce8; border:1px solid #fef08a; border-right:3px solid #eab308; border-radius:10px; padding:10px 14px; font-size:0.82rem; font-weight:700; color:#713f12; margin-bottom:1.2rem; display:flex; gap:8px; }
    .notice-red { background:#fef2f2; border:1px solid #fecaca; border-right:3px solid #ef4444; border-radius:10px; padding:10px 14px; font-size:0.82rem; font-weight:700; color:#7f1d1d; margin-bottom:1.2rem; display:flex; gap:8px; }

    /* Readonly chips */
    .readonly-chips-wrap { background:#f8fafc; border:1.5px dashed #e2e8f0; border-radius:12px; padding:1rem 1.2rem; }
    .readonly-chips-label { font-size:0.75rem; font-weight:800; color:#94a3b8; margin-bottom:8px; display:flex; align-items:center; gap:5px; }
    .chips-row { display:flex; flex-wrap:wrap; gap:6px; }
    .chip { background:#fff; border:1px solid #e2e8f0; border-radius:7px; padding:4px 10px; font-size:0.75rem; font-weight:700; color:#94a3b8; }

    /* Identity bar (edit modal) */
    .identity-bar { background:linear-gradient(135deg,#1a4a2e,#2d7a47); border-radius:12px; padding:1rem 1.2rem; display:flex; align-items:center; gap:14px; color:#fff; margin-bottom:1.2rem; }
    .identity-bar .avatar { width:46px; height:46px; border-radius:12px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; }
    .identity-bar .info h4 { font-size:1rem; font-weight:800; margin:0 0 2px; }
    .identity-bar .info p { font-size:0.78rem; opacity:.8; font-weight:600; margin:0; }
    .identity-bar .id-tag { margin-right:auto; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.2); border-radius:8px; padding:6px 12px; font-size:0.82rem; font-weight:900; font-family:'Courier New',monospace; }

    /* Modal footer */
    .modal-footer { background:#fff; border-top:1px solid #e2e8f0; padding:1.2rem 1.5rem; display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap; }
    .btn-submit { padding:10px 24px; background:linear-gradient(135deg,#1a4a2e,#2d7a47); color:#fff; border:none; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.88rem; font-weight:800; cursor:pointer; transition:all 0.2s; box-shadow:0 4px 12px rgba(45,122,71,.3); display:inline-flex; align-items:center; gap:6px; }
    .btn-submit:hover { transform:translateY(-1px); }
    .btn-cancel { padding:10px 20px; background:#fff; color:#475569; border:1px solid #e2e8f0; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.88rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-cancel:hover { background:#f8fafc; }

    /* Sub-dialogs */
    .dialog-backdrop { display:none; position:fixed; inset:0; background:rgba(15,23,42,.45); backdrop-filter:blur(3px); z-index:1100; align-items:center; justify-content:center; }
    .dialog-backdrop.open { display:flex; }
    .dialog-box { background:#fff; border-radius:18px; width:100%; max-width:460px; box-shadow:0 30px 80px rgba(0,0,0,.2); animation:modalIn 0.25s cubic-bezier(.34,1.56,.64,1); overflow:hidden; }
    .dialog-icon-wrap { width:62px; height:62px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; font-size:1.8rem; }
    .dialog-body { padding:2rem 2rem 1.5rem; text-align:center; }
    .dialog-body h4 { font-size:1.1rem; font-weight:800; color:#0f172a; margin-bottom:8px; }
    .dialog-body p { font-size:0.85rem; color:#64748b; font-weight:600; line-height:1.6; margin-bottom:0; }
    .dialog-footer { padding:1rem 1.5rem; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; gap:10px; justify-content:center; }

    /* Toast */
    .toast { position:fixed; bottom:2rem; left:50%; transform:translateX(-50%) translateY(20px); background:#0f172a; color:#fff; padding:14px 24px; border-radius:12px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:700; display:flex; align-items:center; gap:10px; box-shadow:0 10px 30px rgba(0,0,0,.25); z-index:2000; opacity:0; transition:all 0.4s cubic-bezier(.34,1.56,.64,1); pointer-events:none; }
    .toast.show { opacity:1; transform:translateX(-50%) translateY(0); }
    .toast.green { background:linear-gradient(135deg,#1a4a2e,#2d7a47); }
</style>
@endsection

@section('content')

{{-- ═══════ HEADER & FILTERS ═══════ --}}
<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>🐾 قائمة الحيوانات داخل الحديقة</h2>
            <p>عرض الحيوانات الموجودة داخل الحديقة فعليًا وغير الخارجة نهائيًا.</p>
        </div>
        <div class="page-header-actions">
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="num">342</div>
                    <div class="lbl">حيوان مسجّل</div>
                </div>
                <div class="hero-stat">
                    <div class="num">3</div>
                    <div class="lbl">قيد المتابعة</div>
                </div>
            </div>
            <button type="button" class="btn-add" onclick="openAddModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                إضافة حيوان
            </button>
        </div>
    </div>


    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، الاسم، أو النوع...">
        </div>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option>القططية</option>
            <option>الطيور</option>
            <option>الزواحف</option>
            <option>الغزلان</option>
            <option>القرود</option>
            <option>الثدييات الصغيرة</option>
            <option>الثدييات الكبيرة</option>
            <option>الدب واللامة</option>
        </select>
        <div class="legend-wrap">
            <span class="legend-dot"></span>
            <span class="legend-text">المواليد قيد المتابعة</span>
        </div>
    </div>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">الحيوانات المسجلة</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الحيوان</th>
                    <th>اسم الحيوان</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>الجنس</th>
                    <th>العمر</th>
                    <th>تاريخ التسجيل</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="animal-id">#ANM-0012</span></td>
                    <td style="font-weight:700;">سيمبا</td>
                    <td>أسد أفريقي</td>
                    <td>القططية</td>
                    <td>ذكر</td>
                    <td style="color:#64748b; font-size:0.85rem;">8 سنوات</td>
                    <td style="color:#64748b; font-size:0.85rem;">2018-02-14</td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="/records/animals/ANM-0012" class="btn-tbl btn-tbl-view" title="عرض الملف">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button onclick="openEditModal('ANM-0012','سيمبا')" class="btn-tbl edit" title="تعديل البيانات">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button onclick="showToast('📄 جاري تصدير PDF...')" class="btn-tbl pdf" title="تصدير PDF">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="animal-id monitoring">#ANM-1046</span></td>
                    <td style="color:#94a3b8; font-style:italic;">—</td>
                    <td>قرد مكاك</td>
                    <td>القرود</td>
                    <td>أنثى</td>
                    <td style="color:#64748b; font-size:0.85rem;">يومان</td>
                    <td style="color:#64748b; font-size:0.85rem;">2026-06-05</td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="/records/animals/ANM-1046" class="btn-tbl btn-tbl-view" title="عرض الملف">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button onclick="openEditModal('ANM-1046',null)" class="btn-tbl edit" title="تعديل البيانات">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button onclick="showToast('📄 جاري تصدير PDF...')" class="btn-tbl pdf" title="تصدير PDF">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="animal-id">#ANM-1045</span></td>
                    <td style="color:#94a3b8; font-style:italic;">—</td>
                    <td>غزال الريم</td>
                    <td>الغزلان</td>
                    <td>ذكر</td>
                    <td style="color:#64748b; font-size:0.85rem;">سنتان و 3 أشهر</td>
                    <td style="color:#64748b; font-size:0.85rem;">2026-06-07</td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="/records/animals/ANM-1045" class="btn-tbl btn-tbl-view" title="عرض الملف">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button onclick="openEditModal('ANM-1045',null)" class="btn-tbl edit" title="تعديل البيانات">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button onclick="showToast('📄 جاري تصدير PDF...')" class="btn-tbl pdf" title="تصدير PDF">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- ═══ MODAL: إضافة حيوان ═══ --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="addModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <h3>➕ إضافة حيوان داخل الحديقة</h3>
                <span>بيانات حيوان موجود فعليًا ولم يكن مسجلًا في النظام</span>
            </div>
            <button class="modal-close" onclick="closeModal('addModal')">✕</button>
        </div>

        <div class="modal-body">

            <div class="notice-yellow">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0; margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                هذه الواجهة للحيوانات الموجودة قبل تشغيل النظام فقط. المواليد والحجر الصحي لها مسارات خاصة.
            </div>

            {{-- Section 1: بيانات أساسية --}}
            <div class="modal-section">
                <div class="modal-section-title">
                    <div class="sec-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
                    البيانات الأساسية
                </div>
                <div class="form-grid">
                    <div class="field-group">
                        <label class="field-label">رقم الحيوان <span class="optional">(يولده النظام)</span></label>
                        <input type="text" class="form-control generated" value="#ANM-1050" readonly>
                    </div>
                    <div class="field-group">
                        <label class="field-label">اسم الحيوان <span class="optional">(اختياري)</span></label>
                        <input type="text" class="form-control" placeholder="مثال: سيمبا، صخر...">
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> النوع</label>
                        <input type="text" class="form-control" placeholder="مثال: أسد أفريقي، غزال الريم...">
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> المجموعة</label>
                        <select class="form-control">
                            <option value="" disabled selected>اختر المجموعة...</option>
                            <option>القططية</option><option>الطيور</option><option>الزواحف</option>
                            <option>الغزلان</option><option>القرود</option><option>الثدييات الصغيرة</option>
                            <option>الثدييات الكبيرة</option><option>الدب واللامة</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> الجنس</label>
                        <select class="form-control">
                            <option value="" disabled selected>اختر الجنس...</option>
                            <option>ذكر</option><option>أنثى</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">العلامات المميزة <span class="optional">(اختياري)</span></label>
                        <input type="text" class="form-control" placeholder="مثال: ندبة، وشم، لون مميز...">
                    </div>
                    <div class="field-group field-span-2">
                        <label class="field-label">صورة الحيوان <span class="optional">(اختياري)</span></label>
                        <label class="upload-area" for="addPhoto">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color:#94a3b8;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <p>اضغط لرفع صورة الحيوان<br><span style="font-size:0.72rem; color:#94a3b8;">PNG, JPG حتى 5 ميجابايت</span></p>
                            <input type="file" id="addPhoto" accept="image/*" style="display:none;" onchange="showFileName(this,'addPhotoName')">
                            <p id="addPhotoName" style="color:#1a4a2e; font-weight:700; margin-top:4px;"></p>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Section 2: بيانات العمر --}}
            <div class="modal-section">
                <div class="modal-section-title">
                    <div class="sec-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    بيانات العمر
                </div>
                <div class="field-group" style="margin-bottom:1rem;">
                    <label class="field-label"><span class="required">*</span> طريقة تحديد العمر</label>
                    <div class="age-toggle">
                        <button type="button" class="age-toggle-btn active" id="addBtnBirth" onclick="setAge('add','birth')">📅 تاريخ ميلاد معروف</button>
                        <button type="button" class="age-toggle-btn" id="addBtnApprox" onclick="setAge('add','approx')">🔢 عمر تقريبي</button>
                    </div>
                </div>
                <div class="cond-block visible" id="addBlockBirth">
                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label"><span class="required">*</span> تاريخ الميلاد</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="field-group" style="align-self:end;">
                            <label class="field-label" style="color:#64748b;">العمر المحسوب</label>
                            <input type="text" class="form-control" placeholder="سيُحسب تلقائياً..." disabled>
                        </div>
                    </div>
                </div>
                <div class="cond-block" id="addBlockApprox">
                    <div class="form-grid col-3">
                        <div class="field-group">
                            <label class="field-label"><span class="required">*</span> العمر التقريبي</label>
                            <input type="number" class="form-control" placeholder="مثال: 4" min="1">
                        </div>
                        <div class="field-group">
                            <label class="field-label"><span class="required">*</span> الوحدة</label>
                            <select class="form-control"><option>أيام</option><option>أشهر</option><option selected>سنوات</option></select>
                        </div>
                        <div class="field-group">
                            <label class="field-label" style="color:#64748b;">العمر الحالي</label>
                            <input type="text" class="form-control" placeholder="سيُحسب تلقائياً..." disabled>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: الأصل والمصدر --}}
            <div class="modal-section">
                <div class="modal-section-title">
                    <div class="sec-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
                    الأصل والمصدر
                </div>
                <div class="form-grid">
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> أصل الحيوان</label>
                        <select class="form-control">
                            <option value="" disabled selected>اختر الأصل...</option>
                            <option>مولود داخل الحديقة</option>
                            <option>وارد من خارج الحديقة</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> مصدر الحيوان</label>
                        <input type="text" class="form-control" placeholder="مثال: مولود في الحديقة حسب السجلات الورقية...">
                    </div>
                </div>
            </div>

            {{-- Section 4: التاريخ السابق --}}
            <div class="modal-section">
                <div class="modal-section-title orange">
                    <div class="sec-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    التاريخ السابق قبل تشغيل النظام <span class="optional">(اختياري)</span>
                </div>
                <div class="form-grid">
                    <div class="field-group field-span-2">
                        <label class="field-label">ملخص التاريخ السابق</label>
                        <textarea class="form-control" rows="3" placeholder="أهم التشخيصات أو العلاجات أو الجرعات القديمة إن وجدت..."></textarea>
                    </div>
                    <div class="field-group field-span-2">
                        <label class="field-label">مرفق التاريخ السابق <span class="optional">(PDF أو صورة)</span></label>
                        <label class="upload-area" for="addHistory">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color:#94a3b8;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <p>ارفع ملف من السجل الورقي القديم<br><span style="font-size:0.72rem; color:#94a3b8;">PDF, PNG, JPG حتى 10 ميجابايت</span></p>
                            <input type="file" id="addHistory" accept=".pdf,image/*" style="display:none;" onchange="showFileName(this,'addHistoryName')">
                            <p id="addHistoryName" style="color:#1a4a2e; font-weight:700; margin-top:4px;"></p>
                        </label>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('addModal')">إلغاء</button>
            <button class="btn-submit" onclick="submitAdd()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                حفظ وإنشاء الملف
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- ═══ MODAL: تعديل بيانات الحيوان ═══ --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <h3>✏️ تعديل بيانات الحيوان الرسمية</h3>
                <span>البيانات الأساسية فقط — لا يشمل السجلات الطبية أو القرارات</span>
            </div>
            <button class="modal-close" onclick="closeModal('editModal')">✕</button>
        </div>

        <div class="modal-body">

            <div class="identity-bar" id="editIdentityBar">
                <div class="avatar" id="editAvatar">🐾</div>
                <div class="info">
                    <h4 id="editAnimalName">—</h4>
                    <p id="editAnimalMeta">—</p>
                </div>
                <div class="id-tag" id="editAnimalId">—</div>
            </div>

            <div class="notice-red">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0; margin-top:1px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                التشخيصات والعلاجات والقرارات الطبية والسجلات الرسمية الناتجة عن المسارات غير قابلة للتعديل من هنا.
            </div>

            {{-- Section 1 --}}
            <div class="modal-section">
                <div class="modal-section-title">
                    <div class="sec-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
                    البيانات الأساسية
                </div>
                <div class="form-grid">
                    <div class="field-group">
                        <label class="field-label">اسم الحيوان <span class="optional">(اختياري)</span></label>
                        <input type="text" class="form-control" id="editName" value="سيمبا">
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> النوع</label>
                        <input type="text" class="form-control" value="أسد أفريقي">
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> المجموعة</label>
                        <select class="form-control">
                            <option selected>القططية</option><option>الطيور</option><option>الزواحف</option>
                            <option>الغزلان</option><option>القرود</option><option>الثدييات الصغيرة</option>
                            <option>الثدييات الكبيرة</option><option>الدب واللامة</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> الجنس</label>
                        <select class="form-control"><option selected>ذكر</option><option>أنثى</option></select>
                    </div>
                    <div class="field-group field-span-2">
                        <label class="field-label">العلامات المميزة <span class="optional">(اختياري)</span></label>
                        <input type="text" class="form-control" value="وشم على الأذن اليسرى برقم 012">
                    </div>
                    <div class="field-group field-span-2">
                        <label class="field-label">صورة الحيوان <span class="optional">(اختياري)</span></label>
                        <label class="upload-area" for="editPhoto">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color:#94a3b8;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <p>استبدال الصورة الحالية (simba_photo.jpg)<br><span style="font-size:0.72rem; color:#94a3b8;">PNG, JPG حتى 5 ميجابايت</span></p>
                            <input type="file" id="editPhoto" accept="image/*" style="display:none;" onchange="showFileName(this,'editPhotoName')">
                            <p id="editPhotoName" style="color:#1a4a2e; font-weight:700; margin-top:4px;"></p>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Section 2: العمر --}}
            <div class="modal-section">
                <div class="modal-section-title">
                    <div class="sec-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    تاريخ الميلاد أو العمر التقريبي
                </div>
                <div class="field-group" style="margin-bottom:1rem;">
                    <label class="field-label"><span class="required">*</span> طريقة تحديد العمر</label>
                    <div class="age-toggle">
                        <button type="button" class="age-toggle-btn active" id="editBtnBirth" onclick="setAge('edit','birth')">📅 تاريخ ميلاد معروف</button>
                        <button type="button" class="age-toggle-btn" id="editBtnApprox" onclick="setAge('edit','approx')">🔢 عمر تقريبي</button>
                    </div>
                </div>
                <div class="cond-block visible" id="editBlockBirth">
                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label"><span class="required">*</span> تاريخ الميلاد</label>
                            <input type="date" class="form-control" value="2018-02-14">
                        </div>
                        <div class="field-group" style="align-self:end;">
                            <label class="field-label" style="color:#64748b;">العمر المحسوب</label>
                            <input type="text" class="form-control" value="8 سنوات و 3 أشهر" disabled>
                        </div>
                    </div>
                </div>
                <div class="cond-block" id="editBlockApprox">
                    <div class="form-grid col-3">
                        <div class="field-group"><label class="field-label"><span class="required">*</span> العمر التقريبي</label><input type="number" class="form-control" min="1"></div>
                        <div class="field-group"><label class="field-label"><span class="required">*</span> الوحدة</label><select class="form-control"><option>أيام</option><option>أشهر</option><option selected>سنوات</option></select></div>
                        <div class="field-group"><label class="field-label" style="color:#64748b;">العمر الحالي</label><input type="text" class="form-control" disabled placeholder="سيُحسب..."></div>
                    </div>
                </div>
            </div>

            {{-- Section 3: الأصل --}}
            <div class="modal-section">
                <div class="modal-section-title">
                    <div class="sec-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/></svg></div>
                    الأصل والمصدر
                </div>
                <div class="form-grid">
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> أصل الحيوان</label>
                        <select class="form-control"><option selected>مولود داخل الحديقة</option><option>وارد من خارج الحديقة</option></select>
                    </div>
                    <div class="field-group">
                        <label class="field-label"><span class="required">*</span> مصدر الحيوان</label>
                        <input type="text" class="form-control" value="مولود داخل الحديقة حسب السجلات الورقية">
                    </div>
                </div>
            </div>

            {{-- Section 4: التاريخ السابق --}}
            <div class="modal-section">
                <div class="modal-section-title orange">
                    <div class="sec-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    التاريخ السابق <span class="optional">(اختياري)</span>
                </div>
                <div class="form-grid">
                    <div class="field-group field-span-2">
                        <label class="field-label">ملخص التاريخ السابق</label>
                        <textarea class="form-control" rows="3">لا توجد سجلات طبية موثقة قبل تشغيل النظام، الحيوان مولود في الحديقة منذ عام 2018.</textarea>
                    </div>
                    <div class="field-group field-span-2">
                        <label class="field-label">مرفق التاريخ السابق</label>
                        <label class="upload-area" for="editHistory">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color:#94a3b8;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <p>رفع مرفق جديد أو استبدال الحالي<br><span style="font-size:0.72rem; color:#94a3b8;">PDF, PNG, JPG</span></p>
                            <input type="file" id="editHistory" accept=".pdf,image/*" style="display:none;" onchange="showFileName(this,'editHistoryName')">
                            <p id="editHistoryName" style="color:#1a4a2e; font-weight:700; margin-top:4px;"></p>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Readonly chips --}}
            <div class="readonly-chips-wrap">
                <div class="readonly-chips-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    البيانات غير قابلة للتعديل من هذه الواجهة
                </div>
                <div class="chips-row">
                    <span class="chip">التشخيصات</span>
                    <span class="chip">العلاجات والجرعات</span>
                    <span class="chip">القرارات الطبية</span>
                    <span class="chip">قرار الذبح الاضطراري</span>
                    <span class="chip">نتائج التشريح</span>
                    <span class="chip">قرارات الإفراج الصحي</span>
                    <span class="chip">حالة النفوق</span>
                    <span class="chip">التوصيات الغذائية</span>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('editModal')">إلغاء</button>
            <button class="btn-submit" onclick="submitEdit()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                حفظ التعديلات
            </button>
        </div>
    </div>
</div>

{{-- ═══ SUCCESS DIALOG (add) ═══ --}}
<div class="dialog-backdrop" id="successAddDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#f0fdf4;">✅</div>
            <h4>تمت إضافة الحيوان بنجاح!</h4>
            <p>تم إنشاء ملف الحيوان الرسمي وإضافته إلى قائمة الحيوانات داخل الحديقة.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('successAddDialog'); closeModal('addModal');">قائمة الحيوانات</button>
            <button class="btn-submit" onclick="window.location.href='/records/animals/1050'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                عرض ملف الحيوان
            </button>
        </div>
    </div>
</div>

{{-- ═══ TOAST ═══ --}}
<div class="toast green" id="toastMsg">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastText">تمت العملية بنجاح</span>
</div>

@endsection

@section('scripts')
<script>
    // ── Modal open/close ──
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    // Close on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', function(e) { if (e.target === this) closeModal(this.id); });
    });

    function openAddModal() { openModal('addModal'); }

    function openViewModal(id, name) {
        window.location.href = '/records/animals/' + id;
    }

    function openEditModal(id, name) {
        document.getElementById('editAnimalId').innerText = '#' + id;
        document.getElementById('editAnimalName').innerText = name || '—';
        document.getElementById('editAnimalMeta').innerText = 'أسد أفريقي • القططية • ذكر';
        document.getElementById('editAvatar').innerText = name ? '🦁' : '🐾';
        openModal('editModal');
    }

    // ── Age toggle ──
    function setAge(prefix, method) {
        ['Birth','Approx'].forEach(m => {
            document.getElementById(prefix + 'Btn' + m).classList.remove('active');
            const block = document.getElementById(prefix + 'Block' + m);
            block.classList.remove('visible');
            block.style.display = 'none';
        });
        document.getElementById(prefix + 'Btn' + (method === 'birth' ? 'Birth' : 'Approx')).classList.add('active');
        const active = document.getElementById(prefix + 'Block' + (method === 'birth' ? 'Birth' : 'Approx'));
        active.classList.add('visible');
        active.style.display = 'flex';
    }

    // ── File name ──
    function showFileName(input, targetId) {
        const t = document.getElementById(targetId);
        if (input.files && input.files[0]) t.innerText = '📎 ' + input.files[0].name;
    }

    // ── Dialog ──
    function openDialog(id) { document.getElementById(id).classList.add('open'); }
    function closeDialog(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.dialog-backdrop').forEach(el => {
        el.addEventListener('click', function(e) { if (e.target === this) closeDialog(this.id); });
    });

    // ── Submit actions ──
    function submitAdd() {
        openDialog('successAddDialog');
    }

    function submitEdit() {
        closeModal('editModal');
        showToast('✅ تم تعديل بيانات الحيوان بنجاح.');
    }

    // ── Toast ──
    function showToast(msg) {
        const t = document.getElementById('toastMsg');
        document.getElementById('toastText').innerText = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }
</script>
@endsection
