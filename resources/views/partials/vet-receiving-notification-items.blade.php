@foreach($notifications as $notification)
    @php $taskNumber = $notification->receivingTask?->task_number; @endphp
    <div class="vet-receiving-notification-item"
        data-task="{{ $taskNumber }}"
        @if($taskNumber) onclick="openVetDecisionFromNotification(@json($taskNumber), event)" @endif
        style="font-size:0.8rem;border-bottom:1px solid #f1f5f9;padding:10px 8px;margin-bottom:4px;border-radius:8px;{{ $taskNumber ? 'cursor:pointer;transition:background 0.15s;' : '' }}"
        onmouseover="if(this.dataset.task)this.style.background='#f8fafc'"
        onmouseout="this.style.background='transparent'">
        <p style="font-weight:700;margin-bottom:4px;color:var(--text-main);">📋 {{ $notification->title }}</p>
        <p style="color:var(--text-muted);margin:0;line-height:1.5;">{{ $notification->message }}</p>
    </div>
@endforeach
