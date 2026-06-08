@extends($__layout ?? 'records.layout')
@section('title', 'سجل الولادات | السجلات والتوثيق')
@section('page_title', 'سجل الولادات')

@section('styles')
@include('records.logs.partials.vet-log-styles')
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>🍼 سجل الولادات</h2>
            <p>عرض المواليد التي أكملت فترة المتابعة بنجاح وحصلت على رقم رسمي.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="num">25</div>
                <div class="lbl">ولادة مكتملة</div>
            </div>
            <div class="hero-stat">
                <div class="num">3</div>
                <div class="lbl">هذا الشهر</div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، رقم الأم، أو النوع...">
        </div>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option>القططية</option>
            <option>الطيور</option>
            <option>الزواحف</option>
            <option>الغزلان</option>
            <option>القرود</option>
            <option>الثدييات الصغيرة</option>
            <option>الثدييات الكبيرة</option>
            <option>الدب واللامة</option>
        </select>
        <select class="filter-select">
            <option value="">كل التواريخ</option>
            <option>هذا الشهر</option>
            <option>هذا العام</option>
        </select>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">سجل الولادات</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الحيوان</th>
                    <th>رقم الأم</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>الجنس</th>
                    <th>تاريخ الولادة</th>
                    <th>تاريخ اكتمال المتابعة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#ANM-1015</td>
                    <td>#ANM-0082</td>
                    <td>أسد أفريقي</td>
                    <td>القططية</td>
                    <td>ذكر</td>
                    <td>2026-06-01</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>2026-07-01</span></td>
                    <td>
                        <a href="/records/animals/ANM-1015" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>#ANM-0980</td>
                    <td>#ANM-0145</td>
                    <td>غزال الريم</td>
                    <td>الغزلان</td>
                    <td>أنثى</td>
                    <td>2025-11-15</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>2025-12-15</span></td>
                    <td>
                        <a href="/records/animals/ANM-0980" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>#ANM-0950</td>
                    <td>#ANM-0220</td>
                    <td>قرد مكاك</td>
                    <td>القرود</td>
                    <td>ذكر</td>
                    <td>2025-08-10</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>2025-09-10</span></td>
                    <td>
                        <a href="/records/animals/ANM-0950" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
