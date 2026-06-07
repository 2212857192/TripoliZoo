@extends('care.layout')
@section('title', 'حالات النفوق | الرعاية والتغذية')
@section('page_title', 'حالات النفوق')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.2rem; }
    .page-header { display: flex; justify-content: space-between; align-items: center; }
    .page-header-info h2 { font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0; }
    .page-header-info p { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin: 4px 0 0; }

    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; padding-top: 1.2rem; border-top: 1px solid #F1F5F9; }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input { width: 100%; padding: 10px 40px 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; transition: all 0.2s; }
    .search-box input:focus { border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    .search-box svg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .filter-select { padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #334155; outline: none; cursor: pointer; }
    .filter-select:focus { border-color: #2E7D32; }

    /* ── Table ── */
    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-header { padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #FAFBFC; }
    .table-card-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.8rem; font-weight: 800; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.92rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .custom-table tbody tr:last-child td { border-bottom: none; }

    /* ═══ BADGES ═══ */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-new      { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-new .dot { background: #ef4444; }
    .badge-approved { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-approved .dot { background: #22c55e; }
    .badge-autopsy  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-autopsy .dot { background: #f59e0b; }
    .badge-done     { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-done .dot { background: #3b82f6; }
    .badge-unknown  { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
    .badge-unknown .dot { background: #94a3b8; }

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .case-id, .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    /* ═══ MODAL — VET HOSPITAL STYLE ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 720px; max-height: 92vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    .modal-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1.2rem 1.5rem 0; display: flex; justify-content: space-between; align-items: flex-end; }
    .modal-title-wrap { padding-bottom: 0.8rem; }
    .modal-title-wrap h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .modal-title-wrap span { font-size: 0.8rem; color: #64748b; font-weight: 600; }
    .modal-tabs-wrap { display: flex; align-items: center; gap: 20px; }
    .modal-tabs { display: flex; }
    .modal-tab { padding: 10px 22px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent; transition: all 0.2s; }
    .modal-tab.active { color: #1a4a2e; border-bottom-color: #1a4a2e; }
    .modal-close { width: 32px; height: 32px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.1rem; font-weight: 700; transition: all 0.2s; margin-bottom: 10px; }
    .modal-close:hover { background: #f8fafc; color: #0f172a; }

    .modal-body { padding: 1.5rem; overflow-y: auto; max-height: 68vh; }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; }
    .info-cell { background: #fff; padding: 12px 16px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
    .info-cell-value { font-size: 0.9rem; color: #0f172a; font-weight: 800; }

    .content-box { background: #fff; padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; color: #1e293b; font-weight: 700; line-height: 1.6; border: 1px solid #e2e8f0; border-right: 4px solid #3b82f6; margin-bottom: 1rem; }
    .content-box.orange { border-right-color: #f59e0b; }
    .content-box.red    { border-right-color: #ef4444; }
    .content-box.green  { border-right-color: #22c55e; }
    .section-label { font-size: 0.8rem; color: #64748b; font-weight: 800; margin-bottom: 8px; }

    /* Autopsy section */
    .autopsy-section { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.2rem; margin-top: 1.5rem; }
    .autopsy-header { display: flex; align-items: center; gap: 8px; margin-bottom: 1rem; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; font-size: 0.95rem; font-weight: 800; color: #0f172a; }

    /* Result section */
    .result-section { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1.2rem; margin-top: 1.5rem; }
    .result-header { display: flex; align-items: center; gap: 8px; margin-bottom: 1rem; padding-bottom: 10px; border-bottom: 2px solid #dcfce7; font-size: 0.95rem; font-weight: 800; color: #15803d; }

    /* Modal Footer */
    .modal-footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 1.2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap; }
    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(45,122,71,0.3); display: inline-flex; align-items: center; gap: 6px; }
    .btn-submit:hover { transform: translateY(-1px); }
    .btn-submit-orange { padding: 10px 24px; background: linear-gradient(135deg, #92400e, #d97706); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(217,119,6,0.25); display: inline-flex; align-items: center; gap: 6px; }
    .btn-submit-orange:hover { transform: translateY(-1px); }
    .btn-cancel { padding: 10px 20px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #f8fafc; }

    /* ── Sub-dialog modals (above main modal z=1000) ── */
    .dialog-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(3px); z-index: 1100; align-items: center; justify-content: center; }
    .dialog-backdrop.open { display: flex; }
    .dialog-box { background: #fff; border-radius: 18px; width: 100%; max-width: 480px; box-shadow: 0 30px 80px rgba(0,0,0,0.2); animation: modalIn 0.25s cubic-bezier(0.34,1.56,0.64,1); overflow: hidden; }
    .dialog-icon-wrap { width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.8rem; }
    .dialog-body { padding: 2rem 2rem 1.5rem; text-align: center; }
    .dialog-body h4 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
    .dialog-body p { font-size: 0.85rem; color: #64748b; font-weight: 600; line-height: 1.6; margin-bottom: 0; }
    .dialog-footer { padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: center; }

    /* Reason option cards */
    .reason-options { display: flex; flex-direction: column; gap: 8px; margin-top: 1.2rem; text-align: right; }
    .reason-option { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.2s; }
    .reason-option:hover { border-color: #d97706; background: #fffbeb; }
    .reason-option input[type=radio] { accent-color: #d97706; width: 16px; height: 16px; flex-shrink: 0; }
    .reason-option label { font-size: 0.88rem; font-weight: 700; color: #334155; cursor: pointer; }
    .reason-option.checked { border-color: #d97706; background: #fffbeb; }
    .reason-extra { margin-top: 10px; width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; }
    .reason-extra:focus { border-color: #d97706; }

    /* Toast notification */
    .toast { position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(20px); background: #0f172a; color: #fff; padding: 14px 24px; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); z-index: 2000; opacity: 0; transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1); pointer-events: none; }
    .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    .toast.green { background: linear-gradient(135deg, #1a4a2e, #2d7a47); }
    .toast.orange { background: linear-gradient(135deg, #92400e, #c2710c); }
</style>
@endsection

@section('content')

{{-- ═══════ HEADER & FILTERS ═══════ --}}
<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>💀 حالات النفوق</h2>
            <p>مراجعة حالات النفوق المسجلة من مشرفي المجموعات واتخاذ الإجراء المناسب.</p>
        </div>
    </div>
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، نوع الحيوان، رقم الحالة...">
        </div>
        <select class="filter-select">
            <option value="">كل الحالات</option>
            <option>جديدة</option>
            <option>معتمدة</option>
            <option>محالة للتشريح</option>
            <option>اكتملت النتيجة</option>
        </select>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option>السباع والضواري</option>
            <option>الرئيسيات</option>
            <option>العواشب</option>
            <option>الطيور</option>
        </select>
        <select class="filter-select">
            <option value="">سبب النفوق</option>
            <option>ظاهر</option>
            <option>غير ظاهر</option>
        </select>
        <input type="date" class="filter-select">
    </div>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
            قائمة حالات النفوق
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الحالة</th>
                    <th>رقم الحيوان</th>
                    <th>نوع الحيوان</th>
                    <th>المجموعة</th>
                    <th>تاريخ النفوق</th>
                    <th>سبب النفوق</th>
                    <th>حالة النفوق</th>
                    <th>المشرف</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>

                {{-- Row 1: New + cause visible --}}
                <tr>
                    <td><span class="case-id">MC-2026-001</span></td>
                    <td><span class="animal-id">#ANL-0041-2026</span></td>
                    <td style="font-weight:700;">أسد إفريقي</td>
                    <td>السباع</td>
                    <td>2026-06-07</td>
                    <td style="max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">عضة واضحة من حيوان آخر</td>
                    <td><span class="badge badge-new"><span class="dot"></span>جديدة</span></td>
                    <td>خالد منصور</td>
                    <td>
                        <button onclick="openModal('new_visible')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>

                {{-- Row 2: New + cause unknown --}}
                <tr>
                    <td><span class="case-id">MC-2026-002</span></td>
                    <td><span class="animal-id">#ANL-0182-2025</span></td>
                    <td style="font-weight:700;">قرد المكاك</td>
                    <td>الرئيسيات</td>
                    <td>2026-06-06</td>
                    <td><span style="color:#94a3b8; font-style:italic;">غير ظاهر</span></td>
                    <td><span class="badge badge-new"><span class="dot"></span>جديدة</span></td>
                    <td>ياسر الغيثي</td>
                    <td>
                        <button onclick="openModal('new_unknown')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>

                {{-- Row 3: Approved --}}
                <tr>
                    <td><span class="case-id">MC-2026-003</span></td>
                    <td><span class="animal-id">#ANL-0091-2024</span></td>
                    <td style="font-weight:700;">نعامة إفريقية</td>
                    <td>الطيور</td>
                    <td>2026-06-04</td>
                    <td style="max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">تعرض لضربة شديدة</td>
                    <td><span class="badge badge-approved"><span class="dot"></span>معتمدة</span></td>
                    <td>سالم عبدالله</td>
                    <td>
                        <button onclick="openModal('approved')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>

                {{-- Row 4: Autopsy --}}
                <tr>
                    <td><span class="case-id">MC-2026-004</span></td>
                    <td><span class="animal-id">#ANL-0120-2026</span></td>
                    <td style="font-weight:700;">غزال الريم</td>
                    <td>العواشب</td>
                    <td>2026-06-03</td>
                    <td><span style="color:#94a3b8; font-style:italic;">غير ظاهر</span></td>
                    <td><span class="badge badge-autopsy"><span class="dot"></span>محالة للتشريح</span></td>
                    <td>أحمد الكواري</td>
                    <td>
                        <button onclick="openModal('autopsy')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>

                {{-- Row 5: Completed --}}
                <tr>
                    <td><span class="case-id">MC-2026-005</span></td>
                    <td><span class="animal-id">#ANL-0305-2024</span></td>
                    <td style="font-weight:700;">فهد أفريقي</td>
                    <td>السباع</td>
                    <td>2026-05-28</td>
                    <td><span style="color:#94a3b8; font-style:italic;">غير ظاهر</span></td>
                    <td><span class="badge badge-done"><span class="dot"></span>اكتملت النتيجة</span></td>
                    <td>عمر الفاسي</td>
                    <td>
                        <button onclick="openModal('done')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>

{{-- ═══ MODAL ═══ --}}
<div class="modal-backdrop" id="mortalityModal">
    <div class="modal-box">

        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-title-wrap">
                <h3>تفاصيل حالة نفوق</h3>
                <span id="mSubtitle">—</span>
            </div>
            <div class="modal-tabs-wrap">
                <div class="modal-tabs">
                    <button class="modal-tab active" id="mtab-btn-1" onclick="switchMTab(1)">بيانات الحالة</button>
                    <button class="modal-tab" id="mtab-btn-2" onclick="switchMTab(2)">الملاحظات والمرفقات</button>
                </div>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
        </div>

        {{-- Body --}}
        <div class="modal-body">

            {{-- Tab 1: بيانات الحالة --}}
            <div id="mtab-1">

                {{-- Case ID + status badge --}}
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem; flex-wrap:wrap;">
                    <span style="font-family:'Courier New',monospace; font-size:0.85rem; background:#fef2f2; color:#dc2626; border:1px solid #fecaca; padding:4px 12px; border-radius:6px; font-weight:700;" id="mCaseId">MC-2026-001</span>
                    <span style="font-size:0.8rem; color:#64748b; font-weight:700;">رقم الحالة</span>
                    <span id="mStatusBadge" style="margin-right:auto;"></span>
                </div>

                {{-- Info Grid --}}
                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-cell-label">رقم الحيوان الرسمي</div>
                        <div class="info-cell-value" style="font-family:'Courier New',monospace; color:#64748b;" id="mAnimalId">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">نوع الحيوان</div>
                        <div class="info-cell-value" id="mAnimalType">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">الجنس</div>
                        <div class="info-cell-value" id="mGender">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">العمر</div>
                        <div class="info-cell-value" id="mAge">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المجموعة</div>
                        <div class="info-cell-value" id="mGroup">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ النفوق</div>
                        <div class="info-cell-value" id="mDate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المشرف المسجل</div>
                        <div class="info-cell-value" id="mSupervisor">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">سبب النفوق</div>
                        <div class="info-cell-value" id="mCause">—</div>
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <div class="section-label">الملاحظات المسجلة عن الحيوان</div>
                    <div class="content-box" id="mNotes">—</div>
                </div>

                {{-- Autopsy Section (shown when state = autopsy) --}}
                <div id="autopsySection" style="display:none;">
                    <div class="autopsy-section">
                        <div class="autopsy-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            حالة التشريح
                        </div>
                        <div class="info-grid" style="margin-bottom:0;">
                            <div class="info-cell">
                                <div class="info-cell-label">حالة التشريح</div>
                                <div class="info-cell-value" style="color:#d97706;">بانتظار التوثيق</div>
                            </div>
                            <div class="info-cell">
                                <div class="info-cell-label">تاريخ الإحالة</div>
                                <div class="info-cell-value">2026-06-03</div>
                            </div>
                            <div class="info-cell span-2">
                                <div class="info-cell-label">سبب الإحالة للتشريح</div>
                                <div class="info-cell-value" id="autopsyReason">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Result Section (shown when state = done) --}}
                <div id="resultSection" style="display:none;">
                    <div class="result-section">
                        <div class="result-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            نتيجة التشريح — اكتملت
                        </div>
                        <div class="info-grid" style="margin-bottom:1rem;">
                            <div class="info-cell span-2">
                                <div class="info-cell-label">سبب النفوق النهائي (المُوثَّق)</div>
                                <div class="info-cell-value" style="color:#15803d;">فشل كلوي حاد مصحوب بعدوى بكتيرية</div>
                            </div>
                            <div class="info-cell">
                                <div class="info-cell-label">تاريخ التوثيق</div>
                                <div class="info-cell-value">2026-06-02</div>
                            </div>
                            <div class="info-cell">
                                <div class="info-cell-label">تقرير الصفة التشريحية</div>
                                <div class="info-cell-value">
                                    <a href="#" style="color:#2563eb; font-size:0.85rem; display:flex; align-items:center; gap:4px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        تحميل التقرير
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="section-label">ملاحظات التشريح</div>
                            <div class="content-box green" style="margin-bottom:0;">تم الكشف عن التهاب حاد في الكليتين مع تضخم في الطحال. النتائج تؤكد وفاة طبيعية لا علاقة لها بأي مسبب خارجي.</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Tab 2: الملاحظات والمرفقات --}}
            <div id="mtab-2" style="display:none;">
                <div style="margin-bottom:1.2rem;">
                    <div class="section-label">ملاحظات المشرف الإضافية</div>
                    <div class="content-box">لم يُلاحَظ على الحيوان أي سلوك غير طبيعي قبل 24 ساعة من وفاته. وجد ميتاً في الصباح عند الجولة الأولى.</div>
                </div>
                <div>
                    <div class="section-label">المرفقات</div>
                    <div style="display:flex; gap:10px; margin-top:8px;">
                        <div style="width:80px; height:80px; background:#fee2e2; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#ef4444; border:1px solid #fecaca;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="modal-footer" id="mFooter"></div>
    </div>
</div>

{{-- ═══ CONFIRM APPROVE DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmApproveDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#f0fdf4;">✅</div>
            <h4>تأكيد اعتماد حالة النفوق</h4>
            <p>هل أنت متأكد من اعتماد حالة النفوق رسمياً دون إحالة للتشريح؟<br>سيتم تحديث سجل الحيوان إلى <strong>نافق</strong> وإغلاق الحالة.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('confirmApproveDialog')">إلغاء</button>
            <button class="btn-submit" onclick="confirmApprove()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                نعم، اعتماد
            </button>
        </div>
    </div>
</div>

{{-- ═══ AUTOPSY REASON DIALOG ═══ --}}
<div class="dialog-backdrop" id="autopsyReasonDialog">
    <div class="dialog-box">
        <div class="dialog-body" style="text-align:right;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:1rem; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                <div style="width:40px;height:40px;border-radius:10px;background:#fffbeb;display:flex;align-items:center;justify-content:center;">📋</div>
                <div>
                    <div style="font-size:1rem; font-weight:800; color:#0f172a;">إحالة للتشريح</div>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600;">حدد سبب الإحالة للتشريح رغم وجود سبب ظاهر</div>
                </div>
            </div>
            <div class="reason-options">
                <div class="reason-option" onclick="selectReason(this,'للتأكد من سبب النفوق')">
                    <input type="radio" name="autopsyReason" value="للتأكد من سبب النفوق" id="r1">
                    <label for="r1">للتأكد من سبب النفوق</label>
                </div>
                <div class="reason-option" onclick="selectReason(this,'الاشتباه في مرض معدي')">
                    <input type="radio" name="autopsyReason" value="الاشتباه في مرض معدي" id="r2">
                    <label for="r2">الاشتباه في مرض معدي</label>
                </div>
                <div class="reason-option" onclick="selectReason(this,'تكرار حالات نفوق مشابهة')">
                    <input type="radio" name="autopsyReason" value="تكرار حالات نفوق مشابهة" id="r3">
                    <label for="r3">تكرار حالات نفوق مشابهة</label>
                </div>
                <div class="reason-option" onclick="selectReason(this,'طلب توثيق طبي إضافي')">
                    <input type="radio" name="autopsyReason" value="طلب توثيق طبي إضافي" id="r4">
                    <label for="r4">طلب توثيق طبي إضافي</label>
                </div>
            </div>
            <input type="text" class="reason-extra" id="extraReasonInput" placeholder="أو اكتب سبباً آخر...">
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('autopsyReasonDialog')">إلغاء</button>
            <button class="btn-submit-orange" onclick="confirmAutopsyReason()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                تأكيد الإحالة للتشريح
            </button>
        </div>
    </div>
</div>

{{-- ═══ CONFIRM AUTOPSY (unknown cause) DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmAutopsyDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#fffbeb;">🔬</div>
            <h4>تأكيد الإحالة للتشريح</h4>
            <p>سبب النفوق <strong>غير ظاهر</strong>.<br>هل تؤكد إحالة هذه الحالة للمستشفى البيطري لإجراء التشريح وتوثيق سبب النفوق النهائي؟</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('confirmAutopsyDialog')">إلغاء</button>
            <button class="btn-submit-orange" onclick="confirmAutopsy()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                نعم، إحالة للتشريح
            </button>
        </div>
    </div>
</div>

{{-- ═══ TOAST ═══ --}}
<div class="toast" id="toastMsg">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastText">تمت العملية بنجاح</span>
</div>

@endsection

@section('scripts')
<script>
    function switchMTab(n) {
        document.getElementById('mtab-1').style.display = n === 1 ? 'block' : 'none';
        document.getElementById('mtab-2').style.display = n === 2 ? 'block' : 'none';
        document.getElementById('mtab-btn-1').className = 'modal-tab' + (n === 1 ? ' active' : '');
        document.getElementById('mtab-btn-2').className = 'modal-tab' + (n === 2 ? ' active' : '');
    }

    function openModal(state) {
        // Reset tabs
        switchMTab(1);

        // Hide conditional sections
        document.getElementById('autopsySection').style.display = 'none';
        document.getElementById('resultSection').style.display  = 'none';

        const footer = document.getElementById('mFooter');
        const closeBtn = `<button class="btn-cancel" onclick="closeModal()">إغلاق</button>`;

        // Populate common data
        const data = {
            'new_visible': {
                caseId: 'MC-2026-001', animalId: '#ANL-0041-2026', animalType: 'أسد إفريقي',
                gender: 'ذكر', age: '8 سنوات', group: 'السباع', date: '2026-06-07',
                supervisor: 'خالد منصور', cause: 'عضة واضحة من حيوان آخر',
                notes: 'وجد ميتاً قرب السياج الداخلي. يظهر جرح بالغ الأثر في الرقبة.',
                status: 'جديدة', statusClass: 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca;'
            },
            'new_unknown': {
                caseId: 'MC-2026-002', animalId: '#ANL-0182-2025', animalType: 'قرد المكاك',
                gender: 'أنثى', age: '3 سنوات', group: 'الرئيسيات', date: '2026-06-06',
                supervisor: 'ياسر الغيثي', cause: 'غير ظاهر',
                notes: 'وجد ميتاً في المنطقة المرتفعة. لا يوجد جرح ظاهر ولا علامات تدل على سبب واضح.',
                status: 'جديدة', statusClass: 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca;'
            },
            'approved': {
                caseId: 'MC-2026-003', animalId: '#ANL-0091-2024', animalType: 'نعامة إفريقية',
                gender: 'أنثى', age: '5 سنوات', group: 'الطيور', date: '2026-06-04',
                supervisor: 'سالم عبدالله', cause: 'تعرض لضربة شديدة',
                notes: 'تعرضت لشجار مع قطيعها. تم اعتماد سبب النفوق رسمياً وتحديث سجلها.',
                status: 'معتمدة', statusClass: 'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;'
            },
            'autopsy': {
                caseId: 'MC-2026-004', animalId: '#ANL-0120-2026', animalType: 'غزال الريم',
                gender: 'ذكر', age: '2 سنوات', group: 'العواشب', date: '2026-06-03',
                supervisor: 'أحمد الكواري', cause: 'غير ظاهر',
                notes: 'وجد ميتاً بدون سبب ظاهر. تم إحالته للتشريح للتحقق.',
                status: 'محالة للتشريح', statusClass: 'background:#fffbeb;color:#d97706;border:1px solid #fde68a;'
            },
            'done': {
                caseId: 'MC-2026-005', animalId: '#ANL-0305-2024', animalType: 'فهد أفريقي',
                gender: 'ذكر', age: '11 سنوات', group: 'السباع', date: '2026-05-28',
                supervisor: 'عمر الفاسي', cause: 'غير ظاهر',
                notes: 'وفاة مفاجئة بلا أعراض سابقة. أُحيل للتشريح ووُثِّقت النتيجة.',
                status: 'اكتملت النتيجة', statusClass: 'background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;'
            }
        };

        const d = data[state];

        document.getElementById('mSubtitle').innerText  = d.animalType + ' — ' + d.group;
        document.getElementById('mCaseId').innerText    = d.caseId;
        document.getElementById('mAnimalId').innerText  = d.animalId;
        document.getElementById('mAnimalType').innerText = d.animalType;
        document.getElementById('mGender').innerText    = d.gender;
        document.getElementById('mAge').innerText       = d.age;
        document.getElementById('mGroup').innerText     = d.group;
        document.getElementById('mDate').innerText      = d.date;
        document.getElementById('mSupervisor').innerText = d.supervisor;
        document.getElementById('mNotes').innerText     = d.notes;

        // Cause (styled if unknown)
        const causeEl = document.getElementById('mCause');
        if (d.cause === 'غير ظاهر') {
            causeEl.innerHTML = '<span style="color:#94a3b8; font-style:italic;">غير ظاهر</span>';
        } else {
            causeEl.innerText = d.cause;
        }

        // Status badge
        document.getElementById('mStatusBadge').innerHTML =
            `<span style="padding:5px 12px;border-radius:999px;font-size:0.75rem;font-weight:800;${d.statusClass}">${d.status}</span>`;

        // Buttons & sections per state
        if (state === 'new_visible') {
            footer.innerHTML = closeBtn +
                `<button class="btn-submit-orange" onclick="openAutopsyReasonDialog()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    إحالة للتشريح
                </button>
                <button class="btn-submit" onclick="approveCase()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    اعتماد حالة النفوق
                </button>`;
        }
        else if (state === 'new_unknown') {
            footer.innerHTML = closeBtn +
                `<button class="btn-submit-orange" onclick="referAutopsy()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    إحالة حالة نفوق للتشريح
                </button>`;
        }
        else if (state === 'autopsy') {
            document.getElementById('autopsySection').style.display = 'block';
            document.getElementById('autopsyReason').innerText = 'غير ظاهر — إحالة مباشرة';
            footer.innerHTML = closeBtn;
        }
        else if (state === 'done') {
            document.getElementById('resultSection').style.display = 'block';
            footer.innerHTML = closeBtn;
        }
        else { // approved
            footer.innerHTML = closeBtn;
        }

        document.getElementById('mortalityModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('mortalityModal').classList.remove('open');
    }

    // ── Custom dialogs ──
    function openDialog(id) {
        document.getElementById(id).classList.add('open');
    }
    function closeDialog(id) {
        document.getElementById(id).classList.remove('open');
    }

    function showToast(msg, type='green') {
        const t = document.getElementById('toastMsg');
        const tx = document.getElementById('toastText');
        t.className = 'toast ' + type;
        tx.innerText = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    // ── Approve case ──
    function approveCase() {
        openDialog('confirmApproveDialog');
    }
    function confirmApprove() {
        closeDialog('confirmApproveDialog');
        closeModal();
        showToast("✅ تم اعتماد حالة النفوق. سجل الحيوان حُدِّث إلى 'نافق'.", 'green');
    }

    // ── Autopsy unknown cause ──
    function referAutopsy() {
        openDialog('confirmAutopsyDialog');
    }
    function confirmAutopsy() {
        closeDialog('confirmAutopsyDialog');
        closeModal();
        showToast('🔬 تم إنشاء إحالة تشريح وإرسالها للمستشفى البيطري.', 'orange');
    }

    // ── Autopsy with visible cause (reason dialog) ──
    let selectedReason = '';
    function selectReason(el, val) {
        document.querySelectorAll('.reason-option').forEach(o => o.classList.remove('checked'));
        el.classList.add('checked');
        el.querySelector('input[type=radio]').checked = true;
        selectedReason = val;
        document.getElementById('extraReasonInput').value = '';
    }
    function openAutopsyReasonDialog() {
        selectedReason = '';
        document.querySelectorAll('.reason-option').forEach(o => o.classList.remove('checked'));
        document.querySelectorAll('input[name=autopsyReason]').forEach(r => r.checked = false);
        document.getElementById('extraReasonInput').value = '';
        openDialog('autopsyReasonDialog');
    }
    function confirmAutopsyReason() {
        const extra = document.getElementById('extraReasonInput').value.trim();
        const reason = extra || selectedReason;
        if (!reason) {
            document.getElementById('extraReasonInput').style.borderColor = '#ef4444';
            document.getElementById('extraReasonInput').placeholder = '⚠️ يرجى تحديد سبب الإحالة أولاً';
            return;
        }
        closeDialog('autopsyReasonDialog');
        closeModal();
        showToast('🔬 تم إحالة الحالة للتشريح. السبب: ' + reason, 'orange');
    }

    window.onclick = function(e) {
        const mainModal = document.getElementById('mortalityModal');
        if (e.target === mainModal) closeModal();
    };
</script>
@endsection
