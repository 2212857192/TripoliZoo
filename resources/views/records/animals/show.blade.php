@extends($__layout ?? 'records.layout')
@section('title', 'ملف الحيوان | السجلات والتوثيق')
@section('page_title', 'ملف الحيوان الرسمي')

@section('styles')
<style>
    /* ═══ PAGE HEADER (vet hospital style) ═══ */
    .page-header {
        background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 40%, #43A047 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(46, 125, 50, 0.35);
    }
    .page-header::before {
        content: '';
        position: absolute;
        top: -80px;
        left: -80px;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .header-left { position: relative; z-index: 2; }
    .header-left h2 { font-size: 1.45rem; font-weight: 900; color: #fff; margin: 0 0 5px; }
    .header-left p  { font-size: 0.85rem; color: rgba(255,255,255,0.65); font-weight: 600; margin: 0; }
    .header-right { display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap; position: relative; z-index: 2; }

    .case-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 800;
        color: #fff;
    }
    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px;
        background: rgba(255,255,255,0.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 12px;
        font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800;
        cursor: pointer; transition: all 0.25s;
        text-decoration: none;
    }
    .btn-back:hover { background: rgba(255,255,255,0.25); }
    .btn-header {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 16px;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif; font-size: 0.82rem; font-weight: 800;
        cursor: pointer; transition: all 0.2s; border: 1px solid rgba(255,255,255,0.35);
        background: rgba(255,255,255,0.15); color: #fff;
    }
    .btn-header:hover { background: rgba(255,255,255,0.25); }
    .btn-header.danger {
        background: rgba(254,226,226,0.2);
        border-color: rgba(254,202,202,0.5);
        color: #fff;
    }
    .btn-header.danger:hover { background: rgba(254,226,226,0.35); }

    /* ═══ TABS ═══ */
    .tabs-container {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    .tabs-header {
        display: flex;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(to left, #fafbff, #fff);
        overflow-x: auto;
    }
    .tab-btn {
        flex: 1;
        min-width: max-content;
        padding: 1rem 1.2rem;
        background: none;
        border: none;
        font-family: 'Cairo', sans-serif;
        font-size: 0.88rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 3px solid transparent;
        white-space: nowrap;
    }
    .tab-btn:hover { color: #2E7D32; background: #f8fafc; }
    .tab-btn.active { color: #2E7D32; border-bottom-color: #2E7D32; background: #f8fafc; }
    .tab-content { display: none; padding: 2rem; }
    .tab-content.active { display: block; }

    /* ═══ SUMMARY ═══ */
    .summary-card {
        background: #fafbff;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
    }
    .summary-card:last-child { margin-bottom: 0; }
    .summary-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e8edf5;
    }
    .animal-avatar {
        width: 80px; height: 80px;
        border-radius: 16px;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
        border: 3px solid #2E7D32;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; flex-shrink: 0;
    }
    .animal-info h3 { font-size: 1.2rem; font-weight: 900; color: #0f172a; margin: 0 0 0.3rem; }
    .animal-info p  { font-size: 0.85rem; color: #64748b; font-weight: 600; margin: 0; }
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .info-item {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 10px;
        padding: 1rem;
    }
    .info-item.span-2 { grid-column: span 2; }
    .info-item.span-3 { grid-column: span 3; }
    .info-item-label { font-size: 0.72rem; font-weight: 800; color: #64748b; margin-bottom: 4px; }
    .info-item-value { font-size: 0.85rem; font-weight: 700; color: #0f172a; line-height: 1.5; }
    .info-item-value.mono { font-family: 'Courier New', monospace; }
    .photo-display {
        width: 100%; max-width: 200px; height: 160px;
        border-radius: 12px; background: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 4rem; border: 2px solid #C8E6C9;
    }
    .section-title {
        font-size: 0.95rem; font-weight: 800; color: #2E7D32;
        margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;
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

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1px;
        background: #f1f5f9;
    }

    .info-grid.col-3 { grid-template-columns: 1fr 1fr 1fr; }

    .info-cell { background: #fff; padding: 14px 20px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell.span-3 { grid-column: span 3; }
    .info-cell-label { font-size: 0.72rem; color: #94a3b8; font-weight: 700; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-cell-value { font-size: 0.92rem; color: #0f172a; font-weight: 800; }
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
</style>
@endsection

@section('content')

{{-- ═══ PAGE HEADER ═══ --}}
<div class="page-header">
    <div class="header-left">
        <h2>🐾 #ANM-0012 — ملف الحيوان</h2>
        <p>أسد أفريقي (سيمبا) — القططية — ذكر — 8 سنوات</p>
    </div>
    <div class="header-right">
        <div class="case-status-badge">
            <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;"></span>
            داخل الحديقة
        </div>
        <button type="button" class="btn-header" onclick="openDialog('editDialog')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            تعديل
        </button>
        <button type="button" class="btn-header" onclick="showToast('📄 جاري تصدير ملف الحيوان PDF...')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            PDF
        </button>
        <button type="button" class="btn-header danger" onclick="openModal('exitModal')">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            توثيق خروج
        </button>
        <a href="/records/animals" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            العودة للقائمة
        </a>
    </div>
</div>

{{-- ═══ TABS ═══ --}}
<div class="tabs-container">
    <div class="tabs-header">
        <button type="button" class="tab-btn active" onclick="switchTab('basic', this)">📋 البيانات الأساسية</button>
        <button type="button" class="tab-btn" onclick="switchTab('origin', this)">🌍 الأصل والتسجيل</button>
        <button type="button" class="tab-btn" onclick="switchTab('medical', this)">🏥 التاريخ الطبي</button>
        <button type="button" class="tab-btn" onclick="switchTab('repro', this)">🍼 التاريخ التناسلي</button>
        <button type="button" class="tab-btn" onclick="switchTab('records', this)">📁 السجلات الرسمية</button>
        <button type="button" class="tab-btn" onclick="switchTab('attachments', this)">📎 المرفقات</button>
    </div>

{{-- TAB 1: البيانات الأساسية --}}
<div class="tab-content active" id="tab-basic">

    <div class="summary-card">
        <div class="summary-header">
            <div class="animal-avatar">🦁</div>
            <div class="animal-info">
                <h3>سيمبا — أسد أفريقي</h3>
                <p>#ANM-0012 — القططية — مسجّل منذ 2018-02-14</p>
            </div>
        </div>
        <div class="summary-grid">
            <div class="info-item">
                <div class="info-item-label">رقم الحيوان</div>
                <div class="info-item-value mono">#ANM-0012</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">اسم الحيوان</div>
                <div class="info-item-value">سيمبا</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">النوع</div>
                <div class="info-item-value">أسد أفريقي</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">المجموعة</div>
                <div class="info-item-value">القططية</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">الجنس</div>
                <div class="info-item-value">
                    <span style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;padding:3px 9px;border-radius:6px;font-size:0.72rem;font-weight:700;">ذكر</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-item-label">العمر الحالي</div>
                <div class="info-item-value">8 سنوات و 3 أشهر</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">طريقة تحديد العمر</div>
                <div class="info-item-value">تاريخ ميلاد معروف</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">تاريخ الميلاد</div>
                <div class="info-item-value">2018-02-14</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">تاريخ التسجيل</div>
                <div class="info-item-value">2018-02-14</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">الحالة</div>
                <div class="info-item-value">
                    <span style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;padding:4px 10px;border-radius:18px;font-size:0.75rem;font-weight:800;">داخل الحديقة</span>
                </div>
            </div>
            <div class="info-item span-3">
                <div class="info-item-label">العلامات المميزة</div>
                <div class="info-item-value">وشم على الأذن اليسرى برقم 012، خدش قديم على الكتف الأيمن</div>
            </div>
        </div>
    </div>

    <div class="summary-card">
        <div class="section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            صورة الحيوان
        </div>
        <div class="photo-display">🦁</div>
        <p style="font-size:0.78rem; color:#64748b; font-weight:600; margin:8px 0 0;">simba_photo.jpg</p>
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
                        <th>الجرعة</th>
                        <th>المدة</th>
                        <th>الطبيب</th>
                        <th>مرتبط بتشخيص</th>
                        <th>المرجع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color:#64748b; font-size:0.83rem;">2025-11-10</td>
                        <td style="font-weight:700;">مضاد حيوي أموكسيسيلين</td>
                        <td>500 ملجم</td>
                        <td>7 أيام</td>
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
                        <th>نوع الجرعة</th>
                        <th>اسم الجرعة</th>
                        <th>ملاحظات</th>
                        <th>الطبيب</th>
                        <th>المرجع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color:#64748b; font-size:0.83rem;">2026-01-15</td>
                        <td>لقاح</td>
                        <td style="font-weight:700;">لقاح الكُزاز السنوي</td>
                        <td>جرعة منشطة</td>
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

    {{-- 5. التقارير الطبية الرسمية --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div style="width:30px;height:30px;border-radius:8px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                التقارير الطبية الرسمية
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>نوع التقرير</th>
                        <th>الوصف</th>
                        <th>الملف</th>
                        <th>المرجع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color:#64748b; font-size:0.83rem;">2025-11-10</td>
                        <td>تقرير طبي</td>
                        <td style="font-weight:700;">تقرير تفصيلي عن حالة التهاب اللثة</td>
                        <td>
                            <a href="#" class="btn-download" style="margin:0;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                عرض / تحميل
                            </a>
                        </td>
                        <td><span class="ref-tag">حالة طبية ميدانية رقم 24</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- TAB 4: التاريخ التناسلي --}}
{{-- ══════════════════════════════════════════ --}}
<div class="tab-content" id="tab-repro">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div style="width:30px;height:30px;border-radius:8px;background:#f5f3ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                سجل التكاثر والولادات
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم المولود</th>
                        <th>تاريخ الولادة</th>
                        <th>النوع</th>
                        <th>الجنس</th>
                        <th>علامة التمييز</th>
                        <th>حالة المولود</th>
                        <th>السجل المرتبط</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="animal-id">B-014</span></td>
                        <td style="color:#64748b; font-size:0.83rem;">2026-06-01</td>
                        <td>أسد أفريقي</td>
                        <td>أنثى</td>
                        <td>بقعة بيضاء</td>
                        <td><span class="badge badge-gray"><span class="dot"></span>قيد المتابعة</span></td>
                        <td><span class="ref-tag">سجل الولادات</span></td>
                    </tr>
                    <tr>
                        <td><span class="animal-id">B-015</span></td>
                        <td style="color:#64748b; font-size:0.83rem;">2026-06-01</td>
                        <td>أسد أفريقي</td>
                        <td>ذكر</td>
                        <td>-</td>
                        <td><span class="badge badge-green"><span class="dot"></span>مكتمل</span></td>
                        <td><span class="ref-tag">سجل الولادات</span></td>
                    </tr>
                    <tr>
                        <td><span class="animal-id">B-022</span></td>
                        <td style="color:#64748b; font-size:0.83rem;">2027-02-15</td>
                        <td>أسد أفريقي</td>
                        <td>أنثى</td>
                        <td>-</td>
                        <td><span class="badge badge-red"><span class="dot"></span>نافق</span></td>
                        <td><span class="ref-tag">سجل الولادات النافقة</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- TAB 5: السجلات الرسمية المرتبطة --}}
{{-- ══════════════════════════════════════════ --}}
<div class="tab-content" id="tab-records">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div style="width:30px;height:30px;border-radius:8px;background:#f8fafc;color:#475569;display:flex;align-items:center;justify-content:center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                السجلات الرسمية المرتبطة بالحيوان
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>السجل</th>
                        <th>التاريخ</th>
                        <th>نوع الإدخال</th>
                        <th>المرجع</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span style="font-weight:700;">سجل الحيوانات الداخلة</span></td>
                        <td style="color:#64748b; font-size:0.83rem;">2018-02-14</td>
                        <td>إدخال يدوي بواسطة مسؤول السجلات</td>
                        <td><span class="ref-tag">ملف الحيوان القديم</span></td>
                    </tr>
                    <tr>
                        <td><span style="font-weight:700;">سجل الولادات</span></td>
                        <td style="color:#64748b; font-size:0.83rem;">2026-06-01</td>
                        <td>من مسار ولادة</td>
                        <td><span class="ref-tag">ولادة رقم 55</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- TAB 6: المرفقات والتقارير --}}
{{-- ══════════════════════════════════════════ --}}
<div class="tab-content" id="tab-attachments">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div style="width:30px;height:30px;border-radius:8px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
                الملفات والمرفقات الرسمية
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>اسم الملف / المرفق</th>
                        <th>نوع المرفق</th>
                        <th>التاريخ</th>
                        <th>الوصف</th>
                        <th>الملف</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight:700;">simba_photo.jpg</td>
                        <td>صورة</td>
                        <td style="color:#64748b; font-size:0.83rem;">2018-02-14</td>
                        <td>صورة الحيوان الرسمية</td>
                        <td>
                            <a href="#" class="btn-download" style="margin:0;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                عرض / تحميل
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">simba_history_2018.pdf</td>
                        <td>PDF</td>
                        <td style="color:#64748b; font-size:0.83rem;">2018-02-14</td>
                        <td>مرفق التاريخ السابق</td>
                        <td>
                            <a href="#" class="btn-download" style="margin:0;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                عرض / تحميل
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">medical_report_2025.pdf</td>
                        <td>تقرير طبي</td>
                        <td style="color:#64748b; font-size:0.83rem;">2025-11-10</td>
                        <td>تقرير طبي تفصيلي لالتهاب اللثة</td>
                        <td>
                            <a href="#" class="btn-download" style="margin:0;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                عرض / تحميل
                            </a>
                        </td>
                    </tr>
                </tbody>
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
    // ── Tabs ──
    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tabId).classList.add('active');
        btn.classList.add('active');
    }

    // ── Dialogs & Modals ──
    function openDialog(id) { document.getElementById(id).classList.add('open'); }
    function closeDialog(id) { document.getElementById(id).classList.remove('open'); }
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    
    document.querySelectorAll('.dialog-backdrop, .modal-backdrop').forEach(el => {
        el.addEventListener('click', function(e) { 
            if (e.target === this) {
                this.classList.remove('open');
            }
        });
    });

    // ── Toast ──
    function showToast(msg) {
        const t = document.getElementById('toastMsg');
        document.getElementById('toastText').innerText = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }
</script>
@endsection
