@extends($__layout ?? 'records.layout')
@section('title', 'سجل الحيوانات الخارجة | السجلات والتوثيق')
@section('page_title', 'سجل الحيوانات الخارجة')

@section('styles')
@include('records.logs.partials.vet-log-styles')
<style>
    .table-card-footer { padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; background: #FAFBFC; }
</style>
@endsection

@section('content')

@php
    $portalBase = $portalBase ?? '/records';
    $filters = $filters ?? ['q' => '', 'group' => '', 'exit_type' => '', 'period' => '', 'date' => ''];
    $exitTypes = $exitTypes ?? [];
    $logService = app(\App\Services\RecordsExitLogService::class);
@endphp

<div class="top-card">
    <form method="GET" action="{{ $portalBase }}/logs/exits" class="filter-bar" id="exitsFilterForm">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="بحث برقم الحيوان، الاسم، أو الجهة المستلمة...">
        </div>
        <select class="filter-select" name="exit_type" onchange="this.form.submit()">
            <option value="" @selected($filters['exit_type'] === '')>كل أنواع الخروج</option>
            @foreach($exitTypes as $value => $label)
            <option value="{{ $value }}" @selected($filters['exit_type'] === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'selected' => $filters['group']])
        </select>
        <select class="filter-select" name="period" onchange="onExitPeriodChange(this)">
            <option value="" @selected($filters['period'] === '')>كل التواريخ</option>
            <option value="today" @selected($filters['period'] === 'today')>اليوم</option>
            <option value="month" @selected($filters['period'] === 'month')>هذا الشهر</option>
            <option value="year" @selected($filters['period'] === 'year')>هذا العام</option>
            <option value="custom" @selected($filters['period'] === 'custom')>تاريخ محدد</option>
        </select>
        <input
            type="date"
            name="date"
            id="exitCustomDate"
            class="filter-select"
            value="{{ $filters['date'] }}"
            style="display: {{ $filters['period'] === 'custom' ? 'block' : 'none' }};"
            onchange="document.getElementById('exitsFilterForm').submit()"
        >
        <button type="submit" class="filter-select" style="cursor:pointer;background:#f0fdf4;border-color:#bbf7d0;color:#15803d;">بحث</button>
    </form>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">سجل الحيوانات الخارجة</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
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
                @forelse($exits as $exit)
                @php
                    $animal = $exit->animal;
                    $animalCode = $animal?->code;
                @endphp
                <tr>
                    @include('partials.animal-table-cell', [
                        'name' => $animal?->name,
                        'image' => $animal?->displayPhotoUrl(),
                        'animalId' => $animalCode ? '#'.$animalCode : '—',
                        'sub' => $animal?->species ?? '—',
                    ])
                    <td>{{ $animal?->species ?? '—' }}</td>
                    <td>{{ $animal?->group ?? '—' }}</td>
                    <td>{{ $animal?->gender ?? '—' }}</td>
                    <td style="color:#64748b;font-size:0.85rem;">{{ $exit->exit_date?->format('Y-m-d') ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $logService->exitTypeBadgeClass($exit) }}">
                            <span class="dot"></span>{{ $exit->exit_type->label() }}
                        </span>
                    </td>
                    <td>{{ $exit->recipient ?: '—' }}</td>
                    <td><span class="cause-text">{{ $exit->reason ?: '—' }}</span></td>
                    <td>
                        @if($animalCode)
                        <a href="{{ $portalBase }}/animals/{{ $animalCode }}" class="btn-tbl btn-tbl-view" title="عرض الملف">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        @else
                        <span class="cause-text" style="color:#94a3b8;">لا يوجد ملف</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;color:#94a3b8;font-weight:700;padding:2rem;">لا توجد حيوانات خارجة مسجّلة</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($exits->hasPages())
    <div class="table-card-footer">
        {{ $exits->links() }}
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    function onExitPeriodChange(select) {
        const dateInput = document.getElementById('exitCustomDate');
        if (!dateInput) return;
        dateInput.style.display = select.value === 'custom' ? 'block' : 'none';
        if (select.value !== 'custom') {
            dateInput.value = '';
            select.form.submit();
        }
    }
</script>
@endsection
