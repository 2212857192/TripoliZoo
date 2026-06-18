@foreach($notifications as $notification)

    @php $taskNumber = $notification->receivingTask?->task_number; @endphp

    <div class="care-notification-item"

        data-task="{{ $taskNumber }}"

        @if($taskNumber) onclick="openDecisionFromNotification(@json($taskNumber), event)" @endif

        style="font-size:0.8rem;border-bottom:1px solid #f1f5f9;padding:10px 8px;margin-bottom:4px;border-radius:8px;{{ $taskNumber ? 'cursor:pointer;transition:background 0.15s;' : '' }}"

        onmouseover="if(this.dataset.task)this.style.background='#f8fafc'"

        onmouseout="this.style.background='transparent'">

        <p style="font-weight:700;margin-bottom:4px;color:var(--text-main);">📋 {{ $notification->title }}</p>

        <p style="color:var(--text-muted);margin:0;line-height:1.5;">{{ $notification->message }}</p>

    </div>

@endforeach

@foreach($healthCaseNotifications ?? [] as $notification)
    @php $caseNumber = $notification->healthCase?->case_number; @endphp
    @if($caseNumber)
        <form method="POST" action="{{ route('care.health.notification.read', $caseNumber) }}" style="margin:0;">
            @csrf
            <button type="submit"
                style="width:100%;text-align:right;font-size:0.8rem;border:none;background:transparent;border-bottom:1px solid #f1f5f9;padding:10px 8px;margin-bottom:4px;border-radius:8px;cursor:pointer;transition:background 0.15s;"
                onmouseover="this.style.background='#f8fafc'"
                onmouseout="this.style.background='transparent'">
                <p style="font-weight:700;margin-bottom:4px;color:var(--text-main);">🩺 {{ $notification->title }}</p>
                <p style="color:var(--text-muted);margin:0;line-height:1.5;">{{ $notification->message }}</p>
            </button>
        </form>
    @endif
@endforeach

@foreach($operationalNoteNotifications ?? [] as $notification)
    @php $noteNumber = $notification->operationalNote?->note_number; @endphp
    @if($noteNumber)
        <form method="POST" action="{{ route('care.notes.notification.read', $noteNumber) }}" style="margin:0;">
            @csrf
            <button type="submit"
                style="width:100%;text-align:right;font-size:0.8rem;border:none;background:transparent;border-bottom:1px solid #f1f5f9;padding:10px 8px;margin-bottom:4px;border-radius:8px;cursor:pointer;transition:background 0.15s;"
                onmouseover="this.style.background='#f8fafc'"
                onmouseout="this.style.background='transparent'">
                <p style="font-weight:700;margin-bottom:4px;color:var(--text-main);">📝 {{ $notification->title }}</p>
                <p style="color:var(--text-muted);margin:0;line-height:1.5;">{{ $notification->message }}</p>
            </button>
        </form>
    @endif
@endforeach

