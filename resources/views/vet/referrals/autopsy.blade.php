@extends($__layout ?? 'vet.layout')
@section('title', 'إحالات التشريح | المستشفى البيطري')
@section('page_title', 'إحالات التشريح')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; }

    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input { width: 100%; padding: 10px 40px 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; transition: all 0.2s; }
    .search-box input:focus { border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    .search-box svg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .filter-select { padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #334155; outline: none; cursor: pointer; }
    .filter-select:focus { border-color: #2E7D32; }

    .animal-thumb {
        width: 42px; height: 42px; border-radius: 11px; flex-shrink: 0;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
        border: 1.5px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem; overflow: hidden;
    }
    .animal-name { font-weight: 800; color: #0f172a; line-height: 1.3; }
    .reason-cell { font-size: 0.88rem; color: #334155; font-weight: 600; max-width: 220px; line-height: 1.4; }

    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-header { padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #FAFBFC; }
    .table-card-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.8rem; font-weight: 800; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.92rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .custom-table tbody tr:last-child td { border-bottom: none; }

    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-pending { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-pending .dot { background: #f59e0b; }
    .badge-documented { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-documented .dot { background: #3b82f6; }

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

</style>
@endsection

@php $vetBase = ($readOnly ?? false) ? '/director/vet' : '/vet'; @endphp

@section('content')

<div class="top-card">
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان أو نوعه...">
        </div>
        <select class="filter-select">
            <option value="">كل الحالات</option>
            <option>بانتظار التوثيق</option>
            <option>موثقة</option>
        </select>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option>القطط الكبرى</option>
            <option>القرود</option>
            <option>العناقيد الكبرى</option>
            <option>الطيور</option>
        </select>
        <select class="filter-select">
            <option value="">كل التواريخ</option>
            <option>اليوم</option>
            <option>آخر 7 أيام</option>
            <option>آخر 30 يوم</option>
        </select>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
            إحالات التشريح
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>اسم الحيوان</th>
                    <th>صورة</th>
                    <th>رقم الحيوان</th>
                    <th>نوع الحيوان</th>
                    <th>المجموعة</th>
                    <th>سبب التحويل للتشريح</th>
                    <th>تاريخ الإحالة</th>
                    <th>الحالة</th>
                    <th class="col-actions">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="animal-name">صقر</span></td>
                    <td><div class="animal-thumb">🦅</div></td>
                    <td><span class="animal-id">#ANM-009</span></td>
                    <td style="font-weight:700;">نسر ذهبي</td>
                    <td>الطيور</td>
                    <td><span class="reason-cell">وفاة غير معروفة السبب</span></td>
                    <td>2025-05-13</td>
                    <td><span class="badge badge-pending"><span class="dot"></span>بانتظار التوثيق</span></td>
                    <td class="col-actions">
                        <a href="{{ $vetBase }}/referrals/autopsy/AR-001" class="btn-tbl view" title="عرض التفاصيل">
                            @include('partials.icon-chevron-view')
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><span class="animal-name">كوكو</span></td>
                    <td><div class="animal-thumb">🐒</div></td>
                    <td><span class="animal-id">#ANL-0871</span></td>
                    <td style="font-weight:700;">شمبانزي أفريقي</td>
                    <td>القرود</td>
                    <td><span class="reason-cell">وفاة مفاجئة</span></td>
                    <td>2025-05-15</td>
                    <td><span class="badge badge-pending"><span class="dot"></span>بانتظار التوثيق</span></td>
                    <td class="col-actions">
                        <a href="{{ $vetBase }}/referrals/autopsy/AR-002" class="btn-tbl view" title="عرض التفاصيل">
                            @include('partials.icon-chevron-view')
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><span class="animal-name">جميلة</span></td>
                    <td><div class="animal-thumb">🦒</div></td>
                    <td><span class="animal-id">#ANM-154</span></td>
                    <td style="font-weight:700;">زرافة نيلية</td>
                    <td>العناقيد الكبرى</td>
                    <td><span class="reason-cell">مشاكل تنفسية</span></td>
                    <td>2025-05-10</td>
                    <td><span class="badge badge-documented"><span class="dot"></span>موثقة</span></td>
                    <td class="col-actions">
                        <a href="{{ $vetBase }}/referrals/autopsy/AR-003" class="btn-tbl view" title="عرض التفاصيل">
                            @include('partials.icon-chevron-view')
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
