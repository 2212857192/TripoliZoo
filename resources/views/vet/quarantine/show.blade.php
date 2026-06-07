@extends('vet.layout')
@section('title', 'تفاصيل الحجر الصحي | المستشفى البيطري')
@section('page_title', 'تفاصيل الحجر الصحي')

@section('styles')
<style>
.page-header {
    background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 40%, #43A047 100%);
    border-radius: 20px;
    padding: 2rem 2.5rem;
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

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (max-width: 900px) {
    .detail-grid { grid-template-columns: 1fr; }
}

.info-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e8edf5;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.info-card-header {
    padding: 1.1rem 1.5rem;
    display: flex; align-items: center; gap: 10px;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(to left, #fafbff, #fff);
}

.info-card-title {
    font-size: 0.98rem; font-weight: 800; color: #0f172a;
}

.info-card-body {
    padding: 1.5rem;
}

.animal-profile {
    text-align: center;
    padding: 2rem;
}

.animal-avatar {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
    border: 3px solid #2E7D32;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    margin: 0 auto 1.5rem;
}

.animal-name {
    font-size: 1.5rem;
    font-weight: 900;
    color: #0f172a;
    margin-bottom: 0.5rem;
}

.animal-id {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    background: #E8F5E9;
    color: #2E7D32;
    padding: 4px 12px;
    border-radius: 8px;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 1.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.8rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-row:last-child { border-bottom: none; }

.info-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
}

.info-value {
    font-size: 0.9rem;
    font-weight: 700;
    color: #0f172a;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 800;
}

.status-badge.monitoring {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}

.status-badge.ready {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.status-badge .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.status-badge.monitoring .dot { background: #f59e0b; }
.status-badge.ready .dot { background: #22c55e; }

.timeline-section {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e8edf5;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.timeline-title {
    font-size: 1.1rem;
    font-weight: 900;
    color: #0f172a;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 1rem;
    border-bottom: 2px solid #E8F5E9;
}

.timeline-item {
    display: flex;
    gap: 1.5rem;
    padding-bottom: 2rem;
    border-right: 3px solid #E8F5E9;
    padding-right: 1.5rem;
    position: relative;
    transition: all 0.3s ease;
}

.timeline-item:hover {
    border-right-color: #2E7D32;
}

.timeline-item:last-child {
    border-right: none;
    padding-bottom: 0;
}

.timeline-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2E7D32, #1B5E20);
    position: absolute;
    right: -9.5px;
    top: 0;
    box-shadow: 0 0 0 4px #fff, 0 0 0 6px #E8F5E9;
    transition: all 0.3s ease;
}

.timeline-item:hover .timeline-dot {
    box-shadow: 0 0 0 4px #fff, 0 0 0 6px #2E7D32;
}

.timeline-content {
    flex: 1;
}

.timeline-date {
    font-size: 0.8rem;
    font-weight: 800;
    color: #2E7D32;
    margin-bottom: 8px;
    background: #E8F5E9;
    padding: 4px 12px;
    border-radius: 6px;
    display: inline-block;
}

.timeline-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #334155;
    line-height: 1.8;
    background: #fafbff;
    padding: 1rem;
    border-radius: 10px;
    border: 1px solid #e8edf5;
}
</style>
@endsection

@section('content')
{{-- ═══ PAGE HEADER ═══ --}}
<div class="page-header">
    <div class="header-left">
        <h2>🔒 تفاصيل الحجر الصحي</h2>
        <p>معلومات متكاملة عن الحيوان الخاضع للحجر الصحي الوقائي</p>
    </div>
    <div class="header-right">
        <a href="/vet/quarantine" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            العودة للحجر الصحي
        </a>
    </div>
</div>

{{-- ═══ DETAIL GRID ═══ --}}
<div class="detail-grid">
    {{-- LEFT: Animal Profile --}}
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-title">🐾 بيانات الحيوان</div>
        </div>
        <div class="info-card-body">
            <div class="animal-profile">
                <div class="animal-avatar">🐯</div>
                <div class="animal-name">رعد</div>
                <div class="animal-id">#QRN-204</div>
                
                <div style="text-align: right; margin-top: 1.5rem;">
                    <div class="info-row">
                        <span class="info-label">النوع</span>
                        <span class="info-value">نمر بنغالي</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">الجنس</span>
                        <span class="info-value">
                            <span style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;padding:3px 9px;border-radius:6px;font-size:0.75rem;font-weight:700;">ذكر</span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">العمر</span>
                        <span class="info-value">4 سنوات</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">المصدر</span>
                        <span class="info-value">مركز الحياة البرية</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">تاريخ الدخول</span>
                        <span class="info-value">2026-06-01</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">حالة الحجر</span>
                        <span class="info-value">
                            <span class="status-badge ready">
                                <span class="dot"></span>
                                جاهز للإفراج
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Timeline & Notes --}}
    <div>
        {{-- Timeline Section --}}
        <div class="timeline-section">
            <div class="timeline-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                سجل المتابعة
            </div>
            
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-date">2026-06-03 — 10:30 ص</div>
                    <div class="timeline-text">الفحص الطبي اليومي: الحيوان في حالة صحية جيدة، لا توجد أعراض مرضية ظاهرة. الشهية والنشاط طبيعيين.</div>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-date">2026-06-02 — 09:15 ص</div>
                    <div class="timeline-text">فحص دوري: درجة الحرارة 38.5°C (طبيعية). الوزن مستقر. الحالة العامة ممتازة.</div>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-date">2026-06-01 — 02:00 م</div>
                    <div class="timeline-text">إدخال الحيوان للحجر الصحي: استلام من مركز الحياة البرية. التحقق من الوثائق الصحية. بدء فترة الملاحظة الوقائية (21 يوم).</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
