@props(['cases', 'vetBase', 'mode' => 'active'])

@forelse($cases as $case)
    @php
        $animal = $case->animal;
        $displayName = $animal?->name;
    @endphp
    <tr>
        @include('partials.animal-table-cell', [
            'name' => $displayName,
            'emoji' => '🐾',
            'animalId' => $animal?->code ? '#'.$animal->code : null,
        ])
        <td>{{ $animal?->species ?? '—' }}</td>
        @if($mode !== 'completed')
            <td>{{ $case->group }}</td>
        @endif
        @if($mode === 'active')
            <td>{{ $case->admitted_at?->format('Y-m-d') ?? '—' }}</td>
        @elseif($mode === 'pending')
            <td>{{ $case->closed_at?->format('Y-m-d') ?? '—' }}</td>
        @else
            <td>{{ $case->admitted_at?->format('Y-m-d') ?? '—' }}</td>
            <td>{{ $case->closed_at?->format('Y-m-d') ?? '—' }}</td>
        @endif
        <td>
            <span class="badge {{ $case->status->badgeClass() }}">
                <span class="dot"></span>{{ $case->status->label() }}
            </span>
        </td>
        <td>
            <div style="display:flex; gap:6px; justify-content: center;">
                <a href="{{ $vetBase }}/cases/hospital/{{ $case->case_number }}" class="btn-tbl view" title="عرض">
                    @include('partials.icon-eye-view')
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="{{ $mode === 'completed' ? 6 : 6 }}" style="text-align:center; padding:2rem; color:#94a3b8; font-weight:700;">
            لا توجد حالات في هذا القسم حالياً.
        </td>
    </tr>
@endforelse
