@props([
    'emptyLabel' => null,
    'prompt' => null,
    'withValues' => false,
    'useIds' => false,
    'selected' => null,
])

@if ($prompt)
    <option value="" disabled selected>{{ $prompt }}</option>
@elseif ($emptyLabel !== null)
    <option value="">{{ $emptyLabel }}</option>
@endif

@foreach (animal_group_records() as $group)
    @php
        $value = $useIds ? (string) $group->id : $group->name;
        $isSelected = $useIds
            ? (string) $selected === (string) $group->id
            : $selected === $group->name;
    @endphp
    @if ($withValues || $useIds)
        <option value="{{ $value }}" @selected($isSelected)>{{ $group->name }}</option>
    @else
        <option @selected($isSelected)>{{ $group->name }}</option>
    @endif
@endforeach
