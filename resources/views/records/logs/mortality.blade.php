@extends($__layout ?? 'records.layout')
@section('title', 'سجل النفوق | السجلات والتوثيق')
@section('page_title', 'سجل النفوق')

@section('styles')
@include('records.logs.partials.vet-log-styles')
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>⚰️ سجل النفوق</h2>
            <p>توثيق حالات النفوق للحيوانات الرسمية بعد دخولها الحديقة.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="num">6</div>
                <div class="lbl">حالة مسجّلة</div>
            </div>
            <div class="hero-stat">
                <div class="num">4</div>
                <div class="lbl">بعد تشريح</div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، النوع، أو المجموعة...">
        </div>
        <select class="filter-select">
                        @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات'])
        </select>
        <select class="filter-select">
            <option value="">التشريح: الكل</option>
            <option>نعم — تمت الإحالة</option>
            <option>لا — بدون تشريح</option>
        </select>
        @include('partials.date-filter', ['showWeek' => false, 'showMonth' => true, 'showYear' => true])
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">سجل النفوق</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>تاريخ النفوق</th>
                    <th>سبب النفوق النهائي</th>
                    <th>هل تمت الإحالة للتشريح؟</th>
                    <th>تاريخ الاعتماد / التوثيق</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @include('partials.animal-table-cell', ['emoji' => '🦅', 'animalId' => '#ANM-0899', 'sub' => 'نسر ذهبي'])
                    <td>الطيور</td>
                    <td>2025-12-18</td>
                    <td><span class="cause-text">فشل تنفسي حاد — نتيجة تشريح</span></td>
                    <td><span class="badge badge-completed"><span class="dot"></span>نعم</span></td>
                    <td>2025-12-22</td>
                    <td>
                        <a href="/records/animals/ANM-0899" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    @include('partials.animal-table-cell', ['emoji' => '🦒', 'animalId' => '#ANM-0750', 'sub' => 'زرافة نيلية'])
                    <td>الثدييات الكبيرة</td>
                    <td>2025-09-04</td>
                    <td><span class="cause-text">اعتراض كبد مزمن — اعتماد مباشر</span></td>
                    <td><span class="badge badge-none"><span class="dot"></span>لا</span></td>
                    <td>2025-09-05</td>
                    <td>
                        <a href="/records/animals/ANM-0750" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    @include('partials.animal-table-cell', ['emoji' => '🐒', 'animalId' => '#ANM-0612', 'sub' => 'شمبانزي أفريقي'])
                    <td>القرود</td>
                    <td>2025-06-21</td>
                    <td><span class="cause-text">عدوى بكتيرية — نتيجة تشريح</span></td>
                    <td><span class="badge badge-completed"><span class="dot"></span>نعم</span></td>
                    <td>2025-06-28</td>
                    <td>
                        <a href="/records/animals/ANM-0612" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    @include('partials.animal-table-cell', ['emoji' => '🦁', 'animalId' => '#ANM-0440', 'sub' => 'أسد أفريقي'])
                    <td>القططية</td>
                    <td>2024-11-10</td>
                    <td><span class="cause-text">شيخوخة — اعتماد مباشر</span></td>
                    <td><span class="badge badge-none"><span class="dot"></span>لا</span></td>
                    <td>2024-11-11</td>
                    <td>
                        <a href="/records/animals/ANM-0440" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
