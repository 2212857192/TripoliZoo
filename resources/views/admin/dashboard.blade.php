@extends($__layout ?? 'admin.layout')
@section('title', 'الرئيسية | مدير النظام')
@section('page_title', 'الرئيسية')

@section('styles')
<style>
    .stats-grid-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1300px) { .stats-grid-5 { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 900px) { .stats-grid-5 { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 520px) { .stats-grid-5 { grid-template-columns: 1fr; } }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        padding: 1.3rem 1.2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        border-radius: 0 16px 16px 0;
        background: #1a4a2e;
    }
    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 1rem;
        color: #16a34a;
    }
    .stat-card-title {
        font-size: 0.82rem;
        font-weight: 800;
        color: #64748b;
        margin-bottom: 6px;
    }
    .stat-num {
        font-size: 1.15rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.35;
        margin-bottom: 6px;
    }
    .stat-sub {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        line-height: 1.5;
    }
    .stat-sub-muted {
        font-size: 0.76rem;
        font-weight: 600;
        color: #94a3b8;
        line-height: 1.45;
        margin-top: 2px;
    }

    .alerts-wrap { display: flex; flex-direction: column; gap: 10px; margin-bottom: 1.5rem; }
    .alert-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #FDE68A;
        background: #FFFBEB;
        color: #92400E;
        font-weight: 700;
        font-size: 0.88rem;
    }
    .alert-item a {
        color: #B45309;
        text-decoration: none;
        font-weight: 800;
        white-space: nowrap;
    }

    .section-card {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .section-card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        background: #FAFBFC;
    }
    .section-card-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .section-card-desc {
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.5;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }
    .custom-table thead th {
        background: #F8FAFC;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 800;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
    }
    .custom-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-main);
        vertical-align: middle;
    }
    .visitor-app-table tbody td {
        padding: 18px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-main);
        vertical-align: middle;
    }

    .visitor-app-table tbody td:first-child {
        font-weight: 800;
        color: #0f172a;
    }

    .visitor-app-table tbody td:last-child {
        color: #64748b;
        font-weight: 700;
    }

    .visitor-app-table tbody tr:last-child td { border-bottom: none; }
    .visitor-app-table tbody tr:hover { background: #FAFBFC; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
    }
    .status-pill.ok { background: #DCFCE7; color: #166534; }
    .status-pill.warn { background: #FEF3C7; color: #92400E; }
    .status-pill.neutral { background: #F1F5F9; color: #475569; }

    .today-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        padding: 1.25rem 1.75rem 1.5rem;
    }
    @media (max-width: 900px) { .today-grid { grid-template-columns: 1fr; } }
    .today-item {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1rem 1.1rem;
    }
    .today-item .num { font-size: 1.35rem; font-weight: 900; color: #0f172a; margin-bottom: 4px; }
    .today-item .lbl { font-size: 0.82rem; font-weight: 700; color: #64748b; }
</style>
@endsection

@section('content')

@php
    $stats = $stats ?? [];
    $employees = $stats['employees'] ?? ['total' => 0, 'active' => 0, 'inactive' => 0];
    $ticketTypes = $stats['ticket_types'] ?? ['total' => 0, 'active' => 0, 'inactive' => 0];
    $profiles = $stats['profiles'] ?? ['total' => 0, 'visible' => 0, 'hidden' => 0];
    $mapLocations = $stats['map_locations'] ?? ['total' => 0, 'active' => 0, 'inactive' => 0];
    $ticketsToday = $stats['tickets_today'] ?? ['count' => 0, 'quantity' => 0, 'revenue' => 0];
    $ticketsMonth = $stats['tickets_month'] ?? ['count' => 0, 'quantity' => 0, 'revenue' => 0];
@endphp

@if(!empty($alerts))
<div class="alerts-wrap">
    @foreach($alerts as $alert)
    <div class="alert-item">
        <span>{{ $alert['message'] }}</span>
        <a href="{{ $alert['url'] }}">معالجة ←</a>
    </div>
    @endforeach
</div>
@endif

<div class="stats-grid-5">
    <a href="{{ route('admin.employees.index') }}" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-card-title">حسابات الموظفين</div>
        <div class="stat-num">{{ $employees['total'] }} حساب مسجل</div>
        <div class="stat-sub">نشطة: {{ $employees['active'] }} | غير نشطة: {{ $employees['inactive'] }}</div>
    </a>

    <a href="{{ route('admin.tickets.index') }}" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="stat-card-title">أنواع التذاكر</div>
        <div class="stat-num">{{ $ticketTypes['total'] }} أنواع مسجلة</div>
        <div class="stat-sub">مفعّلة: {{ $ticketTypes['active'] }} | معطلة: {{ $ticketTypes['inactive'] }}</div>
    </a>

    <a href="{{ route('admin.animals.index') }}" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.5 12H16c-.7 2-2 3-4 3s-3.3-1-4-3H2.5"/><path d="M5.5 5.1L2 12v6c0 1.1.9 2 2 2h16a2 2 0 002-2v-6l-3.4-6.9A2 2 0 0017 5H7a2 2 0 00-1.5.1z"/></svg>
        </div>
        <div class="stat-card-title">محتوى الحيوانات للزوار</div>
        <div class="stat-num">{{ $profiles['total'] }} محتوى تعريفي</div>
        <div class="stat-sub">ظاهر: {{ $profiles['visible'] }} | مخفي: {{ $profiles['hidden'] }}</div>
        @if(($stats['animals_without_profile'] ?? 0) > 0)
        <div class="stat-sub-muted">{{ $stats['animals_without_profile'] }} حيوان بلا محتوى</div>
        @endif
    </a>

    <a href="{{ route('admin.map-locations.index') }}" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div class="stat-card-title">مواقع الخريطة</div>
        <div class="stat-num">{{ $mapLocations['total'] }} موقع مضاف</div>
        <div class="stat-sub">نشط: {{ $mapLocations['active'] }} | معطّل: {{ $mapLocations['inactive'] }}</div>
    </a>

    <a href="{{ route('admin.visit-info.show') }}" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-card-title">معلومات الزيارة</div>
        <div class="stat-num">{{ $visitSettings->status_visible ? 'ظاهرة للزوار' : 'مخفية' }}</div>
        <div class="stat-sub-muted">آخر تحديث: {{ $visitSettings->updated_at?->format('d/m/Y') ?? '—' }}</div>
    </a>
</div>

<div class="section-card">
    <div class="section-card-header">
        <div class="section-card-title">حالة محتوى تطبيق الزائر</div>
        <div class="section-card-desc">ملخص عام للعناصر الإدارية المرتبطة بتطبيق الزائر.</div>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table visitor-app-table">
            <thead>
                <tr>
                    <th>العنصر</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visitorAppRows ?? [] as $row)
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td>{{ $row['status'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="color:#64748b;text-align:center;">لا توجد بيانات متاحة</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="section-card">
    <div class="section-card-header">
        <div class="section-card-title">آخر العمليات الإدارية</div>
        <div class="section-card-desc">أحدث العمليات على الحسابات، التذاكر، معلومات الزيارة، محتوى الحيوانات، أو مواقع الخريطة.</div>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>المستخدم</th>
                    <th>النوع</th>
                    <th>العملية</th>
                    <th>التفاصيل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentActivities as $activity)
                <tr>
                    <td style="color:#64748b;">{{ $activity['date'] }}</td>
                    <td>{{ $activity['user_name'] }}</td>
                    <td>{{ $activity['entity_label'] }}</td>
                    <td>{{ $activity['action_label'] }}</td>
                    <td>{{ $activity['summary'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="color:#64748b;text-align:center;">لا توجد عمليات مسجّلة بعد</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
