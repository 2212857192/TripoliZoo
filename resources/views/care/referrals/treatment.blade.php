@extends('care.layout')
@section('title', 'إحالات العلاج | الرعاية والتغذية')
@section('page_title', 'إحالات العلاج')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.2rem; }
    .page-header { display: flex; justify-content: space-between; align-items: center; }
    .page-header-info h2 { font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0; }
    .page-header-info p { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin: 4px 0 0; }

    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; padding-top: 1.2rem; border-top: 1px solid #F1F5F9; }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input { width: 100%; padding: 10px 40px 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; transition: all 0.2s; }
    .search-box input:focus { border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    .search-box svg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .filter-select { padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #334155; outline: none; cursor: pointer; }
    .filter-select:focus { border-color: #2E7D32; }

    /* ── Table ── */
    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-header { padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #FAFBFC; }
    .table-card-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.8rem; font-weight: 800; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.92rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .custom-table tbody tr:last-child td { border-bottom: none; }

    /* ═══ BADGES ═══ */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    
    .badge-pending  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-pending .dot { background: #f59e0b; }
    .badge-approved { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-approved .dot { background: #22c55e; }
    .badge-rejected { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-rejected .dot { background: #ef4444; }

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .ref-id, .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    /* ═══ MODAL ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 720px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    .modal-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1.2rem 1.5rem 0; display: flex; justify-content: space-between; align-items: flex-end; }
    .modal-title-wrap { padding-bottom: 0.8rem; }
    .modal-title-wrap h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .modal-title-wrap span { font-size: 0.8rem; color: #64748b; font-weight: 600; }
    .modal-tabs-wrap { display: flex; align-items: center; gap: 20px; }
    .modal-tabs { display: flex; }
    .modal-tab { padding: 10px 22px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent; transition: all 0.2s; }
    .modal-tab.active { color: #1a4a2e; border-bottom-color: #1a4a2e; }
    .modal-close { width: 32px; height: 32px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.1rem; font-weight: 700; transition: all 0.2s; margin-bottom: 10px; }
    .modal-close:hover { background: #f8fafc; color: #0f172a; }

    .modal-body { padding: 1.5rem; overflow-y: auto; max-height: 65vh; }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; }
    .info-cell { background: #fff; padding: 12px 16px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
    .info-cell-value { font-size: 0.9rem; color: #0f172a; font-weight: 800; }

    .content-box { background: #fff; padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; color: #1e293b; font-weight: 600; line-height: 1.6; border: 1px solid #e2e8f0; border-right: 4px solid #3b82f6; margin-bottom: 1rem; }
    .section-label { font-size: 0.85rem; color: #0f172a; font-weight: 800; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }

    /* Conditional sections */
    .status-section { border-radius: 12px; padding: 1.2rem; margin-top: 1.5rem; border: 1px solid transparent; }
    .status-section-title { font-size: 0.95rem; font-weight: 800; margin-bottom: 1rem; padding-bottom: 10px; border-bottom: 2px solid rgba(0,0,0,0.05); display: flex; align-items: center; gap: 8px; }
    
    .status-pending { background: #f8fafc; border-color: #e2e8f0; text-align: center; color: #475569; font-weight: 700; }
    
    .status-approved { background: #f0fdf4; border-color: #bbf7d0; }
    .status-approved .status-section-title { color: #15803d; }
    
    .status-rejected { background: #fef2f2; border-color: #fecaca; }
    .status-rejected .status-section-title { color: #dc2626; }
    
    .status-box { background: #fff; padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; color: #1e293b; font-weight: 700; border: 1px solid rgba(0,0,0,0.05); }

    /* Modal Footer */
    .modal-footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 1.2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; }
    .btn-cancel { padding: 10px 20px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #f8fafc; }
    .btn-secondary { padding: 10px 20px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-secondary:hover { background: #dbeafe; }
</style>
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>🏥 إحالات العلاج</h2>
            <p>متابعة إحالات العلاج المرسلة إلى قسم المستشفى البيطري.</p>
        </div>
    </div>
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، نوع الحيوان، رقم الإحالة...">
        </div>
        <select class="filter-select">
            <option value="">كل الحالات</option>
            <option>قيد المراجعة</option>
            <option>معتمدة</option>
            <option>مرفوضة</option>
        </select>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option>السباع والضواري</option>
            <option>الرئيسيات</option>
            <option>العواشب</option>
            <option>الطيور</option>
        </select>
        <input type="date" class="filter-select">
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            سجل الإحالات
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الإحالة</th>
                    <th>رقم الحيوان</th>
                    <th>نوع الحيوان</th>
                    <th>المجموعة</th>
                    <th>تاريخ الإحالة</th>
                    <th>حالة الإحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                {{-- Row 1: Pending --}}
                <tr>
                    <td><span class="ref-id">TR-042</span></td>
                    <td><span class="animal-id">#ANL-0041-2022</span></td>
                    <td style="font-weight:700;">أسد إفريقي</td>
                    <td>السباع والضواري</td>
                    <td>2026-06-07</td>
                    <td><span class="badge badge-pending"><span class="dot"></span>قيد المراجعة</span></td>
                    <td>
                        <button onclick="openModal('pending', 'TR-042')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>

                {{-- Row 2: Approved --}}
                <tr>
                    <td><span class="ref-id">TR-041</span></td>
                    <td><span class="animal-id">#ANL-0182-2023</span></td>
                    <td style="font-weight:700;">قرد المكاك</td>
                    <td>الرئيسيات</td>
                    <td>2026-06-06</td>
                    <td><span class="badge badge-approved"><span class="dot"></span>معتمدة</span></td>
                    <td>
                        <button onclick="openModal('approved', 'TR-041')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>

                {{-- Row 3: Rejected --}}
                <tr>
                    <td><span class="ref-id">TR-040</span></td>
                    <td><span class="animal-id">#ANL-0120-2024</span></td>
                    <td style="font-weight:700;">غزال الريم</td>
                    <td>العواشب</td>
                    <td>2026-06-05</td>
                    <td><span class="badge badge-rejected"><span class="dot"></span>مرفوضة</span></td>
                    <td>
                        <button onclick="openModal('rejected', 'TR-040')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ MODAL ═══ --}}
<div class="modal-backdrop" id="referralModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <h3>تفاصيل إحالة علاج</h3>
                <span id="mSubtitle">—</span>
            </div>
            <div class="modal-tabs-wrap">
                <div class="modal-tabs">
                    <button class="modal-tab active" id="mtab-btn-1" onclick="switchMTab(1)">بيانات الإحالة</button>
                    <button class="modal-tab" id="mtab-btn-2" onclick="switchMTab(2)">الحالة الصحية المرتبطة</button>
                </div>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
        </div>

        <div class="modal-body">
            {{-- Tab 1: بيانات الإحالة --}}
            <div id="mtab-1">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem; flex-wrap:wrap;">
                    <span style="font-family:'Courier New',monospace; font-size:0.85rem; background:#f8fafc; color:#334155; border:1px solid #e2e8f0; padding:4px 12px; border-radius:6px; font-weight:800;" id="mRefId">TR-000</span>
                    <span style="font-size:0.8rem; color:#64748b; font-weight:700;">رقم الإحالة</span>
                    <span id="mStatusBadge" style="margin-right:auto;"></span>
                </div>

                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-cell-label">رقم الحيوان الرسمي</div>
                        <div class="info-cell-value" style="font-family:'Courier New',monospace; color:#64748b;" id="mAnimalId">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">نوع الحيوان</div>
                        <div class="info-cell-value" id="mAnimalType">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">الجنس</div>
                        <div class="info-cell-value" id="mGender">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">العمر</div>
                        <div class="info-cell-value" id="mAge">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المجموعة</div>
                        <div class="info-cell-value" id="mGroup">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ إرسال الإحالة</div>
                        <div class="info-cell-value" id="mDate">—</div>
                    </div>
                    <div class="info-cell span-2">
                        <div class="info-cell-label">مُرسل الإحالة</div>
                        <div class="info-cell-value" id="mSender">رئيس قسم الرعاية والتغذية</div>
                    </div>
                </div>

                {{-- Status Sections --}}
                <div id="statusPending" class="status-section status-pending" style="display:none;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" style="margin-bottom:8px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div>الإحالة بانتظار مراجعة رئيس قسم المستشفى البيطري.</div>
                </div>

                <div id="statusApproved" class="status-section status-approved" style="display:none;">
                    <div class="status-section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        تم اعتماد الإحالة
                    </div>
                    <div class="info-grid" style="margin-bottom:0; background:transparent; border:none;">
                        <div class="info-cell" style="background:transparent; padding:0 0 10px 0;">
                            <div class="info-cell-label">تاريخ الاعتماد</div>
                            <div class="info-cell-value" id="appDate">—</div>
                        </div>
                        <div class="info-cell" style="background:transparent; padding:0 0 10px 0;">
                            <div class="info-cell-label">الحالة داخل المستشفى</div>
                            <div class="info-cell-value" style="color:#15803d;">تم فتح حالة علاجية</div>
                        </div>
                    </div>
                </div>

                <div id="statusRejected" class="status-section status-rejected" style="display:none;">
                    <div class="status-section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        تم رفض الإحالة
                    </div>
                    <div class="info-grid" style="margin-bottom:10px; background:transparent; border:none;">
                        <div class="info-cell" style="background:transparent; padding:0;">
                            <div class="info-cell-label">تاريخ الرفض</div>
                            <div class="info-cell-value" id="rejDate">—</div>
                        </div>
                    </div>
                    <div>
                        <div class="info-cell-label" style="margin-bottom:6px;">سبب الرفض</div>
                        <div class="status-box" id="rejReason">—</div>
                    </div>
                </div>
            </div>

            {{-- Tab 2: الحالة الصحية المرتبطة --}}
            <div id="mtab-2" style="display:none;">
                <div class="section-label">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    بيانات الحالة الصحية الأصلية
                </div>
                
                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-cell-label">رقم الحالة الصحية</div>
                        <div class="info-cell-value" style="font-family:'Courier New',monospace;" id="hId">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ تسجيل الحالة</div>
                        <div class="info-cell-value" id="hDate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المشرف المسجل</div>
                        <div class="info-cell-value" id="hSupervisor">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">نوع المتابعة</div>
                        <div class="info-cell-value" style="color:#dc2626;">تحتاج إحالة</div>
                    </div>
                    <div class="info-cell span-2">
                        <div class="info-cell-label">وصف الحالة (من المشرف)</div>
                        <div class="info-cell-value" id="hDesc" style="font-weight:600; line-height:1.6;">—</div>
                    </div>
                </div>

                <div style="margin-top:1rem;">
                    <div class="info-cell-label" style="margin-bottom:6px;">الملاحظات الإضافية المسجلة</div>
                    <div class="content-box" id="hNotes">—</div>
                </div>
            </div>
        </div>

        <div class="modal-footer" id="mFooter"></div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function switchMTab(n) {
        document.getElementById('mtab-1').style.display = n === 1 ? 'block' : 'none';
        document.getElementById('mtab-2').style.display = n === 2 ? 'block' : 'none';
        document.getElementById('mtab-btn-1').className = 'modal-tab' + (n === 1 ? ' active' : '');
        document.getElementById('mtab-btn-2').className = 'modal-tab' + (n === 2 ? ' active' : '');
    }

    const dummyData = {
        'TR-042': {
            animalId: '#ANL-0041-2022', animalType: 'أسد إفريقي', gender: 'ذكر', age: '8 سنوات', group: 'السباع والضواري',
            date: '2026-06-07', hId: 'HC-1102', hDate: '2026-06-07 / 08:30 ص', hSupervisor: 'خالد منصور',
            hDesc: 'جرح عميق في الفخذ الأيمن إثر شجار', hNotes: 'ينزف بشكل متقطع ويرفض الحركة أو الأكل. تم عزله في القفص الداخلي.',
            appDate: '', rejDate: '', rejReason: ''
        },
        'TR-041': {
            animalId: '#ANL-0182-2023', animalType: 'قرد المكاك', gender: 'أنثى', age: '3 سنوات', group: 'الرئيسيات',
            date: '2026-06-06', hId: 'HC-1101', hDate: '2026-06-06 / 10:15 ص', hSupervisor: 'ياسر الغيثي',
            hDesc: 'خمول شديد وارتفاع في الحرارة', hNotes: 'الحيوان لا يتجاوب مع المؤثرات ورافض تماماً لشرب الماء.',
            appDate: '2026-06-06 / 11:30 ص', rejDate: '', rejReason: ''
        },
        'TR-040': {
            animalId: '#ANL-0120-2024', animalType: 'غزال الريم', gender: 'ذكر', age: 'سنتان', group: 'العواشب',
            date: '2026-06-05', hId: 'HC-1100', hDate: '2026-06-05 / 07:00 ص', hSupervisor: 'أحمد الكواري',
            hDesc: 'عرج خفيف في الساق اليسرى الأمامية', hNotes: 'العرج يظهر عند الركض فقط، لكنه يمشي ويأكل بشكل طبيعي.',
            appDate: '', rejDate: '2026-06-05 / 09:00 ص', rejReason: 'الحالة لا تستدعي إحالة علاجية ونقل للمستشفى. يمكن إعطاء مضاد للالتهاب في الحظيرة ومراقبته لـ 48 ساعة بواسطة المشرف.'
        }
    };

    function openModal(status, refId) {
        switchMTab(1);
        const d = dummyData[refId];

        // Populate basic info
        document.getElementById('mSubtitle').innerText = d.animalType + ' — ' + d.group;
        document.getElementById('mRefId').innerText = refId;
        document.getElementById('mAnimalId').innerText = d.animalId;
        document.getElementById('mAnimalType').innerText = d.animalType;
        document.getElementById('mGender').innerText = d.gender;
        document.getElementById('mAge').innerText = d.age;
        document.getElementById('mGroup').innerText = d.group;
        document.getElementById('mDate').innerText = d.date;

        // Populate health info
        document.getElementById('hId').innerText = d.hId;
        document.getElementById('hDate').innerText = d.hDate;
        document.getElementById('hSupervisor').innerText = d.hSupervisor;
        document.getElementById('hDesc').innerText = d.hDesc;
        document.getElementById('hNotes').innerText = d.hNotes;

        // Status Badge & Sections
        const sPending = document.getElementById('statusPending');
        const sApproved = document.getElementById('statusApproved');
        const sRejected = document.getElementById('statusRejected');
        
        sPending.style.display = 'none';
        sApproved.style.display = 'none';
        sRejected.style.display = 'none';

        const footer = document.getElementById('mFooter');
        const closeBtn = `<button class="btn-cancel" onclick="closeModal()">إغلاق</button>`;

        if (status === 'pending') {
            document.getElementById('mStatusBadge').innerHTML = `<span class="badge badge-pending" style="font-size:0.8rem; padding:4px 10px;"><span class="dot"></span>قيد المراجعة</span>`;
            sPending.style.display = 'block';
            footer.innerHTML = closeBtn;
        } 
        else if (status === 'approved') {
            document.getElementById('mStatusBadge').innerHTML = `<span class="badge badge-approved" style="font-size:0.8rem; padding:4px 10px;"><span class="dot"></span>معتمدة</span>`;
            document.getElementById('appDate').innerText = d.appDate;
            sApproved.style.display = 'block';
            footer.innerHTML = closeBtn + 
                `<button class="btn-secondary" onclick="alert('واجهة عرض الحالة في المستشفى - للقراءة فقط')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    عرض الحالة داخل المستشفى
                </button>`;
        } 
        else if (status === 'rejected') {
            document.getElementById('mStatusBadge').innerHTML = `<span class="badge badge-rejected" style="font-size:0.8rem; padding:4px 10px;"><span class="dot"></span>مرفوضة</span>`;
            document.getElementById('rejDate').innerText = d.rejDate;
            document.getElementById('rejReason').innerText = d.rejReason;
            sRejected.style.display = 'block';
            footer.innerHTML = closeBtn;
        }

        document.getElementById('referralModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('referralModal').classList.remove('open');
    }

    window.onclick = function(e) {
        if (e.target === document.getElementById('referralModal')) closeModal();
    };
</script>
@endsection
