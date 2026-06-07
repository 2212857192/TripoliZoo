@extends('vet.layout')
@section('title', 'إدارة الحجر الصحي | المستشفى البيطري')
@section('page_title', 'إدارة الحجر الصحي')

@section('styles')
<style>
    .page-title-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
    .page-title-wrap { display:flex; align-items:center; gap:12px; }
    .title-icon { width:40px; height:40px; border-radius:12px; background:#e6f4ea; color:#1a4a2e; display:flex; align-items:center; justify-content:center; }
    .page-title-wrap h2 { font-size:1.3rem; font-weight:800; color:#0f172a; margin:0; }
    .btn-refresh { display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:700; color:#334155; cursor:pointer; transition:all 0.2s; }
    .btn-refresh:hover { background:#f8fafc; }

    .btn-add { display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#16a34a; border:none; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; color:#fff; cursor:pointer; transition:all 0.2s; box-shadow:0 2px 4px rgba(22,163,74,0.2); }
    .btn-add:hover { background:#15803d; box-shadow:0 4px 8px rgba(22,163,74,0.3); }

    .tabs-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:0.8rem 1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; }
    .segmented-tabs { display:inline-flex; background:#f1f5f9; padding:5px; border-radius:10px; gap:4px; }
    .seg-tab { background:transparent; border:none; padding:9px 24px; border-radius:7px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; color:#64748b; cursor:pointer; transition:all 0.2s; }
    .seg-tab:hover { color:#1a4a2e; }
    .seg-tab.active { background:#fff; color:#1a4a2e; box-shadow:0 2px 4px rgba(0,0,0,0.07); }
    .tab-content { display:none; }
    .tab-content.active { display:block; animation:fadeIn 0.25s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:translateY(0); } }

    .table-card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:2rem; }
    .table-card-header { padding:1.1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f1f5f9; }
    .table-card-title { font-size:1rem; font-weight:800; color:#0f172a; }

    .custom-table { width:100%; border-collapse:collapse; text-align:right; }
    .custom-table thead th { background:#F8FAFC; color:#64748b; font-size:0.78rem; font-weight:800; padding:13px 18px; border-bottom:1px solid #e2e8f0; }
    .custom-table tbody tr { transition:background 0.15s; }
    .custom-table tbody tr:hover { background:#fafbfc; }
    .custom-table tbody td { padding:15px 18px; border-bottom:1px solid #f1f5f9; font-size:0.9rem; font-weight:600; color:#1e293b; vertical-align:middle; }
    .custom-table tbody tr:last-child td { border-bottom:none; }

    .badge { padding:5px 11px; border-radius:999px; font-size:0.73rem; font-weight:700; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; }
    .badge .dot { width:6px; height:6px; border-radius:50%; }
    .badge-followup   { background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; }
    .badge-followup .dot { background:#3b82f6; }
    .badge-cleared    { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-cleared .dot { background:#22c55e; }
    .badge-failed     { background:#fef2f2; color:#e11d48; border:1px solid #fecdd3; }
    .badge-failed .dot { background:#ef4444; }

    .case-id { font-family:'Courier New',monospace; font-size:0.74rem; padding:3px 8px; border-radius:6px; font-weight:700; display:inline-block; }
    .case-id-open   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .case-id-closed { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }

    .btn-tbl { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:9px; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; text-decoration:none; transition:all 0.2s; cursor:pointer; box-shadow:0 1px 2px rgba(0,0,0,0.05); }
    .btn-tbl:hover { transform:translateY(-1px); box-shadow:0 3px 8px rgba(0,0,0,0.1); background:#e2e8f0; border-color:#cbd5e1; color:#0f172a; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
    .btn-tbl.edit:hover { color: #E8651A; background: #FFEDD5; border-color: #FED7AA; }
    .btn-tbl.end:hover { color: #DC2626; background: #FEE2E2; border-color: #FECACA; }

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
    
    .detail-section { margin-bottom:1.4rem; }
    .detail-section h4 { display:flex; align-items:center; gap:8px; font-size:0.9rem; font-weight:800; color:#0f172a; margin-bottom:0.9rem; padding-bottom:8px; border-bottom:2px solid #f1f5f9; }
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .detail-item { display:flex; flex-direction:column; gap:4px; }
    .detail-item label { font-size:0.73rem; color:#64748b; font-weight:700; }
    .detail-item span { font-size:0.86rem; color:#0f172a; font-weight:800; }
    .vet-note { background:#f8fafc; border-right:3px solid #3b82f6; padding:12px 14px; border-radius:8px 0 0 8px; margin-bottom:10px; }
    .note-date { font-size:0.73rem; color:#64748b; font-weight:700; margin-bottom:4px; }
    .note-text { font-size:0.83rem; color:#334155; font-weight:600; line-height:1.5; }
    
    .btn-action-release { padding:9px 18px; background:#16a34a; color:#fff; border:none; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-action-release:hover { background:#15803d; }
    .btn-action-close { padding:9px 18px; background:#e11d48; color:#fff; border:none; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-action-close:hover { background:#be123c; }
    .btn-cancel { padding:9px 18px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-cancel:hover { background:#e2e8f0; }

    /* ═══ FORM ═══ */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: span 2; }
    .form-group label { font-size: 0.8rem; font-weight: 800; color: #374151; }
    .form-group label span.req { color: #ef4444; }
    .form-input, .form-select, .form-textarea { padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #0f172a; background: #fafbff; transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #2d7a47; box-shadow: 0 0 0 3px rgba(45,122,71,0.1); background: #fff; }
    .form-textarea { resize: vertical; min-height: 80px; }
    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(22,163,74,0.3); }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(22,163,74,0.35); }
</style>
@endsection

@section('content')

{{-- ═══ TABS CARD ═══ --}}
<div class="tabs-card">
    <div class="segmented-tabs">
        <button class="seg-tab active" onclick="switchTab(event,'tab-followup')">قيد المتابعة</button>
        <button class="seg-tab" onclick="switchTab(event,'tab-cleared')">تم الإفراج الصحي</button>
        <button class="seg-tab" onclick="switchTab(event,'tab-failed')">لم تجتز الحجر</button>
    </div>
    
    <button class="btn-add" onclick="openAddModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        إضافة للحجر
    </button>
</div>

{{-- ═══ FILTER CARD ═══ --}}
<div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.2rem; margin-bottom: 1.5rem; display: flex; justify-content: flex-start; align-items: center;">
    <div style="display: flex; gap: 15px; width: 100%; max-width: 800px;">
        <div style="flex: 1; position: relative;">
            <svg style="position: absolute; right: 12px; top: 11px; color: #94a3b8;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="البحث بنوع الحيوان..." style="width: 100%; padding: 10px 35px 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 600; outline: none; color: #0f172a;">
        </div>
    </div>
</div>

{{-- ════ TAB 1: قيد المتابعة ════ --}}
<div id="tab-followup" class="tab-content active">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">الحيوانات الخاضعة للحجر الصحي وتخضع للتقييم والمراقبة</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الحجر</th>
                        <th>الطبيب المسؤول</th>
                        <th>الحيوان</th>
                        <th>المجموعة</th>
                        <th>تاريخ الدخول</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="case-id case-id-open">QR-2026-015</span></td>
                        <td>د. أسامة الورفلي</td>
                        <td>فهد آسيوي</td>
                        <td>القطط الكبرى</td>
                        <td>2026-06-01</td>
                        <td><span class="badge badge-followup"><span class="dot"></span>قيد المتابعة</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="javascript:void(0)" onclick="openModal('QR-2026-015', 'followup')" class="btn-tbl view" title="عرض التفاصيل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="javascript:void(0)" onclick="openEditModal('QR-2026-015')" class="btn-tbl edit" title="تعديل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="case-id case-id-open">QR-2026-016</span></td>
                        <td>د. خالد العربي</td>
                        <td>قرد البابون</td>
                        <td>القرود</td>
                        <td>2026-05-28</td>
                        <td><span class="badge badge-followup"><span class="dot"></span>قيد المتابعة</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="javascript:void(0)" onclick="openModal('QR-2026-016', 'followup')" class="btn-tbl view" title="عرض التفاصيل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="javascript:void(0)" onclick="openEditModal('QR-2026-016')" class="btn-tbl edit" title="تعديل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ════ TAB 2: تم الإفراج الصحي ════ --}}
<div id="tab-cleared" class="tab-content">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">السجل التاريخي للحيوانات التي اجتازت فترة الحجر بنجاح</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الحجر</th>
                        <th>الطبيب المسؤول</th>
                        <th>الحيوان</th>
                        <th>المجموعة</th>
                        <th>تاريخ الدخول</th>
                        <th>تاريخ الإفراج</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="case-id case-id-closed">QR-2026-010</span></td>
                        <td>د. فاطمة الزهراء</td>
                        <td>زرافة</td>
                        <td>العناقيد الكبرى</td>
                        <td>2026-05-10</td>
                        <td>2026-05-24</td>
                        <td><span class="badge badge-cleared"><span class="dot"></span>تم الإفراج الصحي</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="javascript:void(0)" onclick="openModal('QR-2026-010', 'cleared')" class="btn-tbl view" title="عرض التفاصيل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="javascript:void(0)" onclick="openEditModal('QR-2026-010')" class="btn-tbl edit" title="تعديل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="case-id case-id-closed">QR-2026-009</span></td>
                        <td>د. أسامة الورفلي</td>
                        <td>نعامة</td>
                        <td>الطيور</td>
                        <td>2026-05-01</td>
                        <td>2026-05-15</td>
                        <td><span class="badge badge-cleared"><span class="dot"></span>تم الإفراج الصحي</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="javascript:void(0)" onclick="openModal('QR-2026-009', 'cleared')" class="btn-tbl view" title="عرض التفاصيل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="javascript:void(0)" onclick="openEditModal('QR-2026-009')" class="btn-tbl edit" title="تعديل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ════ TAB 3: لم تجتز الحجر ════ --}}
<div id="tab-failed" class="tab-content">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">السجل التاريخي للحيوانات التي لم تجتز الحجر الصحي (نقلت للمستشفى أو غيره)</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الحجر</th>
                        <th>الطبيب المسؤول</th>
                        <th>الحيوان</th>
                        <th>المجموعة</th>
                        <th>تاريخ الدخول</th>
                        <th>تاريخ الإغلاق</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="case-id case-id-closed">QR-2026-012</span></td>
                        <td>د. خالد العربي</td>
                        <td>أسد إفريقي</td>
                        <td>القطط الكبرى</td>
                        <td>2026-05-12</td>
                        <td>2026-05-18</td>
                        <td><span class="badge badge-failed"><span class="dot"></span>لم تجتز الحجر (نقل للمستشفى)</span></td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="javascript:void(0)" onclick="openModal('QR-2026-012', 'failed')" class="btn-tbl view" title="عرض التفاصيل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="javascript:void(0)" onclick="openEditModal('QR-2026-012')" class="btn-tbl edit" title="تعديل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
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
    <div class="modal-box" style="max-width: 800px; background: #f8fafc;">
        <div class="modal-header" style="background: transparent; border-bottom: none; display: flex; justify-content: center; position: relative; padding-top: 2rem;">
            <h3 style="font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0;">تفاصيل حيوان في الحجر — <span id="modalCaseId">QR-2025-001</span></h3>
            <button class="modal-close" onclick="closeModal()" style="position: absolute; left: 1.5rem; top: 1.5rem;">✕</button>
        </div>
        <div class="modal-body" style="padding: 1.5rem 2rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                
                <!-- Right Column (Animal Data) -->
                <div style="background: #fff; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center;">بيانات الحيوان</h4>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">الرقم</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_id">QR-2025-001</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">نوع الحيوان</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;">أسد ماراث أفريقي</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">اسم الحيوان</span>
                        <span style="color: #475569; font-size: 0.9rem; font-weight: 700; font-style: italic;">لم يُسمَّ بعد</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">الجنس</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;">ذكر</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">المجموعة الحيوانية</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;">القططية</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">المصدر</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;">استيراد من جنوب أفريقيا</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">الطبيب المسؤول</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;">د. أسامة الورفلي</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">تاريخ دخول الحجر</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;">2025-05-01</span>
                    </div>

                    
                    <div style="margin-top: 1rem;">
                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 700; display: block; text-align: center; margin-bottom: 0.5rem;">ملاحظات أولية</span>
                        <div style="background: #f8fafc; padding: 10px; border-radius: 8px; text-align: center; font-size: 0.9rem; font-weight: 700; color: #334155;">
                            حيوان شاب سليم ظاهرياً
                        </div>
                    </div>
                </div>

                <!-- Left Column -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Doses Card -->
                    <div style="background: #fff; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                        <h4 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center;">الجرعات الوقائية المسجلة</h4>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 0.9rem; color: #475569; font-weight: 700;">
                                <span>جرعة إنفلونزا - 2025-05-02</span>
                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 0.9rem; color: #475569; font-weight: 700;">
                                <span>جرعة سارس - 2025-05-05</span>
                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 0.9rem; color: #475569; font-weight: 700;">
                                <span>مضادات طفيليات - 2025-05-08</span>
                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Health Notes Card -->
                    <div style="background: #fff; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02); flex-grow: 1;">
                        <h4 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center;">الملاحظات الصحية</h4>
                        <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                            <div style="background: #f8fafc; padding: 12px 14px; border-radius: 8px; font-size: 0.85rem; color: #334155; font-weight: 700; text-align: center; border: 1px solid #f1f5f9;">
                                2025-05-03: الحيوان نشيط ويأكل بشكل طبيعي
                            </div>
                            <div style="background: #f8fafc; padding: 12px 14px; border-radius: 8px; font-size: 0.85rem; color: #334155; font-weight: 700; text-align: center; border: 1px solid #f1f5f9;">
                                2025-05-10: لا أعراض مرضية، صحة جيدة
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer" id="modalFooterActions" style="background: transparent; border-top: none; padding: 0 2rem 1.5rem 2rem;">
            <!-- سيتم حقن الأزرار هنا عبر الجافاسكريبت حسب حالة التبويب -->
            <button class="btn-cancel" onclick="closeModal()">إغلاق</button>
        </div>
    </div>
</div>

{{-- ═══ ADD MODAL ═══ --}}
<div class="modal-backdrop" id="addModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>📋 إضافة حيوان للحجر الصحي</h3>
            <button class="modal-close" onclick="closeAddModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>نوع الحيوان <span class="req">*</span></label>
                    <input type="text" class="form-input" placeholder="مثال: أسد إفريقي، نسر ذهبي...">
                </div>
                <div class="form-group">
                    <label>الجنس <span class="req">*</span></label>
                    <select class="form-select">
                        <option value="" disabled selected>اختر الجنس...</option>
                        <option>ذكر</option>
                        <option>أنثى</option>
                        <option>غير محدد</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>العمر</label>
                    <input type="text" class="form-input" placeholder="مثال: 4 سنوات، 6 أشهر...">
                </div>
                <div class="form-group">
                    <label>مصدر الحيوان / جهة الإحضار <span class="req">*</span></label>
                    <input type="text" class="form-input" placeholder="مثال: مركز الحياة البرية...">
                </div>
                <div class="form-group">
                    <label>تاريخ الدخول للحجر <span class="req">*</span></label>
                    <input type="date" class="form-input" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label>صورة الحيوان (اختياري)</label>
                    <input type="file" class="form-input" accept="image/*" style="padding: 6px;">
                </div>
                <div class="form-group full">
                    <label>ملاحظات أولية (اختياري)</label>
                    <textarea class="form-textarea" placeholder="أدخل أي ملاحظات حول صحة الحيوان عند دخول الحجر..."></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeAddModal()">إلغاء</button>
            <button class="btn-submit" onclick="closeAddModal()">تأكيد الإضافة</button>
        </div>
    </div>
</div>

{{-- ═══ EDIT MODAL ═══ --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>✏️ تعديل بيانات الحجر الصحي — <span id="editModalCaseId"></span></h3>
            <button class="modal-close" onclick="closeEditModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>نوع الحيوان <span class="req">*</span></label>
                    <input type="text" class="form-input" value="فهد آسيوي">
                </div>
                <div class="form-group">
                    <label>الجنس <span class="req">*</span></label>
                    <select class="form-select">
                        <option value="ذكر" selected>ذكر</option>
                        <option value="أنثى">أنثى</option>
                        <option value="غير محدد">غير محدد</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>العمر</label>
                    <input type="text" class="form-input" value="3 سنوات">
                </div>
                <div class="form-group">
                    <label>مصدر الحيوان / جهة الإحضار <span class="req">*</span></label>
                    <input type="text" class="form-input" value="مركز الحياة البرية">
                </div>
                <div class="form-group">
                    <label>تاريخ الدخول للحجر <span class="req">*</span></label>
                    <input type="date" class="form-input" value="2026-06-01">
                </div>
                <div class="form-group">
                    <label>تحديث صورة الحيوان</label>
                    <input type="file" class="form-input" accept="image/*" style="padding: 6px;">
                </div>
                <div class="form-group full">
                    <label>تحديث الملاحظات الأولية</label>
                    <textarea class="form-textarea">الحيوان بحالة جيدة عموماً، يحتاج مراقبة في أول أسبوع.</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeEditModal()">إلغاء</button>
            <button class="btn-submit" onclick="closeEditModal()" style="background: #E8651A; box-shadow: 0 4px 12px rgba(232,101,26,0.2);">حفظ التعديلات</button>
        </div>
    </div>
</div>

{{-- ═══ END CASE MODAL ═══ --}}
<div class="modal-backdrop" id="endModal">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-header">
            <h3>🚫 إنهاء حالة الحجر الصحي — <span id="endModalCaseId"></span></h3>
            <button class="modal-close" onclick="closeEndModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-grid">
                <div class="form-group full">
                    <label>سبب الإنهاء <span class="req">*</span></label>
                    <select class="form-select">
                        <option value="" disabled selected>اختر سبب الإنهاء...</option>
                        <option>نفوق داخل الحجر</option>
                        <option>إرجاع الحيوان</option>
                        <option>عدم التأقلم</option>
                        <option>إدخال بالخطأ</option>
                        <option>سبب آخر</option>
                    </select>
                </div>
                <div class="form-group full">
                    <label>ملاحظات إضافية وتوثيق</label>
                    <textarea class="form-textarea" placeholder="أدخل تفاصيل توثيق سبب إنهاء حالة الحجر..."></textarea>
                </div>
            </div>
            <div style="margin-top: 15px; padding: 10px; background: #FEF2F2; border-left: 3px solid #EF4444; border-radius: 4px; font-size: 0.8rem; color: #991B1B;">
                <strong>ملاحظة هامة:</strong> بإنهاء هذه الحالة، سينتقل الحيوان لقائمة الحالات المنتهية ولن يتم تخصيص رقم حيوان رسمي له، وسيبقى كمرجع إداري فقط.
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeEndModal()">إلغاء</button>
            <button class="btn-submit" onclick="closeEndModal()" style="background: #E11D48; box-shadow: 0 4px 12px rgba(225,29,72,0.2);">تأكيد الإنهاء</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function switchTab(evt, tabId) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.seg-tab').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}

function openModal(caseId, tabType) {
    document.getElementById('modalCaseId').textContent = caseId;
    document.getElementById('mdl_id').textContent = caseId;
    
    const footer = document.getElementById('modalFooterActions');
    
    if(tabType === 'followup') {
        footer.innerHTML = `
            <button class="btn-cancel" onclick="closeModal()">إغلاق</button>
            <button class="btn-action-close" onclick="closeModal(); openEndModal('${caseId}')" style="background:#E11D48; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:700; cursor:pointer;">إنهاء الحالة</button>
            <button class="btn-action-release" onclick="alert('إصدار قرار إفراج صحي: سيتم نقل الحيوان للمجموعة بنجاح.'); closeModal();" style="background:#16a34a; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:700; cursor:pointer;">اصدار قرار الافراج الصحي</button>
        `;
    } else {
        footer.innerHTML = `
            <button class="btn-cancel" onclick="closeModal()">إغلاق</button>
        `;
    }
    
    document.getElementById('detailModal').classList.add('open');
}

function closeModal() {
    document.getElementById('detailModal').classList.remove('open');
}

function openAddModal() {
    document.getElementById('addModal').classList.add('open');
}

function closeAddModal() {
    document.getElementById('addModal').classList.remove('open');
}

function openEditModal(caseId) {
    document.getElementById('editModalCaseId').textContent = caseId;
    document.getElementById('editModal').classList.add('open');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}

function openEndModal(caseId) {
    document.getElementById('endModalCaseId').textContent = caseId;
    document.getElementById('endModal').classList.add('open');
}

function closeEndModal() {
    document.getElementById('endModal').classList.remove('open');
}

document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
document.getElementById('endModal').addEventListener('click', function(e) {
    if (e.target === this) closeEndModal();
});
</script>
@endsection
