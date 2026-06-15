@extends($__layout ?? 'admin.layout')
@section('title', 'الرئيسية | مدير النظام')
@section('page_title', 'الرئيسية')

@section('styles')
<style>
    .stats-grid-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
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
    .custom-table tbody tr:last-child td { border-bottom: none; }
    .custom-table tbody tr:hover { background: #FAFBFC; }

    .status-text { font-weight: 700; color: #334155; }
</style>
@endsection

@section('content')

{{-- 1. بطاقات الإحصائيات الأساسية (5) --}}
<div class="stats-grid-5">
    <div class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-card-title">حسابات الموظفين</div>
        <div class="stat-num">24 حساب مسجل</div>
        <div class="stat-sub">نشطة: 21 | غير نشطة: 3</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="stat-card-title">أنواع التذاكر</div>
        <div class="stat-num">5 أنواع مسجلة</div>
        <div class="stat-sub">مفعّلة: 4 | معطلة: 1</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.5 12H16c-.7 2-2 3-4 3s-3.3-1-4-3H2.5"/><path d="M5.5 5.1L2 12v6c0 1.1.9 2 2 2h16a2 2 0 002-2v-6l-3.4-6.9A2 2 0 0017 5H7a2 2 0 00-1.5.1z"/></svg>
        </div>
        <div class="stat-card-title">محتوى الحيوانات للزوار</div>
        <div class="stat-num">41 محتوى تعريفي</div>
        <div class="stat-sub">ظاهر: 36 | مخفي: 5</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div class="stat-card-title">مواقع الخريطة</div>
        <div class="stat-num">18 موقع مضاف</div>
        <div class="stat-sub-muted">ضمن الخريطة التفاعلية</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-card-title">معلومات الزيارة</div>
        <div class="stat-num">آخر تحديث: 13/06/2026</div>
    </div>
</div>

{{-- 2. حالة محتوى تطبيق الزائر --}}
<div class="section-card">
    <div class="section-card-header">
        <div class="section-card-title">حالة محتوى تطبيق الزائر</div>
        <div class="section-card-desc">ملخص عام للعناصر الإدارية المرتبطة بتطبيق الزائر.</div>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>العنصر</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:800;">معلومات الزيارة</td>
                    <td class="status-text">آخر تحديث: 13/06/2026</td>
                </tr>
                <tr>
                    <td style="font-weight:800;">أنواع التذاكر</td>
                    <td class="status-text">4 مفعّلة / 1 معطلة</td>
                </tr>
                <tr>
                    <td style="font-weight:800;">محتوى الحيوانات للزوار</td>
                    <td class="status-text">36 ظاهر / 5 مخفي</td>
                </tr>
                <tr>
                    <td style="font-weight:800;">مواقع الخريطة</td>
                    <td class="status-text">18 موقع مضاف</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- 3. آخر العمليات الإدارية --}}
<div class="section-card">
    <div class="section-card-header">
        <div class="section-card-title">آخر العمليات الإدارية</div>
        <div class="section-card-desc">أحدث العمليات التي تمت على الحسابات، التذاكر، معلومات الزيارة، محتوى الحيوانات، أو مواقع الخريطة.</div>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    <th>العملية</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color:#64748b;">13/06/2026</td>
                    <td>حسابات الموظفين</td>
                    <td>إضافة حساب موظف جديد</td>
                </tr>
                <tr>
                    <td style="color:#64748b;">12/06/2026</td>
                    <td>التذاكر</td>
                    <td>تعديل سعر تذكرة بالغ</td>
                </tr>
                <tr>
                    <td style="color:#64748b;">12/06/2026</td>
                    <td>معلومات الزيارة</td>
                    <td>تحديث معلومات الزيارة</td>
                </tr>
                <tr>
                    <td style="color:#64748b;">11/06/2026</td>
                    <td>محتوى الحيوانات</td>
                    <td>إخفاء محتوى حيوان من تطبيق الزائر</td>
                </tr>
                <tr>
                    <td style="color:#64748b;">10/06/2026</td>
                    <td>مواقع الخريطة</td>
                    <td>إضافة موقع جديد للخريطة</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
