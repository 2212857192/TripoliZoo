@extends($__layout ?? 'records.layout')
@section('title', 'ملف الحيوان | السجلات والتوثيق')
@section('page_title', 'ملف الحيوان الرسمي')

@section('styles')
<style>
    /* ═══ CARE STYLE: Breadcrumb ═══ */
    .breadcrumb { display:flex; align-items:center; gap:8px; margin-bottom:1.5rem; font-size:0.9rem; font-weight:700; color:#64748b; }
    .breadcrumb a { color:#2E7D32; text-decoration:none; transition:color 0.2s; display:flex; align-items:center; gap:4px; }
    .breadcrumb a:hover { color:#1b5e20; }

    .header-card {
        background:var(--white); border:1px solid var(--border); border-radius:16px;
        padding:1.5rem 2rem; margin-bottom:1.5rem;
        display:flex; justify-content:space-between; align-items:center;
        box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); flex-wrap:wrap; gap:1rem;
    }
    .header-info h2 {
        font-size:1.4rem; font-weight:800; color:#0f172a; margin:0 0 10px 0;
        display:flex; align-items:center; gap:12px; flex-wrap:wrap;
    }

    .badge { padding:5px 12px; border-radius:999px; font-size:0.8rem; font-weight:700; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; }
    .badge .dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
    .badge-active    { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-active .dot { background:#22c55e; }
    .badge-dead      { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .badge-dead .dot { background:#ef4444; }
    .badge-stillborn { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
    .badge-stillborn .dot { background:#f97316; }
    .badge-slaughter { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
    .badge-slaughter .dot { background:#dc2626; }
    .badge-exited    { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }
    .badge-exited .dot { background:#94a3b8; }

    .btn-back {
        display:inline-flex; align-items:center; gap:8px;
        padding:10px 20px; background:#f8fafc; color:#475569;
        border:1.5px solid #e2e8f0; border-radius:10px;
        font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800;
        cursor:pointer; transition:all 0.2s; text-decoration:none;
    }
    .btn-back:hover { background:#f1f5f9; color:#0f172a; }
    .btn-export {
        display:inline-flex; align-items:center; gap:8px;
        padding:10px 20px; background:#2E7D32; color:#fff;
        border:1.5px solid #2E7D32; border-radius:10px;
        font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800;
        cursor:pointer; transition:all 0.2s;
    }
    .btn-export:hover { background:#1B5E20; }
    .btn-outline {
        display:inline-flex; align-items:center; gap:8px;
        padding:10px 20px; background:#fff; color:#475569;
        border:1.5px solid #e2e8f0; border-radius:10px;
        font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800;
        cursor:pointer; transition:all 0.2s;
    }
    .btn-outline:hover { background:#f8fafc; color:#0f172a; }
    .btn-danger-outline {
        display:inline-flex; align-items:center; gap:8px;
        padding:10px 20px; background:#fff; color:#dc2626;
        border:1.5px solid #fecaca; border-radius:10px;
        font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800;
        cursor:pointer; transition:all 0.2s;
    }
    .btn-danger-outline:hover { background:#fef2f2; }

    /* ═══ Tabs (autopsy style) ═══ */
    .tabs-container { background:#fff; border-radius:16px; border:1px solid var(--border); overflow:hidden; }
    .tabs-header { display:flex; background:#FAFBFC; border-bottom:1px solid #e2e8f0; padding:0 1rem; overflow-x:auto; }
    .tab-btn {
        padding:16px 24px; border:none; background:transparent;
        font-family:'Cairo',sans-serif; font-size:0.95rem; font-weight:800;
        color:#64748b; cursor:pointer;
        border-bottom:3px solid transparent; transition:all 0.2s;
        display:flex; align-items:center; gap:8px; white-space:nowrap;
    }
    .tab-btn:hover { color:var(--green); }
    .tab-btn.active { color:var(--green); border-bottom-color:var(--green); background:#fff; }
    .tab-content { padding:2rem; display:none; }
    .tab-content.active { display:block; animation:fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:translateY(0); } }

    .summary-layout { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; }
    @media (max-width:768px) { .summary-layout { grid-template-columns:1fr; } }

    .animal-card {
        background:#fff; border-radius:12px; padding:1.5rem;
        border:1px solid #e2e8f0; box-shadow:0 4px 6px rgba(0,0,0,0.02);
        height:fit-content;
    }
    .animal-card-title { font-size:1.1rem; font-weight:800; color:#1e293b; margin-bottom:1.5rem; text-align:center; }
    .animal-photo-wrap { display:flex; justify-content:center; margin-bottom:1.2rem; }
    .animal-photo {
        width:72px; height:72px; border-radius:16px;
        background:linear-gradient(135deg,#E8F5E9,#C8E6C9);
        border:2px solid #bbf7d0;
        display:flex; align-items:center; justify-content:center;
        font-size:2.2rem; overflow:hidden;
    }
    .q-row {
        display:flex; justify-content:space-between; align-items:center;
        margin-bottom:0.8rem; padding-bottom:0.8rem; border-bottom:1px solid #f1f5f9;
    }
    .q-row:last-child { border-bottom:none; margin-bottom:0; padding-bottom:0; }
    .q-label { color:#64748b; font-size:0.9rem; font-weight:700; }
    .q-value { color:#0f172a; font-size:0.95rem; font-weight:800; text-align:left; }

    .section-title {
        font-size:1.1rem; font-weight:800; color:#0f172a;
        margin-bottom:1rem; display:flex; align-items:center; gap:8px;
    }
    .status-block { margin-top:1.5rem; }
    .status-block:first-child { margin-top:0; }

    .field-hint {
        font-size:0.78rem; color:#64748b; font-weight:600;
        margin-top:6px; font-style:italic;
    }

    .id-tag {
        font-family:'Courier New',monospace; font-size:0.85rem;
        background:#f1f5f9; padding:4px 10px; border-radius:6px;
        color:#334155; font-weight:800; display:inline-block; border:1px solid #e2e8f0;
    }

    .content-box {
        background:#f8fafc; padding:16px 20px; border-radius:10px;
        font-size:0.95rem; color:#334155; font-weight:600; line-height:1.7;
        border:1px solid #e2e8f0;
    }

    .report-link {
        display:inline-flex; align-items:center; gap:6px;
        color:#2563eb; font-size:0.85rem; font-weight:700;
        padding:8px 14px; background:#eff6ff; border-radius:8px;
        border:1px solid #bfdbfe; text-decoration:none;
    }
    /* ── Info cards (other tabs) ── */
    .info-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .info-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background: #FAFBFC;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }

    .info-card-header .sec-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        background: #e6f4ea;
        color: #1a4a2e;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .info-card-header .sec-icon.orange { background: #fef3c7; color: #d97706; }
    .info-card-header .sec-icon.blue   { background: #eff6ff; color: #2563eb; }
    .info-card-header .sec-icon.purple { background: #f5f3ff; color: #7c3aed; }
    .info-card-header .sec-icon.red    { background: #fef2f2; color: #dc2626; }
    .info-card-header .sec-icon.gray   { background: #f1f5f9; color: #64748b; }

    .info-grid {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:1px;
        background:#e2e8f0;
        border-radius:12px;
        overflow:hidden;
        border:1px solid #e2e8f0;
    }

    .info-grid.col-3 { grid-template-columns: 1fr 1fr 1fr; }

    .info-cell { background: #fff; padding: 14px 20px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell.span-3 { grid-column: span 3; }
    .info-cell-label { font-size:0.8rem; color:#64748b; font-weight:800; margin-bottom:6px; }
    .info-cell-value { font-size:1rem; color:#0f172a; font-weight:800; }
    .info-cell-value.muted { color: #94a3b8; font-style: italic; font-weight: 600; }
    .info-cell-value.mono  { font-family: 'Courier New', monospace; }

    .animal-id { font-family:'Courier New',monospace; font-size:0.8rem; background:#f0fdf4; padding:3px 8px; border-radius:6px; color:#15803d; font-weight:800; border:1px solid #bbf7d0; }

    /* ── Table ── */
    .table-card {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .table-card-header {
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        background: #FAFBFC;
    }

    .table-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }

    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.78rem; font-weight: 800; padding: 12px 18px; border-bottom: 1px solid var(--border); }
    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td { padding: 14px 18px; border-bottom: 1px solid #F1F5F9; font-size: 0.88rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .custom-table tbody tr:last-child td { border-bottom: none; }

    .ref-tag {
        font-size: 0.78rem;
        font-weight: 700;
        color: #2563eb;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        padding: 3px 8px;
        white-space: nowrap;
    }

    /* Badge */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge-green  { background: #f0fdf4; color: #15803d; }  .badge-green .dot  { background: #22c55e; }
    .badge-blue   { background: #eff6ff; color: #2563eb; }  .badge-blue .dot   { background: #3b82f6; }
    .badge-red    { background: #fef2f2; color: #dc2626; }  .badge-red .dot    { background: #ef4444; }
    .badge-orange { background: #fffbeb; color: #d97706; }  .badge-orange .dot { background: #f59e0b; }
    .badge-gray   { background: #f8fafc; color: #64748b; }  .badge-gray .dot   { background: #94a3b8; }

    /* Content box */
    .content-box { background: #fff; padding: 12px 16px; border-radius: 10px; font-size: 0.88rem; color: #1e293b; font-weight: 700; line-height: 1.6; border: 1px solid #e2e8f0; border-right: 4px solid #3b82f6; margin-bottom: 1rem; }
    .content-box.green  { border-right-color: #22c55e; }
    .content-box.orange { border-right-color: #f59e0b; }

    /* ── Attachment file item ── */
    .attachment-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 8px;
        transition: all 0.2s;
    }

    .attachment-item:hover { background: #fafbfc; }

    .attachment-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .attachment-info h4 { font-size: 0.88rem; font-weight: 800; color: #0f172a; margin: 0 0 2px; }
    .attachment-info p  { font-size: 0.75rem; color: #64748b; font-weight: 600; margin: 0; }

    .btn-download {
        margin-right: auto;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.78rem;
        font-weight: 800;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-download:hover { background: #e2e8f0; }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #94a3b8;
    }

    .empty-state svg { opacity: 0.4; margin-bottom: 12px; }
    .empty-state p { font-size: 0.9rem; font-weight: 700; margin: 0; }

    .attachments-notice {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-right: 3px solid #2E7D32;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        line-height: 1.7;
    }
    .attachments-notice strong { color: #1a4a2e; font-weight: 800; }

    /* ── Dialogs ── */
    .dialog-backdrop { display:none; position:fixed; inset:0; background:rgba(15,23,42,.45); backdrop-filter:blur(3px); z-index:1100; align-items:center; justify-content:center; }
    .dialog-backdrop.open { display:flex; }
    .dialog-box { background:#fff; border-radius:18px; width:100%; max-width:480px; box-shadow:0 30px 80px rgba(0,0,0,.2); animation:modalIn 0.25s cubic-bezier(.34,1.56,.64,1); overflow:hidden; }
    @keyframes modalIn { from { transform:translateY(20px) scale(.95); opacity:0; } to { transform:translateY(0) scale(1); opacity:1; } }
    .dialog-icon-wrap { width:62px; height:62px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; font-size:1.8rem; }
    .dialog-body { padding:2rem 2rem 1.5rem; text-align:center; }
    .dialog-body h4 { font-size:1.1rem; font-weight:800; color:#0f172a; margin-bottom:8px; }
    .dialog-body p { font-size:0.85rem; color:#64748b; font-weight:600; line-height:1.6; margin-bottom:0; }
    .dialog-footer { padding:1rem 1.5rem; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; gap:10px; justify-content:center; }
    .btn-submit { padding:10px 24px; background:linear-gradient(135deg,#1a4a2e,#2d7a47); color:#fff; border:none; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.88rem; font-weight:800; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn-submit:hover { transform:translateY(-1px); }
    .btn-submit-red { padding:10px 24px; background:linear-gradient(135deg,#991b1b,#dc2626); color:#fff; border:none; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.88rem; font-weight:800; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn-submit-red:hover { transform:translateY(-1px); }
    .btn-cancel { padding:10px 20px; background:#fff; color:#475569; border:1px solid #e2e8f0; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.88rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-cancel:hover { background:#f8fafc; }

    /* ── MODALS & FORMS ── */
    .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); backdrop-filter:blur(5px); z-index:1000; align-items:center; justify-content:center; }
    .modal-backdrop.open { display:flex; }
    .modal-box { background:#fff; border-radius:20px; width:100%; max-width:700px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 60px rgba(0,0,0,.2); animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
    .modal-header { padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; background:#fafbfc; position:sticky; top:0; z-index:10; }
    .modal-title { font-size:1.2rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px; }
    .btn-close { background:none; border:none; font-size:1.5rem; color:#64748b; cursor:pointer; transition:color 0.2s; }
    .btn-close:hover { color:#ef4444; }
    .modal-body { padding:24px; }
    .modal-footer { padding:20px 24px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:12px; background:#fafbfc; position:sticky; bottom:0; z-index:10; }
    
    .form-section { margin-bottom:24px; padding-bottom:24px; border-bottom:1px solid #e2e8f0; }
    .form-section:last-child { margin-bottom:0; padding-bottom:0; border-bottom:none; }
    .form-section-title { font-size:0.95rem; font-weight:800; color:#1e293b; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
    
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-group.full { grid-column:1 / -1; }
    .form-label { font-size:0.85rem; font-weight:700; color:#475569; }
    .form-input, .form-select, .form-textarea { padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.9rem; transition:all 0.2s; background:#fff; color:#0f172a; width:100%; box-sizing:border-box; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color:#2563eb; outline:none; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
    .form-textarea { resize:vertical; min-height:80px; }
    .file-upload { display:flex; align-items:center; gap:10px; padding:10px; border:1px dashed #cbd5e1; border-radius:10px; background:#f8fafc; color:#64748b; font-size:0.85rem; cursor:pointer; }
    .file-upload:hover { border-color:#94a3b8; background:#f1f5f9; }
    .file-upload input[type="file"] { display:none; }

    /* Toast */
    .toast { position:fixed; bottom:2rem; left:50%; transform:translateX(-50%) translateY(20px); background:#0f172a; color:#fff; padding:14px 24px; border-radius:12px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:700; display:flex; align-items:center; gap:10px; box-shadow:0 10px 30px rgba(0,0,0,.25); z-index:2000; opacity:0; transition:all 0.4s cubic-bezier(.34,1.56,.64,1); pointer-events:none; }
    .toast.show { opacity:1; transform:translateX(-50%) translateY(0); }
    .toast.green { background:linear-gradient(135deg,#1a4a2e,#2d7a47); }

    .notice-yellow { background:#fefce8; border:1px solid #fef08a; border-right:4px solid #eab308; border-radius:12px; padding:12px 16px; font-size:0.85rem; font-weight:700; color:#713f12; margin-bottom:1.5rem; display:flex; align-items:flex-start; gap:10px; }
    .notice-blue { background:#eff6ff; border:1px solid #bfdbfe; border-right:4px solid #2563eb; border-radius:12px; padding:12px 16px; font-size:0.85rem; font-weight:700; color:#1e40af; margin-bottom:1.5rem; display:flex; align-items:flex-start; gap:10px; }
</style>
@endsection

@section('content')

{{-- ═══ BREADCRUMB ═══ --}}
<div class="breadcrumb">
    <a href="/records/animals">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        قائمة الحيوانات
    </a>
    <span>/</span>
    <span style="color:#0f172a;" id="breadAnimal">ملف الحيوان</span>
</div>

<div class="header-card">
    <div class="header-info">
        <h2>
            ملف الحيوان
            <span id="headerBadge"></span>
        </h2>
        <div style="font-size:0.9rem; color:#64748b; font-weight:700; margin-top:8px;">
            رقم الحيوان: <span class="id-tag" id="topAnimalId">#ANM-0012</span>
            <span style="margin:0 8px; color:#cbd5e1;">|</span>
            <span id="pageSubtitle">سيمبا — أسد أفريقي</span>
        </div>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;" id="headerActions">
        <button type="button" class="btn-outline" id="btnEdit" onclick="openDialog('editDialog')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            تعديل
        </button>
        <button type="button" class="btn-export" id="btnExport" onclick="showToast('📄 جاري تصدير ملف الحيوان PDF...')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            تصدير PDF
        </button>
        <button type="button" class="btn-danger-outline" id="btnExit" onclick="openModal('exitModal')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            توثيق خروج
        </button>
        <a href="/records/animals" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            العودة
        </a>
    </div>
</div>

{{-- ═══ TABS ═══ --}}
<div class="tabs-container">
    <div class="tabs-header">
        <button type="button" class="tab-btn active" onclick="switchTab('basic', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            البيانات الأساسية
        </button>
        <button type="button" class="tab-btn" onclick="switchTab('origin', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            الأصل والتسجيل
        </button>
        <button type="button" class="tab-btn" onclick="switchTab('medical', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            التاريخ الطبي
        </button>
        <button type="button" class="tab-btn" onclick="switchTab('attachments', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
            المرفقات والتقارير
        </button>
    </div>

{{-- TAB 1: البيانات الأساسية --}}
<div class="tab-content active" id="tab-basic">

    <div class="notice-blue" id="quarantineNotice" style="display:none;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>هذا الحيوان دخل النظام عبر مسار الحجر الصحي وليس عبر تسجيل السجلات. البيانات المعروضة للعرض فقط.</span>
    </div>

    <div class="notice-yellow" id="manualNotice" style="display:none;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>هذا الحيوان أُدخل يدوياً للحيوانات الموجودة قبل تشغيل النظام. المواليد والحجر الصحي لها مسارات خاصة.</span>
    </div>

    <div class="summary-layout">
        <div>
            <h3 class="section-title">بيانات الحيوان الأساسية</h3>
            <div class="info-grid" style="margin-bottom:0;">
                <div class="info-cell">
                    <div class="info-cell-label">رقم الحيوان</div>
                    <div class="info-cell-value id-tag" id="fAnimalId">#ANM-0012</div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-label">الاسم</div>
                    <div class="info-cell-value" id="fAnimalName">سيمبا</div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-label">النوع</div>
                    <div class="info-cell-value" id="fAnimalType">أسد أفريقي</div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-label">المجموعة</div>
                    <div class="info-cell-value" id="fGroup">القططية</div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-label">الجنس</div>
                    <div class="info-cell-value" id="fGender">ذكر</div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-label">العمر</div>
                    <div class="info-cell-value" id="fAge">8 سنوات تقريبًا</div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-label">تاريخ التسجيل</div>
                    <div class="info-cell-value" id="fRegDate">2018-02-14</div>
                </div>
                <div class="info-cell span-2">
                    <div class="info-cell-label">العلامات المميزة</div>
                    <div class="info-cell-value" id="fMarks">ندبة صغيرة على الأذن اليسرى</div>
                </div>
            </div>

            <div id="statusCardWrap"></div>
            <div id="reproSection"></div>
        </div>

        <div class="animal-card">
            <h4 class="animal-card-title">بيانات الحيوان</h4>
            <div class="animal-photo-wrap">
                <div class="animal-photo" id="basicPhoto">🦁</div>
            </div>
            <div class="q-row">
                <span class="q-label">رقم الحيوان</span>
                <span class="q-value id-tag" id="qAnimalId">#ANM-0012</span>
            </div>
            <div class="q-row">
                <span class="q-label">نوع الحيوان</span>
                <span class="q-value" id="qAnimalType">أسد أفريقي</span>
            </div>
            <div class="q-row" id="qNameRow">
                <span class="q-label">اسم الحيوان</span>
                <span class="q-value" id="qAnimalName">سيمبا</span>
            </div>
            <div class="q-row">
                <span class="q-label">الجنس</span>
                <span class="q-value" id="qGender">ذكر</span>
            </div>
            <div class="q-row">
                <span class="q-label">العمر</span>
                <span class="q-value" id="qAge">8 سنوات تقريبًا</span>
            </div>
            <div class="q-row">
                <span class="q-label">المجموعة</span>
                <span class="q-value" id="qGroup">القططية</span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- TAB 2: الأصل والتسجيل --}}
{{-- ══════════════════════════════════════════ --}}
<div class="tab-content" id="tab-origin">

    <div class="info-card">
        <div class="info-card-header">
            <div class="sec-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
            معلومات الأصل والمصدر
        </div>
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-cell-label">أصل الحيوان</div>
                <div class="info-cell-value">مولود داخل الحديقة</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">مصدر الحيوان</div>
                <div class="info-cell-value">مولود داخل الحديقة حسب السجلات الورقية القديمة</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">طريقة الإدخال</div>
                <div class="info-cell-value">إدخال يدوي بواسطة مسؤول السجلات</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">تاريخ التسجيل في النظام</div>
                <div class="info-cell-value">2018-02-14</div>
            </div>
        </div>
    </div>

    {{-- التاريخ السابق --}}
    <div class="info-card">
        <div class="info-card-header">
            <div class="sec-icon orange"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
            التاريخ السابق قبل تشغيل النظام
        </div>
        <div style="padding:1.25rem 1.5rem;">
            <div class="content-box green">
                لا توجد سجلات طبية موثقة قبل تشغيل النظام. الحيوان مولود في الحديقة منذ عام 2018 وكان يتمتع بصحة جيدة وفقاً للسجلات الورقية المتوفرة.
            </div>
            <div style="margin-top:1rem;">
                <div style="font-size:0.78rem; color:#94a3b8; font-weight:700; margin-bottom:8px;">مرفق التاريخ السابق</div>
                <div class="attachment-item">
                    <div class="attachment-icon" style="background:#fef3c7;">📄</div>
                    <div class="attachment-info">
                        <h4>simba_history_2018.pdf</h4>
                        <p>PDF &nbsp;•&nbsp; 1.2 ميجابايت &nbsp;•&nbsp; 2018-02-14</p>
                    </div>
                    <a href="#" class="btn-download">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        تحميل
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- TAB 3: التاريخ الطبي --}}
{{-- ══════════════════════════════════════════ --}}
<div class="tab-content" id="tab-medical">

    {{-- 1. جدول التشخيصات --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div style="width:30px;height:30px;border-radius:8px;background:#fef2f2;color:#dc2626;display:flex;align-items:center;justify-content:center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                جدول التشخيصات
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>نوع الحالة</th>
                        <th>التشخيص</th>
                        <th>الطبيب</th>
                        <th>المرجع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color:#64748b; font-size:0.83rem;">2025-11-10</td>
                        <td>حالة طبية ميدانية</td>
                        <td style="font-weight:700;">التهاب في اللثة</td>
                        <td>د. أحمد سعيد</td>
                        <td><span class="ref-tag">حالة طبية ميدانية رقم 24</span></td>
                    </tr>
                    <tr>
                        <td style="color:#64748b; font-size:0.83rem;">2024-03-22</td>
                        <td>حالة داخل المستشفى</td>
                        <td style="font-weight:700;">إصابة في الكتف الأيمن</td>
                        <td>د. سارة خليل</td>
                        <td><span class="ref-tag">حالة داخل المستشفى رقم 15</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. جدول العلاجات والإجراءات الطبية --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div style="width:30px;height:30px;border-radius:8px;background:#fdf4ff;color:#c026d3;display:flex;align-items:center;justify-content:center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                </div>
                جدول العلاجات والإجراءات الطبية
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>العلاج / الإجراء</th>
                        <th>الطبيب</th>
                        <th>مرتبط بتشخيص</th>
                        <th>المرجع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color:#64748b; font-size:0.83rem;">2025-11-10</td>
                        <td style="font-weight:700;">مضاد حيوي أموكسيسيلين</td>
                        <td>د. أحمد سعيد</td>
                        <td>التهاب في اللثة</td>
                        <td><span class="ref-tag">حالة طبية ميدانية رقم 24</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. جدول الجرعات الوقائية --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div style="width:30px;height:30px;border-radius:8px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                جدول الجرعات الوقائية
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>اسم الجرعة</th>
                        <th>الطبيب</th>
                        <th>المرجع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color:#64748b; font-size:0.83rem;">2026-01-15</td>
                        <td style="font-weight:700;">لقاح الكُزاز السنوي</td>
                        <td>د. عمر حسن</td>
                        <td><span class="ref-tag">حالة ميدانية رقم 31</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. جدول التوصيات الغذائية العلاجية --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div style="width:30px;height:30px;border-radius:8px;background:#f0fdf4;color:#16a34a;display:flex;align-items:center;justify-content:center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                جدول التوصيات الغذائية العلاجية
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>تاريخ البداية</th>
                        <th>التوصية</th>
                        <th>المدة</th>
                        <th>الحالة</th>
                        <th>المرجع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color:#64748b; font-size:0.83rem;">2026-06-01</td>
                        <td style="font-weight:700;">وجبة لينة مؤقتًا</td>
                        <td>5 أيام</td>
                        <td><span class="badge badge-gray"><span class="dot"></span>منتهية</span></td>
                        <td><span class="ref-tag">حالة طبية ميدانية رقم 24</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- TAB: المرفقات والتقارير --}}
<div class="tab-content" id="tab-attachments">

    <div class="attachments-notice">
        <strong>تبويب عرض فقط.</strong>
        يعرض المرفقات الرسمية المرتبطة بالحيوان من مسارات النظام الأخرى (التاريخ السابق، التشريح، توثيق الخروج) — دون إمكانية إضافة مرفقات من هنا.
    </div>

    <div class="empty-state" id="attachmentsEmpty" style="display:none;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        <p>لا توجد مرفقات أو تقارير مرتبطة بهذا الحيوان</p>
    </div>

    <div class="table-card" id="attachmentsTableWrap">
        <div class="table-card-header">
            <div class="table-card-title">
                <div style="width:30px;height:30px;border-radius:8px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                </div>
                المرفقات والتقارير الرسمية
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>نوع المرفق</th>
                        <th>الملف</th>
                    </tr>
                </thead>
                <tbody id="attachmentsBody"></tbody>
            </table>
        </div>
    </div>
</div>
</div>

{{-- ═══ DIALOG: تعديل البيانات ═══ --}}
<div class="dialog-backdrop" id="editDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#f0fdf4;">✏️</div>
            <h4>تعديل بيانات الحيوان</h4>
            <p>ستُفتح نافذة تعديل البيانات الرسمية للحيوان سيمبا (#ANM-0012).<br>يمكنك تعديل البيانات الأساسية فقط.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('editDialog')">إلغاء</button>
            <button class="btn-submit" onclick="window.location.href='/records/animals/{{ $id ?? 1 }}/edit'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                فتح نموذج التعديل
            </button>
        </div>
    </div>
</div>

{{-- ═══ MODAL: توثيق الخروج ═══ --}}
<div class="modal-backdrop" id="exitModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">
                <div style="width:36px;height:36px;border-radius:10px;background:#fef2f2;color:#dc2626;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </div>
                توثيق خروج حيوان من الحديقة
            </div>
            <button class="btn-close" onclick="closeModal('exitModal')">&times;</button>
        </div>
        <div class="modal-body">
            
            <div class="form-section">
                <div class="form-section-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    تفاصيل عملية الخروج
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">تاريخ الخروج <span style="color:#ef4444">*</span></label>
                        <input type="date" class="form-input" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">نوع الخروج <span style="color:#ef4444">*</span></label>
                        <select class="form-select" required>
                            <option value="">-- اختر نوع الخروج --</option>
                            <option value="sale">بيع</option>
                            <option value="transfer">نقل</option>
                            <option value="swap">مقايضة</option>
                            <option value="gift">إهداء</option>
                            <option value="handover">تسليم لجهة خارجية</option>
                            <option value="return">إرجاع</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">الجهة المستلمة (اسم الجهة أو المؤسسة أو الشخص) <span style="color:#ef4444">*</span></label>
                        <input type="text" class="form-input" placeholder="مثال: حديقة حيوان بنغازي، السيد محمد..." required>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">سبب الخروج <span style="color:#ef4444">*</span></label>
                        <textarea class="form-textarea" placeholder="اكتب سبب الخروج بوضوح..." required></textarea>
                    </div>
                </div>
            </div>

            <div class="form-section" style="margin-bottom:0; padding-bottom:0; border-bottom:none;">
                <div class="form-section-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                    المرفقات الإضافية
                </div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">مرفق الخروج (مستند داعم إن وجد)</label>
                        <label class="file-upload">
                            <input type="file" accept=".pdf,image/*">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <span>اضغط هنا لرفع المرفق (PDF أو صورة)</span>
                        </label>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">ملاحظات (تفاصيل إضافية إن وجدت)</label>
                        <textarea class="form-textarea" placeholder="أي تفاصيل أخرى ترغب في إضافتها..."></textarea>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('exitModal')">إلغاء</button>
            <button class="btn-submit-red" onclick="closeModal('exitModal'); showToast('✅ تم توثيق خروج الحيوان بنجاح ووضعه في سجل الحيوانات الخارجة.')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                حفظ وتوثيق الخروج
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="toast green" id="toastMsg">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastText">تمت العملية بنجاح</span>
</div>

@endsection

@section('scripts')
<script>
    const animalId = '{{ $id ?? 'ANM-0012' }}';

    const animalDB = {
        'ANM-0012': {
            source: 'records', state: 'active', emoji: '🦁', displayId: '#ANM-0012',
            name: 'سيمبا', type: 'أسد أفريقي', group: 'القططية', gender: 'ذكر',
            age: '8 سنوات تقريبًا', regDate: '2018-02-14',
            marks: 'ندبة صغيرة على الأذن اليسرى',
            manualEntry: true,
            historyAttachment: { date: '2018-02-14', fileName: 'simba_history_2018.pdf' },
            repro: [
                { id: 'B-014', date: '2026-06-01', type: 'أسد أفريقي', gender: 'أنثى', mark: 'بقعة بيضاء', status: 'قيد المتابعة', statusClass: 'badge-gray', ref: 'سجل الولادات' },
                { id: 'B-015', date: '2026-06-01', type: 'أسد أفريقي', gender: 'ذكر', mark: '—', status: 'مكتمل', statusClass: 'badge-green', ref: 'سجل الولادات' }
            ]
        },
        'ANM-1046': {
            source: 'quarantine', state: 'active', emoji: '🐒', displayId: '#MON-1046',
            name: '—', type: 'قرد مكاك', group: 'القرود', gender: 'أنثى',
            age: 'يومان', regDate: '2026-06-05', marks: '—', manualEntry: false, repro: null
        },
        'ANM-1045': {
            source: 'quarantine', state: 'active', emoji: '🦌', displayId: '#GZL-1045',
            name: '—', type: 'غزال الريم', group: 'الغزلان', gender: 'ذكر',
            age: 'سنتان و 3 أشهر', regDate: '2026-06-07', marks: '—', manualEntry: false, repro: null
        },
        'ANM-0200': {
            source: 'records', state: 'dead', emoji: '🦁', displayId: '#ANM-0200',
            name: 'سلطان', type: 'أسد', group: 'القططية', gender: 'ذكر',
            age: '8 سنوات تقريبًا', regDate: '2026-06-07', marks: '—', manualEntry: false, repro: null,
            mortality: {
                deathDate: '2027-01-10', cause: 'التهاب حاد أدى إلى النفوق',
                autopsyReferral: 'نعم', docDate: '2027-01-12',
                reportFile: 'sultan_autopsy_report.pdf'
            }
        },
        'ANM-0201': {
            source: 'records', state: 'stillborn', emoji: '🦁', displayId: '#ANM-0201',
            name: '—', type: 'أسد أفريقي', group: 'القططية', gender: 'أنثى',
            age: '—', regDate: '2026-06-01', marks: '—', manualEntry: false, repro: null,
            stillborn: {
                birthDate: '2026-06-01', deathDate: '2026-06-10',
                cause: 'ضعف عام بعد الولادة', autopsy: 'لا', docDate: '2026-06-10'
            }
        },
        'ANM-0202': {
            source: 'records', state: 'slaughter', emoji: '🐄', displayId: '#ANM-0202',
            name: '—', type: 'بقر', group: 'الثدييات الكبيرة', gender: 'أنثى',
            age: '5 سنوات', regDate: '2024-03-01', marks: '—', manualEntry: false, repro: null,
            slaughter: {
                decisionDate: '2027-02-01', vet: 'د. أحمد', headVet: 'د. منى',
                notes: '—'
            }
        },
        'ANM-0203': {
            source: 'records', state: 'exited', emoji: '🦒', displayId: '#ANM-0203',
            name: '—', type: 'زرافة', group: 'العناقيد الكبرى', gender: 'ذكر',
            age: '4 سنوات', regDate: '2023-01-15', marks: '—', manualEntry: false, repro: null,
            exit: {
                exitDate: '2027-03-15', exitType: 'نقل', recipient: 'جهة خارجية',
                reason: 'نقل إداري', notes: '—',
                exitFile: { date: '2027-03-15', fileName: 'giraffe_exit_document.pdf' }
            }
        }
    };

    function infoFieldsGrid(fields) {
        return `<div class="info-grid">${fields.map(f => {
            let val = f.value;
            if (f.hint && f.muted) {
                val = `<span class="report-link" style="cursor:default;">${f.hint}</span>`;
            } else if (f.hint) {
                val = f.value + `<div class="field-hint">${f.hint}</div>`;
            }
            return `
            <div class="info-cell${f.span ? ' span-2' : ''}">
                <div class="info-cell-label">${f.label}</div>
                <div class="info-cell-value${f.muted && !f.hint ? ' muted' : ''}">${val}</div>
            </div>`;
        }).join('')}</div>`;
    }

    function renderInfoCard(title, fields) {
        return `
            <div class="status-block">
                <h3 class="section-title">${title}</h3>
                ${infoFieldsGrid(fields)}
            </div>`;
    }

    function renderStatusCard(d) {
        const wrap = document.getElementById('statusCardWrap');
        wrap.innerHTML = '';

        if (d.state === 'dead' && d.mortality) {
            const m = d.mortality;
            wrap.innerHTML = renderInfoCard('بيانات النفوق', [
                { label: 'تاريخ النفوق', value: m.deathDate },
                { label: 'سبب النفوق', value: m.cause, span: true },
                { label: 'هل تمت الإحالة للتشريح؟', value: m.autopsyReferral },
                { label: 'تاريخ التوثيق', value: m.docDate },
                ...(m.autopsyReferral === 'نعم' && m.reportFile ? [{
                    label: 'تقرير الصفة التشريحية',
                    value: '—',
                    hint: 'موجود في تبويب المرفقات والتقارير',
                    span: true,
                    muted: true
                }] : [])
            ]);
            return;
        }

        if (d.state === 'stillborn' && d.stillborn) {
            const s = d.stillborn;
            wrap.innerHTML = renderInfoCard('بيانات نفوق المولود', [
                { label: 'تاريخ الولادة', value: s.birthDate },
                { label: 'تاريخ النفوق', value: s.deathDate },
                { label: 'سبب النفوق', value: s.cause, span: true },
                { label: 'هل تم التشريح؟', value: s.autopsy },
                { label: 'تاريخ التوثيق', value: s.docDate }
            ]);
            return;
        }

        if (d.state === 'slaughter' && d.slaughter) {
            const sl = d.slaughter;
            wrap.innerHTML = renderInfoCard('بيانات الذبح الاضطراري', [
                { label: 'تاريخ القرار', value: sl.decisionDate },
                { label: 'الطبيب المسؤول', value: sl.vet },
                { label: 'رئيس القسم المعتمد', value: sl.headVet },
                { label: 'ملاحظات القرار', value: sl.notes !== '—' ? sl.notes : '—', hint: sl.notes === '—' ? 'إن وجدت' : null, span: true, muted: sl.notes === '—' }
            ]);
            return;
        }

        if (d.state === 'exited' && d.exit) {
            const e = d.exit;
            wrap.innerHTML = renderInfoCard('بيانات الخروج', [
                { label: 'تاريخ الخروج', value: e.exitDate },
                { label: 'نوع الخروج', value: e.exitType },
                { label: 'الجهة المستلمة', value: e.recipient },
                { label: 'سبب الخروج', value: e.reason, span: true },
                { label: 'ملاحظات', value: e.notes !== '—' ? e.notes : '—', hint: e.notes === '—' ? 'إن وجدت' : null, span: true, muted: e.notes === '—' }
            ]);
        }
    }

    function buildAttachments(d) {
        const items = [];

        if (d.historyAttachment) {
            items.push({
                date: d.historyAttachment.date || d.regDate,
                type: 'مرفق تاريخ سابق',
                fileName: d.historyAttachment.fileName
            });
        }

        if (d.state === 'dead' && d.mortality && d.mortality.autopsyReferral === 'نعم' && d.mortality.reportFile) {
            items.push({
                date: d.mortality.docDate,
                type: 'تقرير صفة تشريحية',
                fileName: d.mortality.reportFile
            });
        }

        if (d.state === 'exited' && d.exit && d.exit.exitFile) {
            items.push({
                date: d.exit.exitFile.date || d.exit.exitDate,
                type: 'مرفق خروج',
                fileName: d.exit.exitFile.fileName
            });
        }

        return items.sort((a, b) => a.date.localeCompare(b.date));
    }

    function renderAttachmentsTab(d) {
        const items = buildAttachments(d);
        const emptyEl = document.getElementById('attachmentsEmpty');
        const tableWrap = document.getElementById('attachmentsTableWrap');
        const tbody = document.getElementById('attachmentsBody');

        if (!items.length) {
            emptyEl.style.display = 'block';
            tableWrap.style.display = 'none';
            tbody.innerHTML = '';
            return;
        }

        emptyEl.style.display = 'none';
        tableWrap.style.display = '';
        tbody.innerHTML = items.map(item => `
            <tr>
                <td style="color:#64748b; font-size:0.88rem;">${item.date}</td>
                <td style="font-weight:700;">${item.type}</td>
                <td>
                    <a href="#" class="report-link" onclick="return false;" title="${item.fileName}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        عرض / تحميل
                    </a>
                </td>
            </tr>`).join('');
    }

    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tabId).classList.add('active');
        btn.classList.add('active');
    }

    function renderReproSection(repro) {
        const el = document.getElementById('reproSection');
        if (!repro || !repro.length) { el.innerHTML = ''; return; }
        el.innerHTML = `
            <div class="status-block">
                <h3 class="section-title">التاريخ التناسلي</h3>
                <div class="table-card">
                    <div style="overflow-x:auto;">
                        <table class="custom-table">
                        <thead><tr>
                            <th>رقم المولود</th><th>تاريخ الولادة</th><th>النوع</th><th>الجنس</th>
                            <th>علامة التمييز</th><th>حالة المولود</th><th>السجل المرتبط</th>
                        </tr></thead>
                        <tbody>${repro.map(r => `
                            <tr>
                                <td><span class="animal-id">${r.id}</span></td>
                                <td style="color:#64748b;font-size:0.83rem;">${r.date}</td>
                                <td>${r.type}</td><td>${r.gender}</td><td>${r.mark}</td>
                                <td><span class="badge ${r.statusClass}"><span class="dot"></span>${r.status}</span></td>
                                <td><span class="ref-tag">${r.ref}</span></td>
                            </tr>`).join('')}</tbody>
                    </table>
                    </div>
                </div>
            </div>`;
    }

    const stateBadges = {
        active:    '<span class="badge badge-active"><span class="dot"></span>داخل الحديقة</span>',
        dead:      '<span class="badge badge-dead"><span class="dot"></span>نافق</span>',
        stillborn: '<span class="badge badge-stillborn"><span class="dot"></span>مولود نافق</span>',
        slaughter: '<span class="badge badge-slaughter"><span class="dot"></span>ذبح اضطراري</span>',
        exited:    '<span class="badge badge-exited"><span class="dot"></span>خارج من الحديقة</span>'
    };

    function loadAnimal() {
        const d = animalDB[animalId] || animalDB['ANM-0012'];
        const titleName = d.name !== '—' ? d.name + ' — ' + d.type : d.type;

        document.getElementById('breadAnimal').textContent = 'ملف ' + d.displayId;
        document.getElementById('topAnimalId').textContent = d.displayId;
        document.getElementById('pageSubtitle').textContent = titleName;
        document.getElementById('headerBadge').innerHTML = stateBadges[d.state] || stateBadges.active;

        document.getElementById('basicPhoto').textContent = d.emoji;
        document.getElementById('qAnimalId').textContent = d.displayId;
        document.getElementById('qAnimalType').textContent = d.type;
        document.getElementById('qAnimalName').textContent = d.name;
        document.getElementById('qGender').textContent = d.gender;
        document.getElementById('qAge').textContent = d.age;
        document.getElementById('qGroup').textContent = d.group;
        if (d.name === '—') {
            document.getElementById('qAnimalName').style.color = '#94a3b8';
            document.getElementById('qAnimalName').style.fontStyle = 'italic';
        }

        document.getElementById('fAnimalId').textContent = d.displayId;
        document.getElementById('fAnimalName').textContent = d.name;
        document.getElementById('fAnimalType').textContent = d.type;
        document.getElementById('fGroup').textContent = d.group;
        document.getElementById('fGender').textContent = d.gender;
        document.getElementById('fAge').textContent = d.age;
        document.getElementById('fRegDate').textContent = d.regDate;
        document.getElementById('fMarks').textContent = d.marks;

        document.getElementById('quarantineNotice').style.display = d.source === 'quarantine' ? 'flex' : 'none';
        document.getElementById('manualNotice').style.display = d.manualEntry ? 'flex' : 'none';

        const isQuarantine = d.source === 'quarantine';
        const isActive = d.state === 'active';
        document.getElementById('btnEdit').style.display = (isQuarantine || !isActive) ? 'none' : '';
        document.getElementById('btnExport').style.display = (isQuarantine || !isActive) ? 'none' : '';
        document.getElementById('btnExit').style.display = (isQuarantine || !isActive) ? 'none' : '';

        renderStatusCard(d);
        renderReproSection(d.state === 'active' ? d.repro : null);
        renderAttachmentsTab(d);
    }

    function openDialog(id) { document.getElementById(id).classList.add('open'); }
    function closeDialog(id) { document.getElementById(id).classList.remove('open'); }
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    document.querySelectorAll('.dialog-backdrop, .modal-backdrop').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('open');
        });
    });

    function showToast(msg) {
        const t = document.getElementById('toastMsg');
        document.getElementById('toastText').innerText = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    window.onload = loadAnimal;
</script>
@endsection
