@extends($__layout ?? 'admin.layout')
@section('title', 'معلومات الزيارة | Tripoli Zoo')
@section('page_title', 'إدارة معلومات الزيارة')

@section('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --primary-gradient: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        --accent-gradient: linear-gradient(135deg, #E8651A 0%, #f97316 100%);
        --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.02);
        --card-hover-shadow: 0 20px 40px -15px rgba(45, 90, 39, 0.12);
    }

    .visit-container {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 2rem;
        align-items: start;
        perspective: 1000px;
    }

    /* Hero Banner for Visit Info */
    .visit-hero {
        grid-column: span 2;
        background: var(--primary-gradient);
        border-radius: 24px;
        padding: 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 35px -10px rgba(30, 58, 30, 0.3);
        margin-bottom: 0.5rem;
    }

    .visit-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 20%, rgba(232, 101, 26, 0.15) 0%, transparent 50%);
    }

    .visit-hero-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
    }

    .visit-hero h2 {
        font-size: 2rem;
        font-weight: 900;
        margin: 0 0 10px;
        color: #F8FAF6;
    }

    .visit-hero p {
        font-size: 0.95rem;
        color: #E2E8F0;
        line-height: 1.6;
        margin: 0;
    }

    /* Cards Styling */
    .premium-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .premium-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-hover-shadow);
        border-color: rgba(45, 90, 39, 0.2);
    }

    .card-accent-header {
        padding: 1.5rem 1.8rem;
        background: linear-gradient(to left, rgba(45, 90, 39, 0.03), transparent);
        border-bottom: 1.5px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-accent-header h3 {
        font-size: 1.15rem;
        font-weight: 900;
        color: #1e3a1e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .icon-wrapper {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(45, 90, 39, 0.1);
        color: #2d5a27;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .premium-card:nth-child(2) .icon-wrapper {
        background: rgba(232, 101, 26, 0.1);
        color: #E8651A;
    }

    .premium-card:nth-child(3) .icon-wrapper {
        background: rgba(14, 165, 233, 0.1);
        color: #0ea5e9;
    }

    .premium-card-body {
        padding: 1.8rem;
    }

    /* Grid layouts */
    .premium-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .grid-cell {
        background: rgba(248, 250, 252, 0.7);
        border: 1px solid rgba(226, 232, 240, 0.5);
        padding: 1.2rem;
        border-radius: 16px;
        transition: background 0.2s;
    }

    .grid-cell:hover {
        background: #ffffff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.01);
    }

    .grid-cell label {
        display: block;
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 800;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .grid-cell span {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-main);
    }

    /* Rules */
    .luxury-rules {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .luxury-rule-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 1.1rem 1.4rem;
        background: rgba(248, 250, 252, 0.7);
        border: 1px solid rgba(226, 232, 240, 0.5);
        border-radius: 16px;
        font-weight: 700;
        font-size: 0.92rem;
        color: var(--text-main);
    }

    .rule-bullet {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 900;
    }

    .rule-bullet.success { background: #DCFCE7; color: #166534; }
    .rule-bullet.danger  { background: #FEE2E2; color: #991B1B; }

    /* Side Panel */
    .side-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .action-card {
        padding: 2rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid var(--border);
        border-radius: 24px;
        box-shadow: var(--card-shadow);
        text-align: center;
    }

    .btn-luxury-action {
        width: 100%;
        padding: 14px;
        background: var(--accent-gradient);
        color: white;
        border: none;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        box-shadow: 0 6px 20px rgba(232, 101, 26, 0.3);
        transition: all 0.3s;
    }

    .btn-luxury-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(232, 101, 26, 0.4);
    }

    .meta-time {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 12px;
        font-weight: 700;
    }

    /* Facility rows */
    .facility-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .facility-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        background: white;
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: 12px;
        transition: all 0.2s;
    }

    .facility-item:hover {
        border-color: rgba(45, 90, 39, 0.15);
        background: rgba(248, 250, 252, 0.5);
    }

    .facility-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .facility-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--green);
    }

    .facility-dot.maintenance { background: #E8651A; }
    .facility-dot.closed { background: #EF4444; }

    .facility-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .status-pill {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 900;
    }

    .status-pill.active { background: #DCFCE7; color: #166534; }
    .status-pill.maintenance { background: #FEF3C7; color: #92400E; }
    .status-pill.closed { background: #FEE2E2; color: #991B1B; }

    .status-text-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .status-text-value {
        flex: 1;
        min-width: 200px;
        font-size: 1rem;
        font-weight: 800;
        color: #854d0e;
    }

    .btn-visibility {
        padding: 8px 16px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        background: white;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.82rem;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-visibility.visible { color: #166534; border-color: #86EFAC; background: #F0FDF4; }
    .btn-visibility.hidden  { color: #991B1B; border-color: #FECACA; background: #FEF2F2; }

    .entry-instructions {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.8;
        margin: 0;
        white-space: pre-line;
    }

    @media (max-width: 900px) {
        .visit-container { grid-template-columns: 1fr; }
        .visit-hero { grid-column: span 1; }
        .premium-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="visit-container">

    <!-- Main Section -->
    <div class="main-panel" style="grid-column: span 2;">
        <div style="display: flex; flex-direction: column; gap: 1.8rem;">
            
            <!-- Current Status & Urgent Alert -->
            <div class="premium-card" style="border-right: 5px solid #eab308; background: rgba(254, 243, 199, 0.4);">
                <div class="card-accent-header" style="background: transparent; border-bottom: none; padding-bottom: 0.5rem;">
                    <div class="icon-wrapper" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <h3 style="color: #854d0e;">حالة التشغيل والتنبيهات النشطة للزوار</h3>
                </div>
                <div class="premium-card-body" style="padding-top: 0.5rem;">
                    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: stretch;">
                        <div class="grid-cell" style="flex: 1; min-width: 250px; background: white; border: 1.5px solid #fef08a;">
                            <label style="color: #854d0e;">حالة التشغيل</label>
                            <div class="status-text-row" style="margin-top: 8px;">
                                <span class="status-text-value" id="statusText">مفتوحة جزئياً — بعض الأقسام تحت الصيانة</span>
                                <button type="button" class="btn-visibility visible" id="statusVisBtn" onclick="toggleStatusVisibility()">👁 ظاهر للزوار</button>
                            </div>
                        </div>
                        <div class="grid-cell" style="flex: 2; min-width: 300px; background: white; border: 1.5px solid #fef08a;">
                            <label style="color: #854d0e;">التنبيه العاجل للزوار (يظهر في واجهة التطبيق)</label>
                            <span style="font-size: 0.95rem; color: #451a03; display: block; margin-top: 4px; font-weight: 700;">
                                ⚠️ نود إحاطة زوارنا الكرام بأن "منطقة الطيور البرية" ومبنى "القبة الفلكية" مغلقان حالياً لأعمال الصيانة الدورية وتحديث المرافق.
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contacts -->
            <div class="premium-card">
                <div class="card-accent-header">
                    <div class="icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <h3>أرقام الطوارئ والأمن</h3>
                </div>
                <div class="premium-card-body">
                    <div class="premium-grid">
                        <div class="grid-cell">
                            <label>رقم الإسعاف</label>
                            <span dir="ltr">193</span>
                        </div>
                        <div class="grid-cell">
                            <label>رقم الأمن</label>
                            <span dir="ltr">091-555-0123</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entry Instructions -->
            <div class="premium-card">
                <div class="card-accent-header">
                    <div class="icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    </div>
                    <h3>تعليمات الدخول</h3>
                </div>
                <div class="premium-card-body">
                    <p class="entry-instructions">• يرجى الحضور من البوابة الرئيسية مع التذكرة الإلكترونية أو الورقية.
• يُمنع إدخال الطعام والمشروبات من خارج الحديقة.
• يلزم مرافقة الأطفال دون سن 12 سنة في جميع الأوقات.
• يُرجى الالتزام بالمسارات المحددة وعدم تسلق الأسوار أو الأقفاص.</p>
                </div>
            </div>

            <!-- Opening Hours -->
            <div class="premium-card">
                <div class="card-accent-header">
                    <div class="icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <h3>أوقات ومواعيد العمل الرسمية</h3>
                </div>
                <div class="premium-card-body">
                    <div class="premium-grid">
                        <div class="grid-cell">
                            <label>أيام العمل</label>
                            <span>كل أيام الأسبوع (السبت — الخميس)</span>
                        </div>
                        <div class="grid-cell">
                            <label>مواعيد الدوام اليومي</label>
                            <span>09:00 ص — 06:00 م</span>
                        </div>
                        <div class="grid-cell">
                            <label>يوم الإغلاق الأسبوعي</label>
                            <span>الجمعة</span>
                        </div>
                        <div class="grid-cell">
                            <label>آخر دخول متاح للزوار</label>
                            <span>قبل ساعة واحدة من موعد الإغلاق</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons at bottom -->
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                <a href="/admin/visit-info/edit" class="btn-luxury-action" style="width: auto; padding: 12px 30px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    تعديل معلومات الزيارة
                </a>
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    let statusVisible = true;

    function toggleStatusVisibility() {
        statusVisible = !statusVisible;
        const btn = document.getElementById('statusVisBtn');
        if (statusVisible) {
            btn.textContent = '👁 ظاهر للزوار';
            btn.className = 'btn-visibility visible';
        } else {
            btn.textContent = '🚫 مخفي عن الزوار';
            btn.className = 'btn-visibility hidden';
        }
    }
</script>
@endsection
