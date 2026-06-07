@extends('vet.layout')
@section('title', 'تفاصيل حالة طبية ميدانية | المستشفى البيطري')
@section('page_title', 'تفاصيل حالة طبية ميدانية')

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
.case-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.4);
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 800;
    color: #fff;
}
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

/* ═══ SECTION TITLE ═══ */
.section-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
}

/* ═══ SUMMARY CARD ═══ */
.summary-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}
.summary-body { padding: 2rem; }
.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.2rem;
}
.info-item { padding: 0.8rem 0; }
.info-item-label { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 6px; }
.info-item-value { font-size: 0.9rem; font-weight: 700; color: #0f172a; }
.info-item.full { grid-column: span 2; }
.info-box {
    background: #f8fafc;
    padding: 1rem 1.4rem;
    border-radius: 10px;
    border-left: 3px solid #2E7D32;
    font-size: 0.88rem;
    font-weight: 600;
    color: #334155;
    line-height: 1.7;
}

/* ═══ TIMELINE SECTION ═══ */
.timeline-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.timeline-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.timeline-card:hover {
    box-shadow: 0 4px 16px rgba(46,125,50,0.1);
    border-color: #2E7D32;
}
.timeline-card-header {
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e8edf5;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.timeline-date {
    font-size: 0.8rem;
    font-weight: 800;
    color: #2E7D32;
    background: #E8F5E9;
    padding: 6px 14px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.timeline-vet {
    font-size: 0.82rem;
    font-weight: 700;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
}
.timeline-card-body {
    padding: 1.5rem;
}
.timeline-section {
    margin-bottom: 1.2rem;
    padding-bottom: 1.2rem;
    border-bottom: 1px dashed #e8edf5;
}
.timeline-section:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
.timeline-section-label {
    font-size: 0.75rem;
    font-weight: 800;
    color: #2E7D32;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.timeline-section-content {
    font-size: 0.88rem;
    font-weight: 600;
    color: #334155;
    line-height: 1.7;
    background: #f8fafc;
    padding: 1rem 1.4rem;
    border-radius: 10px;
    border-left: 3px solid #2E7D32;
}
.timeline-card-footer {
    padding: 0.8rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-top: 1px solid #e8edf5;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.timeline-card-status {
    font-size: 0.78rem;
    font-weight: 700;
    color: #64748b;
}
</style>
@endsection

@section('content')
{{-- ═══ PAGE HEADER ═══ --}}
<div class="page-header">
    <div class="header-left">
        <h2>تفاصيل حالة طبية ميدانية - FC-2025-001</h2>
        <p>شمبانزي أفريقي — د. ريم الفصل</p>
    </div>
    <div class="header-right">
        <div class="case-status-badge">
            <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;"></span>
            مفتوحة
        </div>
        <a href="/vet/cases/field" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            العودة
        </a>
    </div>
</div>

{{-- ═══ SECTION 1: ملخص الحالة ═══ --}}
<div class="section-title">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
    ملخص الحالة
</div>
<div class="summary-card">
    <div class="summary-body">
        <div class="summary-grid">
            <div class="info-item">
                <div class="info-item-label">رقم الحالة</div>
                <div class="info-item-value">FC-2025-001</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">رقم الحيوان</div>
                <div class="info-item-value">#ANL-0871</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">نوع الحيوان</div>
                <div class="info-item-value">شمبانزي</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">الجنس</div>
                <div class="info-item-value">ذكر</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">العمر</div>
                <div class="info-item-value">6 سنوات</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">المجموعة</div>
                <div class="info-item-value">القرود</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">الطبيب المسؤول</div>
                <div class="info-item-value">د. ريم الفيصل</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">تاريخ فتح الحالة</div>
                <div class="info-item-value">2025-05-13</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">حالة الحالة</div>
                <div class="info-item-value">مفتوحة</div>
            </div>
            <div class="info-item full">
                <div class="info-item-label">سبب فتح الحالة</div>
                <div class="info-box">معاينة جرح بسيط داخل بيت الحيوان</div>
            </div>
            <div class="info-item full">
                <div class="info-item-label">ملاحظات أولية</div>
                <div class="info-box">الحيوان لديه جرح بسيط في الطرف الأمامي ولا يحتاج نقله للمستشفى</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ SECTION 2: المتابعة الطبية الميدانية ═══ --}}
<div class="section-title">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
    المتابعة الطبية الميدانية
</div>
<div class="timeline-container">
    {{-- Card 1: Newest --}}
    <div class="timeline-card">
        <div class="timeline-card-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="timeline-date">2025-05-13 — 10:30</div>
                <div class="timeline-vet">د. ريم الفيصل</div>
            </div>
        </div>
        <div class="timeline-card-body">
            <div class="timeline-section">
                <div class="timeline-section-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    التشخيص
                </div>
                <div class="timeline-section-content">جرح سطحي في الطرف الأمامي</div>
            </div>
            <div class="timeline-section">
                <div class="timeline-section-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    الإجراء الطبي / العلاج
                </div>
                <div class="timeline-section-content">تنظيف الجرح ووضع مطهر موضعي</div>
            </div>
            <div class="timeline-section">
                <div class="timeline-section-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    ملاحظات الطبيب
                </div>
                <div class="timeline-section-content">الحيوان مستقر ولا يحتاج إدخالاً للمستشفى</div>
            </div>
            <div class="timeline-section">
                <div class="timeline-section-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                    توصية غذائية / علاجية
                </div>
                <div class="timeline-section-content">فاكهة طرية لمدة يومين</div>
            </div>
        </div>
        <div class="timeline-card-footer">
            <div class="timeline-card-status">
                <span style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;padding:4px 10px;border-radius:18px;font-size:0.72rem;font-weight:800;">مفتوحة</span>
            </div>
        </div>
    </div>
</div>
@endsection
