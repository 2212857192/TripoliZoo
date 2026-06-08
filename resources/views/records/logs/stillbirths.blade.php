@extends($__layout ?? 'records.layout')
@section('title', 'سجل الولادات النافقة | السجلات والتوثيق')
@section('page_title', 'سجل الولادات النافقة')

@section('styles')
@include('records.logs.partials.vet-log-styles')
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>📋 سجل الولادات النافقة</h2>
            <p>توثيق المواليد التي نفقت قبل إكمال فترة المتابعة الأولية.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="num">4</div>
                <div class="lbl">حالة مسجّلة</div>
            </div>
            <div class="hero-stat">
                <div class="num">3</div>
                <div class="lbl">تم التشريح</div>
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
            <option value="">التشريح: الكل</option>
            <option>نعم — تم التشريح</option>
            <option>لا — لم يُشَرَّح</option>
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
        <div class="table-card-title">سجل الولادات النافقة</div>
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
                    <th>تاريخ النفوق</th>
                    <th>سبب النفوق النهائي</th>
                    <th>هل تم التشريح؟</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#NB-26-007</td>
                    <td>#ANM-0082</td>
                    <td>أسد أفريقي</td>
                    <td>القططية</td>
                    <td>أنثى</td>
                    <td>2026-05-20</td>
                    <td>2026-06-10</td>
                    <td><span class="cause-text">ضعف ولادة — فشل التنفس</span></td>
                    <td><span class="badge badge-completed"><span class="dot"></span>نعم</span></td>
                    <td>
                        <a href="/records/animals/NB-26-007" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>#B-022</td>
                    <td>#ANM-0082</td>
                    <td>أسد أفريقي</td>
                    <td>القططية</td>
                    <td>أنثى</td>
                    <td>2027-02-15</td>
                    <td>2027-02-28</td>
                    <td><span class="cause-text">إجهاض متأخر — تشوهات خلقية</span></td>
                    <td><span class="badge badge-completed"><span class="dot"></span>نعم</span></td>
                    <td>
                        <a href="/records/animals/B-022" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>#NB-25-014</td>
                    <td>#ANM-0145</td>
                    <td>غزال الريم</td>
                    <td>الغزلان</td>
                    <td>ذكر</td>
                    <td>2025-10-03</td>
                    <td>2025-10-18</td>
                    <td><span class="cause-text">عدوى بكتيرية — خلال فترة المتابعة</span></td>
                    <td><span class="badge badge-none"><span class="dot"></span>لا</span></td>
                    <td>
                        <a href="/records/animals/NB-25-014" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>#NB-25-009</td>
                    <td>#ANM-0220</td>
                    <td>قرد مكاك</td>
                    <td>القرود</td>
                    <td>ذكر</td>
                    <td>2025-07-22</td>
                    <td>2025-08-01</td>
                    <td><span class="cause-text">موت فجائي — سبب غير محدد</span></td>
                    <td><span class="badge badge-completed"><span class="dot"></span>نعم</span></td>
                    <td>
                        <a href="/records/animals/NB-25-009" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
