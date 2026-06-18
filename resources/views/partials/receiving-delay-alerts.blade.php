@forelse($receivingDelays ?? [] as $task)
    @php $animal = $task->animal; @endphp
    <a href="{{ ($portalBase ?? '/care') }}/decisions/{{ $task->task_number }}" class="alert-item">
        <div class="alert-content">
            <div class="alert-icon" style="color: #d97706; background: #fffbeb; border-color: #fde68a;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="alert-text">
                تعذر استلام الحيوان {{ $animal?->code ?? '—' }} مؤقتًا
                @if($task->delay_reason)
                    — {{ \Illuminate\Support\Str::limit($task->delay_reason, 60) }}
                @endif
            </div>
        </div>
        <div style="display:flex; align-items:center;">
            <div class="alert-time">{{ $task->delay_recorded_at?->diffForHumans() ?? '—' }}</div>
            <div class="alert-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
        </div>
    </a>
@empty
@endforelse
