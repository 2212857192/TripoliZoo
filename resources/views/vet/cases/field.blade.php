@extends($__layout ?? 'vet.layout')
@section('title', 'الحالات الطبية الميدانية | المستشفى البيطري')
@section('page_title', 'الحالات الطبية الميدانية')

@section('styles')
<style>
    .page-title-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
    .page-title-wrap { display:flex; align-items:center; gap:12px; }
    .title-icon { width:40px; height:40px; border-radius:12px; background:#e6f4ea; color:#1a4a2e; display:flex; align-items:center; justify-content:center; }
    .page-title-wrap h2 { font-size:1.3rem; font-weight:800; color:#0f172a; margin:0; }
    .btn-refresh { display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:700; color:#334155; cursor:pointer; transition:all 0.2s; }
    .btn-refresh:hover { background:#f8fafc; }

    .tabs-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:0.8rem 1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; }
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
    .badge-improved   { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-improved .dot { background:#22c55e; }
    .badge-treated    { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-treated .dot { background:#22c55e; }
    .badge-no-followup { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }
    .badge-no-followup .dot { background:#94a3b8; }

    .case-id { font-family:'Courier New',monospace; font-size:0.74rem; padding:3px 8px; border-radius:6px; font-weight:700; display:inline-block; }
    .case-id-open   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .case-id-closed { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }

    .btn-tbl { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:9px; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; text-decoration:none; transition:all 0.2s; cursor:pointer; box-shadow:0 1px 2px rgba(0,0,0,0.05); }
    .btn-tbl:hover { transform:translateY(-1px); box-shadow:0 3px 8px rgba(0,0,0,0.1); background:#e2e8f0; border-color:#cbd5e1; color:#0f172a; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
    .btn-tbl.edit:hover { color: #E8651A; background: #FFEDD5; border-color: #FED7AA; }

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
    .btn-submit { padding:10px 24px; background:linear-gradient(135deg,#1a4a2e,#2d7a47); color:#fff; border:none; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.88rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-submit:hover { transform:translateY(-1px); }
    .btn-cancel { padding:10px 20px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; border-radius:10px; font-family:'Cairo',sans-serif; font-size:0.88rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-cancel:hover { background:#e2e8f0; }
</style>
@endsection

@section('content')

{{-- ═══ TABS CARD ═══ --}}
<div class="tabs-card">
    <div class="segmented-tabs">
        <button class="seg-tab active" onclick="switchTab(event,'tab-followup')">قيد المتابعة</button>
        <button class="seg-tab" onclick="switchTab(event,'tab-completed')">منتهية</button>
    </div>
</div>

{{-- ═══ FILTER CARD ═══ --}}
<div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.2rem; margin-bottom: 1.5rem; display: flex; justify-content: flex-start; align-items: center;">
    <div style="display: flex; gap: 15px; width: 100%;">
        <div style="flex: 2; position: relative;">
            <svg style="position: absolute; right: 12px; top: 11px; color: #94a3b8;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="البحث برقم الحيوان أو نوع الحيوان أو رقم الحالة..." style="width: 100%; padding: 10px 35px 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 600; outline: none; color: #0f172a;">
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
            <option>د. ريم الفصل</option>
            <option>د. أسامة الورفلي</option>
        </select>
        <select style="flex: 1; padding: 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; color: #475569; font-weight: 600; outline: none;">
            <option>جميع الحالات</option>
            <option>مفتوحة</option>
            <option>مغلقة</option>
        </select>
        <select style="flex: 1; padding: 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; color: #475569; font-weight: 600; outline: none;">
            <option>جميع التواريخ</option>
            <option>اليوم</option>
            <option>آخر 7 أيام</option>
            <option>آخر 30 يوم</option>
        </select>
    </div>
</div>

{{-- ════ TAB 1: قيد المتابعة ════ --}}
<div id="tab-followup" class="tab-content active">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">الحالات التي فتحها الطبيب وما زالت تحتاج متابعة ميدانية</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الحالة الميدانية</th>
                        <th>رقم الحيوان الرسمي</th>
                        <th>الحيوان</th>
                        <th>المجموعة</th>
                        <th>المسؤول الطبي</th>
                        <th>تاريخ فتح الحالة</th>
                        <th>آخر تحديث</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="case-id case-id-open">FC-2026-001</span></td>
                        <td>#ANL-0871</td>
                        <td>شمبانزي أفريقي</td>
                        <td>القرود</td>
                        <td>د. ريم الفصل</td>
                        <td>2026-05-13</td>
                        <td>2026-06-02</td>
                        <td><span class="badge badge-followup"><span class="dot"></span>قيد المتابعة</span></td>
                        <td>
                            <a href="javascript:void(0)" onclick="openModal('FC-2026-001')" class="btn-tbl" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="case-id case-id-open">FC-2026-002</span></td>
                        <td>#ANM-154</td>
                        <td>زرافة نيلية</td>
                        <td>العناقيد الكبرى</td>
                        <td>د. خالد العربي</td>
                        <td>2026-05-20</td>
                        <td>2026-06-01</td>
                        <td><span class="badge badge-followup"><span class="dot"></span>قيد المتابعة</span></td>
                        <td>
                            <a href="javascript:void(0)" onclick="openModal('FC-2026-002')" class="btn-tbl" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="case-id case-id-open">FC-2026-003</span></td>
                        <td>#ANM-220</td>
                        <td>أسد إفريقي (سيمبا)</td>
                        <td>القطط الكبرى</td>
                        <td>د. خالد العربي</td>
                        <td>2026-06-01</td>
                        <td>2026-06-05</td>
                        <td><span class="badge badge-followup"><span class="dot"></span>قيد المتابعة</span></td>
                        <td>
                            <a href="javascript:void(0)" onclick="openModal('FC-2026-003')" class="btn-tbl" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ════ TAB 2: منتهية ════ --}}
<div id="tab-completed" class="tab-content">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">الحالات التي أغلقها الطبيب بعد انتهاء العلاج أو المتابعة — سجل تاريخي</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الحالة</th>
                        <th>رقم الحيوان الرسمي</th>
                        <th>الحيوان</th>
                        <th>المجموعة</th>
                        <th>المسؤول الطبي</th>
                        <th>تاريخ فتح الحالة</th>
                        <th>تاريخ الإغلاق</th>
                        <th>النتيجة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="case-id case-id-closed">FC-2025-088</span></td>
                        <td>#ANM-088</td>
                        <td>نسر ذهبي</td>
                        <td>الطيور</td>
                        <td>د. ريم الفصل</td>
                        <td>2025-05-10</td>
                        <td>2025-05-25</td>
                        <td><span class="badge badge-improved"><span class="dot"></span>تحسّن — لا يحتاج متابعة</span></td>
                        <td>
                            <a href="javascript:void(0)" onclick="openModal('FC-2025-088')" class="btn-tbl view" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="case-id case-id-closed">FC-2025-045</span></td>
                        <td>#ANM-045</td>
                        <td>فيل إفريقي (توم)</td>
                        <td>الفيلة</td>
                        <td>د. خالد العربي</td>
                        <td>2025-04-02</td>
                        <td>2025-04-18</td>
                        <td><span class="badge badge-treated"><span class="dot"></span>تمت المعالجة</span></td>
                        <td>
                            <a href="javascript:void(0)" onclick="openModal('FC-2025-045')" class="btn-tbl view" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="case-id case-id-closed">FC-2025-033</span></td>
                        <td>#ANM-033</td>
                        <td>أنثى أسد</td>
                        <td>القطط الكبرى</td>
                        <td>د. ريم الفصل</td>
                        <td>2025-03-15</td>
                        <td>2025-03-28</td>
                        <td><span class="badge badge-no-followup"><span class="dot"></span>لا يحتاج متابعة</span></td>
                        <td>
                            <a href="javascript:void(0)" onclick="openModal('FC-2025-033')" class="btn-tbl view" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
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
                <h3 style="margin:0; font-size:1.1rem; font-weight:800; color:#0f172a;">تفاصيل الحالة الميدانية</h3>
                <span style="font-size:0.8rem; color:#64748b; font-weight:600;" id="modalCaseId">FC-2026-001</span>
            </div>
            
            {{-- الجانب الأيسر: التبويبات وزر الإغلاق في أقصى اليسار --}}
            <div style="display:flex; align-items:center; gap:20px;">
                <div style="display:flex; gap:10px;">
                    <button id="dtab-btn-1" onclick="switchDTab(1)" style="padding:10px 24px; border:none; background:transparent; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; cursor:pointer; color:#1a4a2e; border-bottom:3px solid #1a4a2e;">ملخص الحالة</button>
                    <button id="dtab-btn-2" onclick="switchDTab(2)" style="padding:10px 24px; border:none; background:transparent; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; cursor:pointer; color:#94a3b8; border-bottom:3px solid transparent;">المتابعة الميدانية</button>
                </div>
                <button onclick="closeModal()" style="width:32px; height:32px; border-radius:8px; background:#f1f5f9; border:none; color:#64748b; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.1rem; font-weight:700; transition:all 0.2s; margin-bottom:10px;">✕</button>
            </div>
        </div>

        {{-- Body --}}
        <div style="padding: 1.5rem; overflow-y: auto; max-height: 65vh;">
            
            {{-- Tab 1: ملخص الحالة --}}
            <div id="dtab-1">
                
                {{-- Case ID --}}
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem;">
                    <span style="font-family:'Courier New',monospace; font-size:0.85rem; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:4px 12px; border-radius:6px; font-weight:700;">FC-2026-001</span>
                    <span style="font-size:0.8rem; color:#64748b; font-weight:700;">رقم الحالة</span>
                </div>

                {{-- Animal Info Grid --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1px; background:#e2e8f0; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0; margin-bottom:1.5rem;">
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">رقم الحيوان</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">#ANL-0871</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">نوع الحيوان</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">شمبانزي أفريقي</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">المجموعة</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">القرود</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">الموقع</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">حظيرة القرود — قسم B</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">تاريخ فتح الحالة</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">2026-05-13</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">الطبيب المسؤول</div>
                        <div style="font-size:0.9rem; color:#0f172a; font-weight:800;">د. ريم الفصل</div>
                    </div>
                    <div style="background:#fff; padding:12px 16px; grid-column:span 2;">
                        <div style="font-size:0.75rem; color:#94a3b8; font-weight:700; margin-bottom:4px;">الحالة الحالية</div>
                        <div style="font-size:0.9rem; color:#2563eb; font-weight:800;">⚡ قيد المتابعة</div>
                    </div>
                </div>

            </div>

            {{-- Tab 2: المتابعة الطبية --}}
            <div id="dtab-2" style="display:none;">
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <span style="font-size:0.85rem; color:#64748b; font-weight:700;">إجمالي الإدخالات: <strong style="color:#0f172a;">3</strong></span>
                </div>

                {{-- Follow-up Card 1 --}}
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.2rem; margin-bottom:1rem; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                    
                    {{-- Card Header --}}
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; border-radius:50%; background:#fce7f3; color:#be185d; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800;">د.ر</div>
                            <div>
                                <div style="font-size:0.9rem; font-weight:800; color:#0f172a;">د. ريم الفصل</div>
                                <div style="font-size:0.75rem; color:#94a3b8; font-weight:700;">2026-06-02</div>
                            </div>
                        </div>
                        <span style="background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:800;">↗ تحسن ملحوظ</span>
                    </div>

                    {{-- Card Body Grid --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div style="background:#f8fafc; padding:10px 14px; border-radius:8px; border:1px solid #f1f5f9;">
                            <div style="font-size:0.7rem; color:#94a3b8; font-weight:800; margin-bottom:4px;">التشخيص</div>
                            <div style="font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5;">حالة مستقرة. الجرح في طور الشفاء.</div>
                        </div>
                        <div style="background:#f8fafc; padding:10px 14px; border-radius:8px; border:1px solid #f1f5f9;">
                            <div style="font-size:0.7rem; color:#94a3b8; font-weight:800; margin-bottom:4px;">العلاج الميداني</div>
                            <div style="font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5;">جرعة مضاد حيوي إضافية ميدانياً.</div>
                        </div>
                        <div style="background:#f8fafc; padding:10px 14px; border-radius:8px; border:1px solid #f1f5f9; grid-column:span 2;">
                            <div style="font-size:0.7rem; color:#94a3b8; font-weight:800; margin-bottom:4px;">الملاحظات والتوصية</div>
                            <div style="font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5;">يُوصى بمتابعة ميدانية خلال 3 أيام للتحقق من التعافي الكامل.</div>
                        </div>
                    </div>
                </div>

                {{-- Follow-up Card 2 --}}
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.2rem; margin-bottom:1rem; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; border-radius:50%; background:#fce7f3; color:#be185d; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800;">د.ر</div>
                            <div>
                                <div style="font-size:0.9rem; font-weight:800; color:#0f172a;">د. ريم الفصل</div>
                                <div style="font-size:0.75rem; color:#94a3b8; font-weight:700;">2026-05-22</div>
                            </div>
                        </div>
                        <span style="background:#eff6ff; color:#2563eb; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:800;">→ مستقر</span>
                    </div>
                    <div style="background:#f8fafc; padding:10px 14px; border-radius:8px; border:1px solid #f1f5f9;">
                        <div style="font-size:0.7rem; color:#94a3b8; font-weight:800; margin-bottom:4px;">الفحص الميداني</div>
                        <div style="font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5;">تم فحص الجرح ميدانياً. لا توجد علامات التهاب. تم تنظيف الجرح وتعقيمه.</div>
                    </div>
                </div>

                {{-- Follow-up Card 3 --}}
                <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.2rem; margin-bottom:1rem; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; border-radius:50%; background:#fce7f3; color:#be185d; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800;">د.ر</div>
                            <div>
                                <div style="font-size:0.9rem; font-weight:800; color:#0f172a;">د. ريم الفصل</div>
                                <div style="font-size:0.75rem; color:#94a3b8; font-weight:700;">2026-05-13</div>
                            </div>
                        </div>
                        <span style="background:#f1f5f9; color:#64748b; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:800;">📂 فتح الحالة</span>
                    </div>
                    <div style="background:#f8fafc; padding:10px 14px; border-radius:8px; border:1px solid #f1f5f9;">
                        <div style="font-size:0.7rem; color:#94a3b8; font-weight:800; margin-bottom:4px;">التشخيص المبدئي</div>
                        <div style="font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5;">فتح الحالة الميدانية إثر ملاحظة جرح في الطرف الأيمن العلوي. تم تسجيل الإجراء الأولي وبدء المتابعة.</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div class="modal-footer" style="background:#fff; border-top:1px solid #e2e8f0; padding:1.2rem 1.5rem;">
            <button class="btn-cancel" onclick="closeModal()">إغلاق</button>
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

@endsection

@section('scripts')
<script>
function switchTab(evt, tabId) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.seg-tab').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}
function openModal(caseId) {
    document.getElementById('modalCaseId').textContent = caseId;
    document.getElementById('detailModal').classList.add('open');
}
function closeModal() {
    document.getElementById('detailModal').classList.remove('open');
}
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
