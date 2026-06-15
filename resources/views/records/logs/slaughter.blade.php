@extends($__layout ?? 'records.layout')
@section('title', 'سجل الذبح الاضطراري | السجلات والتوثيق')
@section('page_title', 'سجل الذبح الاضطراري')

@section('styles')
@include('records.logs.partials.vet-log-styles')
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>🔪 سجل الذبح الاضطراري</h2>
            <p>توثيق قرارات الذبح الاضطراري الصادرة من القسم البيطري.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="num">3</div>
                <div class="lbl">قرار مسجّل</div>
            </div>
            <div class="hero-stat">
                <div class="num">1</div>
                <div class="lbl">هذا العام</div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، النوع، أو الطبيب...">
        </div>
        <select class="filter-select">
                        @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات'])
        </select>
        @include('partials.date-filter', ['showWeek' => false, 'showMonth' => true, 'showYear' => true])
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">سجل الذبح الاضطراري</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>تاريخ القرار</th>
                    <th>سبب القرار</th>
                    <th>الطبيب المسؤول</th>
                    <th>رئيس القسم المعتمد</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @include('partials.animal-table-cell', ['emoji' => '🐴', 'animalId' => '#ANM-0315', 'sub' => 'حصان عربي'])
                    <td>الثدييات الكبيرة</td>
                    <td>2025-08-14</td>
                    <td><span class="cause-text">كسر مفتوح غير قابل للعلاج</span></td>
                    <td>د. أحمد الفيتوري</td>
                    <td>د. سالم الزاوي</td>
                    <td>
                        <a href="/records/animals/ANM-0315" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    @include('partials.animal-table-cell', ['emoji' => '🐃', 'animalId' => '#ANM-0288', 'sub' => 'جاموس مائي'])
                    <td>الثدييات الكبيرة</td>
                    <td>2024-03-22</td>
                    <td><span class="cause-text">إصابة حادة في الأعضاء الداخلية</span></td>
                    <td>د. فاطمة بن عمر</td>
                    <td>د. سالم الزاوي</td>
                    <td>
                        <a href="/records/animals/ANM-0288" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    @include('partials.animal-table-cell', ['emoji' => '🦌', 'animalId' => '#ANM-0195', 'sub' => 'غزال الريم'])
                    <td>الغزلان</td>
                    <td>2023-11-05</td>
                    <td><span class="cause-text">مرض معدٍ مستعصٍ — قرار طبي</span></td>
                    <td>د. أحمد الفيتوري</td>
                    <td>د. سالم الزاوي</td>
                    <td>
                        <a href="/records/animals/ANM-0195" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
