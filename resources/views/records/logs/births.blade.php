@extends($__layout ?? 'records.layout')
@section('title', 'سجل الولادات | السجلات والتوثيق')
@section('page_title', 'سجل الولادات')

@section('styles')
@include('records.logs.partials.vet-log-styles')
<style>
    .table-card-footer { padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; background: #FAFBFC; }
</style>
@endsection

@section('content')

@php
    $portalBase = $portalBase ?? '/records';
    $filters = $filters ?? ['q' => '', 'group' => '', 'status' => '', 'period' => '', 'date' => ''];
    $logService = app(\App\Services\RecordsBirthLogService::class);
@endphp

<div class="top-card">
    <form method="GET" action="{{ $portalBase }}/logs/births" class="filter-bar" id="birthsFilterForm">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="بحث برقم الحيوان، رقم الأم، أو النوع...">
        </div>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'selected' => $filters['group']])
        </select>
        <select class="filter-select" name="status" onchange="this.form.submit()">
            <option value="" @selected($filters['status'] === '')>الحالة: الكل</option>
            <option value="monitoring" @selected($filters['status'] === 'monitoring')>قيد المتابعة</option>
            <option value="completed" @selected($filters['status'] === 'completed')>اكتملت المتابعة</option>
        </select>
        <select class="filter-select" name="period" onchange="onBirthPeriodChange(this)">
            <option value="" @selected($filters['period'] === '')>كل التواريخ</option>
            <option value="today" @selected($filters['period'] === 'today')>اليوم</option>
            <option value="month" @selected($filters['period'] === 'month')>هذا الشهر</option>
            <option value="year" @selected($filters['period'] === 'year')>هذا العام</option>
            <option value="custom" @selected($filters['period'] === 'custom')>تاريخ محدد</option>
        </select>
        <input
            type="date"
            name="date"
            id="birthCustomDate"
            class="filter-select"
            value="{{ $filters['date'] }}"
            style="display: {{ $filters['period'] === 'custom' ? 'block' : 'none' }};"
            onchange="document.getElementById('birthsFilterForm').submit()"
        >
        <button type="submit" class="filter-select" style="cursor:pointer;background:#f0fdf4;border-color:#bbf7d0;color:#15803d;">بحث</button>
    </form>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">سجل الولادات</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
                    <th>رقم الأم</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>الجنس</th>
                    <th>تاريخ الولادة</th>
                    <th>تاريخ اكتمال المتابعة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($newborns as $newborn)
                @php
                    $monitoring = $logService->isMonitoring($newborn);
                    $completionDate = $logService->followUpCompletionDate($newborn);
                    $motherCode = $newborn->mother?->code ? '#'.$newborn->mother->code : '—';
                @endphp
                <tr>
                    @include('partials.animal-table-cell', [
                        'name' => $newborn->name,
                        'image' => $newborn->displayPhotoUrl(),
                        'animalId' => '#'.$newborn->code,
                        'sub' => $newborn->species,
                    ])
                    <td><span class="animal-id">{{ $motherCode }}</span></td>
                    <td>{{ $newborn->species }}</td>
                    <td>{{ $newborn->group }}</td>
                    <td>{{ $newborn->gender ?? '—' }}</td>
                    <td style="color:#64748b;font-size:0.85rem;">{{ $newborn->birth_date?->format('Y-m-d') ?? '—' }}</td>
                    <td>
                        @if($monitoring)
                        <span class="badge badge-pending"><span class="dot"></span>قيد المتابعة</span>
                        @elseif($completionDate)
                        <span class="badge badge-completed"><span class="dot"></span>{{ $completionDate }}</span>
                        @elseif($newborn->status === \App\Enums\AnimalStatus::Dead->value)
                        <span class="badge badge-none"><span class="dot"></span>نافق</span>
                        @else
                        <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ $portalBase }}/animals/{{ $newborn->code }}" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#94a3b8;font-weight:700;padding:2rem;">لا توجد ولادات مسجّلة</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($newborns->hasPages())
    <div class="table-card-footer">
        {{ $newborns->links() }}
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    function onBirthPeriodChange(select) {
        const dateInput = document.getElementById('birthCustomDate');
        if (!dateInput) return;
        dateInput.style.display = select.value === 'custom' ? 'block' : 'none';
        if (select.value !== 'custom') {
            dateInput.value = '';
            select.form.submit();
        }
    }
</script>
@endsection
