@extends($__layout ?? 'records.layout')
@section('title', 'الرئيسية | السجلات والتوثيق')
@section('page_title', 'الرئيسية')

@section('styles')
<style>
    /* ═══ STATS GRID (care dashboard style) ═══ */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        padding: 1.3rem 1.2rem;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: block;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
        transition: width 0.3s;
        background: #1a4a2e;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.1);
        border-color: transparent;
    }

    .stat-card:hover::before { width: 6px; }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 1rem;
        color: #16a34a;
    }

    .stat-num {
        font-size: 2.2rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        line-height: 1.4;
    }

    /* ── Table ── */
    .table-card {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }

    .table-card-header {
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        background: #FAFBFC;
    }

    .table-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
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
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
    }

    .custom-table tbody tr {
        transition: background 0.15s;
    }

    .custom-table tbody tr:hover {
        background: #FAFBFC;
    }

    .custom-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-main);
        vertical-align: middle;
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-primary { background: #eff6ff; color: #2563eb; }
    .badge-success { background: #f0fdf4; color: #16a34a; }
    .badge-danger  { background: #fef2f2; color: #dc2626; }
    .badge-warning { background: #fffbeb; color: #d97706; }
    .badge-gray    { background: #f1f5f9; color: #475569; }

</style>
@endsection

@section('content')

{{-- 1. SUMMARY CARDS --}}
<div class="stats-grid">
    <a href="/records/animals" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div class="stat-num">342</div>
        <div class="stat-label">إجمالي الحيوانات<br>داخل الحديقة</div>
    </a>

    <a href="#" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
        <div class="stat-num">18</div>
        <div class="stat-label">مواليد<br>قيد المتابعة</div>
    </a>

    <a href="#" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="stat-num">504</div>
        <div class="stat-label">إجمالي ملفات<br>الحيوانات</div>
    </a>

    <a href="/records/logs/births" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="stat-num">56</div>
        <div class="stat-label">سجل<br>الولادات</div>
    </a>

    <a href="/records/logs/entries" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        </div>
        <div class="stat-num">120</div>
        <div class="stat-label">سجل الحيوانات<br>الداخلة</div>
    </a>

    <a href="/records/logs/mortality" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path></svg>
        </div>
        <div class="stat-num">14</div>
        <div class="stat-label">سجل<br>النفوق</div>
    </a>

    <a href="/records/logs/slaughter" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="14 2 18 6 7 17 3 17 3 13 14 2"></polygon><line x1="3" y1="22" x2="21" y2="22"></line></svg>
        </div>
        <div class="stat-num">2</div>
        <div class="stat-label">سجل الذبح<br>الاضطراري</div>
    </a>

    <a href="/records/logs/exits" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
        </div>
        <div class="stat-num">4</div>
        <div class="stat-label">سجل الحيوانات<br>الخارجة</div>
    </a>
</div>

{{-- 2. RECENT RECORDS TABLE --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <div class="title-icon" style="background:#e6f4ea; color:#1a4a2e; width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            آخر الملفات أو السجلات المضافة
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>نوع السجل / العملية</th>
                    <th>المجموعة</th>
                    <th>رقم الحيوان</th>
                    <th>النوع</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color:#64748b; font-size:0.85rem;">2026-06-07</td>
                    <td><span class="badge badge-success">إضافة حيوان</span></td>
                    <td>الغزلان</td>
                    <td style="font-family: monospace; color:#0f172a; font-weight:800;">#ANM-1045</td>
                    <td style="font-weight:700;">غزال الريم</td>
                </tr>
                <tr>
                    <td style="color:#64748b; font-size:0.85rem;">2026-06-06</td>
                    <td><span class="badge badge-danger">نفوق</span></td>
                    <td>الطيور</td>
                    <td style="font-family: monospace; color:#0f172a; font-weight:800;">#ANM-0899</td>
                    <td style="font-weight:700;">نعامة</td>
                </tr>
                <tr>
                    <td style="color:#64748b; font-size:0.85rem;">2026-06-05</td>
                    <td><span class="badge badge-primary">ولادة</span></td>
                    <td>القرود</td>
                    <td style="font-family: monospace; color:#8b5cf6; font-weight:800;">#ANM-1046 <span style="font-size:0.7rem;">(قيد المتابعة)</span></td>
                    <td style="font-weight:700;">قرد مكاك</td>
                </tr>
                <tr>
                    <td style="color:#64748b; font-size:0.85rem;">2026-06-05</td>
                    <td><span class="badge badge-gray">تعديل ملف</span></td>
                    <td>القططية</td>
                    <td style="font-family: monospace; color:#0f172a; font-weight:800;">#ANM-0012</td>
                    <td style="font-weight:700;">نمر سيبيري</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
