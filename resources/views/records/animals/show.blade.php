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
    .badge-quarantine { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
    .badge-quarantine .dot { background:#f97316; }
    .badge-pending-receipt { background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; }
    .badge-pending-receipt .dot { background:#3b82f6; }
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
    .profile-layout {
        display: grid;
        grid-template-columns: minmax(240px, 300px) 1fr;
        gap: 1.5rem;
        align-items: start;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 900px) { .profile-layout { grid-template-columns: 1fr; } }
    .profile-photo-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.25rem;
        text-align: center;
    }
    .profile-photo-card .animal-photo {
        width: 100%;
        max-width: 260px;
        height: auto;
        aspect-ratio: 1;
        margin: 0 auto;
        font-size: 4rem;
        border-radius: 18px;
    }
    .profile-fields-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
    }
    .attachments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1rem;
    }
    .attachment-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .attachment-card-type {
        font-size: 0.78rem;
        font-weight: 800;
        color: #64748b;
    }
    .attachment-card-date {
        font-size: 0.72rem;
        font-weight: 700;
        color: #94a3b8;
    }
    .repro-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.1rem;
        margin-bottom: 0.75rem;
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

    .profile-lock-notice {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-right: 3px solid #ea580c;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #9a3412;
        line-height: 1.7;
    }
    .profile-lock-notice strong { color: #c2410c; font-weight: 800; }

    .attachment-preview {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    .attachment-preview img {
        max-width: 180px;
        max-height: 180px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .attachment-preview img:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
    }
    .attachment-preview .report-link { margin-top: 2px; }

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
@php
    $portalBase = $portalBase ?? '/records';
    $readOnly = $readOnly ?? false;
@endphp

@if(session('success'))
<div class="notice-blue" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif

@if(!empty($profileLocked) && !empty($lockMessage))
<div class="profile-lock-notice">
    <strong>ملف مقفول.</strong> {{ $lockMessage }}
</div>
@endif

{{-- ═══ BREADCRUMB ═══ --}}
<div class="breadcrumb">
    <a href="{{ $portalBase }}/animals">
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
            رقم الحيوان: <span class="id-tag" id="topAnimalId">—</span>
            <span style="margin:0 8px; color:#cbd5e1;">|</span>
            <span id="pageSubtitle">—</span>
        </div>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;" id="headerActions">
        @if(!empty($canEdit))
        <a href="{{ route('records.animals.edit', $animal) }}" class="btn-outline" id="btnEdit">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            تعديل
        </a>
        @endif
        @if(!empty($canExport))
        <a href="{{ route('records.animals.export', $animal) }}" class="btn-export" id="btnExport" target="_blank" rel="noopener">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            تصدير PDF
        </a>
        @endif
        @if(!empty($canExit))
        <button type="button" class="btn-danger-outline" id="btnExit" onclick="openModal('exitModal')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            توثيق خروج
        </button>
        @endif
        <a href="{{ $portalBase }}/animals" class="btn-back">
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
        <button type="button" class="tab-btn" onclick="switchTab('medical', this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            التاريخ الطبي
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

    <h3 class="section-title">بيانات الحيوان</h3>
    <div class="profile-layout">
        <div class="profile-photo-card">
            <div class="animal-photo-wrap" style="margin-bottom:0;">
                <div class="animal-photo" id="basicPhoto">🦁</div>
            </div>
        </div>
        <div class="profile-fields-card">
            <div class="q-row">
                <span class="q-value id-tag" id="fAnimalId">—</span>
                <span class="q-label">رقم الحيوان</span>
            </div>
            <div class="q-row">
                <span class="q-value" id="fAnimalName">—</span>
                <span class="q-label">الاسم</span>
            </div>
            <div class="q-row">
                <span class="q-value" id="fAnimalType">—</span>
                <span class="q-label">النوع</span>
            </div>
            <div class="q-row">
                <span class="q-value" id="fGroup">—</span>
                <span class="q-label">المجموعة</span>
            </div>
            <div class="q-row">
                <span class="q-value" id="fGender">—</span>
                <span class="q-label">الجنس</span>
            </div>
            <div class="q-row">
                <span class="q-value" id="fAge">—</span>
                <span class="q-label">العمر</span>
            </div>
            <div class="q-row">
                <span class="q-value" id="fRegDate">—</span>
                <span class="q-label">تاريخ التسجيل</span>
            </div>
            <div class="q-row">
                <span class="q-value" id="fMarks">—</span>
                <span class="q-label">العلامة المميزة</span>
            </div>
        </div>
    </div>

    <div id="statusCardWrap"></div>
    <div id="reproSection"></div>
    <div id="profileAttachmentsSection" class="status-block" style="display:none;">
        <h3 class="section-title">المرفقات والتقارير</h3>
        <div id="profileAttachmentsGrid" class="attachments-grid"></div>
    </div>
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- TAB: التاريخ الطبي --}}
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
                <tbody id="diagnosesBody"></tbody>
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
                <tbody id="treatmentsBody"></tbody>
            </table>
        </div>
    </div>

    {{-- 3. جدول التوصيات الغذائية العلاجية --}}
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
                <tbody id="nutritionBody"></tbody>
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

@if(!empty($canExit))
<div class="modal-backdrop" id="exitModal">
    <div class="modal-box">
        <form method="POST" action="{{ route('records.animals.exit', $animal) }}" enctype="multipart/form-data" id="exitForm">
            @csrf
            <div class="modal-header">
                <div class="modal-title">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fef2f2;color:#dc2626;display:flex;align-items:center;justify-content:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </div>
                    توثيق خروج حيوان من الحديقة
                </div>
                <button type="button" class="btn-close" onclick="closeModal('exitModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">تاريخ الخروج <span style="color:#ef4444">*</span></label>
                        <input type="date" name="exit_date" class="form-input" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">نوع الخروج <span style="color:#ef4444">*</span></label>
                        <select name="exit_type" class="form-select" required>
                            <option value="">اختر نوع الخروج...</option>
                            @foreach($exitTypes ?? [] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">الجهة المستلمة <span style="color:#ef4444">*</span></label>
                        <input type="text" name="recipient" class="form-input" required placeholder="مثال: حديقة حيوان بنغازي">
                    </div>
                    <div class="form-group full">
                        <label class="form-label">سبب الخروج <span style="color:#ef4444">*</span></label>
                        <textarea name="reason" class="form-textarea" required placeholder="اكتب سبب الخروج بوضوح..."></textarea>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">مرفق الخروج <span style="color:#ef4444">*</span></label>
                        <input type="file" name="attachment" class="form-input" accept=".pdf,image/*" required>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">ملاحظات <span style="color:#64748b">(اختياري)</span></label>
                        <textarea name="notes" class="form-textarea" placeholder="تفاصيل إضافية إن وجدت..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('exitModal')">إلغاء</button>
                <button type="submit" class="btn-submit-red">حفظ وتوثيق الخروج</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    const animalId = @json($animal->code);
    const animalDB = @json($animalProfiles ?? []);
    const portalBase = @json($portalBase ?? '/records');

    function emptyRow(colspan, message) {
        return `<tr><td colspan="${colspan}" style="text-align:center;color:#94a3b8;font-weight:700;padding:1.5rem;">${message}</td></tr>`;
    }

    function renderMedicalTable(tbodyId, rows, colspan, columns, emptyMessage) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;
        if (!rows || !rows.length) {
            tbody.innerHTML = emptyRow(colspan, emptyMessage);
            return;
        }
        tbody.innerHTML = rows.map(row => `
            <tr>${columns.map(col => {
                let value = row[col.key] ?? '—';
                if (col.ref) {
                    value = `<span class="ref-tag">${value}</span>`;
                }
                if (col.bold) {
                    value = `<span style="font-weight:700;">${value}</span>`;
                }
                if (col.muted) {
                    value = `<span style="color:#64748b;font-size:0.83rem;">${value}</span>`;
                }
                if (col.badge) {
                    value = `<span class="badge ${row.statusClass || 'badge-gray'}"><span class="dot"></span>${value}</span>`;
                }
                return `<td>${value}</td>`;
            }).join('')}</tr>`).join('');
    }

    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', function(e) { if (e.target === this) closeModal(this.id); });
    });

    function renderMedicalTab(medical) {
        medical = medical || { diagnoses: [], treatments: [], vaccinations: [], nutrition: [] };

        renderMedicalTable('diagnosesBody', medical.diagnoses, 5, [
            { key: 'date', muted: true },
            { key: 'caseType' },
            { key: 'diagnosis', bold: true },
            { key: 'vet' },
            { key: 'ref', ref: true },
        ], 'لا توجد تشخيصات مسجّلة لهذا الحيوان');

        renderMedicalTable('treatmentsBody', medical.treatments, 5, [
            { key: 'date', muted: true },
            { key: 'treatment', bold: true },
            { key: 'vet' },
            { key: 'linkedDiagnosis' },
            { key: 'ref', ref: true },
        ], 'لا توجد علاجات أو إجراءات مسجّلة');

        renderMedicalTable('nutritionBody', medical.nutrition, 5, [
            { key: 'startDate', muted: true },
            { key: 'recommendation', bold: true },
            { key: 'duration' },
            { key: 'status', badge: true },
            { key: 'ref', ref: true },
        ], 'لا توجد توصيات غذائية علاجية');
    }

    function renderStatusTable(title, columns, row) {
        return `
            <div class="status-block">
                <h3 class="section-title">${title}</h3>
                <div class="table-card">
                    <div style="overflow-x:auto;">
                        <table class="custom-table">
                            <thead><tr>${columns.map(c => `<th>${c.label}</th>`).join('')}</tr></thead>
                            <tbody><tr>${columns.map(c => `<td>${row[c.key] ?? '—'}</td>`).join('')}</tr></tbody>
                        </table>
                    </div>
                </div>
            </div>`;
    }

    function renderDecisionsTable(title, columns, rows, emptyMessage) {
        const body = (!rows || !rows.length)
            ? `<tr><td colspan="${columns.length}" style="text-align:center;color:#94a3b8;font-weight:700;padding:1.5rem;">${emptyMessage}</td></tr>`
            : rows.map(row => `<tr>${columns.map(c => `<td>${row[c.key] ?? '—'}</td>`).join('')}</tr>`).join('');

        return `
            <div class="status-block">
                <h3 class="section-title">${title}</h3>
                <div class="table-card">
                    <div style="overflow-x:auto;">
                        <table class="custom-table">
                            <thead><tr>${columns.map(c => `<th>${c.label}</th>`).join('')}</tr></thead>
                            <tbody>${body}</tbody>
                        </table>
                    </div>
                </div>
            </div>`;
    }

    function renderStatusCard(d) {
        const wrap = document.getElementById('statusCardWrap');
        wrap.innerHTML = '';

        if (d.state === 'dead' && d.mortality) {
            const m = d.mortality;
            const columns = [
                { label: 'رقم الحالة', key: 'caseNumber' },
                { label: 'تاريخ النفوق', key: 'deathDate' },
                { label: 'سبب النفوق', key: 'cause' },
                { label: 'المشرف المسجّل', key: 'supervisor' },
                { label: 'حالة الملف', key: 'caseStatus' },
                { label: 'هل تمت الإحالة للتشريح؟', key: 'autopsyReferral' },
                { label: 'سبب التشريح', key: 'autopsyReason' },
                { label: 'تاريخ التوثيق', key: 'docDate' },
                { label: 'المعتمد', key: 'reviewer' },
            ];
            if (m.autopsyReferral === 'نعم' && m.reportFile) {
                columns.push({ label: 'تقرير الصفة التشريحية', key: 'reportFile' });
            }
            const row = {
                ...m,
                reportFile: m.reportFile
                    ? '<span class="report-link" style="cursor:default;">موجود في تبويب المرفقات والتقارير</span>'
                    : '—',
            };
            wrap.innerHTML = renderStatusTable('بيانات النفوق', columns, row);
            if (m.notes && m.notes !== '—') {
                wrap.innerHTML += `<div class="status-block"><h3 class="section-title">ملاحظات النفوق</h3><div class="content-box">${m.notes}</div></div>`;
            }
            return;
        }

        if (d.state === 'stillborn' && d.stillborn) {
            const s = d.stillborn;
            wrap.innerHTML = renderStatusTable('بيانات نفوق المولود', [
                { label: 'رقم الحالة', key: 'caseNumber' },
                { label: 'تاريخ الولادة', key: 'birthDate' },
                { label: 'تاريخ النفوق', key: 'deathDate' },
                { label: 'سبب النفوق', key: 'cause' },
                { label: 'المشرف المسجّل', key: 'supervisor' },
                { label: 'هل تم التشريح؟', key: 'autopsy' },
                { label: 'تاريخ التوثيق', key: 'docDate' },
            ], s);
            if (s.notes && s.notes !== '—') {
                wrap.innerHTML += `<div class="status-block"><h3 class="section-title">ملاحظات</h3><div class="content-box">${s.notes}</div></div>`;
            }
            return;
        }

        if (d.state === 'slaughter' && d.slaughter) {
            const sl = d.slaughter;
            wrap.innerHTML = renderStatusTable('بيانات الذبح الاضطراري', [
                { label: 'رقم الحالة', key: 'caseNumber' },
                { label: 'تاريخ الدخول للمستشفى', key: 'admittedAt' },
                { label: 'تاريخ القرار', key: 'decisionDate' },
                { label: 'الشكوى الرئيسية', key: 'chiefComplaint' },
                { label: 'نتيجة القرار', key: 'closingOutcome' },
                { label: 'الطبيب المعالج', key: 'vet' },
                { label: 'رئيس القسم المعتمد', key: 'headVet' },
            ], sl);
            wrap.innerHTML += renderDecisionsTable('سجل القرارات والإجراءات الطبية', [
                { label: 'التاريخ', key: 'date' },
                { label: 'التشخيص', key: 'diagnosis' },
                { label: 'العلاج / الإجراء', key: 'treatment' },
                { label: 'الطبيب', key: 'vet' },
                { label: 'النتيجة', key: 'result' },
                { label: 'ملاحظة', key: 'note' },
            ], sl.decisions || [], 'لا توجد قرارات أو إجراءات مسجّلة لهذه الحالة');
            return;
        }

        if (d.state === 'exited' && d.exit) {
            const e = d.exit;
            wrap.innerHTML = renderStatusTable('بيانات الخروج', [
                { label: 'تاريخ الخروج', key: 'exitDate' },
                { label: 'نوع الخروج', key: 'exitType' },
                { label: 'الجهة المستلمة', key: 'recipient' },
                { label: 'سبب الخروج', key: 'reason' },
                { label: 'ملاحظات', key: 'notes' },
            ], {
                ...e,
                notes: e.notes !== '—' ? e.notes : '<span style="color:#94a3b8;font-style:italic;">إن وجدت</span>',
            });
        }
    }

    function buildAttachments(d) {
        const items = [];

        if (d.historyAttachment) {
            items.push({
                date: d.historyAttachment.date || d.regDate,
                type: 'مرفق تاريخ سابق',
                fileName: d.historyAttachment.fileName,
                url: d.historyAttachment.url || null,
            });
        }

        if (d.state === 'dead' && d.mortality && d.mortality.autopsyReferral === 'نعم' && d.mortality.reportFile) {
            items.push({
                date: d.mortality.docDate,
                type: 'تقرير صفة تشريحية',
                fileName: d.mortality.reportFile,
                url: d.mortality.reportUrl || null,
            });
        }

        if (d.state === 'dead' && d.mortality && d.mortality.attachmentFile) {
            items.push({
                date: d.mortality.deathDate,
                type: 'مرفق حالة نفوق',
                fileName: d.mortality.attachmentFile,
                url: d.mortality.attachmentUrl || null,
            });
        }

        if (d.state === 'exited' && d.exit && d.exit.exitFile) {
            items.push({
                date: d.exit.exitFile.date || d.exit.exitDate,
                type: 'مرفق خروج',
                fileName: d.exit.exitFile.fileName,
                url: d.exit.exitFile.url || null,
            });
        }

        return items.sort((a, b) => a.date.localeCompare(b.date));
    }

    function isImageAttachment(url, fileName) {
        return /\.(jpe?g|png|gif|webp|bmp)(\?|$)/i.test(url || fileName || '');
    }

    function renderAttachmentCell(item) {
        if (!item.url) {
            return `<span style="color:#94a3b8;">${item.fileName}</span>`;
        }

        if (isImageAttachment(item.url, item.fileName)) {
            return `<div class="attachment-preview">
                <a href="${item.url}" target="_blank" rel="noopener" title="${item.fileName}">
                    <img src="${item.url}" alt="${item.fileName}">
                </a>
                <a href="${item.url}" class="report-link" target="_blank" rel="noopener" title="${item.fileName}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    عرض بالحجم الكامل
                </a>
            </div>`;
        }

        return `<a href="${item.url}" class="report-link" target="_blank" rel="noopener" title="${item.fileName}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            عرض / تحميل
        </a>`;
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
                <td>${renderAttachmentCell(item)}</td>
            </tr>`).join('');
    }

    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tabId).classList.add('active');
        btn.classList.add('active');
    }

    function renderProfileAttachments(d) {
        const items = buildAttachments(d);
        const section = document.getElementById('profileAttachmentsSection');
        const grid = document.getElementById('profileAttachmentsGrid');
        if (!section || !grid) return;

        if (!items.length) {
            section.style.display = 'none';
            grid.innerHTML = '';
            return;
        }

        section.style.display = '';
        grid.innerHTML = items.map(item => `
            <div class="attachment-card">
                <div class="attachment-card-type">${item.type}</div>
                <div class="attachment-card-date">${item.date}</div>
                <div>${renderAttachmentCell(item)}</div>
            </div>`).join('');
    }

    function renderReproSection(repro) {
        const el = document.getElementById('reproSection');
        if (!repro || !repro.length) { el.innerHTML = ''; return; }
        el.innerHTML = `
            <div class="status-block">
                <h3 class="section-title">التاريخ التناسلي</h3>
                ${repro.map(r => `
                    <div class="repro-card">
                        <div class="q-row"><span class="q-value"><a href="${portalBase}/animals/${r.code}" class="animal-id" style="text-decoration:none;">${r.id}</a></span><span class="q-label">رقم المولود</span></div>
                        <div class="q-row"><span class="q-value">${r.date}</span><span class="q-label">تاريخ الولادة</span></div>
                        <div class="q-row"><span class="q-value">${r.type}</span><span class="q-label">النوع</span></div>
                        <div class="q-row"><span class="q-value">${r.gender}</span><span class="q-label">الجنس</span></div>
                        <div class="q-row"><span class="q-value">${r.mark}</span><span class="q-label">علامة التمييز</span></div>
                        <div class="q-row"><span class="q-value"><span class="badge ${r.statusClass}"><span class="dot"></span>${r.status}</span></span><span class="q-label">حالة المولود</span></div>
                    </div>`).join('')}
            </div>`;
    }

    const stateBadges = {
        active:          '<span class="badge badge-active"><span class="dot"></span>داخل الحديقة</span>',
        quarantine:      '<span class="badge badge-quarantine"><span class="dot"></span>تحت الحجر الصحي</span>',
        pending_receipt: '<span class="badge badge-pending-receipt"><span class="dot"></span>بانتظار الاستلام</span>',
        dead:            '<span class="badge badge-dead"><span class="dot"></span>نافق</span>',
        stillborn:       '<span class="badge badge-stillborn"><span class="dot"></span>مولود نافق</span>',
        slaughter:       '<span class="badge badge-slaughter"><span class="dot"></span>ذبح اضطراري</span>',
        exited:          '<span class="badge badge-exited"><span class="dot"></span>خارج من الحديقة</span>'
    };

    function loadAnimal() {
        const d = animalDB[animalId];
        if (!d) return;
        const titleName = d.name !== '—' ? d.name + ' — ' + d.type : d.type;

        document.getElementById('breadAnimal').textContent = 'ملف ' + d.displayId;
        document.getElementById('topAnimalId').textContent = d.displayId;
        document.getElementById('pageSubtitle').textContent = titleName;
        document.getElementById('headerBadge').innerHTML = stateBadges[d.state] || '<span class="badge badge-gray"><span class="dot"></span>—</span>';

        document.getElementById('basicPhoto').textContent = d.emoji;
        if (d.photoUrl) {
            document.getElementById('basicPhoto').innerHTML = `<img src="${d.photoUrl}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">`;
        }

        document.getElementById('fAnimalId').textContent = d.displayId;
        document.getElementById('fAnimalName').textContent = d.name;
        if (d.name === '—') {
            document.getElementById('fAnimalName').classList.add('muted');
            document.getElementById('fAnimalName').style.color = '#94a3b8';
            document.getElementById('fAnimalName').style.fontStyle = 'italic';
        }
        document.getElementById('fAnimalType').textContent = d.type;
        document.getElementById('fGroup').textContent = d.group;
        document.getElementById('fGender').textContent = d.gender;
        document.getElementById('fAge').textContent = d.age;
        document.getElementById('fRegDate').textContent = d.regDate;
        document.getElementById('fMarks').textContent = d.marks;

        document.getElementById('quarantineNotice').style.display = d.source === 'quarantine' ? 'flex' : 'none';
        document.getElementById('manualNotice').style.display = d.manualEntry ? 'flex' : 'none';

        renderStatusCard(d);
        const showRepro = d.state === 'active' && d.gender === 'أنثى';
        renderReproSection(showRepro ? d.repro : null);
        renderMedicalTab(d.medical);
        renderProfileAttachments(d);
    }

    window.onload = loadAnimal;
</script>
@endsection
