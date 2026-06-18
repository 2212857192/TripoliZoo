@extends($__layout ?? 'care.layout')
@section('title', 'القرارات الطبية | الرعاية والتغذية')
@section('page_title', 'القرارات الطبية')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.2rem; }

    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
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

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

</style>
@endsection

@section('content')

<div class="top-card">
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان أو نوع الحيوان..." id="decisionsSearch">
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
                        @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات'])
        </select>
        @include('partials.date-filter')
    </div>
</div>

<div class="table-card">
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>نوع القرار</th>
                    <th>الحيوان</th>
                    <th>نوع الحيوان</th>
                    <th>المجموعة</th>
                    <th>تاريخ القرار</th>
                    <th>حالة الاستلام</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if(($decisions ?? collect())->isNotEmpty())
                    @include('partials.medical-decisions-rows', [
                        'decisions' => $decisions,
                        'decisionsShowRoute' => $decisionsShowRoute ?? 'care.decisions.show',
                    ])
                @else
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2rem;color:#64748b;font-weight:700;">
                            لا توجد قرارات طبية مسجّلة بعد
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('decisionsSearch')?.addEventListener('input', function(e) {
    const q = e.target.value.trim().toLowerCase();
    document.querySelectorAll('.custom-table tbody tr[data-search]').forEach(row => {
        const hay = (row.getAttribute('data-search') || '').toLowerCase();
        row.style.display = !q || hay.includes(q) ? '' : 'none';
    });
});
</script>
@endsection
