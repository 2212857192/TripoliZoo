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
    .filter-bar {
        display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;
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

    .table-card-footer {
        padding: 1.1rem 1.75rem;
        display: flex; align-items: center; justify-content: flex-end;
        border-top: 1px solid #f1f5f9; background: #FAFBFC;
    }
    .table-pagination {
        display: flex; align-items: center; gap: 8px;
        font-size: 0.82rem; font-weight: 700; color: #64748b;
    }
    .table-pagination .page-info { margin-left: 4px; white-space: nowrap; }
    .page-btn {
        width: 34px; height: 34px; border-radius: 9px;
        border: 1px solid #e2e8f0; background: #fff; color: #475569;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; font-family: 'Cairo', sans-serif;
    }
    .page-btn:hover:not(:disabled) { border-color: #2E7D32; color: #2E7D32; background: #f0fdf4; }
    .page-btn:disabled { opacity: 0.45; cursor: not-allowed; }
    .page-btn.active {
        background: linear-gradient(135deg, #1a4a2e, #2d7a47);
        border-color: transparent; color: #fff;
    }

    /* ── Table ── */
    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
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

    /* ═══ MODAL — نفس نافذة تفاصيل الحجر الصحي ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    #caseModal .modal-box { background: #f8fafc; border-radius: 20px; width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    #caseModal .modal-header { background: transparent; border-bottom: none; display: flex; justify-content: center; position: relative; padding: 2rem 1.5rem 0; }
    #caseModal .modal-header h3 { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0; text-align: center; }
    #caseModal .modal-close {
        position: absolute; left: 1.5rem; top: 1.5rem;
        width: 32px; height: 32px; border-radius: 8px; background: #e2e8f0; border: none;
        color: #64748b; display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 1.2rem; font-weight: 700; line-height: 1;
    }
    #caseModal .modal-close:hover { background: #cbd5e1; color: #0f172a; }

    #caseModal .modal-tabs-bar { display: flex; justify-content: center; gap: 0; padding: 1rem 2rem 0; }
    #caseModal .modal-tab {
        padding: 8px 20px; border: none; background: transparent;
        font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800;
        cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent;
    }
    #caseModal .modal-tab.active { color: #16a34a; border-bottom-color: #16a34a; }

    #caseModal .modal-body { padding: 1.5rem 2rem; }
    #caseModal .q-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { #caseModal .q-grid { grid-template-columns: 1fr; } }

    #caseModal .q-card {
        background: #fff; border-radius: 12px; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    #caseModal .q-card-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center; }
    #caseModal .q-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;
    }
    #caseModal .q-row.sep { margin-bottom: 2rem; }
    #caseModal .q-label { color: #64748b; font-size: 0.9rem; font-weight: 700; }
    #caseModal .q-value { color: #0f172a; font-size: 0.95rem; font-weight: 800; }
    #caseModal .q-value.muted { color: #475569; font-weight: 700; font-style: italic; }
    #caseModal .q-note-label { color: #64748b; font-size: 0.85rem; font-weight: 700; display: block; text-align: center; margin-bottom: 0.5rem; }
    #caseModal .q-note-box {
        background: #f8fafc; padding: 10px; border-radius: 8px; text-align: center;
        font-size: 0.9rem; font-weight: 700; color: #334155;
    }
    #caseModal .q-col { display: flex; flex-direction: column; gap: 1.5rem; }
    #caseModal .q-notes-list { display: flex; flex-direction: column; gap: 0.8rem; }
    #caseModal .q-note-item {
        background: #f8fafc; padding: 12px 14px; border-radius: 8px;
        font-size: 0.85rem; color: #334155; font-weight: 700; text-align: center; border: 1px solid #f1f5f9;
    }
    #caseModal .q-attach-wrap { text-align: center; padding: 2rem 1rem; }
    #caseModal .q-attach-img {
        width: 180px; height: 180px; border-radius: 16px; margin: 0 auto;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9); border: 2px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center; font-size: 5rem;
    }

    #caseModal .modal-footer { background: transparent; border-top: none; padding: 0 2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; }
    #caseModal .btn-action-release { padding: 8px 16px; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #caseModal .btn-action-release:hover { background: #15803d; }
    #caseModal .btn-action-close { padding: 8px 16px; background: #e11d48; color: #fff; border: none; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #caseModal .btn-action-close:hover { background: #be123c; }
    #caseModal .btn-cancel { padding: 8px 16px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #caseModal .btn-cancel:hover { background: #e2e8f0; }

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

{{-- ═══════ FILTERS ═══════ --}}
<div class="top-card">
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان أو نوع الحيوان...">
        </div>
        <select class="filter-select">
                        @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات'])
        </select>
        <select class="filter-select">
            <option value="">كل أنواع المتابعة</option>
            <option>تحتاج إحالة</option>
            <option>لا تحتاج إحالة</option>
        </select>
        @include('partials.date-filter')
    </div>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="table-card">
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
                    <th>نوع الحيوان</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @include('partials.animal-table-cell', ['name' => 'سيمبا', 'emoji' => '🦁', 'animalId' => '#ANL-0041-2026'])
                    <td style="font-weight:700;">أسد إفريقي</td>
                    <td><span class="badge badge-status-new"><span class="dot"></span>جديدة</span></td>
                    <td>
                        <button onclick="openModal('new_urgent','HC-2026-001','سيمبا','#ANL-0041-2026','🦁','أسد إفريقي','القططية','جرح عميق بالقدم الأمامية اليمنى','خالد منصور','2026-06-07','تحتاج إحالة','جديدة','الحيوان يرفض تناول الطعام، جرح عميق بالقدم الأمامية.','تنظيف الجرح مبدئياً — يتطلب تدخلاً جراحياً.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    @include('partials.animal-table-cell', ['emoji' => '🐒', 'animalId' => '#ANL-0182-2025'])
                    <td style="font-weight:700;">قرد المكاك</td>
                    <td><span class="badge badge-status-new"><span class="dot"></span>جديدة</span></td>
                    <td>
                        <button onclick="openModal('new_normal','HC-2026-002',null,'#ANL-0182-2025','🐒','قرد المكاك','القرود','كدمة على الرسغ الأيسر','ياسر الغيثي','2026-06-06','لا تحتاج إحالة','جديدة','كدمة بسيطة على الرسغ من احتكاك السياج.','المنطقة المصابة نظيفة ولا توجد عدوى.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    @include('partials.animal-table-cell', ['name' => 'نعيمة', 'emoji' => '🦩', 'animalId' => '#ANL-0091-2024'])
                    <td style="font-weight:700;">نعامة إفريقية</td>
                    <td><span class="badge badge-status-reviewed"><span class="dot"></span>تمت المراجعة</span></td>
                    <td>
                        <button onclick="openModal('reviewed','HC-2026-003','نعيمة','#ANL-0091-2024','🦩','نعامة إفريقية','الطيور','ريش مفقود من الجناح الأيسر','سالم عبدالله','2026-06-05','لا تحتاج إحالة','تمت المراجعة','انخفاض طفيف في كمية الغذاء المتناول.','تمت المراجعة — لا إجراء مطلوب.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    @include('partials.animal-table-cell', ['name' => 'ريم', 'emoji' => '🦌', 'animalId' => '#ANL-0120-2026'])
                    <td style="font-weight:700;">غزال الريم</td>
                    <td><span class="badge badge-status-referred"><span class="dot"></span>محالة للعلاج</span></td>
                    <td>
                        <button onclick="openModal('referred','HC-2026-004','ريم','#ANL-0120-2026','🦌','غزال الريم','الغزلان','عرج واضح في الساق الأمامية اليمنى','أحمد الكواري','2026-06-04','تحتاج إحالة','محالة للعلاج','كسر مشتبه في الساق الأمامية اليمنى.','تم توثيق الإحالة وإرسالها للمستشفى.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="table-card-footer">
        <div class="table-pagination">
            <button class="page-btn" disabled title="السابق">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn" title="التالي">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <span class="page-info">صفحة 1 من 2 — 4 حالات</span>
        </div>
    </div>
</div>

{{-- ═══ MODAL ═══ --}}
<div class="modal-backdrop" id="caseModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>تفاصيل الحالة الصحية — <span id="modalCaseId">HC-2026-001</span></h3>
            <button type="button" class="modal-close" onclick="closeModal()">✕</button>
        </div>

        <div class="modal-tabs-bar">
            <button class="modal-tab active" id="htab-btn-1" onclick="switchHTab(1)">بيانات الحالة</button>
            <button class="modal-tab" id="htab-btn-2" onclick="switchHTab(2)">المرفقات</button>
        </div>

        <div class="modal-body">
            <div id="htab-1">
                <div class="q-grid">
                    <div class="q-card">
                        <h4 class="q-card-title">بيانات الحيوان</h4>

                        <div class="q-row">
                            <span class="q-label">رقم الحيوان</span>
                            <span class="q-value" id="mAnimalId">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">نوع الحيوان</span>
                            <span class="q-value" id="mAnimalType">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">اسم الحيوان</span>
                            <span class="q-value" id="mAnimalName">—</span>
                        </div>
                        <div class="q-row sep">
                            <span class="q-label">الجنس</span>
                            <span class="q-value" id="mGender">ذكر</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">العلامة المميزة</span>
                            <span class="q-value" id="mMark">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">المجموعة الحيوانية</span>
                            <span class="q-value" id="mGroup">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">المشرف</span>
                            <span class="q-value" id="mSupervisor">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">تاريخ التسجيل</span>
                            <span class="q-value" id="mDate">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">نوع المتابعة</span>
                            <span class="q-value" id="mFollowUpVal">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">الحالة</span>
                            <span class="q-value" id="mStatusVal">—</span>
                        </div>
                    </div>

                    <div class="q-col">
                        <div class="q-card">
                            <h4 class="q-card-title">وصف الحالة الصحية</h4>
                            <div class="q-note-box" id="mDesc">—</div>
                        </div>

                        <div class="q-card" style="flex-grow:1;">
                            <h4 class="q-card-title">الملاحظات المسجلة عن الحيوان</h4>
                            <div class="q-notes-list" id="mNotesList">
                                <div class="q-note-item" id="mNotes">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="htab-2" style="display:none;">
                <div class="q-card">
                    <h4 class="q-card-title">المرفقات</h4>
                    <div class="q-attach-wrap">
                        <div class="q-attach-img" id="mAttachmentImg">🦁</div>
                    </div>
                </div>
            </div>
        </div>

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

    function openModal(state, caseId, animalName, animalId, animalEmoji, animalType, group, mark, supervisor, date, followUp, status, desc, notes) {
        switchHTab(1);

        document.getElementById('modalCaseId').textContent = caseId;
        document.getElementById('mAnimalId').textContent = animalId;
        document.getElementById('mAnimalType').textContent = animalType;
        document.getElementById('mAnimalName').innerHTML = animalName
            ? animalName
            : '<span class="q-value muted">لم يُسمَّ بعد</span>';
        document.getElementById('mMark').textContent = mark;
        document.getElementById('mGroup').textContent = group;
        document.getElementById('mSupervisor').textContent = supervisor;
        document.getElementById('mDate').textContent = date;
        document.getElementById('mFollowUpVal').textContent = followUp;
        document.getElementById('mStatusVal').textContent = status;
        document.getElementById('mDesc').textContent = desc;
        document.getElementById('mNotes').textContent = date + ': ' + notes;
        document.getElementById('mAttachmentImg').textContent = animalEmoji;

        const footer = document.getElementById('mFooter');
        const closeBtn = `<button class="btn-cancel" onclick="closeModal()">إغلاق</button>`;

        if (state === 'new_urgent' || state === 'new_normal') {
            footer.innerHTML = closeBtn +
                `<button class="btn-action-close" onclick="referTreatment()">إحالة للعلاج</button>
                <button class="btn-action-release" onclick="markReviewed()">تحديد كمراجعة</button>`;
        } else if (state === 'referred') {
            footer.innerHTML = closeBtn +
                `<a href="/care/referrals/treatment" class="btn-action-release" style="text-decoration:none;display:inline-flex;align-items:center;">عرض إحالة العلاج</a>`;
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
