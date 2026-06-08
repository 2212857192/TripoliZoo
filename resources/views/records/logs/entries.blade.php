@extends($__layout ?? 'records.layout')
@section('title', 'سجل الحيوانات الداخلة | السجلات والتوثيق')
@section('page_title', 'سجل الحيوانات الداخلة')

@section('styles')
@include('records.logs.partials.vet-log-styles')
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>🏥 سجل الحيوانات الداخلة</h2>
            <p>توثيق الحيوانات التي دخلت الحديقة رسميًا بعد اكتمال مسار الحجر الصحي.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="num">12</div>
                <div class="lbl">حيوان داخل</div>
            </div>
            <div class="hero-stat">
                <div class="num">2</div>
                <div class="lbl">هذا الشهر</div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، النوع، أو المجموعة...">
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
        <div class="table-card-title">سجل الحيوانات الداخلة</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الحيوان</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>الجنس</th>
                    <th>تاريخ دخول الحجر</th>
                    <th>تاريخ الإفراج الصحي</th>
                    <th>تاريخ تأكيد الاستلام</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#ANM-1045</td>
                    <td>زرافة نيلية</td>
                    <td>العناقيد الكبرى</td>
                    <td>أنثى</td>
                    <td>2025-11-02</td>
                    <td>2025-11-28</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>2025-11-30</span></td>
                    <td>
                        <a href="/records/animals/ANM-1045" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>#ANM-1038</td>
                    <td>نسر ذهبي</td>
                    <td>الطيور</td>
                    <td>ذكر</td>
                    <td>2025-09-15</td>
                    <td>2025-10-08</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>2025-10-10</span></td>
                    <td>
                        <a href="/records/animals/ANM-1038" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>#ANM-1022</td>
                    <td>شمبانزي أفريقي</td>
                    <td>القرود</td>
                    <td>ذكر</td>
                    <td>2025-07-01</td>
                    <td>2025-07-25</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>2025-07-27</span></td>
                    <td>
                        <a href="/records/animals/ANM-1022" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>#ANM-1010</td>
                    <td>غزال دوركاس</td>
                    <td>الغزلان</td>
                    <td>أنثى</td>
                    <td>2025-04-12</td>
                    <td>2025-05-03</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>2025-05-05</span></td>
                    <td>
                        <a href="/records/animals/ANM-1010" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
