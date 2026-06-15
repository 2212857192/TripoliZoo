@extends($__layout ?? 'vet.layout')
@section('title', 'تفاصيل حالة طبية ميدانية | المستشفى البيطري')
@section('page_title', 'تفاصيل حالة طبية ميدانية')

@section('styles')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 700; color: #64748b; }
    .breadcrumb a { color: #2E7D32; text-decoration: none; transition: color 0.2s; display: flex; align-items: center; gap: 4px; }
    .breadcrumb a:hover { color: #1b5e20; }

    .header-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
    .header-info h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-open { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .status-open .dot { background: #3b82f6; }
    .status-closed { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .status-closed .dot { background: #94a3b8; }

    .tabs-container { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; }
    .tabs-header { display: flex; background: #FAFBFC; border-bottom: 1px solid #e2e8f0; padding: 0 1rem; }
    .tab-btn { padding: 16px 24px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.95rem; font-weight: 800; color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .tab-btn:hover { color: var(--green); }
    .tab-btn.active { color: var(--green); border-bottom-color: var(--green); background: #fff; }

    .tab-content { padding: 2rem; display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    .summary-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { .summary-layout { grid-template-columns: 1fr; } }

    .animal-card {
        background: #fff; border-radius: 12px; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .animal-card-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center; }
    .animal-photo-wrap { display: flex; justify-content: center; margin-bottom: 1.2rem; }
    .animal-photo {
        width: 72px; height: 72px; border-radius: 16px;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
        border: 2px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem; overflow: hidden;
    }
    .animal-photo img { width: 100%; height: 100%; object-fit: cover; }
    .q-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;
    }
    .q-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .q-label { color: #64748b; font-size: 0.9rem; font-weight: 700; }
    .q-value { color: #0f172a; font-size: 0.95rem; font-weight: 800; text-align: left; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
    .info-cell { background: #fff; padding: 16px 20px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.8rem; color: #64748b; font-weight: 800; margin-bottom: 6px; }
    .info-cell-value { font-size: 1rem; color: #0f172a; font-weight: 800; }
    .content-box { background: #f8fafc; padding: 16px 20px; border-radius: 10px; font-size: 0.95rem; color: #334155; font-weight: 600; line-height: 1.7; border: 1px solid #e2e8f0; }
    .section-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }
    .id-tag { font-family: 'Courier New', monospace; font-size: 0.85rem; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

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
    .nutrition-block {
        margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0;
    }
    .nutrition-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 0.5rem; }
    @media (max-width: 600px) { .nutrition-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@php $vetBase = ($readOnly ?? false) ? '/director/vet' : '/vet'; @endphp

@section('content')

<div class="breadcrumb">
    <a href="{{ $vetBase }}/cases/field">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        الحالات الطبية الميدانية
    </a>
    <span>/</span>
    <span style="color:#0f172a;" id="breadCaseId">تفاصيل الحالة {{ $id ?? 'FC-2026-001' }}</span>
</div>

<div class="header-card">
    <div class="header-info">
        <h2>
            تفاصيل حالة طبية ميدانية
            <span id="headerBadge"></span>
        </h2>
        <div style="font-size:0.9rem; color:#64748b; font-weight:700; margin-top:8px;">
            رقم الحالة: <span class="id-tag" id="topCaseId">{{ $id ?? 'FC-2026-001' }}</span>
        </div>
    </div>
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

    {{-- TAB 1: Summary --}}
    <div class="tab-content active" id="tab-1">
        <div class="summary-layout">
            <div>
                <h3 class="section-title">معلومات الحالة</h3>
                <div class="info-grid" style="margin-bottom:1.5rem;">
                    <div class="info-cell">
                        <div class="info-cell-label">رقم الحالة</div>
                        <div class="info-cell-value id-tag" id="sCaseId">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ فتح الحالة</div>
                        <div class="info-cell-value" id="sOpenDate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">آخر تحديث</div>
                        <div class="info-cell-value" id="sLastUpdate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">الطبيب المسؤول</div>
                        <div class="info-cell-value" id="sVet">—</div>
                    </div>
                    <div class="info-cell span-2" id="sCloseDateCell" style="display:none;">
                        <div class="info-cell-label">تاريخ الإغلاق</div>
                        <div class="info-cell-value" id="sCloseDate">—</div>
                    </div>
                </div>
                <h3 class="section-title" style="margin-top:1.5rem;">سبب فتح الحالة</h3>
                <div class="content-box" id="sReason">—</div>
            </div>

            <div class="animal-card">
                <h4 class="animal-card-title">بيانات الحيوان</h4>
                <div class="animal-photo-wrap">
                    <div class="animal-photo" id="sAnimalPhoto">🐒</div>
                </div>
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
                    <span class="q-label">المجموعة</span>
                    <span class="q-value" id="sGroup">—</span>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: Medical Follow-up --}}
    <div class="tab-content" id="tab-2">
        <h3 class="section-title">سجل المتابعة الطبية</h3>
        <div id="followList"></div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const caseId = '{{ $id ?? 'FC-2026-001' }}';

    const fieldDB = {
        'FC-2026-001': {
            status: 'open', statusText: 'قيد المتابعة',
            openDate: '2026-05-13', lastUpdate: '2026-06-02', closeDate: '',
            vet: 'د. ريم الفصل',
            reason: 'معاينة جرح بسيط داخل بيت الحيوان إثر ملاحظة من المشرف.',
            animalId: '#ANL-0871', animalType: 'شمبانزي أفريقي', animalName: 'كوكو', mark: 'ندبة على الذراع الأيمن',
            animalEmoji: '🐒', group: 'القرود',
            followUps: [
                {
                    date: '2026-06-02', vet: 'د. ريم الفصل',
                    diagnosis: 'حالة مستقرة. الجرح في طور الشفاء.',
                    treatment: 'جرعة مضاد حيوي إضافية ميدانياً.',
                    note: 'يُوصى بمتابعة ميدانية خلال 3 أيام.',
                    nutrition: { text: 'فاكهة طرية غنية بالفيتامينات', start: '2026-06-03', end: '2026-06-05', note: 'تجنب الأطعمة الصلبة' }
                },
                {
                    date: '2026-05-22', vet: 'د. ريم الفصل',
                    diagnosis: 'لا توجد علامات التهاب. الجرح نظيف.',
                    treatment: 'تنظيف الجرح وتعقيمه موضعياً.',
                    note: '', nutrition: null
                },
                {
                    date: '2026-05-13', vet: 'د. ريم الفصل',
                    diagnosis: 'جرح سطحي في الطرف الأيمن العلوي.',
                    treatment: 'تسجيل الإجراء الأولي وبدء المتابعة.',
                    note: 'الحيوان نشيط ويأكل بشكل طبيعي.', nutrition: null
                }
            ]
        },
        'FC-2026-002': {
            status: 'open', statusText: 'قيد المتابعة',
            openDate: '2026-05-20', lastUpdate: '2026-06-01', closeDate: '',
            vet: 'د. خالد العربي',
            reason: 'خمول ملحوظ ورفض جزئي للطعام.',
            animalId: '#ANM-154', animalType: 'زرافة نيلية', animalName: '', mark: '',
            animalEmoji: '🦒', group: 'العناقيد الكبرى',
            followUps: [
                { date: '2026-06-01', vet: 'د. خالد العربي', diagnosis: 'تحسن في الشهية.', treatment: 'مكملات فيتامين.', note: '', nutrition: null }
            ]
        },
        'FC-2025-088': {
            status: 'closed', statusText: 'مغلقة',
            openDate: '2025-05-10', lastUpdate: '2025-05-25', closeDate: '2025-05-25',
            vet: 'د. ريم الفصل',
            reason: 'إصابة في الجناح أثناء الطيران داخل الحظيرة.',
            animalId: '#ANM-088', animalType: 'نسر ذهبي', animalName: 'صقر', mark: 'ريش ذهبي فاتح على الرأس',
            animalEmoji: '🦅', group: 'الطيور',
            followUps: [
                { date: '2025-05-25', vet: 'د. ريم الفصل', diagnosis: 'التئام كامل للجناح.', treatment: 'إزالة الضمادات.', note: 'لا حاجة لمتابعة.', nutrition: null }
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
                            <div class="follow-field-label">التوصية الغذائية العلاجية</div>
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
                        <div class="follow-field-label">ملاحظة</div>
                        <div class="follow-field-value">${f.note}</div>
                    </div>` : ''}
                    ${nutritionHtml}
                </div>`;
        }).join('');
    }

    window.onload = function() {
        const d = fieldDB[caseId] || fieldDB['FC-2026-001'];

        document.getElementById('breadCaseId').textContent = 'تفاصيل الحالة ' + caseId;
        document.getElementById('topCaseId').textContent = caseId;
        document.getElementById('sCaseId').textContent = caseId;

        const badge = document.getElementById('headerBadge');
        if (d.status === 'open') {
            badge.innerHTML = '<span class="badge status-open"><span class="dot"></span>قيد المتابعة</span>';
        } else {
            badge.innerHTML = '<span class="badge status-closed"><span class="dot"></span>مغلقة</span>';
            document.getElementById('sCloseDateCell').style.display = 'block';
            document.getElementById('sCloseDate').textContent = d.closeDate;
        }

        document.getElementById('sOpenDate').textContent = d.openDate;
        document.getElementById('sLastUpdate').textContent = d.lastUpdate;
        document.getElementById('sVet').textContent = d.vet;
        document.getElementById('sReason').textContent = d.reason;

        document.getElementById('sAnimalPhoto').textContent = d.animalEmoji;
        document.getElementById('sAnimalId').textContent = d.animalId;
        document.getElementById('sAnimalType').textContent = d.animalType;
        document.getElementById('sGroup').textContent = d.group;

        if (d.animalName) {
            document.getElementById('sAnimalName').textContent = d.animalName;
            document.getElementById('sNameRow').style.display = 'flex';
        }
        if (d.mark) {
            document.getElementById('sMark').textContent = d.mark;
            document.getElementById('sMarkRow').style.display = 'flex';
        }

        renderFollowUps(d.followUps);
    };
</script>
@endsection
