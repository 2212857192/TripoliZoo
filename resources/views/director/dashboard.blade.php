@extends('director.layout')
@section('title', 'لوحة التحكم | مدير الحديقة')
@section('page_title', 'لوحة التحكم')

@section('styles')
@include('director.partials.read-only-styles')
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>لوحة تحكم مدير الحديقة</h2>
            <p>متابعة وتحليل — عرض فقط بدون صلاحيات تنفيذ</p>
        </div>
        <div class="status-pill">
            <span class="pulse-dot"></span>
            عرض فقط
        </div>
    </div>
</div>

{{-- التبويبات — مباشرة تحت العنوان --}}
<div class="dashboard-tabs-card">
    <div class="dashboard-tabs-label">أقسام اللوحة</div>
    <div class="segmented-tabs">
        <button type="button" class="seg-tab active" data-tab="tab-overview">نظرة عامة</button>
        <button type="button" class="seg-tab" data-tab="tab-visits">الزيارات والتذاكر</button>
        <button type="button" class="seg-tab" data-tab="tab-ops">التشغيل</button>
        <button type="button" class="seg-tab" data-tab="tab-charts">التحليل</button>
        <button type="button" class="seg-tab" data-tab="tab-feed">
            المتابعة <span class="tab-badge">5</span>
        </button>
    </div>
</div>

{{-- ═══ تبويب 1: نظرة عامة ═══ --}}
<div id="tab-overview" class="dash-tab-content active">

    <div class="info-box">
        <strong>إجمالي الحيوانات داخل الحديقة</strong> يشمل: الحيوانات بعد الإفراج الصحي وتأكيد الاستلام، والمواليد قيد المتابعة أو المكتملة.
        لا يشمل: النافقة، الذبح الاضطراري، الخارجة، الحجر قبل الاستلام، أو حالات الحجر المنتهية غير المدخلة رسميًّا.
        <br><strong>مصدر العدد:</strong> الحيوانات داخل المجموعات + المضافة يدويًّا من مسؤول السجلات والتوثيق.
    </div>

    <div class="stats-grid-8">
        <a href="/director/records/animals" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
            <div class="stat-num">248</div>
            <div class="stat-label">إجمالي الحيوانات داخل الحديقة</div>
            <div class="stat-sub">+3 مواليد قيد المتابعة</div>
        </a>
        <a href="/director/care/health" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
            <div class="stat-num">7</div>
            <div class="stat-label">الحالات الصحية الجديدة</div>
            <div class="stat-sub warn">3 تحتاج إحالة علاج</div>
        </a>
        <a href="/director/vet/cases/hospital" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg></div>
            <div class="stat-num">5</div>
            <div class="stat-label">الحالات داخل المستشفى</div>
            <div class="stat-sub warn">2 جاهزة لقرار خروج / ذبح</div>
        </a>
        <a href="/director/vet/cases/field" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div>
            <div class="stat-num">4</div>
            <div class="stat-label">الحالات الطبية الميدانية</div>
            <div class="stat-sub warn">1 حالة تحتاج متابعة</div>
        </a>
        <a href="/director/vet/quarantine" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <div class="stat-num">6</div>
            <div class="stat-label">الحيوانات داخل الحجر الصحي</div>
            <div class="stat-sub">2 جاهزة للإفراج الصحي</div>
        </a>
        <a href="/director/care/births" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div class="stat-num">4</div>
            <div class="stat-label">المواليد الجديدة</div>
            <div class="stat-sub">1 قريب من إكمال 30 يومًا</div>
        </a>
        <a href="/director/care/mortality" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg></div>
            <div class="stat-num">2</div>
            <div class="stat-label">حالات النفوق</div>
            <div class="stat-sub danger">آخر 7 أيام — 1 بانتظار تشريح</div>
        </a>
        <a href="/director/records/logs/exits" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/></svg></div>
            <div class="stat-num">3</div>
            <div class="stat-label">الحيوانات الخارجة</div>
            <div class="stat-sub muted">3 حالات خروج هذا الشهر</div>
        </a>
    </div>

    <h3 class="section-heading">ملخص اليوم <span>2026-06-08</span></h3>
    <div class="today-grid">
        <div class="today-item"><div class="num">5</div><div class="lbl">حالات صحية مسجلة</div></div>
        <div class="today-item"><div class="num">2</div><div class="lbl">بلاغات صحية مرسلة</div></div>
        <div class="today-item"><div class="num">1</div><div class="lbl">ولادات جديدة</div></div>
        <div class="today-item"><div class="num">0</div><div class="lbl">حالات نفوق</div></div>
        <div class="today-item"><div class="num">6</div><div class="lbl">إجراءات طبية</div></div>
        <div class="today-item"><div class="num">3</div><div class="lbl">توصيات غذائية علاجية</div></div>
        <div class="today-item"><div class="num">2</div><div class="lbl">مهام استلام جديدة</div></div>
        <div class="today-item"><div class="num">126</div><div class="lbl">تذاكر مباعة</div></div>
    </div>
</div>

{{-- ═══ تبويب 2: الزيارات والتذاكر ═══ --}}
<div id="tab-visits" class="dash-tab-content">

    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom:1.5rem;">
        <a href="/director/admin/tickets" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <div class="stat-num">126</div>
            <div class="stat-label">تذاكر اليوم</div>
            <div class="stat-sub">+18% مقارنة بأمس</div>
        </a>
        <div class="stat-card" style="cursor:default;">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div class="stat-num">1,260</div>
            <div class="stat-label">إيرادات اليوم (د.ل)</div>
        </div>
        <div class="stat-card" style="cursor:default;">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div class="stat-num">126</div>
            <div class="stat-label">الزوار المتوقعون اليوم</div>
        </div>
    </div>

    <div class="chart-card" style="margin-bottom:1.5rem;">
        <h4>أكثر أنواع التذاكر مبيعًا اليوم</h4>
        <div class="ticket-types">
            <div class="ticket-type-pill"><span>80</span>بالغ</div>
            <div class="ticket-type-pill"><span>35</span>طفل</div>
            <div class="ticket-type-pill"><span>11</span>عائلة</div>
        </div>
    </div>

    <div class="chart-card">
        <h4>حركة التذاكر — آخر 7 أيام</h4>
        <div class="bar-chart">
            <div class="bar-col"><div class="bar-fill" style="height:55%"></div><div class="bar-val">98</div><div class="bar-label">أحد</div></div>
            <div class="bar-col"><div class="bar-fill" style="height:70%"></div><div class="bar-val">112</div><div class="bar-label">إثن</div></div>
            <div class="bar-col"><div class="bar-fill" style="height:45%"></div><div class="bar-val">78</div><div class="bar-label">ثل</div></div>
            <div class="bar-col"><div class="bar-fill" style="height:60%"></div><div class="bar-val">105</div><div class="bar-label">أرب</div></div>
            <div class="bar-col"><div class="bar-fill" style="height:80%"></div><div class="bar-val">134</div><div class="bar-label">خم</div></div>
            <div class="bar-col"><div class="bar-fill" style="height:95%"></div><div class="bar-val">148</div><div class="bar-label">جم</div></div>
            <div class="bar-col"><div class="bar-fill" style="height:85%"></div><div class="bar-val">126</div><div class="bar-label">سب</div></div>
        </div>
    </div>
</div>

{{-- ═══ تبويب 3: التشغيل ═══ --}}
<div id="tab-ops" class="dash-tab-content">

    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom:1.5rem;">
        <a href="/director/care/health" class="stat-card">
            <div class="stat-num">3</div>
            <div class="stat-label">البلاغات الصحية النشطة</div>
            <div class="stat-sub danger">1 عاجل جدًا</div>
        </a>
        <a href="/director/vet/referrals/treatment" class="stat-card">
            <div class="stat-num">4</div>
            <div class="stat-label">إحالات علاج بانتظار الاعتماد</div>
        </a>
        <a href="/director/vet/quarantine" class="stat-card">
            <div class="stat-num">3</div>
            <div class="stat-label">مهام استلام معلقة</div>
            <div class="stat-sub warn">1 تعذر مؤقتًا</div>
        </a>
        <a href="/director/records/logs/slaughter" class="stat-card">
            <div class="stat-num">1</div>
            <div class="stat-label">الذبح الاضطراري</div>
            <div class="stat-sub muted">1 حالة هذا الشهر</div>
        </a>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="title-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/></svg></div>
                قرارات طبية حديثة
            </div>
        </div>
        <div style="padding:1rem 1.5rem;">
            <div class="decisions-list">
                <div class="decision-chip"><span class="badge badge-green"><span class="dot"></span>إفراج صحي</span> زرافة نيلية — 2026-06-07</div>
                <div class="decision-chip"><span class="badge badge-blue"><span class="dot"></span>خروج بعد العلاج</span> أسد أفريقي (سيمبا) — 2026-06-06</div>
                <div class="decision-chip"><span class="badge badge-orange"><span class="dot"></span>ذبح اضطراري</span> نعامة #ANM-0899 — 2026-06-05</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ تبويب 4: التحليل ═══ --}}
<div id="tab-charts" class="dash-tab-content">

    <div class="charts-grid">
        <div class="chart-card">
            <h4>الحالات الصحية — آخر 30 يومًا</h4>
            <div class="bar-chart">
                <div class="bar-col"><div class="bar-fill orange" style="height:40%"></div><div class="bar-val">12</div><div class="bar-label">أس1</div></div>
                <div class="bar-col"><div class="bar-fill orange" style="height:55%"></div><div class="bar-val">18</div><div class="bar-label">أس2</div></div>
                <div class="bar-col"><div class="bar-fill orange" style="height:70%"></div><div class="bar-val">24</div><div class="bar-label">أس3</div></div>
                <div class="bar-col"><div class="bar-fill orange" style="height:50%"></div><div class="bar-val">16</div><div class="bar-label">أس4</div></div>
            </div>
        </div>
        <div class="chart-card">
            <h4>توزيع الحيوانات حسب المجموعات</h4>
            <div class="donut-wrap">
                <div class="donut"></div>
                <div class="legend-list">
                    <div class="legend-row"><span>القططية</span><span>18 <span class="legend-dot" style="background:#1a4a2e"></span></span></div>
                    <div class="legend-row"><span>الطيور</span><span>60 <span class="legend-dot" style="background:#16a34a"></span></span></div>
                    <div class="legend-row"><span>الزواحف</span><span>22 <span class="legend-dot" style="background:#22c55e"></span></span></div>
                    <div class="legend-row"><span>الثدييات الكبيرة</span><span>35 <span class="legend-dot" style="background:#4ade80"></span></span></div>
                    <div class="legend-row"><span>القرود</span><span>15 <span class="legend-dot" style="background:#86efac"></span></span></div>
                </div>
            </div>
        </div>
        <div class="chart-card">
            <h4>حالات المستشفى والحجر الصحي</h4>
            <div class="bar-chart">
                <div class="bar-col"><div class="bar-fill blue" style="height:70%"></div><div class="bar-val">5</div><div class="bar-label">مستشفى</div></div>
                <div class="bar-col"><div class="bar-fill blue" style="height:55%"></div><div class="bar-val">4</div><div class="bar-label">ميداني</div></div>
                <div class="bar-col"><div class="bar-fill" style="height:85%"></div><div class="bar-val">6</div><div class="bar-label">حجر</div></div>
                <div class="bar-col"><div class="bar-fill orange" style="height:30%"></div><div class="bar-val">2</div><div class="bar-label">جاهز إفراج</div></div>
            </div>
        </div>
        <div class="chart-card">
            <h4>الولادات والنفوق خلال الشهر</h4>
            <div class="bar-chart" style="height:100px;">
                <div class="bar-col"><div class="bar-fill" style="height:75%"></div><div class="bar-val">6</div><div class="bar-label">ولادات</div></div>
                <div class="bar-col"><div class="bar-fill red" style="height:35%"></div><div class="bar-val">2</div><div class="bar-label">نفوق</div></div>
                <div class="bar-col"><div class="bar-fill orange" style="height:15%"></div><div class="bar-val">1</div><div class="bar-label">ولادة نافقة</div></div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ تبويب 5: المتابعة ═══ --}}
<div id="tab-feed" class="dash-tab-content">

    <div class="two-col">
        <div class="table-card" style="margin:0;">
            <div class="table-card-header">
                <div class="table-card-title">
                    <div class="title-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    آخر الأحداث في النظام
                </div>
            </div>
            <table class="custom-table">
                <thead><tr><th>التاريخ</th><th>النوع</th><th>التفاصيل</th><th>القسم</th></tr></thead>
                <tbody>
                    <tr><td style="color:#64748b;font-size:0.82rem;">2026-06-08</td><td><span class="badge badge-blue">صحية</span></td><td>تسجيل حالة صحية — حيوان #102</td><td>الرعاية</td></tr>
                    <tr><td style="color:#64748b;font-size:0.82rem;">2026-06-08</td><td><span class="badge badge-green">إجراء طبي</span></td><td>تسجيل علاج ميداني — غزال</td><td>البيطري</td></tr>
                    <tr><td style="color:#64748b;font-size:0.82rem;">2026-06-08</td><td><span class="badge badge-green">ولادة</span></td><td>تسجيل مولود جديد #305</td><td>الرعاية</td></tr>
                    <tr><td style="color:#64748b;font-size:0.82rem;">2026-06-07</td><td><span class="badge badge-orange">قرار طبي</span></td><td>إصدار قرار خروج بعد العلاج</td><td>البيطري</td></tr>
                    <tr><td style="color:#64748b;font-size:0.82rem;">2026-06-07</td><td><span class="badge badge-gray">تذكرة</span></td><td>بيع 126 تذكرة</td><td>الإدارة</td></tr>
                    <tr><td style="color:#64748b;font-size:0.82rem;">2026-06-07</td><td><span class="badge badge-green">سجل</span></td><td>إضافة حيوان #ANM-1045</td><td>السجلات</td></tr>
                    <tr><td style="color:#64748b;font-size:0.82rem;">2026-06-06</td><td><span class="badge badge-orange">إحالة</span></td><td>إحالة علاج — أسد #ANM-0041</td><td>الرعاية</td></tr>
                    <tr><td style="color:#64748b;font-size:0.82rem;">2026-06-06</td><td><span class="badge badge-red">نفوق</span></td><td>تسجيل نفوق — نعامة #ANM-0899</td><td>الرعاية</td></tr>
                </tbody>
            </table>
        </div>

        <div class="table-card" style="margin:0;">
            <div class="table-card-header">
                <div class="table-card-title">
                    <div class="title-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg></div>
                    التنبيهات المهمة
                </div>
            </div>
            <div style="padding:1rem 1.25rem;">
                <div class="alert-list">
                    <a href="/director/care/health" class="alert-item-row high">
                        <div class="alert-body">
                            <strong>بلاغ صحي عاجل لم يُغلق منذ ساعتين</strong>
                            <span>مستوى التنبيه: عالي — الرعاية والتغذية</span>
                        </div>
                        <span class="alert-action">عرض ←</span>
                    </a>
                    <a href="/director/vet/referrals/treatment" class="alert-item-row medium">
                        <div class="alert-body">
                            <strong>4 إحالات علاج بانتظار الاعتماد</strong>
                            <span>مستوى التنبيه: متوسط — البيطري</span>
                        </div>
                        <span class="alert-action">عرض الإحالات ←</span>
                    </a>
                    <a href="/director/care/mortality" class="alert-item-row medium">
                        <div class="alert-body">
                            <strong>حالة نفوق بانتظار نتيجة التشريح</strong>
                            <span>مستوى التنبيه: متوسط</span>
                        </div>
                        <span class="alert-action">عرض ←</span>
                    </a>
                    <a href="/director/vet/quarantine" class="alert-item-row medium">
                        <div class="alert-body">
                            <strong>حيوان جاهز للإفراج الصحي ولم يُستلم</strong>
                            <span>مهمة استلام معلقة</span>
                        </div>
                        <span class="alert-action">عرض ←</span>
                    </a>
                    <a href="/director/care/births" class="alert-item-row">
                        <div class="alert-body">
                            <strong>مولود متبقٍ له يومان لإكمال المتابعة</strong>
                            <span>مستوى التنبيه: متوسط</span>
                        </div>
                        <span class="alert-action">عرض المواليد ←</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

