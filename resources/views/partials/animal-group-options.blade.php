@props([
    'emptyLabel' => null,
    'prompt' => null,
    'withValues' => false,
])

@if ($prompt)
    <option value="" disabled selected>{{ $prompt }}</option>
@elseif ($emptyLabel !== null)
    <option value="">{{ $emptyLabel }}</option>
@endif

@foreach (animal_groups() as $group)
    @if ($withValues)
        <option value="{{ $group }}">{{ $group }}</option>
    @else
        <option>{{ $group }}</option>
    @endif
@endforeach
