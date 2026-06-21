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

@php
    $vetBase = ($readOnly ?? false) ? '/director/vet' : '/vet';
    $animal = $referral->animal;
    $mortality = $referral->mortalityCase;
    $isPending = $referral->status->value === 'pending';
    $displayCause = $mortality?->displayCause() ?? 'غير ظاهر';
@endphp

@section('content')

<div class="breadcrumb">
    <a href="{{ $vetBase }}/referrals/autopsy">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        إحالات التشريح
    </a>
    <span>/</span>
    <span style="color:#0f172a;">تفاصيل الإحالة {{ $referral->referral_number }}</span>
</div>

<div class="header-card">
    <div class="header-info">
        <h2>
            تفاصيل إحالة تشريح
            @if($isPending)
                <span class="badge status-pending"><span class="dot"></span>بانتظار التوثيق</span>
            @else
                <span class="badge status-documented"><span class="dot"></span>موثقة</span>
            @endif
        </h2>
        <div style="font-size:0.9rem; color:#64748b; font-weight:700; margin-top:8px;">
            رقم الإحالة: <span class="id-tag">{{ $referral->referral_number }}</span>
        </div>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        @if(($canDocument ?? false) && $isPending)
            <button type="button" class="btn-document" onclick="showDocumentModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                توثيق نتيجة التشريح
            </button>
        @endif
        <a href="{{ $vetBase }}/referrals/autopsy" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            العودة
        </a>
    </div>
</div>

<div class="tabs-container">
    <div class="tabs-header">
        <button type="button" class="tab-btn active" onclick="switchTab(1, this)">ملخص الإحالة</button>
        <button type="button" class="tab-btn" onclick="switchTab(2, this)">بيانات النفوق قبل التشريح</button>
    </div>

    <div class="tab-content active" id="tab-1">
        <div class="summary-layout">
            <div class="animal-card">
                <h4 class="animal-card-title">بيانات الحيوان</h4>
                <div class="animal-photo-wrap"><div class="animal-photo">🐾</div></div>
                <div class="q-row"><span class="q-label">رقم الحيوان</span><span class="q-value id-tag">{{ $animal?->code ?? $mortality?->subject_code ?? '—' }}</span></div>
                <div class="q-row"><span class="q-label">نوع الحيوان</span><span class="q-value">{{ $animal?->species ?? $mortality?->subject_type ?? '—' }}</span></div>
                <div class="q-row"><span class="q-label">اسم الحيوان</span><span class="q-value">{{ $animal?->name ?? '—' }}</span></div>
                <div class="q-row"><span class="q-label">الجنس</span><span class="q-value">{{ $animal?->gender ?? '—' }}</span></div>
                <div class="q-row"><span class="q-label">العمر</span><span class="q-value">{{ $animal?->formattedAge() ?? '—' }}</span></div>
                <div class="q-row"><span class="q-label">المجموعة</span><span class="q-value">{{ $referral->group }}</span></div>
            </div>
            <div>
                <h3 class="section-title">معلومات الإحالة</h3>
                <div class="info-grid" style="margin-bottom:1.5rem;">
                    <div class="info-cell"><div class="info-cell-label">رقم الإحالة</div><div class="info-cell-value id-tag">{{ $referral->referral_number }}</div></div>
                    <div class="info-cell"><div class="info-cell-label">تاريخ الإحالة</div><div class="info-cell-value">{{ $referral->referred_at?->format('Y-m-d') }}</div></div>
                    <div class="info-cell"><div class="info-cell-label">رقم حالة النفوق</div><div class="info-cell-value id-tag">{{ $mortality?->case_number ?? '—' }}</div></div>
                    <div class="info-cell"><div class="info-cell-label">المشرف المسجل</div><div class="info-cell-value">{{ $mortality?->supervisor?->name ?? '—' }}</div></div>
                    @if($referral->transfer_reason)
                        <div class="info-cell span-2"><div class="info-cell-label">سبب الإحالة للتشريح</div><div class="info-cell-value">{{ $referral->transfer_reason }}</div></div>
                    @endif
                </div>
                @unless($isPending)
                    <h3 class="section-title">نتيجة التشريح</h3>
                    <div class="follow-card">
                        <div class="follow-card-header">
                            <span class="follow-vet">{{ $referral->documenter?->name ?? 'رئيس المستشفى البيطري' }}</span>
                            <span class="follow-date">{{ $referral->documented_at?->format('Y-m-d') }}</span>
                        </div>
                        <div class="follow-field">
                            <div class="follow-field-label">سبب النفوق النهائي</div>
                            <div class="follow-field-value">{{ $referral->final_death_cause }}</div>
                        </div>
                        @if($referral->autopsy_notes)
                            <div class="follow-field">
                                <div class="follow-field-label">ملاحظات التشريح</div>
                                <div class="follow-field-value">{{ $referral->autopsy_notes }}</div>
                            </div>
                        @endif
                        @if($referral->report_path)
                            <div class="follow-field">
                                <div class="follow-field-label">تقرير الصفة التشريحية</div>
                                <a href="{{ $vetBase }}/referrals/autopsy/{{ $referral->referral_number }}/report" class="report-link" target="_blank">عرض / تحميل التقرير</a>
                            </div>
                        @endif
                    </div>
                @endunless
            </div>
        </div>
    </div>

    <div class="tab-content" id="tab-2">
        <h3 class="section-title">البيانات المسجلة عند إرسال الإحالة</h3>
        <div class="follow-card">
            <div class="follow-card-header">
                <span class="follow-vet">{{ $mortality?->supervisor?->name ?? '—' }}</span>
                <span class="follow-date">تاريخ النفوق: {{ $mortality?->death_date?->format('Y-m-d') ?? '—' }}</span>
            </div>
            <div class="follow-field">
                <div class="follow-field-label">سبب النفوق (من المشرف)</div>
                <div class="follow-field-value">{{ $displayCause }}</div>
            </div>
            @if($mortality?->notes)
                <div class="follow-field">
                    <div class="follow-field-label">الملاحظات</div>
                    <div class="follow-field-value">{{ $mortality->notes }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($canDocument ?? false)
<div class="modal-backdrop" id="documentModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>توثيق نتيجة التشريح</h3>
            <button type="button" class="modal-close" onclick="closeDocumentModal()">&times;</button>
        </div>
        <form method="POST" action="{{ $vetBase }}/referrals/autopsy/{{ $referral->referral_number }}/document" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="modal-section">
                    <div class="modal-label">سبب النفوق النهائي <span style="color:#ef4444;">*</span></div>
                    <textarea class="modal-textarea" name="final_death_cause" required placeholder="يرجى إدخال سبب النفوق النهائي..."></textarea>
                </div>
                <div class="modal-section">
                    <div class="modal-label">ملاحظات التشريح</div>
                    <textarea class="modal-textarea" name="autopsy_notes" placeholder="اختياري..."></textarea>
                </div>
                <div class="modal-section">
                    <div class="modal-label">تقرير الصفة التشريحية</div>
                    <input type="file" name="report" accept="image/*,.pdf" class="modal-input">
                </div>
                <div class="modal-section">
                    <div class="modal-label">تاريخ التوثيق</div>
                    <input type="date" name="documented_at" class="modal-input" value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal btn-modal-cancel" onclick="closeDocumentModal()">إلغاء</button>
                <button type="submit" class="btn-modal btn-modal-confirm">حفظ النتيجة</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    function switchTab(n, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + n).classList.add('active');
        btn.classList.add('active');
    }
    function showDocumentModal() { document.getElementById('documentModal')?.classList.add('open'); }
    function closeDocumentModal() { document.getElementById('documentModal')?.classList.remove('open'); }
    window.onclick = function(e) { if (e.target === document.getElementById('documentModal')) closeDocumentModal(); };
    @if(request()->query('document') === '1' && ($canDocument ?? false))
        document.addEventListener('DOMContentLoaded', showDocumentModal);
    @endif
</script>
@endsection
