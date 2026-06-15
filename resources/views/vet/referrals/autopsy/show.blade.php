@extends($__layout ?? 'vet.layout')
@section('title', 'تفاصيل إحالة تشريح | المستشفى البيطري')
@section('page_title', 'تفاصيل إحالة تشريح')

@section('styles')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 700; color: #64748b; }
    .breadcrumb a { color: #2E7D32; text-decoration: none; transition: color 0.2s; display: flex; align-items: center; gap: 4px; }
    .breadcrumb a:hover { color: #1b5e20; }

    .header-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); flex-wrap: wrap; gap: 1rem; }
    .header-info h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-pending { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .status-pending .dot { background: #f59e0b; }
    .status-documented { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .status-documented .dot { background: #3b82f6; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; background: #f8fafc; color: #475569;
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800;
        cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .btn-back:hover { background: #f1f5f9; color: #0f172a; }
    .btn-document {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; background: #2E7D32; color: #fff;
        border: 1.5px solid #2E7D32; border-radius: 10px;
        font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-document:hover { background: #1B5E20; }

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

    .report-link {
        display: inline-flex; align-items: center; gap: 6px;
        color: #2563eb; font-size: 0.85rem; font-weight: 700;
        padding: 8px 14px; background: #eff6ff; border-radius: 8px;
        border: 1px solid #bfdbfe; text-decoration: none;
    }

    /* نموذج التوثيق */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 16px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
    .modal-header { padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
    .modal-header h3 { font-size: 1.1rem; font-weight: 900; color: #0f172a; margin: 0; }
    .modal-close { background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; }
    .modal-body { padding: 2rem; }
    .modal-section { margin-bottom: 1.5rem; }
    .modal-section:last-child { margin-bottom: 0; }
    .modal-label { font-size: 0.8rem; font-weight: 800; color: #2E7D32; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .modal-textarea, .modal-input { width: 100%; padding: 12px 16px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 600; color: #334155; outline: none; transition: all 0.2s; box-sizing: border-box; }
    .modal-textarea { resize: vertical; min-height: 100px; }
    .modal-textarea:focus, .modal-input:focus { border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    .modal-footer { padding: 1.5rem 2rem; border-top: 1px solid #f1f5f9; display: flex; gap: 1rem; justify-content: flex-end; }
    .btn-modal { padding: 10px 20px; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800; cursor: pointer; transition: all 0.2s; border: 1px solid; }
    .btn-modal-confirm { background: #2E7D32; color: #fff; border-color: #2E7D32; }
    .btn-modal-confirm:hover { background: #1B5E20; }
    .btn-modal-cancel { background: #fff; color: #64748b; border-color: #e2e8f0; }
    .btn-modal-cancel:hover { background: #f8fafc; }
</style>
@endsection

@php $vetBase = ($readOnly ?? false) ? '/director/vet' : '/vet'; @endphp

@section('content')

<div class="breadcrumb">
    <a href="{{ $vetBase }}/referrals/autopsy">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        إحالات التشريح
    </a>
    <span>/</span>
    <span style="color:#0f172a;" id="breadRefId">تفاصيل الإحالة {{ $id ?? 'AR-001' }}</span>
</div>

<div class="header-card">
    <div class="header-info">
        <h2>
            تفاصيل إحالة تشريح
            <span id="headerBadge"></span>
        </h2>
        <div style="font-size:0.9rem; color:#64748b; font-weight:700; margin-top:8px;">
            رقم الإحالة: <span class="id-tag" id="topRefId">{{ $id ?? 'AR-001' }}</span>
        </div>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <span id="headerActions"></span>
        <a href="{{ $vetBase }}/referrals/autopsy" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            العودة
        </a>
    </div>
</div>

<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab(1, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            ملخص الإحالة
        </button>
        <button class="tab-btn" onclick="switchTab(2, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
            بيانات النفوق قبل التشريح
        </button>
    </div>

    {{-- TAB 1: Summary --}}
    <div class="tab-content active" id="tab-1">
        <div class="summary-layout">
            <div>
                <h3 class="section-title">معلومات الإحالة</h3>
                <div class="info-grid" style="margin-bottom:1.5rem;">
                    <div class="info-cell">
                        <div class="info-cell-label">رقم الإحالة</div>
                        <div class="info-cell-value id-tag" id="sRefId">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ الإحالة</div>
                        <div class="info-cell-value" id="sRefDate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">رقم حالة النفوق المرتبطة</div>
                        <div class="info-cell-value id-tag" id="sMortalityId">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المشرف المسجل</div>
                        <div class="info-cell-value" id="sSupervisor">—</div>
                    </div>
                </div>
                <h3 class="section-title" style="margin-top:1.5rem;">سبب التحويل للتشريح</h3>
                <div class="content-box" id="sTransferReason">—</div>
                <div id="resultBlock" style="margin-top:1.5rem;"></div>
            </div>

            <div class="animal-card">
                <h4 class="animal-card-title">بيانات الحيوان</h4>
                <div class="animal-photo-wrap">
                    <div class="animal-photo" id="sAnimalPhoto">🦅</div>
                </div>
                <div class="q-row">
                    <span class="q-label">رقم الحيوان</span>
                    <span class="q-value id-tag" id="sAnimalId">—</span>
                </div>
                <div class="q-row">
                    <span class="q-label">نوع الحيوان</span>
                    <span class="q-value" id="sAnimalType">—</span>
                </div>
                <div class="q-row" id="sNameRow">
                    <span class="q-label">اسم الحيوان</span>
                    <span class="q-value" id="sAnimalName">—</span>
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
            </div>
        </div>
    </div>

    {{-- TAB 2: Death data before autopsy --}}
    <div class="tab-content" id="tab-2">
        <h3 class="section-title">البيانات المسجلة عند إرسال الإحالة</h3>
        <div id="deathCard"></div>
    </div>

</div>

{{-- نموذج توثيق النتيجة --}}
<div class="modal-backdrop" id="documentModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>توثيق نتيجة التشريح</h3>
            <button class="modal-close" onclick="closeDocumentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-section">
                <div class="modal-label">
                    سبب النفوق النهائي
                    <span style="color:#ef4444;font-size:0.7rem;margin-right:4px;">*</span>
                </div>
                <textarea class="modal-textarea" id="deathCause" placeholder="يرجى إدخال سبب النفوق النهائي..."></textarea>
            </div>
            <div class="modal-section">
                <div class="modal-label">ملاحظات التشريح</div>
                <textarea class="modal-textarea" id="autopsyNotes" placeholder="يرجى إدخال ملاحظات التشريح (اختياري)..."></textarea>
            </div>
            <div class="modal-section">
                <div class="modal-label">تقرير الصفة التشريحية</div>
                <div style="border:1.5px dashed #e2e8f0;border-radius:10px;padding:2rem;text-align:center;cursor:pointer;" onclick="document.getElementById('autopsyReport').click()">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <p style="color:#64748b;font-weight:600;font-size:0.85rem;margin-top:0.5rem;">اضغط لرفع صورة أو ملف</p>
                    <input type="file" id="autopsyReport" style="display:none;">
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-label">تاريخ التوثيق</div>
                <input type="date" class="modal-input" id="documentationDate" value="2025-05-14">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal btn-modal-cancel" onclick="closeDocumentModal()">إلغاء</button>
            <button class="btn-modal btn-modal-confirm" onclick="confirmDocument()">حفظ النتيجة</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const refId = '{{ $id ?? 'AR-001' }}';

    const autopsyDB = {
        'AR-001': {
            animalName: 'صقر', animalEmoji: '🦅',
            animalId: '#ANM-009', mortalityId: 'MC-2025-009', animalType: 'نسر ذهبي', gender: 'ذكر', age: '8 سنوات', group: 'الطيور',
            refDate: '2025-05-13', transferReason: 'وفاة غير معروفة السبب',
            deathDate: '2025-05-13', supervisor: 'سالم عبدالله',
            hCause: 'غير ظاهر', hNotes: 'الحيوان وجد ميتاً في القفص بدون أسباب واضحة.', hCustomReason: '',
            finalCause: '', docDate: '', docBy: '', docNotes: ''
        },
        'AR-002': {
            animalName: 'كوكو', animalEmoji: '🐒',
            animalId: '#ANL-0871', mortalityId: 'MC-2025-011', animalType: 'شمبانزي أفريقي', gender: 'ذكر', age: '6 سنوات', group: 'القرود',
            refDate: '2025-05-15', transferReason: 'وفاة مفاجئة',
            deathDate: '2025-05-15', supervisor: 'ياسر الغيثي',
            hCause: 'غير ظاهر', hNotes: 'وفاة مفاجئة دون أعراض سابقة ملحوظة.', hCustomReason: '',
            finalCause: '', docDate: '', docBy: '', docNotes: ''
        },
        'AR-003': {
            animalName: 'جميلة', animalEmoji: '🦒',
            animalId: '#ANM-154', mortalityId: 'MC-2025-008', animalType: 'زرافة نيلية', gender: 'أنثى', age: '4 سنوات', group: 'العناقيد الكبرى',
            refDate: '2025-05-10', transferReason: 'مشاكل تنفسية',
            deathDate: '2025-05-10', supervisor: 'خالد منصور',
            hCause: 'صعوبة تنفس حادة', hNotes: 'تدهور سريع في التنفس قبل الوفاة.', hCustomReason: '',
            finalCause: 'التهاب رئوي حاد', docDate: '2025-05-12', docBy: 'د. محمود (رئيس المستشفى البيطري)',
            docNotes: 'تبين من التشريح التهاب رئوي منتشر مع تجمع سوائل في التجويف الصدري.'
        }
    };

    function switchTab(n, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + n).classList.add('active');
        btn.classList.add('active');
    }

    function renderDeathCard(d) {
        const causeDisplay = d.hCause === 'غير ظاهر'
            ? '<span style="color:#94a3b8;font-style:italic;">غير ظاهر</span>'
            : d.hCause;

        let customHtml = '';
        if (d.hCustomReason) {
            customHtml = `
                <div class="follow-field">
                    <div class="follow-field-label" style="color:#dc2626;">سبب الإحالة للتشريح (استثنائياً)</div>
                    <div class="follow-field-value" style="color:#dc2626;">${d.hCustomReason}</div>
                </div>`;
        }

        document.getElementById('deathCard').innerHTML = `
            <div class="follow-card">
                <div class="follow-card-header">
                    <div>
                        <div class="follow-vet">${d.supervisor}</div>
                        <div class="follow-date">تاريخ النفوق: ${d.deathDate}</div>
                    </div>
                </div>
                <div class="follow-field">
                    <div class="follow-field-label">سبب النفوق (من المشرف)</div>
                    <div class="follow-field-value">${causeDisplay}</div>
                </div>
                ${customHtml}
                <div class="follow-field">
                    <div class="follow-field-label">الملاحظات المسجلة عن الحيوان</div>
                    <div class="follow-field-value">${d.hNotes}</div>
                </div>
            </div>`;
    }

    function renderResultBlock(d) {
        const container = document.getElementById('resultBlock');
        if (!d.finalCause) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = `
            <h3 class="section-title">نتيجة التشريح</h3>
            <div class="follow-card">
                <div class="follow-card-header">
                    <div>
                        <div class="follow-vet">${d.docBy}</div>
                        <div class="follow-date">تاريخ التوثيق: ${d.docDate}</div>
                    </div>
                </div>
                <div class="follow-field">
                    <div class="follow-field-label">سبب النفوق النهائي (المُوثَّق) <span class="req">*</span></div>
                    <div class="follow-field-value" style="color:#15803d;">${d.finalCause}</div>
                </div>
                <div class="follow-field">
                    <div class="follow-field-label">ملاحظات التشريح</div>
                    <div class="follow-field-value">${d.docNotes || '—'}</div>
                </div>
                <div class="follow-field">
                    <div class="follow-field-label">تقرير الصفة التشريحية</div>
                    <a href="#" class="report-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        عرض / تحميل التقرير
                    </a>
                </div>
            </div>`;
    }

    function loadDetails() {
        const d = autopsyDB[refId] || autopsyDB['AR-001'];
        const isPending = !d.finalCause;

        document.getElementById('breadRefId').textContent = 'تفاصيل الإحالة ' + refId;
        document.getElementById('topRefId').textContent = refId;
        document.getElementById('sRefId').textContent = refId;

        const badge = document.getElementById('headerBadge');
        if (isPending) {
            badge.innerHTML = '<span class="badge status-pending"><span class="dot"></span>بانتظار التوثيق</span>';
            document.getElementById('headerActions').innerHTML = `
                <button class="btn-document" onclick="showDocumentModal()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    توثيق نتيجة التشريح
                </button>`;
        } else {
            badge.innerHTML = '<span class="badge status-documented"><span class="dot"></span>موثقة</span>';
            document.getElementById('headerActions').innerHTML = '';
        }

        document.getElementById('sRefDate').textContent = d.refDate;
        document.getElementById('sMortalityId').textContent = d.mortalityId;
        document.getElementById('sSupervisor').textContent = d.supervisor;
        document.getElementById('sTransferReason').textContent = d.transferReason;

        document.getElementById('sAnimalPhoto').textContent = d.animalEmoji;
        document.getElementById('sAnimalId').textContent = d.animalId;
        document.getElementById('sAnimalType').textContent = d.animalType;
        document.getElementById('sAnimalName').textContent = d.animalName || '—';
        document.getElementById('sGender').textContent = d.gender;
        document.getElementById('sAge').textContent = d.age;
        document.getElementById('sGroup').textContent = d.group;

        renderDeathCard(d);
        renderResultBlock(d);
    }

    function showDocumentModal() {
        document.getElementById('documentModal').classList.add('open');
    }

    function closeDocumentModal() {
        document.getElementById('documentModal').classList.remove('open');
    }

    function confirmDocument() {
        const deathCause = document.getElementById('deathCause').value;
        const autopsyNotes = document.getElementById('autopsyNotes').value;
        const documentationDate = document.getElementById('documentationDate').value;

        if (!deathCause.trim()) {
            alert('يرجى إدخال سبب النفوق النهائي');
            return;
        }

        closeDocumentModal();

        autopsyDB[refId].finalCause = deathCause;
        autopsyDB[refId].docNotes = autopsyNotes;
        autopsyDB[refId].docDate = documentationDate;
        autopsyDB[refId].docBy = 'رئيس قسم المستشفى البيطري';

        loadDetails();
        alert('تم حفظ نتيجة التشريح بنجاح');
    }

    window.onload = function() {
        loadDetails();
        const params = new URLSearchParams(window.location.search);
        if (params.get('document') === '1') {
            showDocumentModal();
        }
    };

    window.onclick = function(e) {
        if (e.target === document.getElementById('documentModal')) closeDocumentModal();
    };
</script>
@endsection
