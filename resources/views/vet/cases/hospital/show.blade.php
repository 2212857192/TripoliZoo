@extends($__layout ?? 'vet.layout')
@section('title', 'تفاصيل الحالة | المستشفى البيطري')
@section('page_title', 'تفاصيل الحالة')

@section('styles')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 700; color: #64748b; }
    .breadcrumb a { color: #2E7D32; text-decoration: none; transition: color 0.2s; display: flex; align-items: center; gap: 4px; }
    .breadcrumb a:hover { color: #1b5e20; }

    .header-card {
        background: #fff; border: 1px solid var(--border); border-radius: 16px;
        padding: 1.5rem 2rem; margin-bottom: 1.5rem;
        display: flex; justify-content: space-between; align-items: center;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .header-info h2 {
        font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0;
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    }

    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-ready { background: #f0fdfa; color: #0f766e; border: 1px solid #ccfbf1; }
    .status-ready .dot { background: #14b8a6; }
    .status-watch { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    .status-watch .dot { background: #f59e0b; }
    .status-no-response { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .status-no-response .dot { background: #ef4444; }
    .status-handover { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .status-handover .dot { background: #3b82f6; }
    .status-unavailable { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .status-unavailable .dot { background: #f97316; }
    .status-discharged { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .status-discharged .dot { background: #22c55e; }
    .status-slaughter { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-slaughter .dot { background: #ef4444; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; background: #f8fafc; color: #475569;
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800;
        cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .btn-back:hover { background: #f1f5f9; color: #0f172a; }

    .tabs-container { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; }
    .tabs-header { display: flex; background: #FAFBFC; border-bottom: 1px solid #e2e8f0; padding: 0 1rem; }
    .tab-btn {
        padding: 16px 24px; border: none; background: transparent;
        font-family: 'Cairo', sans-serif; font-size: 0.95rem; font-weight: 800;
        color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s;
        display: flex; align-items: center; gap: 8px;
    }
    .tab-btn:hover { color: var(--green); }
    .tab-btn.active { color: var(--green); border-bottom-color: var(--green); background: #fff; }

    .tab-content { padding: 2rem; display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    .case-top-bar {
        display: flex; align-items: center; gap: 1.2rem;
        padding-bottom: 1.5rem; margin-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .animal-photo {
        width: 72px; height: 72px; border-radius: 16px; flex-shrink: 0;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
        border: 2px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem; overflow: hidden;
    }
    .animal-photo img { width: 100%; height: 100%; object-fit: cover; }
    .case-top-name { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
    .case-top-vet { font-size: 0.9rem; color: #64748b; font-weight: 700; }

    .summary-layout { display: grid; grid-template-columns: 1fr 320px; gap: 1.5rem; align-items: start; }
    @media (max-width: 900px) { .summary-layout { grid-template-columns: 1fr; } }

    .animal-card {
        background: #fff; border-radius: 12px; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .animal-card-title { font-size: 1.05rem; font-weight: 800; color: #1e293b; margin-bottom: 1.2rem; }
    .q-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;
    }
    .q-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .q-label { color: #64748b; font-size: 0.9rem; font-weight: 700; }
    .q-value { color: #0f172a; font-size: 0.95rem; font-weight: 800; text-align: left; }

    .notes-panel {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        position: sticky; top: 1rem;
    }
    .notes-panel-title { font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; }
    .notes-panel-body {
        background: #f8fafc; padding: 14px 16px; border-radius: 10px;
        font-size: 0.9rem; color: #334155; font-weight: 600; line-height: 1.7;
        border: 1px solid #e2e8f0; min-height: 120px;
    }

    .section-title { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 1.5rem 0 1rem; }
    .content-box {
        background: #f8fafc; padding: 16px 20px; border-radius: 10px;
        font-size: 0.95rem; color: #334155; font-weight: 600; line-height: 1.7;
        border: 1px solid #e2e8f0;
    }
    .id-tag {
        font-family: 'Courier New', monospace; font-size: 0.85rem;
        background: #f1f5f9; padding: 4px 10px; border-radius: 6px;
        color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0;
    }

    .follow-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 1.2rem 1.4rem; margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .follow-card-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1rem; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;
    }
    .follow-vet { font-size: 0.9rem; font-weight: 800; color: #0f172a; }
    .follow-date { font-size: 0.75rem; color: #94a3b8; font-weight: 700; }
    .follow-field { margin-bottom: 1rem; }
    .follow-field:last-child { margin-bottom: 0; }
    .follow-field-label { font-size: 0.75rem; color: #64748b; font-weight: 800; margin-bottom: 6px; }
    .follow-field-label .req { color: #ef4444; }
    .follow-field-value {
        background: #f8fafc; padding: 12px 14px; border-radius: 8px;
        font-size: 0.88rem; color: #1e293b; font-weight: 700; line-height: 1.6;
        border: 1px solid #f1f5f9;
    }
    .nutrition-block { margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0; }
    .nutrition-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 0.5rem; }
    @media (max-width: 600px) { .nutrition-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@php $vetBase = ($readOnly ?? false) ? '/director/vet' : '/vet'; @endphp

@section('content')

<div class="breadcrumb">
    <a href="{{ $vetBase }}/cases/hospital">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        الحالات داخل المستشفى
    </a>
    <span>/</span>
    <span style="color:#0f172a;">تفاصيل الحالة</span>
</div>

<div class="header-card">
    <div class="header-info">
        <h2>
            تفاصيل الحالة
            <span id="headerBadge"></span>
        </h2>
    </div>
    <a href="{{ $vetBase }}/cases/hospital" class="btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        العودة
    </a>
</div>

<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab(1, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            ملخص الحالة
        </button>
        <button class="tab-btn" onclick="switchTab(2, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            المتابعة الطبية
        </button>
    </div>

    <div class="tab-content active" id="tab-1">
        <div class="case-top-bar">
            <div class="animal-photo" id="topAnimalPhoto">🦁</div>
            <div>
                <div class="case-top-name" id="topAnimalName">—</div>
                <div class="case-top-vet">الطبيب المشرف: <span id="topVet">—</span></div>
            </div>
        </div>

        <div class="summary-layout">
            <div>
                <div class="animal-card">
                    <h4 class="animal-card-title">ملخص الحالة</h4>
                    <div class="q-row">
                        <span class="q-label">رقم الحيوان</span>
                        <span class="q-value id-tag" id="sAnimalId">—</span>
                    </div>
                    <div class="q-row">
                        <span class="q-label">نوع الحيوان</span>
                        <span class="q-value" id="sAnimalType">—</span>
                    </div>
                    <div class="q-row" id="sNameRow" style="display:none;">
                        <span class="q-label">اسم الحيوان</span>
                        <span class="q-value" id="sAnimalName">—</span>
                    </div>
                    <div class="q-row" id="sMarkRow" style="display:none;">
                        <span class="q-label">العلامة المميزة</span>
                        <span class="q-value" id="sMark">—</span>
                    </div>
                    <div class="q-row">
                        <span class="q-label">الجنس</span>
                        <span class="q-value" id="sGender">—</span>
                    </div>
                    <div class="q-row">
                        <span class="q-label">العمر</span>
                        <span class="q-value" id="sAge">—</span>
                    </div>
                    <div class="q-row">
                        <span class="q-label">المجموعة</span>
                        <span class="q-value" id="sGroup">—</span>
                    </div>
                    <div class="q-row">
                        <span class="q-label">تاريخ دخول المستشفى</span>
                        <span class="q-value" id="sAdmissionDate">—</span>
                    </div>
                    <div class="q-row" id="sDischargeDateRow" style="display:none;">
                        <span class="q-label">تاريخ الخروج</span>
                        <span class="q-value" id="sDischargeDate">—</span>
                    </div>
                </div>

                <h3 class="section-title">سبب الإحالة</h3>
                <div class="content-box" id="sReason">—</div>
            </div>

            <div class="notes-panel">
                <div class="notes-panel-title">الملاحظات المسجلة عن الحيوان</div>
                <div class="notes-panel-body" id="sNotes">—</div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="tab-2">
        <h3 class="section-title" style="margin-top:0;">سجل المتابعة الطبية</h3>
        <div id="followList"></div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const caseId = '{{ $id ?? 'HC-2025-001' }}';

    const hospitalDB = {
        'HC-2025-001': {
            statusClass: 'status-ready', statusText: 'جاهز للخروج',
            vet: 'د. خالد العربي',
            reason: 'إصابة في الطرف الأمامي مع التهاب موضعي يستدعي الرعاية داخل المستشفى.',
            notes: 'تم نقل الحيوان من حظيرة الأسود الرئيسية. يُراقب الشهية والحركة يومياً. آخر وزن: 185 كغ.',
            animalId: '#ANM-101', animalType: 'أسد إفريقي', animalName: 'سيمبا', mark: '',
            animalEmoji: '🦁', gender: 'ذكر', age: '6 سنوات', group: 'القطط الكبرى',
            admissionDate: '2026-05-30', dischargeDate: '',
            followUps: [
                {
                    date: '2026-06-05 — 10:00 ص', vet: 'د. خالد العربي',
                    diagnosis: 'التئام الجرح بنسبة 90%. الحالة مستقرة وجاهزة للخروج.',
                    treatment: 'إيقاف المضاد الحيوي. متابعة الجرح موضعياً فقط.',
                    note: 'يُوصى بإعادة الحيوان إلى حظيرته خلال 48 ساعة.',
                    nutrition: { text: 'لحم مطبوخ جيداً مع مكملات فيتامين', start: '2026-06-06', end: '2026-06-12', note: 'تجنب الأطعمة النيئة' }
                },
                {
                    date: '2026-05-30 — 02:00 م', vet: 'د. فاطمة الزهراء',
                    diagnosis: 'جرح عميق في اليد اليسرى مفتوح مع عدوى بسيطة.',
                    treatment: 'تنظيف الجرح، ضمادات يومية، مضاد حيوي واسع الطيف.',
                    note: 'بدء العلاج داخل المستشفى.', nutrition: null
                }
            ]
        },
        'HC-2025-002': {
            statusClass: 'status-watch', statusText: 'قيد العلاج',
            vet: 'د. فاطمة الزهراء',
            reason: 'خمول ورفض جزئي للطعام مع إصابة في الساق الخلفية.',
            notes: 'الحيوان تحت المراقبة المستمرة. يحتاج دعم غذائي.',
            animalId: '#ANM-154', animalType: 'زرافة نيلية', animalName: 'جميلة', mark: 'بقع بنية على الرقبة',
            animalEmoji: '🦒', gender: 'أنثى', age: '4 سنوات', group: 'العناقيد الكبرى',
            admissionDate: '2026-06-02', dischargeDate: '',
            followUps: [
                { date: '2026-06-04', vet: 'د. فاطمة الزهراء', diagnosis: 'تحسن طفيف في الشهية.', treatment: 'مكملات غذائية ومسكنات.', note: '', nutrition: null }
            ]
        },
        'HC-2025-003': {
            statusClass: 'status-no-response', statusText: 'لا يستجيب للعلاج',
            vet: 'د. خالد العربي',
            reason: 'إصابة في الجناح مع عدوى متقدمة.',
            notes: 'الحالة لا تستجيب للبروتوكول العلاجي الحالي. يُراجع القرار الطبي.',
            animalId: '#ANM-088', animalType: 'نسر ذهبي', animalName: '', mark: '',
            animalEmoji: '🦅', gender: 'ذكر', age: '3 سنوات', group: 'الطيور',
            admissionDate: '2026-05-29', dischargeDate: '',
            followUps: [
                { date: '2026-06-03', vet: 'د. خالد العربي', diagnosis: 'لا تحسن ملحوظ.', treatment: 'تعديل الجرعة العلاجية.', note: 'مراجعة خلال 48 ساعة.', nutrition: null }
            ]
        },
        'HC-2025-006': {
            statusClass: 'status-discharged', statusText: 'خروج بعد العلاج',
            vet: 'د. خالد العربي',
            reason: 'التهاب معوي حاد.',
            notes: 'تم استلام الحيوان من مسؤول المجموعة.',
            animalId: '#ANM-042', animalType: 'شمبانزي', animalName: 'بونغو', mark: '',
            animalEmoji: '🐒', gender: 'ذكر', age: '8 سنوات', group: 'الرئيسيات',
            admissionDate: '2026-05-10', dischargeDate: '2026-05-20',
            followUps: [
                { date: '2026-05-20', vet: 'د. خالد العربي', diagnosis: 'شفاء تام.', treatment: 'إيقاف العلاج.', note: 'خروج بعد العلاج.', nutrition: null }
            ]
        }
    };

    function switchTab(n, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + n).classList.add('active');
        btn.classList.add('active');
    }

    function renderFollowUps(list) {
        const container = document.getElementById('followList');
        if (!list || !list.length) {
            container.innerHTML = '<p style="color:#64748b;font-weight:700;text-align:center;padding:2rem;">لا توجد متابعات مسجلة.</p>';
            return;
        }
        container.innerHTML = list.map(f => {
            let nutritionHtml = '';
            if (f.nutrition) {
                nutritionHtml = `
                    <div class="nutrition-block">
                        <div class="follow-field">
                            <div class="follow-field-label">التوصيات الغذائية العلاجية</div>
                            <div class="follow-field-value">${f.nutrition.text}</div>
                        </div>
                        <div class="nutrition-grid">
                            <div class="follow-field">
                                <div class="follow-field-label">تاريخ البداية</div>
                                <div class="follow-field-value">${f.nutrition.start}</div>
                            </div>
                            <div class="follow-field">
                                <div class="follow-field-label">تاريخ النهاية</div>
                                <div class="follow-field-value">${f.nutrition.end}</div>
                            </div>
                        </div>
                        ${f.nutrition.note ? `
                        <div class="follow-field" style="margin-top:10px;">
                            <div class="follow-field-label">ملاحظة التوصية</div>
                            <div class="follow-field-value">${f.nutrition.note}</div>
                        </div>` : ''}
                    </div>`;
            }
            return `
                <div class="follow-card">
                    <div class="follow-card-header">
                        <div>
                            <div class="follow-vet">${f.vet}</div>
                            <div class="follow-date">${f.date}</div>
                        </div>
                    </div>
                    <div class="follow-field">
                        <div class="follow-field-label">التشخيص <span class="req">*</span></div>
                        <div class="follow-field-value">${f.diagnosis}</div>
                    </div>
                    <div class="follow-field">
                        <div class="follow-field-label">العلاج <span class="req">*</span></div>
                        <div class="follow-field-value">${f.treatment}</div>
                    </div>
                    ${f.note ? `
                    <div class="follow-field">
                        <div class="follow-field-label">الملاحظات</div>
                        <div class="follow-field-value">${f.note}</div>
                    </div>` : ''}
                    ${nutritionHtml}
                </div>`;
        }).join('');
    }

    window.onload = function() {
        const d = hospitalDB[caseId] || hospitalDB['HC-2025-001'];

        document.getElementById('headerBadge').innerHTML =
            '<span class="badge ' + d.statusClass + '"><span class="dot"></span>' + d.statusText + '</span>';

        document.getElementById('topAnimalPhoto').textContent = d.animalEmoji;
        document.getElementById('topAnimalName').textContent = d.animalName || d.animalType;
        document.getElementById('topVet').textContent = d.vet;

        document.getElementById('sAnimalId').textContent = d.animalId;
        document.getElementById('sAnimalType').textContent = d.animalType;
        document.getElementById('sGender').textContent = d.gender;
        document.getElementById('sAge').textContent = d.age;
        document.getElementById('sGroup').textContent = d.group;
        document.getElementById('sAdmissionDate').textContent = d.admissionDate;
        document.getElementById('sReason').textContent = d.reason;
        document.getElementById('sNotes').textContent = d.notes;

        if (d.animalName) {
            document.getElementById('sAnimalName').textContent = d.animalName;
            document.getElementById('sNameRow').style.display = 'flex';
        }
        if (d.mark) {
            document.getElementById('sMark').textContent = d.mark;
            document.getElementById('sMarkRow').style.display = 'flex';
        }
        if (d.dischargeDate) {
            document.getElementById('sDischargeDate').textContent = d.dischargeDate;
            document.getElementById('sDischargeDateRow').style.display = 'flex';
        }

        renderFollowUps(d.followUps);
    };
</script>
@endsection
