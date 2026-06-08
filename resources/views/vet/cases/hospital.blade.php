@extends($__layout ?? 'vet.layout')
@section('title', 'الحالات داخل المستشفى | المستشفى البيطري')
@section('page_title', 'الحالات الطبية داخل المستشفى')

@section('styles')
<style>
    /* ── Header Area ── */
    .page-title-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .page-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .title-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #e6f4ea;
        color: #1a4a2e;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .page-title-wrap h2 {
        font-size: 1.3rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .btn-refresh {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-refresh:hover { background: #f8fafc; }

    /* ══ Segmented Tabs ══ */
    .tabs-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:0.8rem 1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; }
    .segmented-tabs {
        display: inline-flex;
        background: #f1f5f9;
        padding: 5px;
        border-radius: 10px;
        gap: 4px;
    }
    .seg-tab {
        background: transparent;
        border: none;
        padding: 9px 24px;
        border-radius: 7px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        font-weight: 800;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
    }
    .seg-tab:hover { color: #1a4a2e; }
    .seg-tab.active { background: #fff; color: #1a4a2e; box-shadow: 0 2px 4px rgba(0,0,0,0.07); }
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .btn-add { display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#16a34a; border:none; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; color:#fff; cursor:pointer; transition:all 0.2s; box-shadow:0 2px 4px rgba(22,163,74,0.2); }
    .btn-add:hover { background:#15803d; box-shadow:0 4px 8px rgba(22,163,74,0.3); }
    .btn-tbl:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
    .btn-tbl.edit:hover { color: #E8651A; background: #FFEDD5; border-color: #FED7AA; }

    /* ── Filter Card ── */
    .filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.2rem;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        align-items: center;
        justify-content: flex-start;
    }

    /* ── Table ── */
    .table-card {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-card-header {
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
    }

    .table-card-title {
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
    .badge-handover { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-handover .dot { background: #d97706; }
    .badge-unavailable { background: #fef2f2; color: #e11d48; border: 1px solid #fecdd3; }
    .badge-unavailable .dot { background: #e11d48; }
    .badge-received { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-received .dot { background: #15803d; }
    .badge-slaughter { background: #1e293b; color: #f8fafc; border: 1px solid #0f172a; }
    .badge-slaughter .dot { background: #ef4444; }
    .badge-watch { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .badge-watch .dot { background: #f59e0b; }
    .badge-critical { background: #fef2f2; color: #e11d48; border: 1px solid #fecdd3; }
    .badge-critical .dot { background: #ef4444; }

    /* ═══ ACTION BUTTON (Icon Only) ═══ */
    .btn-tbl {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px; padding: 0; border-radius: 9px;
        cursor: pointer; text-decoration: none; transition: all 0.2s;
        border: 1px solid #e2e8f0; flex-shrink: 0;
        background: #f8fafc; color: #475569;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-tbl:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        background: #e2e8f0; border-color: #cbd5e1; color: #0f172a;
    }

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
    .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); backdrop-filter:blur(5px); z-index:1000; align-items:center; justify-content:center; }
    .modal-backdrop.open { display:flex; }
    .modal-box { background:#fff; border-radius:20px; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px rgba(0,0,0,0.15); animation:modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform:translateY(24px) scale(0.97); opacity:0; } to { transform:translateY(0) scale(1); opacity:1; } }
    .modal-header { padding:1.4rem 1.8rem; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; background:#F8FAFC; border-radius:20px 20px 0 0; }
    .modal-header h3 { font-size:1.15rem; font-weight:800; color:#0f172a; margin:0; }
    .modal-close { width:32px; height:32px; border-radius:8px; background:#e2e8f0; border:none; color:#64748b; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.2rem; font-weight:700; transition:all 0.2s; line-height:1; }
    .modal-close:hover { background:#cbd5e1; color:#0f172a; }
    .modal-body { padding:1.8rem; }
    .modal-footer { padding:1.4rem 1.8rem; border-top:1px solid #e2e8f0; display:flex; gap:10px; justify-content:flex-end; background:#F8FAFC; border-radius:0 0 20px 20px; }
    
    .decision-box { background: #fff; border-radius: 20px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    .decision-header { padding: 1.4rem 1.8rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #F8FAFC; border-radius: 20px 20px 0 0; }
    .decision-header h3 { font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px; }
    .decision-body { padding: 1.8rem; }
    .decision-footer { padding: 1.4rem 1.8rem; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: flex-end; background: #F8FAFC; border-radius: 0 0 20px 20px; }
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

    /* ═══ TIMELINE & DETAILS MODAL ═══ */
    .content-grid { display:grid; grid-template-columns:1fr 2fr; gap:1.5rem; }
    @media (max-width:992px) { .content-grid { grid-template-columns:1fr; } }
    .card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; padding:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.02); }
    .card-title { font-size:1.1rem; font-weight:800; color:#0f172a; margin-bottom:1.2rem; display:flex; align-items:center; gap:8px; border-bottom:2px solid #f8fafc; padding-bottom:10px; }
    .info-list { display:flex; flex-direction:column; gap:12px; }
    .info-item { display:flex; flex-direction:column; gap:4px; padding-bottom:12px; border-bottom:1px solid #f1f5f9; }
    .info-item:last-child { border-bottom:none; padding-bottom:0; }
    .info-label { font-size:0.75rem; color:#64748b; font-weight:700; }
    .info-val { font-size:0.9rem; color:#0f172a; font-weight:800; }
    .info-box { background:#f8fafc; padding:10px; border-radius:8px; font-size:0.85rem; color:#334155; font-weight:600; line-height:1.5; border-right:3px solid #3b82f6; }
    .timeline { position:relative; padding-right:1rem; }
    .timeline::before { content:''; position:absolute; right:3px; top:0; bottom:0; width:2px; background:#e2e8f0; }
    .entry-card { position:relative; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.2rem; margin-bottom:1.5rem; box-shadow:0 2px 4px rgba(0,0,0,0.02); }
    .entry-card::before { content:''; position:absolute; right:-21px; top:20px; width:12px; height:12px; border-radius:50%; background:#fff; border:3px solid #3b82f6; z-index:1; }
    .entry-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:10px; border-bottom:1px solid #f1f5f9; }
    .entry-doctor { font-size:0.95rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:6px; }
    .entry-date { font-size:0.8rem; color:#64748b; font-weight:600; }
    .entry-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
    .entry-box { background:#f8fafc; padding:12px; border-radius:10px; border:1px solid #f1f5f9; }
    .entry-box.full { grid-column:span 2; }
    .box-title { font-size:0.75rem; color:#64748b; font-weight:800; margin-bottom:6px; display:block; }
    .box-text { font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5; }
    .entry-footer { display:flex; justify-content:space-between; align-items:center; margin-top:1rem; padding-top:10px; border-top:1px dashed #e2e8f0; }
    .status-badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:0.75rem; font-weight:800; }
    .status-better { background:#F0FDF4; color:#16A34A; }
    .status-stable { background:#EFF6FF; color:#2563EB; }
    
</style>
@endsection

@section('content')

{{-- ═══ TABS CARD ═══ --}}
<div class="tabs-card">
    <div class="segmented-tabs">
        <button class="seg-tab active" onclick="switchTab(event, 'active-cases')">الحالات النشطة</button>
        <button class="seg-tab" onclick="switchTab(event, 'pending-handover')">بانتظار الاستلام</button>
        <button class="seg-tab" onclick="switchTab(event, 'completed-cases')">الحالات المنتهية</button>
    </div>
</div>

{{-- ═══ FILTER CARD ═══ --}}
<div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.2rem; margin-bottom: 1.5rem; display: flex; justify-content: flex-start; align-items: center;">
    <div style="display: flex; gap: 15px; width: 100%;">
        <div style="flex: 2; position: relative;">
            <svg style="position: absolute; right: 12px; top: 11px; color: #94a3b8;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="البحث باسم الحيوان أو رقم الحالة..." style="width: 100%; padding: 10px 35px 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 600; outline: none; color: #0f172a;">
        </div>
        <select style="flex: 1; padding: 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; color: #475569; font-weight: 600; outline: none;">
            <option>جميع المجموعات</option>
            <option>القطط الكبرى</option>
            <option>الطيور</option>
            <option>الزواحف</option>
            <option>الرئيسيات</option>
        </select>
        <select style="flex: 1; padding: 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; color: #475569; font-weight: 600; outline: none;">
            <option>جميع الأطباء</option>
            <option>د. خالد العربي</option>
            <option>د. فاطمة الزهراء</option>
            <option>د. أسامة الورفلي</option>
        </select>
    </div>
</div>

<!-- ════ TAB 1: الحالات النشطة ════ -->
<div id="active-cases" class="tab-content active">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">الحالات تحت الرعاية والعلاج حالياً</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الحالة</th>
                        <th>رقم الحيوان الرسمي</th>
                        <th>الحيوان</th>
                        <th>المجموعة</th>
                        <th>تاريخ الدخول</th>
                        <th>المسؤول الطبي</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="animal-id">HC-2025-001</span></td>
                        <td>#ANM-101</td>
                        <td>أسد إفريقي (سيمبا)</td>
                        <td>القطط الكبرى</td>
                        <td>2026-05-30</td>
                        <td>د. خالد العربي</td>
                        <td><span class="badge badge-ready"><span class="dot"></span>جاهز للخروج</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content: center;">
                                <a href="/vet/cases/hospital/show" class="btn-tbl view" title="عرض">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="animal-id">HC-2025-002</span></td>
                        <td>#ANM-154</td>
                        <td>زرافة نيلية (جميلة)</td>
                        <td>العناقيد الكبرى</td>
                        <td>2026-06-02</td>
                        <td>د. فاطمة الزهراء</td>
                        <td><span class="badge badge-watch"><span class="dot"></span>قيد العلاج</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content: center;">
                                <a href="/vet/cases/hospital/show" class="btn-tbl view" title="عرض">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="animal-id">HC-2025-003</span></td>
                        <td>#ANM-088</td>
                        <td>نسر ذهبي</td>
                        <td>الطيور</td>
                        <td>2026-05-29</td>
                        <td>د. خالد العربي</td>
                        <td><span class="badge badge-critical"><span class="dot"></span>نقاهة حرجة</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content: center;">
                                <a href="/vet/cases/hospital/show" class="btn-tbl view" title="عرض">
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

<!-- ════ TAB 2: بانتظار الاستلام ════ -->
<div id="pending-handover" class="tab-content">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">حالات صدر لها قرار خروج وبانتظار مسؤول المجموعة</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الحالة</th>
                        <th>الحيوان</th>
                        <th>المجموعة</th>
                        <th>تاريخ قرار الخروج</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="animal-id">HC-2025-004</span></td>
                        <td>غزال الدوركاس</td>
                        <td>العناقيد الكبرى</td>
                        <td>2026-06-05</td>
                        <td><span class="badge badge-handover"><span class="dot"></span>بانتظار الاستلام</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content: center;">
                                <a href="/vet/cases/hospital/show" class="btn-tbl view" title="متابعة">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="animal-id">HC-2025-005</span></td>
                        <td>فهد إفريقي</td>
                        <td>القطط الكبرى</td>
                        <td>2026-06-04</td>
                        <td><span class="badge badge-unavailable"><span class="dot"></span>تعذر الاستلام مؤقتًا</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content: center;">
                                <a href="/vet/cases/hospital/show" class="btn-tbl view" title="متابعة">
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

<!-- ════ TAB 3: الحالات المنتهية ════ -->
<div id="completed-cases" class="tab-content">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">السجل التاريخي للحالات المنتهية أو المستلمة</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الحالة</th>
                        <th>الحيوان</th>
                        <th>تاريخ الدخول</th>
                        <th>تاريخ الانتهاء</th>
                        <th>النتيجة / الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="animal-id">HC-2025-006</span></td>
                        <td>شمبانزي (بونغو)</td>
                        <td>2026-05-10</td>
                        <td>2026-05-20</td>
                        <td><span class="badge badge-received"><span class="dot"></span>خرج بعد العلاج وتم الاستلام</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content: center;">
                                <a href="/vet/cases/hospital/show" class="btn-tbl view" title="عرض التفاصيل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="animal-id">HC-2025-007</span></td>
                        <td>مها أبو حراب</td>
                        <td>2026-05-25</td>
                        <td>2026-05-27</td>
                        <td><span class="badge badge-slaughter"><span class="dot"></span>ذبح اضطراري</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content: center;">
                                <a href="/vet/cases/hospital/show" class="btn-tbl view" title="عرض التفاصيل">
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

{{-- ═══ DETAIL MODAL ═══ --}}
<div class="modal-backdrop" id="detailModal">
    <div class="modal-box" style="max-width: 680px; background: #f1f5f9; overflow: hidden; display: flex; flex-direction: column;">
        
        {{-- Header --}}
        <div class="modal-header" style="background:#fff; border-bottom:1px solid #e2e8f0; padding:1.2rem 1.5rem 0; display:flex; justify-content:space-between; align-items:flex-end;">
            
            {{-- الجانب الأيمن: العنوان --}}
            <div style="padding-bottom: 0.8rem;">
                <h3 style="margin:0; font-size:1.1rem; font-weight:800; color:#0f172a;">تفاصيل الحالة</h3>
                <span style="font-size:0.8rem; color:#64748b; font-weight:600;" id="detailAnimalName">سيمبا - أسد إفريقي</span>
            </div>
            
            {{-- الجانب الأيسر: التبويبات وزر الإغلاق في أقصى اليسار --}}
            <div style="display:flex; align-items:center; gap:20px;">
                <div style="display:flex; gap:10px;">
                    <button id="dtab-btn-1" onclick="switchDTab(1)" style="padding:10px 24px; border:none; background:transparent; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; cursor:pointer; color:#1a4a2e; border-bottom:3px solid #1a4a2e;">ملخص الحالة</button>
                    <button id="dtab-btn-2" onclick="switchDTab(2)" style="padding:10px 24px; border:none; background:transparent; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; cursor:pointer; color:#94a3b8; border-bottom:3px solid transparent;">المتابعة الطبية</button>
                </div>
                <button onclick="closeModal('detailModal')" style="width:32px; height:32px; border-radius:8px; background:#f1f5f9; border:none; color:#64748b; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.1rem; font-weight:700; transition:all 0.2s; margin-bottom:10px;">✕</button>
            </div>
        </div>

        {{-- Body --}}
        <div style="padding: 1.5rem; overflow-y: auto; max-height: 65vh;">
            
            {{-- Tab 1: ملخص الحالة --}}
            <div id="dtab-1">
                
                {{-- Case ID --}}
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem;">
                    <span style="font-family:'Courier New',monospace; font-size:0.85rem; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:4px 12px; border-radius:6px; font-weight:700;">HC-2025-001</span>
                    <span style="font-size:0.8rem; color:#64748b; font-weight:700;">رقم الحالة</span>
                </div>

                {{-- Animal Info Grid --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1px; background:#e2e8f0; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0; margin-bottom:1.5rem;">
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">رقم الحيوان</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">ANL-0871</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">نوع الحيوان</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">شمبانزي</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">الجنس</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">ذكر</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">العمر</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">6 سنوات</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">المجموعة</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">القرود</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">تاريخ دخول المستشفى</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">2025-05-13</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">الطبيب المسؤول</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">د. ريم الفيصل</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">حالة الحالة</div>
                        <div style="font-size:0.9rem; color:#2563eb; font-weight:800;">قيد العلاج</div>
                    </div>
                </div>

                {{-- Reason & Notes --}}
                <div style="margin-bottom:1.5rem;">
                    <div style="font-size:0.8rem; color:#64748b; font-weight:800; margin-bottom:8px;">سبب الإحالة</div>
                    <div style="background:#fff; border-right:4px solid #f59e0b; padding:12px 16px; border-radius:8px; font-size:0.9rem; color:#1e293b; font-weight:700; border:1px solid #e2e8f0; border-right-width:4px;">
                        إصابة في الطرف الأمامي
                    </div>
                </div>
                <div>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:800; margin-bottom:8px;">الملاحظات المسجلة قبل التحويل</div>
                    <div style="background:#fff; border-right:4px solid #3b82f6; padding:12px 16px; border-radius:8px; font-size:0.9rem; color:#1e293b; font-weight:700; line-height:1.6; border:1px solid #e2e8f0; border-right-width:4px;">
                        الحيوان لا يستخدم يده اليسرى، مع وجود جرح واضح
                    </div>
                </div>
            </div>

            {{-- Tab 2: المتابعة الطبية --}}
            <div id="dtab-2" style="display:none;">
                
                {{-- Follow-up Card 1 --}}
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.2rem; margin-bottom:1rem; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                    
                    {{-- Card Header --}}
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; border-radius:50%; background:#fce7f3; color:#be185d; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800;">د.ر</div>
                            <div>
                                <div style="font-size:0.9rem; font-weight:800; color:#0f172a;">د. ريم الفيصل</div>
                                <div style="font-size:0.75rem; color:#94a3b8; font-weight:700;">2025-05-13 — 10:30</div>
                            </div>
                        </div>
                        <span style="background:#eff6ff; color:#2563eb; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:800;">قيد العلاج</span>
                    </div>

                    {{-- Card Body Grid --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div style="background:#f8fafc; padding:10px 14px; border-radius:8px; border:1px solid #f1f5f9;">
                            <div style="font-size:0.7rem; color:#94a3b8; font-weight:800; margin-bottom:4px;">التشخيص</div>
                            <div style="font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5;">جرح مفتوح في اليد اليسرى مع عدوى بسيطة</div>
                        </div>
                        <div style="background:#f8fafc; padding:10px 14px; border-radius:8px; border:1px solid #f1f5f9;">
                            <div style="font-size:0.7rem; color:#94a3b8; font-weight:800; margin-bottom:4px;">الإجراء الطبي / العلاج</div>
                            <div style="font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5;">تنظيف الجرح وخياطة، مضادات حيوية</div>
                        </div>
                        <div style="background:#f8fafc; padding:10px 14px; border-radius:8px; border:1px solid #f1f5f9;">
                            <div style="font-size:0.7rem; color:#94a3b8; font-weight:800; margin-bottom:4px;">ملاحظات الطبيب</div>
                            <div style="font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5;">الحيوان هادئ ومتعاون مع العلاج</div>
                        </div>
                        <div style="background:#fffbeb; padding:10px 14px; border-radius:8px; border-right:3px solid #f59e0b;">
                            <div style="font-size:0.7rem; color:#b45309; font-weight:800; margin-bottom:4px;">توصية غذائية علاجية</div>
                            <div style="font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5;">فاكهة طرية وعصائر</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div class="modal-footer" style="background:#fff; border-top:1px solid #e2e8f0; padding:1.2rem 1.5rem;">
            <button class="btn-cancel" onclick="closeModal('detailModal')">إغلاق</button>
            <button class="btn-submit" style="background:linear-gradient(135deg, #c2410c, #ea580c); box-shadow: 0 4px 12px rgba(234,88,12,0.25);" onclick="closeModal('detailModal'); openDecisionModal('سيمبا')">إصدار قرار</button>
        </div>
    </div>
</div>

<script>
function switchDTab(tabNum) {
    // Hide all contents
    document.getElementById('dtab-1').style.display = 'none';
    document.getElementById('dtab-2').style.display = 'none';
    
    // Reset buttons
    document.getElementById('dtab-btn-1').style.color = '#94a3b8';
    document.getElementById('dtab-btn-1').style.borderBottomColor = 'transparent';
    document.getElementById('dtab-btn-2').style.color = '#94a3b8';
    document.getElementById('dtab-btn-2').style.borderBottomColor = 'transparent';
    
    // Activate selected
    document.getElementById('dtab-' + tabNum).style.display = 'block';
    document.getElementById('dtab-btn-' + tabNum).style.color = '#1a4a2e';
    document.getElementById('dtab-btn-' + tabNum).style.borderBottomColor = '#1a4a2e';
}
</script>

{{-- ═══ DECISION MODAL ═══ --}}
<div class="modal-backdrop" id="decisionModal">
    <div class="decision-box">
        <div class="decision-header">
            <h3>✅ إصدار قرار طبي — <span id="decisionAnimalName"></span></h3>
            <button class="modal-close" onclick="closeModal('decisionModal')">✕</button>
        </div>
        <div class="decision-body">
            <p style="font-size:0.83rem; color:#64748b; font-weight:600; margin-bottom:1.2rem; line-height:1.6;">
                اختر نوع القرار الطبي المناسب. سيُسجَّل القرار في قسم <strong>القرارات الطبية</strong> ويخرج الحيوان من قائمة الحالات النشطة.
            </p>
            <div class="decision-options">
                <div class="decision-option selected" onclick="selectDecision(this)">
                    <div class="opt-icon" style="background:#f0fdf4;">🏠</div>
                    <div>
                        <div class="opt-title">خروج بعد العلاج</div>
                        <div class="opt-desc">الحيوان تعافى وجاهز للعودة إلى موقعه في الحديقة</div>
                    </div>
                </div>
                <div class="decision-option" onclick="selectDecision(this)">
                    <div class="opt-icon" style="background:#fef2f2; color:#ef4444;">⚠️</div>
                    <div>
                        <div class="opt-title">ذبح اضطراري</div>
                        <div class="opt-desc">الحالة ميؤوس منها وتستدعي الذبح الاضطراري وفق الإجراءات الطبية</div>
                    </div>
                </div>
            </div>
            <div style="margin-top:1rem;">
                <label style="font-size:0.8rem; font-weight:800; color:#374151; display:block; margin-bottom:6px;">ملاحظة القرار (اختياري)</label>
                <textarea style="width:100%; padding:9px 12px; border:1.5px solid #e2e8f0; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.83rem; font-weight:600; resize:vertical; min-height:70px; outline:none;" placeholder="أضف أي تفاصيل إضافية عن القرار..."></textarea>
            </div>
        </div>
        <div class="decision-footer">
            <button class="btn-cancel" onclick="closeModal('decisionModal')" style="padding:9px 18px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer;">إلغاء</button>
            <button class="btn-submit" style="background:#16a34a; padding:9px 22px; color:#fff; border:none; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer;">تأكيد وإصدار القرار</button>
        </div>
    </div>
</div>

{{-- ═══ SLAUGHTER CONFIRM ═══ --}}
<div class="modal-backdrop" id="slaughterModal">
    <div class="confirm-box">
        <div class="confirm-icon">⚠️</div>
        <h3>تأكيد قرار الذبح الاضطراري</h3>
        <p>هل أنت متأكد من إصدار قرار الذبح الاضطراري للحيوان <strong id="slaughterAnimalName"></strong>؟<br>
        هذا الإجراء لا يمكن التراجع عنه وسيُسجَّل في القرارات الطبية.</p>
        <div class="confirm-actions">
            <button class="btn-cancel" onclick="closeModal('slaughterModal')">إلغاء</button>
            <button class="btn-confirm-slaughter">تأكيد الذبح الاضطراري</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openDetailModal(name) {
    document.getElementById('detailAnimalName').textContent = name;
    document.getElementById('detailModal').classList.add('open');
}
function openDecisionModal(name) {
    document.getElementById('decisionAnimalName').textContent = name;
    document.getElementById('decisionModal').classList.add('open');
}
function openSlaughterModal(name) {
    document.getElementById('slaughterAnimalName').textContent = name;
    document.getElementById('slaughterModal').classList.add('open');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}
function selectDecision(el) {
    document.querySelectorAll('.decision-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
}
function switchTab(evt, tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}
document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
    backdrop.addEventListener('click', function(e) {
        if (e.target === backdrop) backdrop.classList.remove('open');
    });
});
</script>
@endsection
