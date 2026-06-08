@extends($__layout ?? 'care.layout')
@section('title', 'إحالات التشريح | الرعاية والتغذية')
@section('page_title', 'إحالات التشريح')

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
    .badge-documented { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-documented .dot { background: #3b82f6; }

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .ref-id, .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    /* ═══ MODAL ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 760px; max-height: 92vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
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

    .modal-body { padding: 1.5rem; overflow-y: auto; max-height: 68vh; }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; }
    .info-cell { background: #fff; padding: 12px 16px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
    .info-cell-value { font-size: 0.9rem; color: #0f172a; font-weight: 800; }

    .content-box { background: #fff; padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; color: #1e293b; font-weight: 600; line-height: 1.6; border: 1px solid #e2e8f0; border-right: 4px solid #64748b; margin-bottom: 1rem; }
    .content-box.green { border-right-color: #22c55e; }
    .section-label { font-size: 0.85rem; color: #0f172a; font-weight: 800; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }

    /* Conditional sections */
    .status-section { border-radius: 12px; padding: 1.2rem; margin-top: 1.5rem; border: 1px solid transparent; }
    .status-section-title { font-size: 0.95rem; font-weight: 800; margin-bottom: 1rem; padding-bottom: 10px; border-bottom: 2px solid rgba(0,0,0,0.05); display: flex; align-items: center; gap: 8px; }
    
    .status-pending { background: #f8fafc; border-color: #e2e8f0; text-align: center; color: #475569; font-weight: 700; }
    
    .status-documented { background: #f0fdf4; border-color: #bbf7d0; }
    .status-documented .status-section-title { color: #15803d; }
    
    /* Modal Footer */
    .modal-footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 1.2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; }
    .btn-cancel { padding: 10px 20px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #f8fafc; }
</style>
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>🔬 إحالات التشريح</h2>
            <p>متابعة إحالات التشريح المرسلة إلى قسم المستشفى البيطري ونتائجها.</p>
        </div>
    </div>
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، نوع الحيوان، رقم الإحالة...">
        </div>
        <select class="filter-select">
            <option value="">كل الحالات</option>
            <option>بانتظار التوثيق</option>
            <option>موثقة</option>
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
            سجل إحالات التشريح
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
                    <td><span class="ref-id">AR-105</span></td>
                    <td><span class="animal-id">#ANL-0120-2026</span></td>
                    <td style="font-weight:700;">غزال الريم</td>
                    <td>العواشب</td>
                    <td>2026-06-03</td>
                    <td><span class="badge badge-pending"><span class="dot"></span>بانتظار التوثيق</span></td>
                    <td>
                        <button onclick="openModal('pending', 'AR-105')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>

                {{-- Row 2: Documented --}}
                <tr>
                    <td><span class="ref-id">AR-104</span></td>
                    <td><span class="animal-id">#ANL-0305-2024</span></td>
                    <td style="font-weight:700;">فهد أفريقي</td>
                    <td>السباع والضواري</td>
                    <td>2026-05-28</td>
                    <td><span class="badge badge-documented"><span class="dot"></span>موثقة</span></td>
                    <td>
                        <button onclick="openModal('documented', 'AR-104')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                
                {{-- Row 3: Documented with custom reason --}}
                <tr>
                    <td><span class="ref-id">AR-103</span></td>
                    <td><span class="animal-id">#ANL-0099-2022</span></td>
                    <td style="font-weight:700;">نسر أسمر</td>
                    <td>الطيور</td>
                    <td>2026-05-15</td>
                    <td><span class="badge badge-documented"><span class="dot"></span>موثقة</span></td>
                    <td>
                        <button onclick="openModal('documented_custom', 'AR-103')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ MODAL ═══ --}}
<div class="modal-backdrop" id="autopsyModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <h3>تفاصيل إحالة تشريح</h3>
                <span id="mSubtitle">—</span>
            </div>
            <div class="modal-tabs-wrap">
                <div class="modal-tabs">
                    <button class="modal-tab active" id="mtab-btn-1" onclick="switchMTab(1)">بيانات الحيوان والإحالة</button>
                    <button class="modal-tab" id="mtab-btn-2" onclick="switchMTab(2)">بيانات النفوق قبل التشريح</button>
                </div>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
        </div>

        <div class="modal-body">
            {{-- Tab 1: بيانات الإحالة والحيوان + النتيجة --}}
            <div id="mtab-1">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem; flex-wrap:wrap;">
                    <span style="font-family:'Courier New',monospace; font-size:0.85rem; background:#f8fafc; color:#334155; border:1px solid #e2e8f0; padding:4px 12px; border-radius:6px; font-weight:800;" id="mRefId">AR-000</span>
                    <span style="font-size:0.8rem; color:#64748b; font-weight:700;">رقم الإحالة</span>
                    <span id="mStatusBadge" style="margin-right:auto;"></span>
                </div>

                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-cell-label">رقم الحيوان الرسمي</div>
                        <div class="info-cell-value" style="font-family:'Courier New',monospace; color:#64748b;" id="mAnimalId">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">رقم حالة النفوق المرتبطة</div>
                        <div class="info-cell-value" style="font-family:'Courier New',monospace;" id="mMortalityId">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">نوع الحيوان</div>
                        <div class="info-cell-value" id="mAnimalType">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المجموعة</div>
                        <div class="info-cell-value" id="mGroup">—</div>
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
                        <div class="info-cell-label">تاريخ النفوق</div>
                        <div class="info-cell-value" id="mDeathDate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ الإحالة</div>
                        <div class="info-cell-value" id="mRefDate">—</div>
                    </div>
                    <div class="info-cell span-2">
                        <div class="info-cell-label">المشرف المسجل</div>
                        <div class="info-cell-value" id="mSupervisor">—</div>
                    </div>
                </div>

                {{-- Status Sections --}}
                <div id="statusPending" class="status-section status-pending" style="display:none;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" style="margin-bottom:8px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div>لم يتم توثيق نتيجة التشريح بعد.</div>
                </div>

                <div id="statusDocumented" class="status-section status-documented" style="display:none;">
                    <div class="status-section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        نتيجة التشريح
                    </div>
                    <div class="info-grid" style="margin-bottom:1rem; background:transparent; border:none;">
                        <div class="info-cell span-2" style="background:transparent; padding:0 0 10px 0;">
                            <div class="info-cell-label">سبب النفوق النهائي (المُوثَّق)</div>
                            <div class="info-cell-value" style="color:#15803d; font-size:1rem;" id="finalCause">—</div>
                        </div>
                        <div class="info-cell" style="background:transparent; padding:0 0 10px 0;">
                            <div class="info-cell-label">تاريخ التوثيق</div>
                            <div class="info-cell-value" id="docDate">—</div>
                        </div>
                        <div class="info-cell" style="background:transparent; padding:0 0 10px 0;">
                            <div class="info-cell-label">موثق النتيجة</div>
                            <div class="info-cell-value" id="docBy">—</div>
                        </div>
                    </div>
                    <div style="margin-bottom:1rem;">
                        <div class="info-cell-label" style="margin-bottom:6px;">ملاحظات التشريح</div>
                        <div class="content-box green" id="docNotes" style="margin-bottom:0;">—</div>
                    </div>
                    <div>
                        <div class="info-cell-label" style="margin-bottom:6px;">تقرير الصفة التشريحية</div>
                        <a href="#" style="color:#2563eb; font-size:0.85rem; font-weight:700; display:inline-flex; align-items:center; gap:4px; padding:8px 14px; background:#eff6ff; border-radius:8px; border:1px solid #bfdbfe; text-decoration:none;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            عرض / تحميل التقرير
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tab 2: بيانات النفوق قبل التشريح --}}
            <div id="mtab-2" style="display:none;">
                <div class="section-label">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
                    البيانات المسجلة عند إرسال الإحالة
                </div>
                
                <div class="info-grid">
                    <div class="info-cell span-2">
                        <div class="info-cell-label">سبب النفوق (من المشرف)</div>
                        <div class="info-cell-value" id="hCause">—</div>
                    </div>
                    <div class="info-cell span-2" id="hCustomReasonWrap" style="display:none;">
                        <div class="info-cell-label" style="color:#dc2626;">سبب الإحالة للتشريح (استثنائياً)</div>
                        <div class="info-cell-value" id="hCustomReason" style="font-weight:700; color:#dc2626;">—</div>
                    </div>
                </div>

                <div style="margin-top:1rem;">
                    <div class="info-cell-label" style="margin-bottom:6px;">الملاحظات المسجلة عن الحيوان</div>
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
        'AR-105': { // Pending (Unknown Cause)
            animalId: '#ANL-0120-2026', mortalityId: 'MC-2026-004', animalType: 'غزال الريم', gender: 'ذكر', age: '2 سنوات', group: 'العواشب',
            deathDate: '2026-06-03', refDate: '2026-06-03', supervisor: 'أحمد الكواري',
            hCause: 'غير ظاهر', hNotes: 'وجد ميتاً بدون سبب ظاهر في الحظيرة.',
            hCustomReason: '',
            finalCause: '', docDate: '', docBy: '', docNotes: ''
        },
        'AR-104': { // Documented (Unknown Cause initially)
            animalId: '#ANL-0305-2024', mortalityId: 'MC-2026-005', animalType: 'فهد أفريقي', gender: 'ذكر', age: '11 سنوات', group: 'السباع والضواري',
            deathDate: '2026-05-28', refDate: '2026-05-28', supervisor: 'عمر الفاسي',
            hCause: 'غير ظاهر', hNotes: 'وفاة مفاجئة بلا أعراض سابقة. أُحيل للتشريح لمعرفة السبب.',
            hCustomReason: '',
            finalCause: 'فشل كلوي حاد مصحوب بعدوى بكتيرية', docDate: '2026-06-02', docBy: 'د. محمود (رئيس المستشفى البيطري)',
            docNotes: 'تم الكشف عن التهاب حاد في الكليتين مع تضخم في الطحال. النتائج تؤكد وفاة طبيعية لا علاقة لها بأي مسبب خارجي.'
        },
        'AR-103': { // Documented (Custom Reason despite visible cause)
            animalId: '#ANL-0099-2022', mortalityId: 'MC-2026-010', animalType: 'نسر أسمر', gender: 'ذكر', age: '4 سنوات', group: 'الطيور',
            deathDate: '2026-05-15', refDate: '2026-05-15', supervisor: 'سالم عبدالله',
            hCause: 'كسر في العنق', hNotes: 'سقط من ارتفاع وتوفي. تم تسجيل كسر واضح في العنق.',
            hCustomReason: 'الاشتباه في مرض (ضعف في العظام أو تسمم قد يكون سبب السقوط)',
            finalCause: 'تسمم بمادة كيميائية (مبيد حشري)', docDate: '2026-05-18', docBy: 'د. محمود (رئيس المستشفى البيطري)',
            docNotes: 'تبين من تحليل عينات المعدة وجود آثار لمبيد حشري أدى لاختلال توازنه وسقوطه. كسر العنق هو السبب المباشر، لكن التسمم هو المسبب الأساسي.'
        }
    };

    function openModal(status, refId) {
        switchMTab(1);
        const d = dummyData[refId];

        // Populate Tab 1 (Animal & Referral data)
        document.getElementById('mSubtitle').innerText = d.animalType + ' — ' + d.group;
        document.getElementById('mRefId').innerText = refId;
        document.getElementById('mAnimalId').innerText = d.animalId;
        document.getElementById('mMortalityId').innerText = d.mortalityId;
        document.getElementById('mAnimalType').innerText = d.animalType;
        document.getElementById('mGender').innerText = d.gender;
        document.getElementById('mAge').innerText = d.age;
        document.getElementById('mGroup').innerText = d.group;
        document.getElementById('mDeathDate').innerText = d.deathDate;
        document.getElementById('mRefDate').innerText = d.refDate;
        document.getElementById('mSupervisor').innerText = d.supervisor;

        // Populate Tab 2 (Pre-Autopsy Data)
        const causeEl = document.getElementById('hCause');
        if (d.hCause === 'غير ظاهر') {
            causeEl.innerHTML = '<span style="color:#94a3b8; font-style:italic;">غير ظاهر</span>';
        } else {
            causeEl.innerText = d.hCause;
        }
        document.getElementById('hNotes').innerText = d.hNotes;
        
        const customReasonWrap = document.getElementById('hCustomReasonWrap');
        if (d.hCustomReason) {
            document.getElementById('hCustomReason').innerText = d.hCustomReason;
            customReasonWrap.style.display = 'block';
        } else {
            customReasonWrap.style.display = 'none';
        }

        // Status Badge & Sections
        const sPending = document.getElementById('statusPending');
        const sDocumented = document.getElementById('statusDocumented');
        sPending.style.display = 'none';
        sDocumented.style.display = 'none';

        const footer = document.getElementById('mFooter');
        const closeBtn = `<button class="btn-cancel" onclick="closeModal()">إغلاق</button>`;

        if (status === 'pending') {
            document.getElementById('mStatusBadge').innerHTML = `<span class="badge badge-pending" style="font-size:0.8rem; padding:4px 10px;"><span class="dot"></span>بانتظار التوثيق</span>`;
            sPending.style.display = 'block';
        } else {
            document.getElementById('mStatusBadge').innerHTML = `<span class="badge badge-documented" style="font-size:0.8rem; padding:4px 10px;"><span class="dot"></span>موثقة</span>`;
            
            document.getElementById('finalCause').innerText = d.finalCause;
            document.getElementById('docDate').innerText = d.docDate;
            document.getElementById('docBy').innerText = d.docBy;
            document.getElementById('docNotes').innerText = d.docNotes;
            
            sDocumented.style.display = 'block';
        }
        
        footer.innerHTML = closeBtn;

        document.getElementById('autopsyModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('autopsyModal').classList.remove('open');
    }

    window.onclick = function(e) {
        if (e.target === document.getElementById('autopsyModal')) closeModal();
    };
</script>
@endsection
