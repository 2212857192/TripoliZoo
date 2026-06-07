@extends('care.layout')
@section('title', 'القرارات الطبية | الرعاية والتغذية')
@section('page_title', 'القرارات الطبية')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.2rem; }
    .page-header { display: flex; justify-content: space-between; align-items: center; }
    .page-header-info h2 { font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0; }
    .page-header-info p { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin: 4px 0 0; }

    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; padding-top: 1.2rem; border-top: 1px solid #F1F5F9; }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input { width: 100%; padding: 10px 40px 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; transition: all 0.2s; }
    .search-box input:focus { border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    .search-box svg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .filter-select { padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #334155; outline: none; cursor: pointer; }
    .filter-select:focus { border-color: #2E7D32; }

    /* ── Table ── */
    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-header { padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #FAFBFC; }
    .table-card-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.8rem; font-weight: 800; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.92rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .custom-table tbody tr:last-child td { border-bottom: none; }

    /* ═══ BADGES ═══ */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    
    /* Decision Types */
    .type-discharge { background: #f0fdfa; color: #0f766e; border: 1px solid #ccfbf1; } /* خروج بعد العلاج */
    .type-discharge .dot { background: #14b8a6; }
    
    .type-release { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; } /* إفراج صحي */
    .type-release .dot { background: #3b82f6; }
    
    .type-slaughter { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; } /* ذبح اضطراري */
    .type-slaughter .dot { background: #ef4444; }

    /* Reception Status */
    .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; } /* بانتظار الاستلام */
    .status-received { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce3; } /* تم الاستلام */
    .status-failed { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; } /* تعذر مؤقتا */
    .status-none { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; } /* لا يتطلب استلام */

    .source-badge { font-size: 0.75rem; font-weight: 700; color: #475569; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; }

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
    .btn-tbl.export:hover { color: #16a34a; background: #dcfce3; border-color: #bbf7d0; }

    .dec-id, .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    .actions-flex { display: flex; gap: 8px; }
</style>
@endsection

@section('content')

<div class="top-card">
    <div class="page-header">
        <div class="page-header-info">
            <h2>📜 القرارات الطبية</h2>
            <p>عرض القرارات الطبية الصادرة من قسم المستشفى البيطري والمتعلقة بحيوانات المجموعات.</p>
        </div>
    </div>
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان، نوع الحيوان، رقم القرار...">
        </div>
        <select class="filter-select">
            <option value="">نوع القرار</option>
            <option>خروج بعد العلاج</option>
            <option>إفراج صحي</option>
            <option>ذبح اضطراري</option>
        </select>
        <select class="filter-select">
            <option value="">حالة الاستلام</option>
            <option>بانتظار الاستلام</option>
            <option>تم الاستلام</option>
            <option>تعذر مؤقتاً</option>
            <option>لا يتطلب استلام</option>
        </select>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option>السباع والضواري</option>
            <option>الرئيسيات</option>
            <option>العواشب</option>
            <option>الطيور</option>
        </select>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            سجل القرارات
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم القرار</th>
                    <th>نوع القرار</th>
                    <th>رقم الحيوان</th>
                    <th>نوع الحيوان</th>
                    <th>المجموعة</th>
                    <th>مصدر القرار</th>
                    <th>تاريخ القرار</th>
                    <th>حالة الاستلام</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                {{-- Row 1: Discharge, Pending Reception --}}
                <tr>
                    <td><span class="dec-id">MD-801</span></td>
                    <td><span class="badge type-discharge"><span class="dot"></span>خروج بعد العلاج</span></td>
                    <td><span class="animal-id">#ANL-0041-2022</span></td>
                    <td style="font-weight:700;">أسد إفريقي</td>
                    <td>السباع والضواري</td>
                    <td><span class="source-badge">المستشفى</span></td>
                    <td>2026-06-07</td>
                    <td><span class="badge status-pending">بانتظار الاستلام</span></td>
                    <td>
                        <div class="actions-flex">
                            <a href="{{ route('care.decisions.show', 'MD-801') }}" class="btn-tbl view" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button onclick="alert('جاري تصدير النموذج...')" class="btn-tbl export" title="تصدير النموذج">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>

                {{-- Row 2: Release, Received --}}
                <tr>
                    <td><span class="dec-id">MD-800</span></td>
                    <td><span class="badge type-release"><span class="dot"></span>إفراج صحي</span></td>
                    <td><span class="animal-id">#Q-0182-2026</span></td>
                    <td style="font-weight:700;">قرد المكاك</td>
                    <td>الرئيسيات</td>
                    <td><span class="source-badge">الحجر الصحي</span></td>
                    <td>2026-06-06</td>
                    <td><span class="badge status-received">تم الاستلام</span></td>
                    <td>
                        <div class="actions-flex">
                            <a href="{{ route('care.decisions.show', 'MD-800') }}" class="btn-tbl view" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button onclick="alert('جاري تصدير النموذج...')" class="btn-tbl export" title="تصدير النموذج">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>

                {{-- Row 3: Emergency Slaughter, None --}}
                <tr>
                    <td><span class="dec-id">MD-799</span></td>
                    <td><span class="badge type-slaughter"><span class="dot"></span>ذبح اضطراري</span></td>
                    <td><span class="animal-id">#ANL-0120-2024</span></td>
                    <td style="font-weight:700;">غزال الريم</td>
                    <td>العواشب</td>
                    <td><span class="source-badge">المستشفى</span></td>
                    <td>2026-06-05</td>
                    <td><span class="badge status-none">لا يتطلب استلام</span></td>
                    <td>
                        <div class="actions-flex">
                            <a href="{{ route('care.decisions.show', 'MD-799') }}" class="btn-tbl view" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button onclick="alert('جاري تصدير النموذج...')" class="btn-tbl export" title="تصدير النموذج">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                
                {{-- Row 4: Discharge, Failed --}}
                <tr>
                    <td><span class="dec-id">MD-795</span></td>
                    <td><span class="badge type-discharge"><span class="dot"></span>خروج بعد العلاج</span></td>
                    <td><span class="animal-id">#ANL-0250-2025</span></td>
                    <td style="font-weight:700;">نسر أسمر</td>
                    <td>الطيور</td>
                    <td><span class="source-badge">المستشفى</span></td>
                    <td>2026-06-04</td>
                    <td><span class="badge status-failed">تعذر مؤقتاً</span></td>
                    <td>
                        <div class="actions-flex">
                            <a href="{{ route('care.decisions.show', 'MD-795') }}" class="btn-tbl view" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button onclick="alert('جاري تصدير النموذج...')" class="btn-tbl export" title="تصدير النموذج">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
