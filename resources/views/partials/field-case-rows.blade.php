@props(['cases', 'vetBase', 'mode' => 'active'])

@php
    $fieldCaseService = app(\App\Services\FieldCaseService::class);
@endphp

@forelse($cases as $case)
    @php
        $animal = $case->animal;
        $lastUpdate = $fieldCaseService->lastUpdatedAt($case);
    @endphp
    <tr>
        @include('partials.animal-table-cell', [
            'name' => $animal?->name,
            'emoji' => '🐾',
            'image' => $animal?->displayPhotoUrl(),
            'animalId' => $animal?->code ? '#'.$animal->code : null,
        ])
        <td>{{ $animal?->species ?? '—' }}</td>
        <td>{{ $case->group }}</td>
        <td>{{ $case->opened_at?->format('Y-m-d') ?? '—' }}</td>
        @if($mode === 'closed')
            <td>{{ $case->closed_at?->format('Y-m-d') ?? '—' }}</td>
        @else
            <td>{{ $lastUpdate?->format('Y-m-d') ?? '—' }}</td>
        @endif
        <td>
            <a href="{{ $vetBase }}/cases/field/{{ $case->case_number }}" class="btn-tbl view" title="عرض التفاصيل">
                @include('partials.icon-eye-view')
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" style="text-align:center; padding:2rem; color:#94a3b8; font-weight:700;">
            لا توجد حالات في هذا القسم حالياً.
        </td>
    </tr>
@endforelse
