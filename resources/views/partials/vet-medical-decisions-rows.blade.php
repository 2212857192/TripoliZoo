@foreach($decisions as $decision)
    @php
        $animal = $decision->animal;
    @endphp
    <tr
        data-search="{{ $decision->searchText }}"
        data-type="{{ $decision->typeKey }}"
        data-source="{{ $decision->sourceFilterKey() }}"
        data-receiving="{{ $decision->receivingStatusFilterKey }}"
    >
        <td><span style="font-family:'Courier New',monospace;font-size:0.75rem;background:#f0fdf4;color:#15803d;padding:3px 8px;border-radius:6px;font-weight:700;">{{ $decision->referenceNumber }}</span></td>
        <td>{{ $decision->typeLabel }}</td>
        @include('partials.animal-table-cell', [
            'name' => $animal?->name,
            'emoji' => '🐾',
            'animalId' => $animal?->code,
        ])
        <td>{{ $animal?->species ?? '—' }}</td>
        <td>{{ $decision->sourceLabel }}</td>
        <td>{{ $decision->formattedDecisionDate() }}</td>
        <td>
            <span class="badge {{ $decision->receivingStatusBadgeClass }}">
                <span class="dot"></span>{{ $decision->receivingStatusLabel }}
            </span>
        </td>
        <td class="col-actions">
            <a href="{{ $decision->showUrl }}" class="btn-tbl view" title="عرض التفاصيل">
                @include('partials.icon-eye-view')
            </a>
        </td>
    </tr>
@endforeach
