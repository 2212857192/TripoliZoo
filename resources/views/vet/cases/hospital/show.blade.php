@extends('vet.layout')
@section('title', 'HC-2025-001 — تفاصيل الحالة | المستشفى البيطري')
@section('page_title', 'HC-2025-001 — تفاصيل الحالة')

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

/* ═══ TABS ═══ */
.tabs-container {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e8edf5;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.tabs-header {
    display: flex;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(to left, #fafbff, #fff);
}

.tab-btn {
    flex: 1;
    padding: 1rem 1.5rem;
    background: none;
    border: none;
    font-family: 'Cairo', sans-serif;
    font-size: 0.9rem;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    border-bottom: 3px solid transparent;
}

.tab-btn:hover {
    color: #2E7D32;
    background: #f8fafc;
}

.tab-btn.active {
    color: #2E7D32;
    border-bottom-color: #2E7D32;
    background: #f8fafc;
}

.tab-content {
    display: none;
    padding: 2rem;
}

.tab-content.active {
    display: block;
}

/* ═══ SUMMARY SECTION ═══ */
.summary-card {
    background: #fafbff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    padding: 2rem;
}

.summary-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e8edf5;
}

.animal-avatar {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
    border: 3px solid #2E7D32;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    flex-shrink: 0;
}

.animal-info h3 {
    font-size: 1.2rem;
    font-weight: 900;
    color: #0f172a;
    margin-bottom: 0.3rem;
}

.animal-info p {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 10px;
    padding: 1rem;
}

.info-item-label {
    font-size: 0.72rem;
    font-weight: 800;
    color: #64748b;
    margin-bottom: 4px;
}

.info-item-value {
    font-size: 0.85rem;
    font-weight: 700;
    color: #0f172a;
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
    box-shadow: 0 4px 16px rgba(46, 125, 50, 0.1);
    border-color: #2E7D32;
}

.timeline-card-header {
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e8edf5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
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
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0 2rem;
}

.timeline-card.expanded .timeline-card-body {
    max-height: 1000px;
    padding: 2rem;
}

.timeline-section {
    margin-bottom: 1.2rem;
    padding-bottom: 1.2rem;
    border-bottom: 1px dashed #e8edf5;
}

.timeline-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

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

.timeline-actions {
    display: flex;
    gap: 8px;
}

.btn-timeline-action {
    padding: 6px 12px;
    border-radius: 6px;
    font-family: 'Cairo', sans-serif;
    font-size: 0.72rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid;
    text-decoration: none;
}

.btn-timeline-edit {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}

.btn-timeline-edit:hover {
    background: #dbeafe;
}

.expand-icon {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.timeline-card.expanded .expand-icon {
    transform: rotate(180deg);
}

.add-followup-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #14532d, #15803d);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: 'Cairo', sans-serif;
    font-size: 0.9rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(21, 128, 61, 0.3);
}

.add-followup-btn:hover {
    background: linear-gradient(135deg, #1a5c2e, #1d8a4a);
    transform: translateY(-1px);
}

.actions-bar {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
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

.btn-decision {
    background: linear-gradient(135deg, #14532d, #15803d);
    color: #fff;
    border-color: #15803d;
    box-shadow: 0 4px 12px rgba(21, 128, 61, 0.3);
}

.btn-decision:hover {
    background: linear-gradient(135deg, #1a5c2e, #1d8a4a);
    transform: translateY(-1px);
}
</style>
@endsection

@section('content')
{{-- ═══ PAGE HEADER ═══ --}}
<div class="page-header">
    <div class="header-left">
        <h2>🏥 HC-2025-001 — تفاصيل الحالة</h2>
        <p>الأسد الإفريقي (سيمبا)</p>
    </div>
    <div class="header-right">
        <div class="case-status-badge">
            <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;"></span>
            جاهز للخروج
        </div>
        <button class="btn-action btn-decision" onclick="openDecisionModal()" style="padding:10px 20px;background:linear-gradient(135deg,#14532d,#15803d);color:#fff;border:none;border-radius:12px;font-family:'Cairo',sans-serif;font-size:0.85rem;font-weight:800;cursor:pointer;transition:all 0.2s;box-shadow:0 4px 12px rgba(21,128,61,0.3);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            إصدار قرار
        </button>
        <a href="/vet/cases/hospital" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            العودة للحالات
        </a>
    </div>
</div>

{{-- ═══ TABS CONTAINER ═══ --}}
<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab('summary')">📋 ملخص الحالة</button>
        <button class="tab-btn" onclick="switchTab('followup')">� المتابعة الطبية</button>
    </div>

    {{-- Tab 1: Summary --}}
    <div class="tab-content active" id="tab-summary">
        <div class="summary-card">
            <div class="summary-header">
                <div class="animal-avatar">🦁</div>
                <div class="animal-info">
                    <h3>الأسد الإفريقي (سيمبا)</h3>
                    <p>HC-2025-001 — #ANM-101</p>
                </div>
            </div>
            
            <div class="summary-grid">
                <div class="info-item">
                    <div class="info-item-label">رقم الحالة</div>
                    <div class="info-item-value">HC-2025-001</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">رقم الحيوان</div>
                    <div class="info-item-value">#ANM-101</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">نوع الحيوان</div>
                    <div class="info-item-value">أسد إفريقي</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">الجنس</div>
                    <div class="info-item-value">
                        <span style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;padding:3px 9px;border-radius:6px;font-size:0.72rem;font-weight:700;">ذكر</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">العمر</div>
                    <div class="info-item-value">6 سنوات</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">المجموعة</div>
                    <div class="info-item-value">القطط الكبرى</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">تاريخ دخول المستشفى</div>
                    <div class="info-item-value">2026-05-30</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">المسؤول الطبي</div>
                    <div class="info-item-value">د. خالد العربي</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">حالة الحالة</div>
                    <div class="info-item-value">
                        <span style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;padding:4px 10px;border-radius:18px;font-size:0.75rem;font-weight:800;">جاهز للخروج</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">سبب الإحالة</div>
                    <div class="info-item-value">إصابة في الطرف الأمامي</div>
                </div>
                <div class="info-item">
                    <div class="info-item-label">الملاحظات قبل المسجلة</div>
                    <div class="info-item-value">التحويل والملاحظات</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab 2: Medical Follow-up --}}
    <div class="tab-content" id="tab-followup">
        <div class="timeline-container">
            {{-- Card 1: Oldest --}}
            <div class="timeline-card" onclick="toggleTimeline(this)">
                <div class="timeline-card-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="timeline-date">2026-05-30 — 02:00 م</div>
                        <div class="timeline-vet">د. فاطمة الزهراء</div>
                    </div>
                    <svg class="expand-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="timeline-card-body">
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            التشخيص
                        </div>
                        <div class="timeline-section-content">جرح عميق في اليد اليسرى مفتوح مع عدوى بسيطة.</div>
                    </div>
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            الإجراءات العلاجية
                        </div>
                        <div class="timeline-section-content">تنظيف الجرح، خياطة، مضادات حيوية.</div>
                    </div>
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            الملاحظات
                        </div>
                        <div class="timeline-section-content">الحيوان هادئ ومتعاون مع الطبيب. ملاحظات وعصائر طرية فاكهة علاجية.</div>
                    </div>
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                            التوصيات الغذائية
                        </div>
                        <div class="timeline-section-content">توصية غذائية علاجية بعد الحيوان حالة الإجراء.</div>
                    </div>
                </div>
                <div class="timeline-card-footer">
                    <div class="timeline-card-status">
                        <span style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;padding:4px 10px;border-radius:18px;font-size:0.72rem;font-weight:800;">قيد العلاج</span>
                    </div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="timeline-card" onclick="toggleTimeline(this)">
                <div class="timeline-card-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="timeline-date">2026-06-01 — 09:15 ص</div>
                        <div class="timeline-vet">د. خالد العربي</div>
                    </div>
                    <svg class="expand-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="timeline-card-body">
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            التشخيص
                        </div>
                        <div class="timeline-section-content">لا علامات على التهاب. الجرح يلتئم بشكل جيد.</div>
                    </div>
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            الإجراءات العلاجية
                        </div>
                        <div class="timeline-section-content">تغيير الضمادة، إعطاء جرعة مضاد حيوي يومية.</div>
                    </div>
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            الملاحظات
                        </div>
                        <div class="timeline-section-content">الحيوان مستقر.</div>
                    </div>
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                            التوصيات الغذائية
                        </div>
                        <div class="timeline-section-content">لا توجد توصية غذائية.</div>
                    </div>
                </div>
                <div class="timeline-card-footer">
                    <div class="timeline-card-status">
                        <span style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;padding:4px 10px;border-radius:18px;font-size:0.72rem;font-weight:800;">قيد العلاج</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Newest --}}
            <div class="timeline-card" onclick="toggleTimeline(this)">
                <div class="timeline-card-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="timeline-date">2026-06-03 — 10:30 ص</div>
                        <div class="timeline-vet">د. ريم الفيصل</div>
                    </div>
                    <svg class="expand-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="timeline-card-body">
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            التشخيص
                        </div>
                        <div class="timeline-section-content">تحسن ملحوظ في حركة الطرف. الجرح في طور الالتئام.</div>
                    </div>
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            الإجراءات العلاجية
                        </div>
                        <div class="timeline-section-content">تغيير الضمادة، تنظيف الجرح، إعطاء مضادات حيوية وخياطة.</div>
                    </div>
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            الملاحظات
                        </div>
                        <div class="timeline-section-content">الحيوان هادئ ومتعاون مع الطبيب. الملاحظات وعصائر طرية فاكهة علاجية.</div>
                    </div>
                    <div class="timeline-section">
                        <div class="timeline-section-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                            التوصيات الغذائية
                        </div>
                        <div class="timeline-section-content">توصية غذائية بعد الحيوان حالة الإجراء.</div>
                    </div>
                </div>
                <div class="timeline-card-footer">
                    <div class="timeline-card-status">
                        <span style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;padding:4px 10px;border-radius:18px;font-size:0.72rem;font-weight:800;">جاهز للخروج</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ DECISION MODAL ═══ --}}
<div class="modal-backdrop" id="decisionModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;">
    <div class="decision-box" style="background:#fff;border-radius:20px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,0.22);animation:modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1);">
        <div class="decision-header" style="padding:1.4rem 1.6rem;background:linear-gradient(135deg,#14532d,#15803d);display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:1rem;font-weight:800;color:#fff;">✅ إصدار قرار طبي — الأسد الإفريقي (سيمبا)</h3>
            <button onclick="closeModal('decisionModal')" style="width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.15);border:none;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1.1rem;font-weight:700;transition:background 0.2s;">✕</button>
        </div>
        <div class="decision-body" style="padding:1.6rem;">
            <p style="font-size:0.83rem;color:#64748b;font-weight:600;margin-bottom:1.2rem;line-height:1.6;">
                اختر نوع القرار الطبي المناسب. سيُسجَّل القرار في قسم <strong>القرارات الطبية</strong> ويخرج الحيوان من قائمة الحالات النشطة.
            </p>
            <div class="decision-options" style="display:flex;flex-direction:column;gap:10px;margin-bottom:1.2rem;">
                <div class="decision-option" onclick="selectDecision(this)" style="padding:1rem 1.2rem;border:2px solid #e2e8f0;border-radius:12px;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:12px;">
                    <div class="opt-icon" style="width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;background:#f0fdf4;">🏠</div>
                    <div>
                        <div class="opt-title" style="font-size:0.88rem;font-weight:800;color:#0f172a;">خروج بعد العلاج</div>
                        <div class="opt-desc" style="font-size:0.74rem;color:#64748b;font-weight:600;">الحيوان تعافى وجاهز للعودة إلى موقعه في الحديقة</div>
                    </div>
                </div>
                <div class="decision-option" onclick="selectDecision(this)" style="padding:1rem 1.2rem;border:2px solid #e2e8f0;border-radius:12px;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:12px;">
                    <div class="opt-icon" style="width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;background:#fef2f2;">🛑</div>
                    <div>
                        <div class="opt-title" style="font-size:0.88rem;font-weight:800;color:#b91c1c;">ذبح اضطراري</div>
                        <div class="opt-desc" style="font-size:0.74rem;color:#64748b;font-weight:600;">إنهاء حياة الحيوان لأسباب طبية طارئة ولا يمكن علاجه</div>
                    </div>
                </div>
            </div>
            <div style="margin-top:1rem;">
                <label style="font-size:0.8rem;font-weight:800;color:#374151;display:block;margin-bottom:6px;">ملاحظة القرار (اختياري)</label>
                <textarea style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-family:'Cairo',sans-serif;font-size:0.83rem;font-weight:600;resize:vertical;min-height:70px;outline:none;" placeholder="أضف أي تفاصيل إضافية عن القرار..."></textarea>
            </div>
        </div>
        <div class="decision-footer" style="padding:1rem 1.6rem;border-top:1px solid #f1f5f9;display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="closeModal('decisionModal')" style="padding:10px 18px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:10px;font-family:'Cairo',sans-serif;font-size:0.88rem;font-weight:800;cursor:pointer;transition:background 0.2s;">إلغاء</button>
            <button style="padding:10px 22px;background:linear-gradient(135deg,#14532d,#15803d);color:#fff;border:none;border-radius:10px;font-family:'Cairo',sans-serif;font-size:0.88rem;font-weight:800;cursor:pointer;transition:all 0.2s;box-shadow:0 4px 12px rgba(21,128,61,0.3);">تأكيد وإصدار القرار</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    event.target.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

function toggleTimeline(card) {
    card.classList.toggle('expanded');
}

function openDecisionModal() {
    document.getElementById('decisionModal').style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
function selectDecision(el) {
    document.querySelectorAll('.decision-option').forEach(o => o.style.borderColor = '#e2e8f0');
    el.style.borderColor = '#15803d';
    el.style.background = '#f0fdf4';
}
document.getElementById('decisionModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
