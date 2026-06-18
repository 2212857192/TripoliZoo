@extends($__layout ?? 'records.layout')
@section('title', 'سجل النفوق | السجلات والتوثيق')
@section('page_title', 'سجل النفوق')

@section('styles')
@include('records.logs.partials.vet-log-styles')
<style>
    .table-card-footer { padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; background: #FAFBFC; }
</style>
@endsection

@section('content')

@php
    $portalBase = $portalBase ?? '/records';
    $filters = $filters ?? ['q' => '', 'group' => '', 'autopsy' => '', 'period' => '', 'date' => ''];
    $logService = app(\App\Services\RecordsMortalityLogService::class);
@endphp

<div class="top-card">
    <form method="GET" action="{{ $portalBase }}/logs/mortality" class="filter-bar" id="mortalityFilterForm">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="بحث برقم الحيوان، النوع، أو المجموعة...">
        </div>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'selected' => $filters['group']])
        </select>
        <select class="filter-select" name="autopsy" onchange="this.form.submit()">
            <option value="" @selected($filters['autopsy'] === '')>التشريح: الكل</option>
            <option value="yes" @selected($filters['autopsy'] === 'yes')>نعم — تمت الإحالة</option>
            <option value="no" @selected($filters['autopsy'] === 'no')>لا — بدون تشريح</option>
        </select>
        <select class="filter-select" name="period" onchange="onMortalityPeriodChange(this)">
            <option value="" @selected($filters['period'] === '')>كل التواريخ</option>
            <option value="today" @selected($filters['period'] === 'today')>اليوم</option>
            <option value="month" @selected($filters['period'] === 'month')>هذا الشهر</option>
            <option value="year" @selected($filters['period'] === 'year')>هذا العام</option>
            <option value="custom" @selected($filters['period'] === 'custom')>تاريخ محدد</option>
        </select>
        <input
            type="date"
            name="date"
            id="mortalityCustomDate"
            class="filter-select"
            value="{{ $filters['date'] }}"
            style="display: {{ $filters['period'] === 'custom' ? 'block' : 'none' }};"
            onchange="document.getElementById('mortalityFilterForm').submit()"
        >
        <button type="submit" class="filter-select" style="cursor:pointer;background:#f0fdf4;border-color:#bbf7d0;color:#15803d;">بحث</button>
    </form>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">سجل النفوق</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>تاريخ النفوق</th>
                    <th>سبب النفوق النهائي</th>
                    <th>هل تمت الإحالة للتشريح؟</th>
                    <th>تاريخ الاعتماد / التوثيق</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cases as $case)
                @php
                    $animal = $case->animal;
                    $animalCode = $animal?->code ?? $case->subject_code;
                    $hasAutopsy = $logService->hasAutopsy($case);
                    $docDate = $logService->documentationDateFor($case);
                @endphp
                <tr>
                    @include('partials.animal-table-cell', [
                        'name' => $animal?->name,
                        'image' => $animal?->displayPhotoUrl(),
                        'animalId' => '#'.$animalCode,
                        'sub' => $animal?->species ?? $case->subject_type,
                    ])
                    <td>{{ $animal?->species ?? $case->subject_type ?? '—' }}</td>
                    <td>{{ $case->group }}</td>
                    <td style="color:#64748b;font-size:0.85rem;">{{ $case->death_date?->format('Y-m-d') ?? '—' }}</td>
                    <td><span class="cause-text">{{ $logService->finalCauseFor($case) }}</span></td>
                    <td>
                        @if($hasAutopsy)
                        <span class="badge badge-completed"><span class="dot"></span>نعم</span>
                        @else
                        <span class="badge badge-none"><span class="dot"></span>لا</span>
                        @endif
                    </td>
                    <td style="color:#64748b;font-size:0.85rem;">{{ $docDate ?? '—' }}</td>
                    <td>
                        @if($animalCode)
                        <a href="{{ $portalBase }}/animals/{{ $animalCode }}" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#94a3b8;font-weight:700;padding:2rem;">لا توجد حالات نفوق مسجّلة</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cases->hasPages())
    <div class="table-card-footer">
        {{ $cases->links() }}
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    function onMortalityPeriodChange(select) {
        const dateInput = document.getElementById('mortalityCustomDate');
        if (!dateInput) return;
        dateInput.style.display = select.value === 'custom' ? 'block' : 'none';
        if (select.value !== 'custom') {
            dateInput.value = '';
            select.form.submit();
        }
    }
</script>
@endsection
