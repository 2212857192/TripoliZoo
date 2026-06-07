@extends('vet.layout')
@section('title', 'إحالات التشريح | المستشفى البيطري')
@section('page_title', 'إحالات التشريح')

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

    /* ── Page Header ── */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-header-info h2 {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .page-header-info p {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        margin: 4px 0 0;
    }

    .hero-stats { display: flex; gap: 0.8rem; align-items: center; }
    .hero-stat {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 8px 14px;
        text-align: center;
    }
    .hero-stat .num { font-size: 1.4rem; font-weight: 900; color: #1E293B; line-height: 1; }
    .hero-stat .lbl { font-size: 0.65rem; color: #64748B; font-weight: 700; }

    /* ═══ FILTERS BAR ═══ */
    .filter-bar {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        padding-top: 1.2rem;
        border-top: 1px solid #F1F5F9;
    }
    .search-box {
        flex: 1;
        min-width: 250px;
        position: relative;
    }
    .search-box input {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        outline: none;
        transition: all 0.2s;
    }
    .search-box input:focus {
        border-color: #2E7D32;
        box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
    }
    .search-box svg {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .filter-select {
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-select:focus {
        border-color: #2E7D32;
    }

    /* ── Table ── */
    .table-card {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .data-card-header, .table-card-header {
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        background: #FAFBFC;
    }

    .data-card-title, .table-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .custom-table thead th {
        background: #F8FAFC;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
    }

    .custom-table tbody tr {
        transition: background 0.15s;
    }

    .custom-table tbody tr:hover {
        background: #FAFBFC;
    }

    .custom-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-main);
        vertical-align: middle;
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ═══ BADGES ═══ */
    .badge {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        white-space: nowrap;
    }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }

    .badge-treatment  { background: #eff6ff; color: #2563eb; }
    .badge-treatment .dot { background: #3b82f6; }
    .badge-autopsy    { background: #fef2f2; color: #dc2626; }
    .badge-autopsy .dot { background: #ef4444; }
    .badge-quarantine { background: #fff7ed; color: #ea580c; }
    .badge-quarantine .dot { background: #f97316; }
    .badge-hospital   { background: #f0fdf4; color: #16a34a; }
    .badge-hospital .dot { background: #22c55e; }
    .badge-pending   { background: #fef2f2; color: #e11d48; border: 1px solid #fecdd3; }
    .badge-pending .dot { background: #e11d48; }
    .badge-review    { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-review .dot { background: #d97706; }
    .badge-ready     { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-ready .dot { background: #15803d; }
    .badge-completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-completed .dot { background: #15803d; }
    .badge-none { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .badge-none .dot { background: #94a3b8; }
    .badge-active   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-active .dot { background: #22c55e; }

    /* ═══ ACTION BUTTON ═══ */
    .btn-action, .btn-tbl {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; border-radius: 8px;
        font-family: 'Cairo', sans-serif; font-size: 0.8rem; font-weight: 700;
        cursor: pointer; text-decoration: none; transition: all 0.2s;
        border: 1px solid #e2e8f0; white-space: nowrap;
        background: #ffffff; color: #334155;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-action:hover, .btn-tbl:hover {
        background: #f8fafc; border-color: #cbd5e1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-tbl-view { background: #fff; color: #334155; border-color: #e2e8f0; }
    .btn-tbl-view:hover { background: #f8fafc; border-color: #94a3b8; }
    .btn-tbl-export { background: #fff; color: #16a34a; border-color: #bbf7d0; }
    .btn-tbl-export:hover { background: #f0fdf4; border-color: #22c55e; }

    /* Animal ID style */
    .animal-id {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        background: #f8fafc;
        padding: 2px 6px;
        border-radius: 6px;
        color: #64748b;
        font-weight: 700;
        display: inline-block;
        margin-top: 4px;
        border: 1px solid #e2e8f0;
    }

    /* ═══ SIDE PANEL & OTHERS ═══ */
    .side-panel { display: flex; flex-direction: column; gap: 1.2rem; }
    .activity-feed { background: #fff; border-radius: 18px; border: 1px solid #e8edf5; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
    .activity-item { display: flex; gap: 12px; padding: 1rem 1.2rem; border-bottom: 1px solid #f8fafc; align-items: flex-start; transition: background 0.15s; }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: #f8faff; }
    .activity-dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 4px; flex-shrink: 0; }
    .activity-text { flex: 1; font-size: 0.8rem; font-weight: 600; color: #334155; line-height: 1.5; }
    .activity-time { font-size: 0.7rem; color: #94a3b8; font-weight: 600; white-space: nowrap; margin-top: 2px; }

    /* ═══ QUICK LINKS ═══ */
    .quick-links { background: #fff; border-radius: 18px; border: 1px solid #e8edf5; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
    .quick-link-item { display: flex; align-items: center; gap: 12px; padding: 0.9rem 1.2rem; text-decoration: none; border-bottom: 1px solid #f8fafc; transition: all 0.2s; }
    .quick-link-item:last-child { border-bottom: none; }
    .quick-link-item:hover { background: #f0fdf4; padding-right: 1.5rem; }
    .quick-link-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .quick-link-text { flex: 1; font-size: 0.83rem; font-weight: 700; color: #334155; }
    .quick-link-arr { color: #cbd5e1; transition: color 0.2s, transform 0.2s; }
    .quick-link-item:hover .quick-link-arr { color: #1a4a2e; transform: translateX(-4px); }

    /* ═══ MODAL ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; box-shadow: 0 30px 80px rgba(0,0,0,0.25); animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
    @keyframes modalIn { from { transform: translateY(30px) scale(0.96); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
    .modal-header { padding: 1.4rem 1.6rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #0d2818, #1a4a2e); border-radius: 20px 20px 0 0; }
    .modal-header h3 { font-size: 1.05rem; font-weight: 800; color: #fff; }
    .modal-close { width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.15); border: none; color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.1rem; font-weight: 700; transition: background 0.2s; }
    .modal-close:hover { background: rgba(255,255,255,0.25); }
    .modal-body { padding: 1.6rem; }
    .modal-footer { padding: 1rem 1.6rem; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; justify-content: flex-end; }
    
    .decision-box { background: #fff; border-radius: 20px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 30px 80px rgba(0,0,0,0.25); animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
    .decision-header { padding: 1.4rem 1.6rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius: 20px 20px 0 0; }
    .decision-header h3 { font-size: 1.05rem; font-weight: 800; color: #fff; }
    .decision-body { padding: 1.6rem; }
    .decision-footer { padding: 1rem 1.6rem; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; justify-content: flex-end; }
    .decision-options { display: flex; flex-direction: column; gap: 10px; }
    .decision-option { display: flex; align-items: center; gap: 15px; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.2s; }
    .decision-option:hover { border-color: #94a3b8; background: #f8fafc; }
    .decision-option.selected { border-color: #2E7D32; background: #F0FDF4; }
    .opt-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .opt-title { font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
    .opt-desc { font-size: 0.78rem; color: #64748b; font-weight: 600; line-height: 1.4; }
    .detail-section { margin-bottom: 1.5rem; }
    .detail-section h4 { display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; padding-bottom: 8px; border-bottom: 2px solid #f1f5f9; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .detail-item { display: flex; flex-direction: column; gap: 4px; }
    .detail-item label { font-size: 0.75rem; color: #64748b; font-weight: 700; }
    .detail-item span { font-size: 0.88rem; color: #0f172a; font-weight: 800; }
    .vet-note { background: #f8fafc; border-left: 3px solid #3b82f6; padding: 12px 15px; border-radius: 0 8px 8px 0; margin-bottom: 10px; }
    .note-date { font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 4px; }
    .note-text { font-size: 0.85rem; color: #334155; font-weight: 600; line-height: 1.5; }

    /* ═══ FORM ═══ */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: span 2; }
    .form-group label { font-size: 0.8rem; font-weight: 800; color: #374151; }
    .form-group label span.req { color: #ef4444; }
    .form-input, .form-select, .form-textarea { padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #0f172a; background: #fafbff; transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #2d7a47; box-shadow: 0 0 0 3px rgba(45,122,71,0.1); background: #fff; }
    .form-textarea { resize: vertical; min-height: 80px; }
    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(45,122,71,0.3); }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(45,122,71,0.35); }
    .btn-cancel { padding: 10px 20px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #e2e8f0; }

    /* ═══ CONFIRM MODAL ═══ */
    .confirm-box, .release-box { background: #fff; border-radius: 18px; width: 100%; max-width: 420px; padding: 2rem; text-align: center; box-shadow: 0 30px 80px rgba(0,0,0,0.2); animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
    .confirm-icon, .release-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.6rem; }
    .confirm-icon { background: #fff1f2; }
    .release-icon { background: #f0fdf4; }
    .confirm-box h3, .release-box h3 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
    .confirm-box p, .release-box p { font-size: 0.83rem; color: #64748b; font-weight: 600; margin-bottom: 1.5rem; line-height: 1.6; }
    .confirm-actions { display: flex; gap: 10px; justify-content: center; }
    .btn-confirm-delete, .btn-confirm-slaughter { padding: 9px 22px; background: #e11d48; color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-confirm-delete:hover, .btn-confirm-slaughter:hover { background: #be123c; }
    .btn-confirm-release { padding: 9px 22px; background: #16a34a; color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-confirm-release:hover { background: #15803d; }


/* ═══ SUMMARY CARD ═══ */
.summary-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.summary-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e8edf5;
    display: flex;
    align-items: center;
    gap: 1.2rem;
}
.animal-avatar {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
    border: 2px solid #2E7D32;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}
.animal-info h3 { font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-bottom: 4px; }
.animal-info p { font-size: 0.8rem; color: #64748b; font-weight: 600; }
.summary-body { padding: 2rem; }
.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}
.info-item { padding: 0.8rem 0; }
.info-item-label { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; }
.info-item-value { font-size: 0.85rem; font-weight: 700; color: #0f172a; }

/* ═══ REASON CARD ═══ */
.reason-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.reason-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e8edf5;
}
.reason-header h3 { font-size: 1.1rem; font-weight: 900; color: #0f172a; }
.reason-body { padding: 2rem; }
.reason-section { margin-bottom: 1.5rem; }
.reason-section:last-child { margin-bottom: 0; }
.reason-label { font-size: 0.8rem; font-weight: 800; color: #2E7D32; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
.reason-content {
    font-size: 0.9rem;
    font-weight: 600;
    color: #334155;
    line-height: 1.7;
    background: #f8fafc;
    padding: 1rem 1.4rem;
    border-radius: 10px;
    border-left: 3px solid #2E7D32;
}

</style>
@endsection

@section('content')
{{-- ═══════ HEADER & FILTERS ═══════ --}}
<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>🔬 إحالات التشريح</h2>
            <p>مراجعة إحالات التشريح الواردة وتوثيق نتيجة التشريح للحالات المطلوبة</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="num">2</div>
                <div class="lbl">بانتظار التوثيق</div>
            </div>
            <div class="hero-stat">
                <div class="num">1</div>
                <div class="lbl">موثقة</div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، نوع الحيوان، أو رقم الإحالة...">
        </div>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option value="big">القطط الكبرى</option>
            <option value="primates">القرود</option>
            <option value="herbivores">العناقيد الكبرى</option>
            <option value="birds">الطيور</option>
        </select>
        <select class="filter-select">
            <option value="">كل الحالات</option>
            <option value="pending">بانتظار التوثيق</option>
            <option value="documented">موثقة</option>
        </select>
        <select class="filter-select">
            <option value="">كل التواريخ</option>
            <option value="today">اليوم</option>
            <option value="week">هذا الأسبوع</option>
            <option value="month">هذا الشهر</option>
        </select>
    </div>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">إحالات التشريح</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الإحالة</th>
                    <th>الرقم الرسمي للحيوان</th>
                    <th>نوع الحيوان</th>
                    <th>المجموعة الحيوانية</th>
                    <th>تاريخ الإرسال</th>
                    <th>سبب الإحالة</th>
                    <th>حالة التشريح</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                {{-- Row 1: Pending --}}
                <tr>
                    <td><span style="font-family:'Courier New',monospace;font-size:0.75rem;background:#fffbeb;color:#b45309;padding:3px 8px;border-radius:6px;font-weight:700;">001-2025-AR</span></td>
                    <td>#ANM-009</td>
                    <td>نسر ذهبي</td>
                    <td>الطيور</td>
                    <td>2025-05-13</td>
                    <td>وفاة غير معروفة السبب</td>
                    <td><span class="badge badge-pending"><span class="dot"></span>بانتظار التوثيق</span></td>
                    <td>
                        <a href="/vet/referrals/autopsy/show" class="btn-tbl btn-tbl-view" style="padding: 8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                {{-- Row 2: Pending --}}
                <tr>
                    <td><span style="font-family:'Courier New',monospace;font-size:0.75rem;background:#fffbeb;color:#b45309;padding:3px 8px;border-radius:6px;font-weight:700;">002-2025-AR</span></td>
                    <td>#ANL-0871</td>
                    <td>شمبانزي أفريقي</td>
                    <td>القرود</td>
                    <td>2025-05-15</td>
                    <td>وفاة مفاجئة</td>
                    <td><span class="badge badge-pending"><span class="dot"></span>بانتظار التوثيق</span></td>
                    <td>
                        <a href="/vet/referrals/autopsy/show" class="btn-tbl btn-tbl-view" style="padding: 8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                {{-- Row 3: Documented --}}
                <tr>
                    <td><span style="font-family:'Courier New',monospace;font-size:0.75rem;background:#f0fdf4;color:#15803d;padding:3px 8px;border-radius:6px;font-weight:700;">003-2025-AR</span></td>
                    <td>#ANM-154</td>
                    <td>زرافة نيلية</td>
                    <td>العناقيد الكبرى</td>
                    <td>2025-05-10</td>
                    <td>مشاكل تنفسية</td>
                    <td><span class="badge badge-documented"><span class="dot"></span>موثقة</span></td>
                    <td>
                        <a href="/vet/referrals/autopsy/show" class="btn-tbl btn-tbl-view" style="padding: 8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ DETAIL MODAL ═══ --}}
<div class="modal-backdrop" id="autopsyDetailModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.7); backdrop-filter:blur(5px); z-index:9999; align-items:center; justify-content:center; padding:2rem;">
    <div class="modal-box" style="max-width:680px; background:#f1f5f9; overflow:hidden; display:flex; flex-direction:column;">

        <div class="modal-header" style="background:#fff; border-bottom:1px solid #e2e8f0; padding:1.2rem 1.5rem 0; display:flex; justify-content:space-between; align-items:flex-end;">
            <div style="padding-bottom:0.8rem;">
                <h3 style="margin:0; font-size:1.1rem; font-weight:800; color:#0f172a;">تفاصيل إحالة التشريح</h3>
                <span style="font-size:0.8rem; color:#64748b; font-weight:600;">001-2025-AR — نسر ذهبي</span>
            </div>
            <div style="display:flex; align-items:center; gap:20px;">
                <div style="display:flex; gap:10px;">
                    <button id="atab-btn-animal" onclick="switchATab('animal')" style="padding:10px 18px; border:none; background:transparent; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer; color:#1a4a2e; border-bottom:3px solid #1a4a2e;">بيانات الحيوان</button>
                    <button id="atab-btn-reason" onclick="switchATab('reason')" style="padding:10px 18px; border:none; background:transparent; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer; color:#94a3b8; border-bottom:3px solid transparent;">سبب الإحالة</button>
                </div>
                <button onclick="closeAutopsyModal()" style="width:32px; height:32px; border-radius:8px; background:#f1f5f9; border:none; color:#64748b; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.1rem; font-weight:700; transition:all 0.2s; margin-bottom:10px;">✕</button>
            </div>
        </div>

        <div style="padding:1.5rem; overflow-y:auto; max-height:65vh;">
            <div id="atab-animal">
        <div class="summary-card">
            <div class="summary-header">
                <div class="animal-info">
                    <h3>نسر ذهبي</h3>
                    <p>001-2025-AR — #ANM-009</p>
                </div>
            </div>
            <div class="summary-body">
                <div class="summary-grid">
                    <div class="info-item">
                        <div class="info-item-label">رقم الإحالة</div>
                        <div class="info-item-value">AR-2025-001</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">رقم الحيوان</div>
                        <div class="info-item-value">#ANM-009</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">نوع الحيوان</div>
                        <div class="info-item-value">نسر ذهبي</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">الجنس</div>
                        <div class="info-item-value">ذكر</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">العمر</div>
                        <div class="info-item-value">8 سنوات</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">المجموعة</div>
                        <div class="info-item-value">الطيور</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">تاريخ الإحالة</div>
                        <div class="info-item-value">2025-05-13</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">حالة التشريح</div>
                        <div class="info-item-value">
                            <span class="badge badge-pending"><span class="dot"></span>بانتظار التوثيق</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            </div>

            <div id="atab-reason" style="display:none;">
        <div class="reason-card">
            <div class="reason-header">
                <h3>سبب الإحالة والملاحظات</h3>
            </div>
            <div class="reason-body">
                <div class="reason-section">
                    <div class="reason-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        سبب الإحالة
                    </div>
                    <div class="reason-content">وفاة غير معروفة السبب</div>
                </div>
                <div class="reason-section">
                    <div class="reason-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        الملاحظات المسجلة قبل التحويل
                    </div>
                    <div class="reason-content">الحيوان وجد ميتاً في القفص بدون أسباب واضحة</div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>

<script>
function openAutopsyModal() {
    document.getElementById('autopsyDetailModal').style.display = 'flex';
}

function closeAutopsyModal() {
    document.getElementById('autopsyDetailModal').style.display = 'none';
}

function switchATab(tabName) {
    document.getElementById('atab-animal').style.display = 'none';
    document.getElementById('atab-reason').style.display = 'none';

    document.getElementById('atab-btn-animal').style.color = '#94a3b8';
    document.getElementById('atab-btn-animal').style.borderBottomColor = 'transparent';
    document.getElementById('atab-btn-reason').style.color = '#94a3b8';
    document.getElementById('atab-btn-reason').style.borderBottomColor = 'transparent';

    document.getElementById('atab-' + tabName).style.display = 'block';
    document.getElementById('atab-btn-' + tabName).style.color = '#1a4a2e';
    document.getElementById('atab-btn-' + tabName).style.borderBottomColor = '#1a4a2e';
}
</script>
@endsection
