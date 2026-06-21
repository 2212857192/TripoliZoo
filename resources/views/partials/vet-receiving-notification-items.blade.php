@foreach($notifications as $notification)
    @php
        $taskNumber = $notification->receivingTask?->task_number;
        $isUnread = $notification->read_at === null;
    @endphp
    <div class="portal-notification-item vet-receiving-notification-item {{ $isUnread ? 'is-unread' : 'is-read' }}"
        data-task="{{ $taskNumber }}"
        data-unread="{{ $isUnread ? '1' : '0' }}"
        data-created-at="{{ $notification->created_at?->timestamp ?? 0 }}"
        @if($taskNumber) onclick="openVetDecisionFromNotification(@json($taskNumber), event)" @endif>
        <div class="portal-notification-head">
            <p class="portal-notification-title">📋 {{ $notification->title }}</p>
            @if($isUnread)
                <span class="notification-new-badge">جديد</span>
            @endif
        </div>
        <p class="portal-notification-message">{{ $notification->message }}</p>
        @if($notification->created_at)
            <span class="portal-notification-time">{{ $notification->created_at->diffForHumans() }}</span>
        @endif
    </div>
@endforeach
