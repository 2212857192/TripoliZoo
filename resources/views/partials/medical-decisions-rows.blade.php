@foreach($decisions as $decision)
    @php
        $animal = $decision->animal;
        $typeClass = match ($decision->typeKey) {
            'release' => 'type-release',
            'slaughter' => 'type-slaughter',
            default => 'type-discharge',
        };
    @endphp
    <tr
        data-search="{{ $decision->searchText }}"
        data-type="{{ $decision->typeKey }}"
        data-receiving="{{ $decision->receivingStatusFilterKey }}"
    >
        <td>
            <span class="badge {{ $typeClass }}">
                <span class="dot"></span>{{ $decision->typeLabel }}
            </span>
        </td>
        @include('partials.animal-table-cell', [
            'name' => $animal?->name,
            'emoji' => '🐾',
            'animalId' => $animal?->code,
        ])
        <td style="font-weight:700;">{{ $animal?->species ?? '—' }}</td>
        <td>{{ $animal?->group ?? '—' }}</td>
        <td>{{ $decision->formattedDecisionDate() }}</td>
        <td>
            <span class="badge {{ $decision->receivingStatusFilterKey === 'not-required' ? 'status-none' : match ($decision->receivingStatusFilterKey) {
                'pending' => 'status-pending',
                'received' => 'status-received',
                'failed' => 'status-failed',
                default => 'status-none',
            } }}">
                {{ $decision->receivingStatusLabel }}
            </span>
        </td>
        <td>
            <a href="{{ $decision->showUrl }}" class="btn-tbl view" title="عرض التفاصيل">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
        </td>
    </tr>
@endforeach
