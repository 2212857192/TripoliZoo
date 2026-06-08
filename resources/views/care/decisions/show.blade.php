@extends($__layout ?? 'care.layout')
@section('title', 'تفاصيل قرار طبي | الرعاية والتغذية')
@section('page_title', 'تفاصيل قرار طبي')

@section('styles')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 700; color: #64748b; }
    .breadcrumb a { color: #2E7D32; text-decoration: none; transition: color 0.2s; display: flex; align-items: center; gap: 4px; }
    .breadcrumb a:hover { color: #1b5e20; }

    .header-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
    .header-info h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; display: flex; align-items: center; gap: 12px; }
    
    /* ═══ BADGES ═══ */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    
    .type-discharge { background: #f0fdfa; color: #0f766e; border: 1px solid #ccfbf1; }
    .type-discharge .dot { background: #14b8a6; }
    .type-release { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .type-release .dot { background: #3b82f6; }
    .type-slaughter { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .type-slaughter .dot { background: #ef4444; }

    .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    .status-received { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce3; }
    .status-failed { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }

    .btn-export { padding: 10px 20px; background: #fff; color: #16a34a; border: 1.5px solid #bbf7d0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(22,163,74,0.05); }
    .btn-export:hover { background: #f0fdf4; transform: translateY(-1px); }

    /* ── TABS ── */
    .tabs-container { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; }
    .tabs-header { display: flex; background: #FAFBFC; border-bottom: 1px solid #e2e8f0; padding: 0 1rem; }
    .tab-btn { padding: 16px 24px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.95rem; font-weight: 800; color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .tab-btn:hover { color: #0f172a; }
    .tab-btn.active { color: #1a4a2e; border-bottom-color: #1a4a2e; background: #fff; }

    .tab-content { padding: 2rem; display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
    .info-cell { background: #fff; padding: 16px 20px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.8rem; color: #64748b; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .info-cell-value { font-size: 1rem; color: #0f172a; font-weight: 800; }
    
    .content-box { background: #f8fafc; padding: 16px 20px; border-radius: 10px; font-size: 0.95rem; color: #334155; font-weight: 600; line-height: 1.7; border: 1px solid #e2e8f0; }
    .section-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }

    .id-tag { font-family: 'Courier New', monospace; font-size: 0.85rem; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }
</style>
@endsection

@section('content')

<div class="breadcrumb">
    <a href="{{ route('care.decisions.index') }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        القرارات الطبية
    </a>
    <span>/</span>
    <span style="color:#0f172a;" id="breadId">تفاصيل القرار {{ $id }}</span>
</div>

<div class="header-card">
    <div class="header-info">
        <h2>
            تفاصيل قرار طبي
            <span id="headerBadge"></span>
        </h2>
        <div style="font-size:0.9rem; color:#64748b; font-weight:700; margin-top:8px;">
            رقم القرار: <span class="id-tag" id="topId">{{ $id }}</span>
        </div>
    </div>
    <div>
        <button class="btn-export" onclick="alert('جاري تصدير النموذج بتنسيق PDF...')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            تصدير نموذج قرار طبي
        </button>
    </div>
</div>

<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab(1, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            بيانات القرار
        </button>
        <button class="tab-btn" onclick="switchTab(2, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            بيانات الحيوان
        </button>
        <button class="tab-btn" onclick="switchTab(3, this)" id="tab3-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            حالة الاستلام
        </button>
    </div>

    {{-- TAB 1: Decision Data --}}
    <div class="tab-content active" id="tab-1">
        <h3 class="section-title">المعلومات الأساسية للقرار</h3>
        <div class="info-grid" style="margin-bottom:1.5rem;">
            <div class="info-cell">
                <div class="info-cell-label">رقم القرار</div>
                <div class="info-cell-value id-tag">{{ $id }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">نوع القرار</div>
                <div class="info-cell-value" id="dType">—</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">مصدر القرار</div>
                <div class="info-cell-value" id="dSource">—</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">تاريخ إصدار القرار</div>
                <div class="info-cell-value" id="dDate">—</div>
            </div>
            <div class="info-cell span-2">
                <div class="info-cell-label">صادر بواسطة</div>
                <div class="info-cell-value" id="dIssuer">—</div>
            </div>
        </div>

        <h3 class="section-title" style="margin-top:2rem;">التفاصيل الطبية المرفقة</h3>
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div>
                <div class="info-cell-label">سبب أو توضيح القرار</div>
                <div class="content-box" id="dReason">—</div>
            </div>
            <div>
                <div class="info-cell-label">ملاحظات إضافية</div>
                <div class="content-box" id="dNotes">—</div>
            </div>
        </div>
    </div>

    {{-- TAB 2: Animal Data --}}
    <div class="tab-content" id="tab-2">
        <h3 class="section-title">بيانات الحيوان المرتبط بالقرار</h3>
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-cell-label" id="aIdLabel">رقم الحيوان</div>
                <div class="info-cell-value id-tag" id="aId">—</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">المجموعة المرتبطة</div>
                <div class="info-cell-value" id="aGroup">—</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">نوع الحيوان</div>
                <div class="info-cell-value" id="aType">—</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">الجنس</div>
                <div class="info-cell-value" id="aGender">—</div>
            </div>
            <div class="info-cell span-2">
                <div class="info-cell-label">العمر الموثق</div>
                <div class="info-cell-value" id="aAge">—</div>
            </div>
        </div>
    </div>

    {{-- TAB 3: Reception Status --}}
    <div class="tab-content" id="tab-3">
        <div id="receptionContent">
            <h3 class="section-title">متابعة مهمة الاستلام</h3>
            <div class="info-grid">
                <div class="info-cell span-2">
                    <div class="info-cell-label">حالة الاستلام الحالية</div>
                    <div class="info-cell-value" id="rStatusBadge">—</div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-label">المشرف المسؤول</div>
                    <div class="info-cell-value" id="rSupervisor">—</div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-label">تاريخ إنشاء مهمة الاستلام</div>
                    <div class="info-cell-value" id="rTaskDate">—</div>
                </div>
                <div class="info-cell span-2" id="rActionDateCell" style="display:none;">
                    <div class="info-cell-label" id="rActionDateLabel">تاريخ الاستلام</div>
                    <div class="info-cell-value" id="rActionDate">—</div>
                </div>
            </div>

            <div id="rFailedReasonWrap" style="display:none; margin-top:1.5rem;">
                <div class="info-cell-label" style="color:#dc2626;">سبب التعذر المؤقت</div>
                <div class="content-box" style="border-color:#fecaca; background:#fef2f2; color:#b91c1c;" id="rFailedReason">—</div>
            </div>
        </div>

        <div id="noReceptionContent" style="display:none; text-align:center; padding:3rem 0;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" style="margin-bottom:1rem;"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            <h3 style="font-size:1.2rem; color:#475569; margin:0 0 8px;">لا توجد مهمة استلام</h3>
            <p style="color:#64748b; font-weight:600; font-size:0.95rem; margin:0;">هذا القرار من نوع "ذبح اضطراري" ولا يتطلب إنشاء مهمة استلام في قسم الرعاية.</p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const decisionId = '{{ $id }}';

    // Dummy DB for the views
    const dummyDB = {
        'MD-801': { // Discharge, Pending
            type: 'discharge', typeText: 'خروج بعد العلاج', source: 'حالة داخل المستشفى', date: '2026-06-07', issuer: 'د. محمود (رئيس المستشفى)',
            reason: 'استقرار الحالة الصحية للحيوان وزوال الأعراض. جاهز للعودة للمجموعة.', notes: 'يُفضل إبقاء الحيوان بعيداً عن التجمعات الكبيرة لأول يومين.',
            aId: '#ANL-0041-2022', aIsQuarantine: false, aType: 'أسد إفريقي', aGender: 'ذكر', aAge: '8 سنوات', aGroup: 'السباع والضواري',
            rStatus: 'pending', rSupervisor: 'خالد منصور', rTaskDate: '2026-06-07 / 08:30 ص', rActionDate: '', rFailedReason: ''
        },
        'MD-800': { // Release, Received
            type: 'release', typeText: 'إفراج صحي', source: 'حجر صحي', date: '2026-06-06', issuer: 'د. محمود (رئيس المستشفى)',
            reason: 'اجتياز فترة الحجر الصحي المقررة (30 يوم) بنجاح. خلو تام من الأمراض المعدية.', notes: 'لا توجد أي توصيات خاصة. الحيوان سليم.',
            aId: '#Q-0182-2026', aIsQuarantine: true, aType: 'قرد المكاك', aGender: 'أنثى', aAge: '3 سنوات', aGroup: 'الرئيسيات',
            rStatus: 'received', rSupervisor: 'ياسر الغيثي', rTaskDate: '2026-06-06 / 10:00 ص', rActionDate: '2026-06-06 / 11:15 ص', rFailedReason: ''
        },
        'MD-799': { // Slaughter, None
            type: 'slaughter', typeText: 'ذبح اضطراري', source: 'حالة داخل المستشفى', date: '2026-06-05', issuer: 'د. محمود (رئيس المستشفى)',
            reason: 'كسر مضاعف في الساق الأمامية غير قابل للشفاء أو التجبير. تدهور حاد في حالة الحيوان.', notes: 'تم تنفيذ الإجراء وفق المعايير الطبية المعتمدة للقتل الرحيم.',
            aId: '#ANL-0120-2024', aIsQuarantine: false, aType: 'غزال الريم', aGender: 'ذكر', aAge: 'سنتان', aGroup: 'العواشب',
            rStatus: 'none', rSupervisor: '', rTaskDate: '', rActionDate: '', rFailedReason: ''
        },
        'MD-795': { // Discharge, Failed
            type: 'discharge', typeText: 'خروج بعد العلاج', source: 'حالة داخل المستشفى', date: '2026-06-04', issuer: 'د. صالح (طبيب معالج)',
            reason: 'التئام الجرح بشكل كامل بعد الخياطة والمضادات الحيوية.', notes: 'يرجى التأكد من نظافة الحظيرة لمنع التلوث.',
            aId: '#ANL-0250-2025', aIsQuarantine: false, aType: 'نسر أسمر', aGender: 'ذكر', aAge: '4 سنوات', aGroup: 'الطيور',
            rStatus: 'failed', rSupervisor: 'سالم عبدالله', rTaskDate: '2026-06-04 / 09:00 ص', rActionDate: '2026-06-04 / 02:00 م', rFailedReason: 'لا يوجد قفص عزل متاح حالياً لاستقباله كما طُلب في التعليمات. سيتم تجهيز قفص غداً واستلامه.'
        }
    };

    function switchTab(n, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById('tab-' + n).classList.add('active');
        btn.classList.add('active');
    }

    window.onload = function() {
        const d = dummyDB[decisionId];
        if(!d) return; // Fallback if ID not in dummy DB

        // Setup Header Badge
        const hBadge = document.getElementById('headerBadge');
        if(d.type === 'discharge') hBadge.innerHTML = `<span class="badge type-discharge"><span class="dot"></span>خروج بعد العلاج</span>`;
        if(d.type === 'release') hBadge.innerHTML = `<span class="badge type-release"><span class="dot"></span>إفراج صحي</span>`;
        if(d.type === 'slaughter') hBadge.innerHTML = `<span class="badge type-slaughter"><span class="dot"></span>ذبح اضطراري</span>`;

        // Tab 1: Decision
        document.getElementById('dType').innerText = d.typeText;
        document.getElementById('dSource').innerText = d.source;
        document.getElementById('dDate').innerText = d.date;
        document.getElementById('dIssuer').innerText = d.issuer;
        document.getElementById('dReason').innerText = d.reason;
        document.getElementById('dNotes').innerText = d.notes;

        // Tab 2: Animal
        document.getElementById('aId').innerText = d.aId;
        document.getElementById('aIdLabel').innerText = d.aIsQuarantine ? 'رقم الحجر الصحي' : 'الرقم الرسمي للحيوان';
        document.getElementById('aType').innerText = d.aType;
        document.getElementById('aGender').innerText = d.aGender;
        document.getElementById('aAge').innerText = d.aAge;
        document.getElementById('aGroup').innerText = d.aGroup;

        // Tab 3: Reception Status
        const recContent = document.getElementById('receptionContent');
        const noRecContent = document.getElementById('noReceptionContent');
        
        if (d.type === 'slaughter') {
            recContent.style.display = 'none';
            noRecContent.style.display = 'block';
        } else {
            recContent.style.display = 'block';
            noRecContent.style.display = 'none';

            document.getElementById('rSupervisor').innerText = d.rSupervisor;
            document.getElementById('rTaskDate').innerText = d.rTaskDate;
            
            const rStatusBadge = document.getElementById('rStatusBadge');
            const rActionDateCell = document.getElementById('rActionDateCell');
            const rActionDateLabel = document.getElementById('rActionDateLabel');
            const rActionDate = document.getElementById('rActionDate');
            const rFailedReasonWrap = document.getElementById('rFailedReasonWrap');

            if (d.rStatus === 'pending') {
                rStatusBadge.innerHTML = `<span class="badge status-pending" style="font-size:0.9rem; padding:6px 14px;">بانتظار الاستلام</span>`;
                rActionDateCell.style.display = 'none';
                rFailedReasonWrap.style.display = 'none';
            } 
            else if (d.rStatus === 'received') {
                rStatusBadge.innerHTML = `<span class="badge status-received" style="font-size:0.9rem; padding:6px 14px;">تم الاستلام</span>`;
                rActionDateLabel.innerText = 'تاريخ الاستلام';
                rActionDate.innerText = d.rActionDate;
                rActionDateCell.style.display = 'block';
                rFailedReasonWrap.style.display = 'none';
            }
            else if (d.rStatus === 'failed') {
                rStatusBadge.innerHTML = `<span class="badge status-failed" style="font-size:0.9rem; padding:6px 14px;">تعذر مؤقتاً</span>`;
                rActionDateLabel.innerText = 'تاريخ تسجيل التعذر';
                rActionDate.innerText = d.rActionDate;
                rActionDateCell.style.display = 'block';
                
                document.getElementById('rFailedReason').innerText = d.rFailedReason;
                rFailedReasonWrap.style.display = 'block';
            }
        }
    };
</script>
@endsection
