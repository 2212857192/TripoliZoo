@extends($__layout ?? 'vet.layout')
@section('title', 'الرئيسية | المستشفى البيطري')
@section('page_title', 'لوحة المتابعة الطبية')

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

    .status-pill {
        background: #DCFCE7;
        border: 1px solid #BBF7D0;
        border-radius: 30px;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #166534;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22C55E;
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.3); }
    }

    /* ═══ STATS GRID ═══ */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        padding: 1.3rem 1.2rem;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: block;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        border-radius: 0 16px 16px 0;
        transition: width 0.3s;
        background: #1a4a2e; /* لون الشريط الأخضر الداكن */
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.1);
        border-color: transparent;
    }

    .stat-card:hover::before { width: 6px; }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 1rem;
        color: #16a34a; /* لون الأيقونة الأخضر */
    }

    .stat-num {
        font-size: 2.2rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        line-height: 1.4;
    }

    /* ═══ SECTION LAYOUT ═══ */
    .dash-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
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

    .view-all-link {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--green);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: var(--bg-color);
        border: 1px solid var(--border);
        border-radius: 8px;
        transition: all 0.2s;
    }
    .view-all-link:hover {
        background: #F1F5F9;
        border-color: #CBD5E1;
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

    /* ═══ ACTION BUTTON (Icon Only) ═══ */
    .actions-cell { display: flex; gap: 6px; align-items: center; justify-content: flex-end; }
    .btn-action, .btn-tbl {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px; padding: 0; border-radius: 9px;
        font-family: 'Cairo', sans-serif; font-size: 0.8rem; font-weight: 700;
        cursor: pointer; text-decoration: none; transition: all 0.2s;
        border: 1px solid #e2e8f0; flex-shrink: 0;
        background: #f8fafc; color: #475569;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-action:hover, .btn-tbl:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    .btn-tbl-view { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .btn-tbl-view:hover { background: #e2e8f0; border-color: #94a3b8; color: #0f172a; }
    .btn-tbl-export { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
    .btn-tbl-export:hover { background: #dcfce7; border-color: #22c55e; }

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

</style>
@endsection

@section('content')



{{-- ═══════ STATS CARDS ═══════ --}}
<div class="stats-grid">
    <a href="/vet/decisions" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-num">2</div>
        <div class="stat-label">قرارات طبية<br>حديثة</div>
    </a>

    <a href="/vet/referrals" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="stat-num">1</div>
        <div class="stat-label">إحالات تحتاج<br>متابعة</div>
    </a>

    <a href="/vet/quarantine" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="stat-num">3</div>
        <div class="stat-label">الحجر الصحي<br>الوقائي</div>
    </a>

    <a href="/vet/cases/hospital" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div class="stat-num">3</div>
        <div class="stat-label">حالات داخل<br>المستشفى</div>
    </a>

    <a href="/vet/referrals/treatment" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="stat-num">1</div>
        <div class="stat-label">حالات علاج<br>قيد المراجعة</div>
    </a>
</div>

{{-- ═══════ MAIN GRID ═══════ --}}
<div class="dash-grid">

    {{-- ─── LEFT: Referrals Table ─── --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="title-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                آخر الإحالات الواردة
            </div>
            <a href="/vet/referrals/treatment" class="view-all-link">
                عرض الكل
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
        </div>
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>نوع الإحالة</th>
                        <th>الحيوان</th>
                        <th>المجموعة</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th>إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge badge-treatment">إحالة علاج</span></td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">الفهد البري (صخر)</div>
                            <div class="animal-id">#ANM-109</div>
                        </td>
                        <td>السباع والضواري</td>
                        <td>2026-06-03</td>
                        <td><span class="badge badge-pending">قيد المراجعة</span></td>
                        <td>
                            <div class="actions-cell">
                                <a href="/vet/referrals/treatment" class="btn-tbl btn-tbl-view" title="التفاصيل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-autopsy">إحالة تشريح</span></td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">طائر العقاب الذهبي</div>
                            <div class="animal-id">#ANM-009-D</div>
                        </td>
                        <td>بيت الطيور الكبرى</td>
                        <td>2026-06-02</td>
                        <td><span class="badge badge-pending">انتظار التقرير</span></td>
                        <td>
                            <div class="actions-cell">
                                <a href="/vet/referrals/autopsy" class="btn-tbl btn-tbl-view" title="التفاصيل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════ BOTTOM FULL TABLE: Cases needing action ═══════ --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <div class="title-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
            حالات تتطلب إجراءً عاجلاً
        </div>
        <span style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; border-radius:20px; padding:4px 12px; font-size:0.75rem; font-weight:800;">
            4 حالات
        </span>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>نوع الحالة</th>
                    <th>الحيوان</th>
                    <th>الوضع الحالي</th>
                    <th>التاريخ</th>
                    <th>الوضع الإجرائي</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-hospital">حالة مستشفى</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">الأسد الإفريقي (سيمبا)</div>
                        <div class="animal-id">#ANM-101</div>
                    </td>
                    <td style="max-width:220px;">تماثل للشفاء الكامل بعد علاج جروح القدم</td>
                    <td>2026-05-30</td>
                    <td><span class="badge badge-ready">جاهز لإصدار الخروج</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/decisions" class="btn-tbl btn-tbl-view" title="التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-quarantine">حجر صحي</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">النمر البنغالي (رعد)</div>
                        <div class="animal-id">#ANM-204</div>
                    </td>
                    <td style="max-width:220px;">انتهاء مدة الملاحظة الوقائية بنجاح دون أعراض</td>
                    <td>2026-06-01</td>
                    <td><span class="badge badge-ready">جاهز للإفراج الصحي</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/quarantine" class="btn-tbl btn-tbl-view" title="التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-treatment">إحالة علاج</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">الفهد البري (صخر)</div>
                        <div class="animal-id">#ANM-109</div>
                    </td>
                    <td style="max-width:220px;">اشتباه بكسر كتف يحتاج قرار استدعاء للمستشفى</td>
                    <td>2026-06-03</td>
                    <td><span class="badge badge-review">قيد مراجعة رئيس القسم</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/referrals/treatment" class="btn-tbl btn-tbl-view" title="التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-autopsy">إحالة تشريح</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">طائر العقاب الذهبي</div>
                        <div class="animal-id">#ANM-009-D</div>
                    </td>
                    <td style="max-width:220px;">بانتظار إجراء التشريح وتوثيق التقرير النهائي للوفاة</td>
                    <td>2026-06-02</td>
                    <td><span class="badge badge-pending">بانتظار التوثيق</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/referrals/autopsy" class="btn-tbl btn-tbl-view" title="التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
