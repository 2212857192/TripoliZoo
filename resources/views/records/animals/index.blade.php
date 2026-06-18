@extends($__layout ?? 'records.layout')
@section('title', 'الحيوانات داخل الحديقة | السجلات والتوثيق')
@section('page_title', 'الحيوانات داخل الحديقة')

@section('styles')
@include('records.logs.partials.vet-log-styles')
<style>
    .legend-wrap { display:flex; align-items:center; gap:6px; margin-right:auto; }
    .legend-dot { width:10px; height:10px; border-radius:50%; background:#15803d; }
    .legend-text { font-size:0.8rem; font-weight:700; color:#64748b; }

    .animal-id { font-family:'Courier New',monospace; font-size:0.8rem; background:#f8fafc; padding:3px 8px; border-radius:6px; color:#334155; font-weight:800; display:inline-block; border:1px solid #e2e8f0; }
    .animal-id.monitoring { color:#15803d; background:#f0fdf4; border-color:#bbf7d0; }
    .source-tag { font-size:0.68rem; font-weight:800; padding:2px 7px; border-radius:6px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; margin-top:3px; display:inline-block; }
</style>
@endsection

@section('content')

{{-- ═══════ HEADER & FILTERS ═══════ --}}
@php
    $portalBase = $portalBase ?? '/records';
    $filters = $filters ?? ['q' => '', 'group' => ''];
@endphp

<div class="top-card">
    <form method="GET" action="{{ $portalBase }}/animals" class="filter-bar" id="animalsFilterForm">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="بحث برقم الحيوان، الاسم، أو النوع...">
        </div>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'selected' => $filters['group']])
        </select>
        <div class="legend-wrap">
            <span class="legend-dot"></span>
            <span class="legend-text">المواليد قيد المتابعة</span>
        </div>
    </form>
</div>

{{-- ═══ TABLE ═══ --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">الحيوانات داخل الحديقة فعلياً</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>الجنس</th>
                    <th>العمر</th>
                    <th>تاريخ التسجيل</th>
                    <th class="col-actions">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($animals ?? [] as $animal)
                @php
                    $isMonitoring = $animal->status === \App\Enums\AnimalStatus::UnderBirthFollowUp->value;
                    $sourceTag = match (true) {
                        $animal->source === 'records' => 'إدخال يدوي',
                        $animal->status === \App\Enums\AnimalStatus::UnderBirthFollowUp->value => 'مولود قيد المتابعة',
                        $animal->source === 'quarantine' => 'وارد رسمياً',
                        default => null,
                    };
                @endphp
                <tr>
                    @include('partials.animal-table-cell', [
                        'name' => $animal->name,
                        'image' => $animal->displayPhotoUrl(),
                        'animalId' => '#'.$animal->code,
                        'sourceTag' => $sourceTag,
                    ])
                    <td style="font-weight:700;">{{ $animal->species }}</td>
                    <td>{{ $animal->group }}</td>
                    <td>{{ $animal->gender }}</td>
                    <td style="color:#64748b; font-size:0.85rem;">{{ $animal->formattedAge() }}</td>
                    <td style="color:#64748b; font-size:0.85rem;">{{ $animal->registered_at?->format('Y-m-d') ?? '—' }}</td>
                    <td class="col-actions">
                        <a href="{{ $portalBase }}/animals/{{ $animal->code }}" class="btn-tbl btn-tbl-view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:#94a3b8;font-weight:700;padding:2rem;">لا توجد حيوانات مسجّلة حالياً</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
