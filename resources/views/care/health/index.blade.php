@extends($__layout ?? 'care.layout')
@section('title', 'الحالات الصحية | الرعاية والتغذية')
@section('page_title', 'الحالات الصحية')

@section('styles')
<style>
    /* ── Top Card (Header + Filters) ── */
    .top-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.4rem 1.8rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }
    .page-header { display: flex; justify-content: space-between; align-items: center; }
    .page-header-info h2 { font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0; }
    .page-header-info p { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin: 4px 0 0; }

    .filter-bar {
        display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;
        padding-top: 1.2rem; border-top: 1px solid #F1F5F9;
    }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input {
        width: 100%; padding: 10px 40px 10px 14px;
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600;
        outline: none; transition: all 0.2s;
    }
    .search-box input:focus { border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    .search-box svg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .filter-select {
        padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600;
        color: #334155; outline: none; cursor: pointer; transition: all 0.2s;
    }
    .filter-select:focus { border-color: #2E7D32; }

    /* ── Table ── */
    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-header { padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #FAFBFC; }
    .table-card-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.8rem; font-weight: 800; letter-spacing: 0.5px; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.92rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .custom-table tbody tr:last-child td { border-bottom: none; }

    /* ═══ BADGES ═══ */
    .badge { padding: 6px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge-need-referral { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
    .badge-need-referral .dot { background: #ef4444; }
    .badge-no-referral { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .badge-no-referral .dot { background: #94a3b8; }
    .badge-status-new { background: #eff6ff; color: #2563eb; }
    .badge-status-new .dot { background: #3b82f6; }
    .badge-status-reviewed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-status-reviewed .dot { background: #22c55e; }
    .badge-status-referred { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-status-referred .dot { background: #d97706; }

    /* Action Buttons */
    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; padding: 0; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .case-id, .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    /* ═══ MODAL — VET HOSPITAL STYLE ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 680px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    /* Modal Header (white bg with tabs) */
    .modal-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1.2rem 1.5rem 0; display: flex; justify-content: space-between; align-items: flex-end; }
    .modal-title-wrap { padding-bottom: 0.8rem; }
    .modal-title-wrap h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .modal-title-wrap span { font-size: 0.8rem; color: #64748b; font-weight: 600; }
    .modal-tabs-wrap { display: flex; align-items: center; gap: 20px; }
    .modal-tabs { display: flex; gap: 0; }
    .modal-tab { padding: 10px 24px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800; cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent; transition: all 0.2s; }
    .modal-tab.active { color: #1a4a2e; border-bottom-color: #1a4a2e; }
    .modal-close { width: 32px; height: 32px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.1rem; font-weight: 700; transition: all 0.2s; margin-bottom: 10px; }
    .modal-close:hover { background: #f8fafc; color: #0f172a; }

    /* Modal Body */
    .modal-body { padding: 1.5rem; overflow-y: auto; max-height: 65vh; }

    /* Info Grid - vet hospital style */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; }
    .info-cell { background: #fff; padding: 12px 16px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
    .info-cell-value { font-size: 0.9rem; color: #0f172a; font-weight: 800; }

    /* Content box with border */
    .content-box { background: #fff; border-right: 4px solid #3b82f6; padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; color: #1e293b; font-weight: 700; line-height: 1.6; border: 1px solid #e2e8f0; border-right-width: 4px; }
    .content-box.warning { border-right-color: #f59e0b; }
    .section-label { font-size: 0.8rem; color: #64748b; font-weight: 800; margin-bottom: 8px; }

    /* Modal Footer */
    .modal-footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 1.2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; }
    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(45,122,71,0.3); }
    .btn-submit:hover { transform: translateY(-1px); }
    .btn-submit-red { padding: 10px 24px; background: linear-gradient(135deg, #991b1b, #dc2626); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(220,38,38,0.25); }
    .btn-submit-red:hover { transform: translateY(-1px); }
    .btn-cancel { padding: 10px 20px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #f8fafc; }

/* ── Sub-dialog modals ── */
.dialog-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(3px); z-index: 1100; align-items: center; justify-content: center; }
.dialog-backdrop.open { display: flex; }
.dialog-box { background: #fff; border-radius: 18px; width: 100%; max-width: 460px; box-shadow: 0 30px 80px rgba(0,0,0,0.2); animation: modalIn 0.25s cubic-bezier(0.34,1.56,0.64,1); overflow: hidden; }
.dialog-icon-wrap { width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.8rem; }
.dialog-body { padding: 2rem 2rem 1.5rem; text-align: center; }
.dialog-body h4 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
.dialog-body p { font-size: 0.85rem; color: #64748b; font-weight: 600; line-height: 1.6; margin-bottom: 0; }
.dialog-footer { padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: center; }
/* Toast */
.toast { position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(20px); background: #0f172a; color: #fff; padding: 14px 24px; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); z-index: 2000; opacity: 0; transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1); pointer-events: none; }
.toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
.toast.green { background: linear-gradient(135deg, #1a4a2e, #2d7a47); }
.toast.red { background: linear-gradient(135deg, #991b1b, #dc2626); }
</style>
@endsection

@section('content')

{{-- ═══════ HEADER & FILTERS ═══════ --}}
<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>🩺 الحالات الصحية</h2>
            <p>مراجعة الحالات الصحية المسجلة من مشرفي المجموعات واتخاذ الإجراء المناسب عند الحاجة.</p>
        </div>
    </div>
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، نوع الحيوان، اسم المشرف...">
        </div>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option>السباع والضواري</option>
            <option>الرئيسيات</option>
            <option>العواشب</option>
            <option>الطيور</option>
        </select>
        <select class="filter-select">
            <option value="">كل أنواع المتابعة</option>
            <option>تحتاج إحالة</option>
            <option>لا تحتاج إحالة</option>
        </select>
        <select class="filter-select">
            <option value="">كل الحالات</option>
            <option>جديدة</option>
            <option>تمت المراجعة</option>
            <option>محالة للعلاج</option>
        </select>
        <input type="date" class="filter-select">
    </div>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
            قائمة الحالات
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الحالة</th>
                    <th>الرقم الرسمي للحيوان</th>
                    <th>نوع الحيوان</th>
                    <th>المجموعة</th>
                    <th>تاريخ الحالة</th>
                    <th>المشرف</th>
                    <th>نوع المتابعة</th>
                    <th>حالة الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="case-id">HC-2026-001</span></td>
                    <td><span class="animal-id">#ANL-0041-2026</span></td>
                    <td style="font-weight:700;">أسد إفريقي</td>
                    <td>السباع</td>
                    <td>2026-06-07</td>
                    <td>خالد منصور</td>
                    <td><span class="badge badge-need-referral"><span class="dot"></span>تحتاج إحالة</span></td>
                    <td><span class="badge badge-status-new"><span class="dot"></span>جديدة</span></td>
                    <td>
                        <button onclick="openModal('new_urgent','أسد إفريقي','السباع','خالد منصور','2026-06-07','تحتاج إحالة','جديدة','الحيوان يرفض تناول الطعام، جرح عميق بالقدم الأمامية.','تنظيف الجرح مبدئياً — يتطلب تدخلاً جراحياً.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td><span class="case-id">HC-2026-002</span></td>
                    <td><span class="animal-id">#ANL-0182-2025</span></td>
                    <td style="font-weight:700;">قرد المكاك</td>
                    <td>الرئيسيات</td>
                    <td>2026-06-06</td>
                    <td>ياسر الغيثي</td>
                    <td><span class="badge badge-no-referral"><span class="dot"></span>لا تحتاج إحالة</span></td>
                    <td><span class="badge badge-status-new"><span class="dot"></span>جديدة</span></td>
                    <td>
                        <button onclick="openModal('new_normal','قرد المكاك','الرئيسيات','ياسر الغيثي','2026-06-06','لا تحتاج إحالة','جديدة','كدمة بسيطة على الرسغ من احتكاك السياج.','المنطقة المصابة نظيفة ولا توجد عدوى.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td><span class="case-id">HC-2026-003</span></td>
                    <td><span class="animal-id">#ANL-0091-2024</span></td>
                    <td style="font-weight:700;">نعامة إفريقية</td>
                    <td>الطيور</td>
                    <td>2026-06-05</td>
                    <td>سالم عبدالله</td>
                    <td><span class="badge badge-no-referral"><span class="dot"></span>لا تحتاج إحالة</span></td>
                    <td><span class="badge badge-status-reviewed"><span class="dot"></span>تمت المراجعة</span></td>
                    <td>
                        <button onclick="openModal('reviewed','نعامة إفريقية','الطيور','سالم عبدالله','2026-06-05','لا تحتاج إحالة','تمت المراجعة','انخفاض طفيف في كمية الغذاء المتناول.','تمت المراجعة — لا إجراء مطلوب.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td><span class="case-id">HC-2026-004</span></td>
                    <td><span class="animal-id">#ANL-0120-2026</span></td>
                    <td style="font-weight:700;">غزال الريم</td>
                    <td>العواشب</td>
                    <td>2026-06-04</td>
                    <td>أحمد الكواري</td>
                    <td><span class="badge badge-need-referral"><span class="dot"></span>تحتاج إحالة</span></td>
                    <td><span class="badge badge-status-referred"><span class="dot"></span>محالة للعلاج</span></td>
                    <td>
                        <button onclick="openModal('referred','غزال الريم','العواشب','أحمد الكواري','2026-06-04','تحتاج إحالة','محالة للعلاج','كسر مشتبه في الساق الأمامية اليمنى.','تم توثيق الإحالة وإرسالها للمستشفى.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ MODAL ═══ --}}
<div class="modal-backdrop" id="caseModal">
    <div class="modal-box">

        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-title-wrap">
                <h3>تفاصيل الحالة الصحية</h3>
                <span id="mSubtitle">—</span>
            </div>
            <div class="modal-tabs-wrap">
                <div class="modal-tabs">
                    <button class="modal-tab active" id="htab-btn-1" onclick="switchHTab(1)">بيانات الحالة</button>
                    <button class="modal-tab" id="htab-btn-2" onclick="switchHTab(2)">الملاحظات والمرفقات</button>
                </div>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
        </div>

        {{-- Body --}}
        <div class="modal-body">

            {{-- Tab 1: بيانات الحالة --}}
            <div id="htab-1">
                {{-- Case ID row --}}
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem;">
                    <span style="font-family:'Courier New',monospace; font-size:0.85rem; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:4px 12px; border-radius:6px; font-weight:700;" id="mCaseId">HC-2026-001</span>
                    <span style="font-size:0.8rem; color:#64748b; font-weight:700;">رقم الحالة</span>
                    <span id="mFollowUpBadge" style="margin-right:auto;"></span>
                    <span id="mStatusBadge"></span>
                </div>

                {{-- Info Grid --}}
                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-cell-label">نوع الحيوان</div>
                        <div class="info-cell-value" id="mAnimalType">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المجموعة</div>
                        <div class="info-cell-value" id="mGroup">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">الجنس</div>
                        <div class="info-cell-value">ذكر</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">العمر</div>
                        <div class="info-cell-value">6 سنوات</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ الحالة</div>
                        <div class="info-cell-value" id="mDate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المشرف</div>
                        <div class="info-cell-value" id="mSupervisor">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">نوع المتابعة</div>
                        <div class="info-cell-value" id="mFollowUpVal">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">حالة الحالة</div>
                        <div class="info-cell-value" id="mStatusVal">—</div>
                    </div>
                </div>

                <div style="margin-bottom:1.2rem;">
                    <div class="section-label">وصف الحالة الصحية</div>
                    <div class="content-box warning" id="mDesc">—</div>
                </div>
            </div>

            {{-- Tab 2: الملاحظات والمرفقات --}}
            <div id="htab-2" style="display:none;">
                <div style="margin-bottom:1.2rem;">
                    <div class="section-label">الملاحظات المسجلة عن الحيوان</div>
                    <div class="content-box" id="mNotes">—</div>
                </div>
                <div style="margin-top:1.5rem;">
                    <div class="section-label">المرفقات</div>
                    <div style="display:flex; gap:10px; margin-top:8px;">
                        <div style="width:80px; height:80px; background:#e2e8f0; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
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

{{-- ═══ CONFIRM REVIEW DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmReviewDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#f0fdf4;">✅</div>
            <h4>تأكيد مراجعة الحالة</h4>
            <p>هل أنت متأكد من مراجعة الحالة وإنهاء الإجراء دون إحالة للمستشفى؟<br>ستصبح الحالة <strong>"تمت المراجعة"</strong> ولن يُنشأ أي طلب علاج.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('confirmReviewDialog')">إلغاء</button>
            <button class="btn-submit" onclick="confirmReview()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                نعم، تحديد كمراجعة
            </button>
        </div>
    </div>
</div>

{{-- ═══ CONFIRM REFER DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmReferDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#fff1f2;">🏥</div>
            <h4>تأكيد إحالة الحالة للعلاج</h4>
            <p>هل أنت متأكد من إحالة هذه الحالة الصحية للمستشفى البيطري للعلاج؟<br>سيتم إنشاء طلب إحالة وإرساله فوراً.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('confirmReferDialog')">إلغاء</button>
            <button class="btn-submit-red" onclick="confirmRefer()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13"/><path d="M22 2L15 22L11 13L2 9L22 2Z"/></svg>
                نعم، إحالة للعلاج
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
    let currentHTab = 1;

    function switchHTab(n) {
        document.getElementById('htab-1').style.display = n === 1 ? 'block' : 'none';
        document.getElementById('htab-2').style.display = n === 2 ? 'block' : 'none';
        document.getElementById('htab-btn-1').className = 'modal-tab' + (n === 1 ? ' active' : '');
        document.getElementById('htab-btn-2').className = 'modal-tab' + (n === 2 ? ' active' : '');
        currentHTab = n;
    }

    function openModal(state, animalType, group, supervisor, date, followUp, status, desc, notes) {
        // Reset tabs
        switchHTab(1);

        // Fill data
        document.getElementById('mSubtitle').innerText = animalType + ' — ' + group;
        document.getElementById('mAnimalType').innerText = animalType;
        document.getElementById('mGroup').innerText = group;
        document.getElementById('mSupervisor').innerText = supervisor;
        document.getElementById('mDate').innerText = date;
        document.getElementById('mFollowUpVal').innerText = followUp;
        document.getElementById('mFollowUpVal').style.color = followUp === 'تحتاج إحالة' ? '#e11d48' : '#475569';
        document.getElementById('mStatusVal').innerText = status;
        document.getElementById('mDesc').innerText = desc;
        document.getElementById('mNotes').innerText = notes;

        // Badges
        const fuColor = followUp === 'تحتاج إحالة'
            ? 'background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;'
            : 'background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;';
        document.getElementById('mFollowUpBadge').innerHTML = `<span style="padding:4px 10px;border-radius:999px;font-size:0.75rem;font-weight:800;${fuColor}">${followUp}</span>`;

        const stColors = {
            'جديدة':         'background:#eff6ff;color:#2563eb;',
            'تمت المراجعة':  'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;',
            'محالة للعلاج':  'background:#fffbeb;color:#d97706;border:1px solid #fde68a;'
        };
        document.getElementById('mStatusBadge').innerHTML = `<span style="padding:4px 10px;border-radius:999px;font-size:0.75rem;font-weight:800;${stColors[status]||''}">${status}</span>`;

        // Footer buttons
        const footer = document.getElementById('mFooter');
        footer.innerHTML = '';

        const closeBtn = `<button class="btn-cancel" onclick="closeModal()">إغلاق</button>`;

        if (state === 'new_urgent' || state === 'new_normal') {
            footer.innerHTML = closeBtn +
                `<button class="btn-submit" onclick="markReviewed()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:6px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    تحديد كمراجعة
                </button>
                <button class="btn-submit-red" onclick="referTreatment()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:6px;"><path d="M22 2L11 13"/><path d="M22 2L15 22L11 13L2 9L22 2Z"/></svg>
                    إحالة للعلاج
                </button>`;
        } else if (state === 'referred') {
            footer.innerHTML = closeBtn +
                `<a href="/care/referrals/treatment" class="btn-submit" style="text-decoration:none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:6px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    عرض إحالة العلاج
                </a>`;
        } else {
            footer.innerHTML = closeBtn;
        }

        document.getElementById('caseModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('caseModal').classList.remove('open');
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

    function markReviewed() {
        openDialog('confirmReviewDialog');
    }
    function confirmReview() {
        closeDialog('confirmReviewDialog');
        closeModal();
        showToast("✅ تم تحديث حالة الحالة إلى 'تمت المراجعة'.", 'green');
    }

    function referTreatment() {
        openDialog('confirmReferDialog');
    }
    function confirmRefer() {
        closeDialog('confirmReferDialog');
        closeModal();
        showToast('🏥 تم إنشاء إحالة علاج وإرسالها للمستشفى البيطري.', 'red');
    }

    window.onclick = function(e) {
        if (e.target === document.getElementById('caseModal')) closeModal();
    };
</script>
@endsection
