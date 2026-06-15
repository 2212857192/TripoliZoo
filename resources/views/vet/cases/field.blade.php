@extends($__layout ?? 'vet.layout')
@section('title', 'الحالات الطبية الميدانية | المستشفى البيطري')
@section('page_title', 'الحالات الطبية الميدانية')

@section('styles')
<style>
    .page-title-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
    .page-title-wrap { display:flex; align-items:center; gap:12px; }
    .title-icon { width:40px; height:40px; border-radius:12px; background:#e6f4ea; color:#2E7D32; display:flex; align-items:center; justify-content:center; }
    .page-title-wrap h2 { font-size:1.3rem; font-weight:800; color:#0f172a; margin:0; }
    .btn-refresh { display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:700; color:#334155; cursor:pointer; transition:all 0.2s; }
    .btn-refresh:hover { background:#f8fafc; }

    .tabs-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:0.8rem 1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; }
    .segmented-tabs { display:inline-flex; background:#f1f5f9; padding:5px; border-radius:10px; gap:4px; }
    .seg-tab { background:transparent; border:none; padding:9px 24px; border-radius:7px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; color:#64748b; cursor:pointer; transition:all 0.2s; }
    .seg-tab:hover { color:#2E7D32; }
    .seg-tab.active { background:#fff; color:#2E7D32; box-shadow:0 2px 4px rgba(0,0,0,0.07); }
    .tab-content { display:none; }
    .tab-content.active { display:block; animation:fadeIn 0.25s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:translateY(0); } }

    .table-card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:2rem; }
    .table-card-header { padding:1.1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f1f5f9; }
    .table-card-title { font-size:1rem; font-weight:800; color:#0f172a; }

    .custom-table { width:100%; border-collapse:collapse; text-align:right; }
    .custom-table thead th { background:#F8FAFC; color:#64748b; font-size:0.78rem; font-weight:800; padding:13px 18px; border-bottom:1px solid #e2e8f0; }
    .custom-table tbody tr { transition:background 0.15s; }
    .custom-table tbody tr:hover { background:#fafbfc; }
    .custom-table tbody td { padding:15px 18px; border-bottom:1px solid #f1f5f9; font-size:0.9rem; font-weight:600; color:#1e293b; vertical-align:middle; }
    .custom-table tbody tr:last-child td { border-bottom:none; }

    .badge { padding:5px 11px; border-radius:999px; font-size:0.73rem; font-weight:700; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; }
    .badge .dot { width:6px; height:6px; border-radius:50%; }
    .badge-followup   { background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; }
    .badge-followup .dot { background:#3b82f6; }
    .badge-improved   { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-improved .dot { background:#22c55e; }
    .badge-treated    { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-treated .dot { background:#22c55e; }
    .badge-no-followup { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }
    .badge-no-followup .dot { background:#94a3b8; }

    .case-id { font-family:'Courier New',monospace; font-size:0.74rem; padding:3px 8px; border-radius:6px; font-weight:700; display:inline-block; }
    .case-id-open   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .case-id-closed { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }

    .btn-tbl { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:9px; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; text-decoration:none; transition:all 0.2s; cursor:pointer; box-shadow:0 1px 2px rgba(0,0,0,0.05); }
    .btn-tbl:hover { transform:translateY(-1px); box-shadow:0 3px 8px rgba(0,0,0,0.1); background:#e2e8f0; border-color:#cbd5e1; color:#0f172a; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
</style>
@endsection

@php $vetBase = ($readOnly ?? false) ? '/director/vet' : '/vet'; @endphp

@section('content')

{{-- ═══ TABS CARD ═══ --}}
<div class="tabs-card">
    <div class="segmented-tabs">
        <button class="seg-tab active" onclick="switchTab(event,'tab-followup')">قيد المتابعة</button>
        <button class="seg-tab" onclick="switchTab(event,'tab-closed')">مغلقة</button>
    </div>
</div>

{{-- ═══ FILTER CARD ═══ --}}
<div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.2rem; margin-bottom: 1.5rem; display: flex; justify-content: flex-start; align-items: center;">
    <div style="display: flex; gap: 15px; width: 100%;">
        <div style="flex: 2; position: relative;">
            <svg style="position: absolute; right: 12px; top: 11px; color: #94a3b8;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان أو نوعه..." style="width: 100%; padding: 10px 35px 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 600; outline: none; color: #0f172a;">
        </div>
        <select style="flex: 1; padding: 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; color: #475569; font-weight: 600; outline: none;">
                        @include('partials.animal-group-options', ['emptyLabel' => 'جميع المجموعات'])
        </select>
        @include('partials.date-filter', [
            'filterId' => 'fieldDateFilter',
            'selectClass' => '',
            'wrapperStyle' => 'flex: 1;',
            'selectStyle' => 'width: 100%; padding: 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: Cairo, sans-serif; font-size: 0.9rem; color: #475569; font-weight: 600; outline: none;',
            'showWeek' => false,
            'showLast7' => true,
            'showLast30' => true,
        ])
    </div>
</div>

{{-- ════ TAB 1: قيد المتابعة ════ --}}
<div id="tab-followup" class="tab-content active">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">الحالات التي فتحها الطبيب وما زالت تحتاج متابعة ميدانية</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>الحيوان</th>
                        <th>نوع الحيوان</th>
                        <th>المجموعة</th>
                        <th>تاريخ فتح الحالة</th>
                        <th>آخر تحديث</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @include('partials.animal-table-cell', ['name' => 'كوكو', 'emoji' => '🐒', 'animalId' => '#ANL-0871'])
                        <td>شمبانزي أفريقي</td>
                        <td>القرود</td>
                        <td>2026-05-13</td>
                        <td>2026-06-02</td>
                        <td>
                            <a href="{{ $vetBase }}/cases/field/FC-2026-001" class="btn-tbl view" title="عرض التفاصيل">
                                @include('partials.icon-eye-view')
                            </a>
                        </td>
                    </tr>
                    <tr>
                        @include('partials.animal-table-cell', ['emoji' => '🦒', 'animalId' => '#ANM-154'])
                        <td>زرافة نيلية</td>
                        <td>الثدييات الكبيرة</td>
                        <td>2026-05-20</td>
                        <td>2026-06-01</td>
                        <td>
                            <a href="{{ $vetBase }}/cases/field/FC-2026-002" class="btn-tbl view" title="عرض التفاصيل">
                                @include('partials.icon-eye-view')
                            </a>
                        </td>
                    </tr>
                    <tr>
                        @include('partials.animal-table-cell', ['name' => 'سيمبا', 'emoji' => '🦁', 'animalId' => '#ANM-220'])
                        <td>أسد إفريقي</td>
                        <td>القططية</td>
                        <td>2026-06-01</td>
                        <td>2026-06-05</td>
                        <td>
                            <a href="{{ $vetBase }}/cases/field/FC-2026-003" class="btn-tbl view" title="عرض التفاصيل">
                                @include('partials.icon-eye-view')
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ════ TAB 2: مغلقة ════ --}}
<div id="tab-closed" class="tab-content">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">الحالات التي أغلقها الطبيب بعد انتهاء العلاج أو المتابعة — سجل تاريخي</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>الحيوان</th>
                        <th>نوع الحيوان</th>
                        <th>المجموعة</th>
                        <th>تاريخ فتح الحالة</th>
                        <th>تاريخ الإغلاق</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @include('partials.animal-table-cell', ['name' => 'صقر', 'emoji' => '🦅', 'animalId' => '#ANM-088'])
                        <td>نسر ذهبي</td>
                        <td>الطيور</td>
                        <td>2025-05-10</td>
                        <td>2025-05-25</td>
                        <td>
                            <a href="{{ $vetBase }}/cases/field/FC-2025-088" class="btn-tbl view" title="عرض التفاصيل">
                                @include('partials.icon-eye-view')
                            </a>
                        </td>
                    </tr>
                    <tr>
                        @include('partials.animal-table-cell', ['name' => 'توم', 'emoji' => '🐘', 'animalId' => '#ANM-045'])
                        <td>فيل إفريقي</td>
                        <td>الثدييات الكبيرة</td>
                        <td>2025-04-02</td>
                        <td>2025-04-18</td>
                        <td>
                            <a href="{{ $vetBase }}/cases/field/FC-2025-045" class="btn-tbl view" title="عرض التفاصيل">
                                @include('partials.icon-eye-view')
                            </a>
                        </td>
                    </tr>
                    <tr>
                        @include('partials.animal-table-cell', ['emoji' => '🦁', 'animalId' => '#ANM-033'])
                        <td>أنثى أسد</td>
                        <td>القططية</td>
                        <td>2025-03-15</td>
                        <td>2025-03-28</td>
                        <td>
                            <a href="{{ $vetBase }}/cases/field/FC-2025-033" class="btn-tbl view" title="عرض التفاصيل">
                                @include('partials.icon-eye-view')
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function switchTab(evt, tabId) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.seg-tab').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}
</script>
@endsection
