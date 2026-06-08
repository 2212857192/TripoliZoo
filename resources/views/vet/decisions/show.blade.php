@extends($__layout ?? 'vet.layout')
@section('title', 'تفاصيل القرار الطبي | المستشفى البيطري')
@section('page_title', 'تفاصيل القرار الطبي')

@section('styles')
<style>
/* ═══ PAGE HEADER ═══ */
.page-header {
    background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 40%, #43A047 100%);
    border-radius: 20px;
    padding: 1.8rem 2.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
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
.header-left h2 { font-size: 1.45rem; font-weight: 900; color: #fff; margin-bottom: 5px; }
.header-left p  { font-size: 0.85rem; color: rgba(255,255,255,0.6); font-weight: 500; }
.header-right { display: flex; align-items: center; gap: 1rem; position: relative; z-index: 2; }
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

/* ═══ DECISION CARD ═══ */
.decision-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.decision-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e8edf5;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.decision-header h3 { font-size: 1.2rem; font-weight: 900; color: #0f172a; }
.decision-body { padding: 2rem; }
.decision-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}
.info-item { padding: 0.8rem 0; }
.info-item-label { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; }
.info-item-value { font-size: 0.85rem; font-weight: 700; color: #0f172a; }

/* ═══ DECISION CONTENT ═══ */
.decision-content {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 1.5rem;
}
.decision-content-label {
    font-size: 0.8rem;
    font-weight: 800;
    color: #2E7D32;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.decision-content-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #334155;
    line-height: 1.8;
}

/* ═══ STATUS BADGES ═══ */
.badge { padding: 4px 10px; border-radius: 18px; font-size: 0.72rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }
.badge .dot { width: 5px; height: 5px; border-radius: 50%; }
.badge-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.badge-pending .dot { background: #f59e0b; }
.badge-completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.badge-completed .dot { background: #22c55e; }
.badge-none { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
.badge-none .dot { background: #94a3b8; }

/* ═══ ACTION BUTTONS ═══ */
.actions-bar {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-family: 'Cairo', sans-serif;
    font-size: 0.9rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid;
    text-decoration: none;
}
.btn-export {
    background: #f97316;
    color: #fff;
    border-color: #f97316;
}
.btn-export:hover { background: #ea580c; }
.btn-print {
    background: #fff;
    color: #334155;
    border-color: #e2e8f0;
}
.btn-print:hover { background: #f8fafc; border-color: #94a3b8; }
</style>
@endsection

@section('content')
{{-- ═══ PAGE HEADER ═══ --}}
<div class="page-header">
    <div class="header-left">
        <h2>📋 تفاصيل قرار طبي - 001-2025-MD</h2>
        <p>نسر ذهبي</p>
    </div>
    <div class="header-right">
        <div class="case-status-badge">
            <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;"></span>
            إفراج صحي
        </div>
        <button class="btn-action btn-export" style="padding: 10px 20px; font-size: 0.85rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            تصدير نموذج قرار طبي
        </button>
        <a href="/vet/decisions" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            رجوع
        </a>
    </div>
</div>

{{-- ═══ DECISION CARD ═══ --}}
<div class="decision-card">
    <div class="decision-header">
        <h3>بيانات القرار الطبي</h3>
        <span class="badge badge-pending"><span class="dot"></span>بانتظار الاستلام</span>
    </div>
    <div class="decision-body">
        <div class="decision-grid">
            <div class="info-item">
                <div class="info-item-label">رقم القرار</div>
                <div class="info-item-value">001-2025-MD</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">نوع القرار</div>
                <div class="info-item-value">إفراج صحي</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">رقم الحيوان</div>
                <div class="info-item-value">#ANM-009</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">نوع الحيوان</div>
                <div class="info-item-value">نسر ذهبي</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">مصدر القرار</div>
                <div class="info-item-value">حجر صحي</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">تاريخ القرار</div>
                <div class="info-item-value">2025-05-13</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">حالة الاستلام</div>
                <div class="info-item-value"><span class="badge badge-pending"><span class="dot"></span>بانتظار الاستلام</span></div>
            </div>
            <div class="info-item">
                <div class="info-item-label">الطبيب المعتمد</div>
                <div class="info-item-value">د. أسامة الورفلي</div>
            </div>
        </div>

        <div class="decision-content">
            <div class="decision-content-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                نص القرار الطبي
            </div>
            <div class="decision-content-text">
                بناءً على الفحوصات الطبية المتكاملة واكتمال فترة الحجر الصحي المقررة لحالة النسر الذهبي (رقم الحيوان: #ANM-009)، وتبين خلو الحيوان من أي أمراض معدية أو طفيلية، يقرر المستشفى البيطري إخلاء الحيوان من الحجر الصحي وإعادته إلى حظيرته الأصلية في قسم الطيور الكبرى.
            </div>
        </div>
    </div>
</div>

@endsection
