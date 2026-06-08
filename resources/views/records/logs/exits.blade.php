@extends($__layout ?? 'records.layout')
@section('title', 'سجل الحيوانات الخارجة | السجلات والتوثيق')
@section('page_title', 'سجل الحيوانات الخارجة')

@section('styles')
@include('records.logs.partials.vet-log-styles')
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>🚪 سجل الحيوانات الخارجة</h2>
            <p>توثيق الحيوانات التي غادرت الحديقة رسميًا (بيع، نقل، مقايضة، وغيرها).</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="num">8</div>
                <div class="lbl">حيوان خارج</div>
            </div>
            <div class="hero-stat">
                <div class="num">2</div>
                <div class="lbl">هذا العام</div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، الاسم، أو الجهة المستلمة...">
        </div>
        <select class="filter-select">
            <option value="">كل أنواع الخروج</option>
            <option>بيع</option>
            <option>نقل</option>
            <option>مقايضة</option>
            <option>إهداء</option>
            <option>تسليم</option>
            <option>إرجاع</option>
            <option>أخرى</option>
        </select>
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
        <div class="table-card-title">سجل الحيوانات الخارجة</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الحيوان</th>
                    <th>اسم الحيوان</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>الجنس</th>
                    <th>تاريخ الخروج</th>
                    <th>نوع الخروج</th>
                    <th>الجهة المستلمة</th>
                    <th>سبب الخروج</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#ANM-0520</td>
                    <td>لونا</td>
                    <td>لاما</td>
                    <td>الدب واللامة</td>
                    <td>أنثى</td>
                    <td>2025-10-20</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>نقل</span></td>
                    <td>حديقة طرابلس البحرية</td>
                    <td><span class="cause-text">برنامج تبادل حيوانات</span></td>
                    <td>
                        <a href="/records/animals/ANM-0520" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>#ANM-0488</td>
                    <td>—</td>
                    <td>ببغاء أفريقي رمادي</td>
                    <td>الطيور</td>
                    <td>ذكر</td>
                    <td>2025-07-08</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>إهداء</span></td>
                    <td>مركز التعليم البيئي</td>
                    <td><span class="cause-text">دعم برنامج توعية</span></td>
                    <td>
                        <a href="/records/animals/ANM-0488" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>—</td>
                    <td>—</td>
                    <td>ثعبان ملكي</td>
                    <td>الزواحف</td>
                    <td>أنثى</td>
                    <td>2024-12-01</td>
                    <td><span class="badge badge-none"><span class="dot"></span>إرجاع</span></td>
                    <td>مربي أصلي — خارجي</td>
                    <td><span class="cause-text">انتهاء فترة إيداع</span></td>
                    <td>
                        <span class="cause-text" style="color:#94a3b8;">لا يوجد ملف</span>
                    </td>
                </tr>
                <tr>
                    <td>#ANM-0330</td>
                    <td>رجو</td>
                    <td>غزال الريم</td>
                    <td>الغزلان</td>
                    <td>ذكر</td>
                    <td>2024-05-15</td>
                    <td><span class="badge badge-completed"><span class="dot"></span>مقايضة</span></td>
                    <td>حديقة حيوانات بنغازي</td>
                    <td><span class="cause-text">تنويع السلالات</span></td>
                    <td>
                        <a href="/records/animals/ANM-0330" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
