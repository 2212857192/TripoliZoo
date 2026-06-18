@props([
    'emptyLabel' => null,
    'prompt' => null,
    'withValues' => false,
    'selected' => null,
])

@if ($prompt)
    <option value="" disabled selected>{{ $prompt }}</option>
@elseif ($emptyLabel !== null)
    <option value="">{{ $emptyLabel }}</option>
@endif

@foreach (animal_groups() as $group)
    @if ($withValues)
        <option value="{{ $group }}" @selected($selected === $group)>{{ $group }}</option>
    @else
        <option @selected($selected === $group)>{{ $group }}</option>
    @endif
@endforeach
