@extends('director.layout')
@section('title', 'لوحة المتابعة | مدير الحديقة')
@section('page_title', 'لوحة المتابعة')

@section('styles')
@include('director.partials.read-only-styles')
@endsection

@section('content')

{{-- التبويبات — مباشرة تحت العنوان --}}
<div class="dashboard-tabs-card">
    <div class="dashboard-tabs-label">أقسام اللوحة</div>
    <div class="segmented-tabs">
        <button type="button" class="seg-tab active" data-tab="tab-overview">نظرة عامة</button>
        <button type="button" class="seg-tab" data-tab="tab-visits">الزيارات والتذاكر</button>
        <button type="button" class="seg-tab" data-tab="tab-ops">التشغيل</button>
        <button type="button" class="seg-tab" data-tab="tab-charts">التحليل</button>
        <button type="button" class="seg-tab" data-tab="tab-feed">
            المتابعة @if(($feedAlertCount ?? 0) > 0)<span class="tab-badge">{{ $feedAlertCount }}</span>@endif
        </button>
    </div>
</div>

@php
    $overview = $overviewStats ?? [];
    $today = $todaySummary ?? [];
    $visitsData = $visits ?? [];
    $ops = $operations ?? [];
    $chartsData = $charts ?? [];
@endphp

{{-- ═══ تبويب 1: نظرة عامة ═══ --}}
<div id="tab-overview" class="dash-tab-content active">

    <div class="info-box">
        <strong>إجمالي الحيوانات داخل الحديقة</strong> يشمل: الحيوانات بعد الإفراج الصحي وتأكيد الاستلام، والمواليد قيد المتابعة أو المكتملة.
        لا يشمل: النافقة، الذبح الاضطراري، الخارجة، الحجر قبل الاستلام، أو حالات الحجر المنتهية غير المدخلة رسميًّا.
        <br><strong>مصدر العدد:</strong> الحيوانات داخل المجموعات + المضافة يدويًّا من مسؤول السجلات والتوثيق.
    </div>

    <div class="stats-grid-8">
        <div class="stat-card" style="cursor:default;">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div class="stat-num">{{ $overview['expected_visitors_today'] ?? 0 }}</div>
            <div class="stat-label">الزوار المتوقعون اليوم</div>
            <div class="stat-sub muted">تقدير من التذاكر والحجوزات</div>
        </div>
        <a href="/director/records/animals" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
            <div class="stat-num">{{ $overview['total_animals_in_zoo'] ?? 0 }}</div>
            <div class="stat-label">إجمالي الحيوانات داخل الحديقة</div>
            <div class="stat-sub">@if(($overview['births_under_follow_up'] ?? 0) > 0)+{{ $overview['births_under_follow_up'] }} مواليد قيد المتابعة@elseلا مواليد قيد المتابعة@endif</div>
        </a>
        <a href="/director/care/health" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
            <div class="stat-num">{{ $overview['new_health_cases'] ?? 0 }}</div>
            <div class="stat-label">الحالات الصحية الجديدة</div>
            <div class="stat-sub warn">@if(($overview['health_needing_referral'] ?? 0) > 0){{ $overview['health_needing_referral'] }} تحتاج إحالة علاج@elseلا حالات تحتاج إحالة@endif</div>
        </a>
        <a href="/director/vet/cases/hospital" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg></div>
            <div class="stat-num">{{ $overview['hospital_cases_active'] ?? 0 }}</div>
            <div class="stat-label">الحالات داخل المستشفى</div>
            <div class="stat-sub warn">@if(($overview['hospital_awaiting_decision'] ?? 0) > 0){{ $overview['hospital_awaiting_decision'] }} جاهزة لقرار خروج / ذبح@elseلا قرارات معلقة@endif</div>
        </a>
        <a href="/director/vet/cases/field" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div>
            <div class="stat-num">{{ $overview['field_cases_active'] ?? 0 }}</div>
            <div class="stat-label">الحالات الطبية الميدانية</div>
            <div class="stat-sub warn">@if(($overview['field_cases_active'] ?? 0) > 0)حالات قيد المتابعة@elseلا حالات مفتوحة@endif</div>
        </a>
        <a href="/director/vet/quarantine" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <div class="stat-num">{{ $overview['quarantine_active'] ?? 0 }}</div>
            <div class="stat-label">الحيوانات داخل الحجر الصحي</div>
            <div class="stat-sub">@if(($overview['quarantine_ready_release'] ?? 0) > 0){{ $overview['quarantine_ready_release'] }} جاهزة للإفراج الصحي@elseلا حالات جاهزة للإفراج@endif</div>
        </a>
        <a href="/director/care/births" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div class="stat-num">{{ $overview['new_births'] ?? 0 }}</div>
            <div class="stat-label">المواليد الجديدة</div>
            <div class="stat-sub">@if(($overview['births_near_completion'] ?? 0) > 0){{ $overview['births_near_completion'] }} قريب من إكمال 30 يومًا@elseلا مواليد قريبة من الإكمال@endif</div>
        </a>
        <a href="/director/care/mortality" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg></div>
            <div class="stat-num">{{ $overview['mortality_last_7_days'] ?? 0 }}</div>
            <div class="stat-label">حالات النفوق</div>
            <div class="stat-sub danger">آخر 7 أيام — @if(($overview['mortality_pending_autopsy'] ?? 0) > 0){{ $overview['mortality_pending_autopsy'] }} بانتظار تشريح@elseلا حالات بانتظار تشريح@endif</div>
        </a>
        <a href="/director/records/logs/exits" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/></svg></div>
            <div class="stat-num">{{ $overview['exited_animals'] ?? 0 }}</div>
            <div class="stat-label">الحيوانات الخارجة</div>
            <div class="stat-sub muted">@if(($overview['slaughter_this_month'] ?? 0) > 0){{ $overview['slaughter_this_month'] }} ذبح اضطراري هذا الشهر@elseلا سجلات خروج مسجّلة@endif</div>
        </a>
    </div>

    <h3 class="section-heading">ملخص اليوم <span>{{ $todayDate ?? now()->format('Y-m-d') }}</span></h3>
    <div class="today-grid">
        <div class="today-item"><div class="num">{{ $today['health_cases'] ?? 0 }}</div><div class="lbl">حالات صحية مسجلة</div></div>
        <div class="today-item"><div class="num">{{ $today['health_reports'] ?? 0 }}</div><div class="lbl">بلاغات صحية مرسلة</div></div>
        <div class="today-item"><div class="num">{{ $today['births'] ?? 0 }}</div><div class="lbl">ولادات جديدة</div></div>
        <div class="today-item"><div class="num">{{ $today['mortality'] ?? 0 }}</div><div class="lbl">حالات نفوق</div></div>
        <div class="today-item"><div class="num">{{ $today['medical_procedures'] ?? 0 }}</div><div class="lbl">إجراءات طبية</div></div>
        <div class="today-item"><div class="num">{{ $today['nutrition_recommendations'] ?? 0 }}</div><div class="lbl">توصيات غذائية علاجية</div></div>
        <div class="today-item"><div class="num">{{ $today['receiving_tasks'] ?? 0 }}</div><div class="lbl">مهام استلام جديدة</div></div>
        <div class="today-item"><div class="num">{{ $today['tickets_sold'] ?? 0 }}</div><div class="lbl">تذاكر مباعة</div></div>
    </div>
</div>

{{-- ═══ تبويب 2: الزيارات والتذاكر ═══ --}}
<div id="tab-visits" class="dash-tab-content">

    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom:1.5rem;">
        <a href="/director/admin/tickets" class="stat-card">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <div class="stat-num">{{ $visitsData['tickets_today'] ?? 0 }}</div>
            <div class="stat-label">تذاكر اليوم</div>
            <div class="stat-sub">@if(!is_null($visitsData['tickets_change_pct'] ?? null)){{ $visitsData['tickets_change_pct'] >= 0 ? '+' : '' }}{{ $visitsData['tickets_change_pct'] }}% مقارنة بأمس@elseلا بيانات أمس للمقارنة@endif</div>
        </a>
        <div class="stat-card" style="cursor:default;">
            <div class="stat-icon-wrap"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div class="stat-num">{{ number_format($visitsData['revenue_today'] ?? 0, 0) }}</div>
            <div class="stat-label">إيرادات اليوم (د.ل)</div>
        </div>
    </div>

    <div class="chart-card" style="margin-bottom:1.5rem;">
        <h4>أكثر أنواع التذاكر مبيعًا اليوم</h4>
        <div class="ticket-types">
            @forelse($visitsData['ticket_types'] ?? [] as $type)
            <div class="ticket-type-pill"><span>{{ $type['count'] }}</span>{{ $type['name'] }}</div>
            @empty
            <div style="color:#94a3b8;font-weight:700;padding:0.5rem 0;">لا مبيعات تذاكر اليوم</div>
            @endforelse
        </div>
    </div>

    <div class="chart-card">
        <h4>حركة التذاكر — آخر 7 أيام</h4>
        <div class="bar-chart">
            @foreach($visitsData['weekly_chart'] ?? [] as $day)
            <div class="bar-col"><div class="bar-fill" style="height:{{ max($day['height_pct'], 4) }}%"></div><div class="bar-val">{{ $day['count'] }}</div><div class="bar-label">{{ $day['label'] }}</div></div>
            @endforeach
        </div>
    </div>
</div>

{{-- ═══ تبويب 3: التشغيل ═══ --}}
<div id="tab-ops" class="dash-tab-content">

    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom:1.5rem;">
        <a href="/director/care/health" class="stat-card">
            <div class="stat-num">{{ $ops['active_health_cases'] ?? 0 }}</div>
            <div class="stat-label">البلاغات الصحية النشطة</div>
            <div class="stat-sub danger">@if(($ops['urgent_health_reports'] ?? 0) > 0){{ $ops['urgent_health_reports'] }} عاجل@elseلا بلاغات عاجلة@endif</div>
        </a>
        <a href="/director/vet/referrals/treatment" class="stat-card">
            <div class="stat-num">{{ $ops['pending_treatment_referrals'] ?? 0 }}</div>
            <div class="stat-label">إحالات علاج بانتظار الاعتماد</div>
        </a>
        <a href="/director/vet/quarantine" class="stat-card">
            <div class="stat-num">{{ $ops['pending_receiving_tasks'] ?? 0 }}</div>
            <div class="stat-label">مهام استلام معلقة</div>
            <div class="stat-sub warn">@if(($ops['delayed_receiving_tasks'] ?? 0) > 0){{ $ops['delayed_receiving_tasks'] }} تعذر مؤقتًا@elseلا تعذر استلام@endif</div>
        </a>
        <a href="/director/records/logs/slaughter" class="stat-card">
            <div class="stat-num">{{ $ops['slaughter_this_month'] ?? 0 }}</div>
            <div class="stat-label">الذبح الاضطراري</div>
            <div class="stat-sub muted">هذا الشهر</div>
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
                @forelse($recentDecisions ?? [] as $decision)
                <div class="decision-chip"><span class="badge {{ $decision['badge_class'] }}"><span class="dot"></span>{{ $decision['label'] }}</span> {{ $decision['animal_label'] }} — {{ $decision['date'] }}</div>
                @empty
                <div style="color:#94a3b8;font-weight:700;">لا قرارات طبية حديثة</div>
                @endforelse
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
                @foreach($chartsData['health_weekly'] ?? [] as $week)
                <div class="bar-col"><div class="bar-fill orange" style="height:{{ max($week['height_pct'], 4) }}%"></div><div class="bar-val">{{ $week['count'] }}</div><div class="bar-label">{{ $week['label'] }}</div></div>
                @endforeach
            </div>
        </div>
        <div class="chart-card">
            <h4>توزيع الحيوانات حسب المجموعات</h4>
            <div class="donut-wrap">
                <div class="donut"></div>
                <div class="legend-list">
                    @forelse($chartsData['animals_by_group'] ?? [] as $groupRow)
                    <div class="legend-row"><span>{{ $groupRow['group'] }}</span><span>{{ $groupRow['count'] }} <span class="legend-dot" style="background:{{ $groupRow['color'] }}"></span></span></div>
                    @empty
                    <div class="legend-row"><span style="color:#94a3b8;">لا حيوانات مسجّلة</span></div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="chart-card">
            <h4>حالات المستشفى والحجر الصحي</h4>
            <div class="bar-chart">
                @foreach($chartsData['medical_facility'] ?? [] as $bar)
                <div class="bar-col"><div class="bar-fill {{ $bar['class'] }}" style="height:{{ max($bar['height_pct'], 4) }}%"></div><div class="bar-val">{{ $bar['count'] }}</div><div class="bar-label">{{ $bar['label'] }}</div></div>
                @endforeach
            </div>
        </div>
        <div class="chart-card">
            <h4>الولادات والنفوق خلال الشهر</h4>
            <div class="bar-chart" style="height:100px;">
                @foreach($chartsData['birth_mortality_month'] ?? [] as $bar)
                <div class="bar-col"><div class="bar-fill {{ $bar['class'] }}" style="height:{{ max($bar['height_pct'], 4) }}%"></div><div class="bar-val">{{ $bar['count'] }}</div><div class="bar-label">{{ $bar['label'] }}</div></div>
                @endforeach
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
                    @forelse($feedEvents ?? [] as $event)
                    <tr>
                        <td style="color:#64748b;font-size:0.82rem;">{{ $event['date'] }}</td>
                        <td><span class="badge {{ $event['badge_class'] }}">{{ $event['type_label'] }}</span></td>
                        <td>{{ $event['details'] }}</td>
                        <td>{{ $event['department'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#94a3b8;font-weight:700;padding:1.5rem;">لا أحداث مسجّلة حالياً</td></tr>
                    @endforelse
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
                    @forelse($feedAlerts ?? [] as $alert)
                    <a href="{{ $alert['url'] }}" class="alert-item-row {{ $alert['level'] }}">
                        <div class="alert-body">
                            <strong>{{ $alert['title'] }}</strong>
                            <span>{{ $alert['subtitle'] }}</span>
                        </div>
                        <span class="alert-action">{{ $alert['action'] }}</span>
                    </a>
                    @empty
                    <div class="alert-item-row" style="cursor:default;">
                        <div class="alert-body">
                            <strong style="color:#94a3b8;">لا تنبيهات مهمة حالياً</strong>
                            <span>كل الأنظمة ضمن الوضع الطبيعي</span>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

