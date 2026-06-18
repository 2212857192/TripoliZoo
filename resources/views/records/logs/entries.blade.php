@extends($__layout ?? 'records.layout')
@section('title', 'سجل الحيوانات الداخلة | السجلات والتوثيق')
@section('page_title', 'سجل الحيوانات الداخلة')

@section('styles')
@include('records.logs.partials.vet-log-styles')
<style>
    .table-card-footer { padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; background: #FAFBFC; }
</style>
@endsection

@section('content')

@php
    $portalBase = $portalBase ?? '/records';
    $filters = $filters ?? ['q' => '', 'group' => '', 'receipt' => '', 'period' => '', 'date' => ''];
    $logService = app(\App\Services\RecordsEntryLogService::class);
@endphp

<div class="top-card">
    <form method="GET" action="{{ $portalBase }}/logs/entries" class="filter-bar" id="entriesFilterForm">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="بحث برقم الحيوان، النوع، أو المجموعة...">
        </div>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'selected' => $filters['group']])
        </select>
        <select class="filter-select" name="receipt" onchange="this.form.submit()">
            <option value="" @selected($filters['receipt'] === '')>الاستلام: الكل</option>
            <option value="yes" @selected($filters['receipt'] === 'yes')>تم تأكيد الاستلام</option>
            <option value="no" @selected($filters['receipt'] === 'no')>لم يُستلَم بعد</option>
        </select>
        <select class="filter-select" name="period" onchange="onEntryPeriodChange(this)">
            <option value="" @selected($filters['period'] === '')>كل التواريخ</option>
            <option value="today" @selected($filters['period'] === 'today')>اليوم</option>
            <option value="month" @selected($filters['period'] === 'month')>هذا الشهر</option>
            <option value="year" @selected($filters['period'] === 'year')>هذا العام</option>
            <option value="custom" @selected($filters['period'] === 'custom')>تاريخ محدد</option>
        </select>
        <input
            type="date"
            name="date"
            id="entryCustomDate"
            class="filter-select"
            value="{{ $filters['date'] }}"
            style="display: {{ $filters['period'] === 'custom' ? 'block' : 'none' }};"
            onchange="document.getElementById('entriesFilterForm').submit()"
        >
        <button type="submit" class="filter-select" style="cursor:pointer;background:#f0fdf4;border-color:#bbf7d0;color:#15803d;">بحث</button>
    </form>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">سجل الحيوانات الداخلة</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
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
                @forelse($entries as $entry)
                @php
                    $animal = $entry->animal;
                    $animalCode = $animal?->code ?? '—';
                    $receiptDate = $logService->receiptDateFor($entry);
                @endphp
                <tr>
                    @include('partials.animal-table-cell', [
                        'name' => $animal?->name,
                        'image' => $animal?->displayPhotoUrl(),
                        'animalId' => '#'.$animalCode,
                        'sub' => $animal?->species ?? '—',
                    ])
                    <td>{{ $animal?->species ?? '—' }}</td>
                    <td>{{ $animal?->group ?? '—' }}</td>
                    <td>{{ $animal?->gender ?? '—' }}</td>
                    <td style="color:#64748b;font-size:0.85rem;">{{ $entry->entry_date?->format('Y-m-d') ?? '—' }}</td>
                    <td style="color:#64748b;font-size:0.85rem;">{{ $entry->released_at?->format('Y-m-d') ?? '—' }}</td>
                    <td>
                        @if($receiptDate)
                        <span class="badge badge-completed"><span class="dot"></span>{{ $receiptDate }}</span>
                        @elseif($entry->released_at)
                        <span class="badge badge-pending"><span class="dot"></span>بانتظار الاستلام</span>
                        @else
                        <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($animal)
                        <a href="{{ $portalBase }}/animals/{{ $animalCode }}" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#94a3b8;font-weight:700;padding:2rem;">لا توجد حيوانات داخلة مسجّلة</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($entries->hasPages())
    <div class="table-card-footer">
        {{ $entries->links() }}
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    function onEntryPeriodChange(select) {
        const dateInput = document.getElementById('entryCustomDate');
        if (!dateInput) return;
        dateInput.style.display = select.value === 'custom' ? 'block' : 'none';
        if (select.value !== 'custom') {
            dateInput.value = '';
            select.form.submit();
        }
    }
</script>
@endsection
