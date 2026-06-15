@props(['name' => null, 'emoji' => '🦁', 'image' => null, 'sub' => null, 'animalId' => null, 'sourceTag' => null])

<td>
    <div class="animal-cell">
        <div class="animal-thumb">
            @if(!empty($image))
                <img src="{{ $image }}" alt="{{ $name ?? '' }}">
            @else
                {{ $emoji }}
            @endif
        </div>
        <div>
            <div class="animal-cell-name @if(empty($name)) is-muted @endif">{{ $name ?? '—' }}</div>
            @if(!empty($animalId))
                <div class="animal-cell-id">{{ $animalId }}</div>
            @endif
            @if(!empty($sub))
                <div class="animal-cell-sub">{{ $sub }}</div>
            @endif
            @if(!empty($sourceTag))
                <span class="source-tag">{{ $sourceTag }}</span>
            @endif
        </div>
    </div>
</td>
