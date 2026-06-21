@foreach($notifications as $notification)
    @php
        $taskNumber = $notification->receivingTask?->task_number;
        $isUnread = $notification->read_at === null;
    @endphp
    <div class="portal-notification-item care-notification-item {{ $isUnread ? 'is-unread' : 'is-read' }}"
        data-task="{{ $taskNumber }}"
        data-unread="{{ $isUnread ? '1' : '0' }}"
        data-created-at="{{ $notification->created_at?->timestamp ?? 0 }}"
        @if($taskNumber) onclick="openDecisionFromNotification(@json($taskNumber), event)" @endif>
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

@foreach($healthCaseNotifications ?? [] as $notification)
    @php
        $caseNumber = $notification->healthCase?->case_number;
        $isUnread = $notification->read_at === null;
    @endphp
    @if($caseNumber)
        <div class="portal-notification-item care-health-notification-item {{ $isUnread ? 'is-unread' : 'is-read' }}"
            data-case="{{ $caseNumber }}"
            data-unread="{{ $isUnread ? '1' : '0' }}"
            data-created-at="{{ $notification->created_at?->timestamp ?? 0 }}"
            onclick="openHealthCaseFromNotification(@json($caseNumber), event)">
            <div class="portal-notification-head">
                <p class="portal-notification-title">🩺 {{ $notification->title }}</p>
                @if($isUnread)
                    <span class="notification-new-badge">جديد</span>
                @endif
            </div>
            <p class="portal-notification-message">{{ $notification->message }}</p>
            @if($notification->created_at)
                <span class="portal-notification-time">{{ $notification->created_at->diffForHumans() }}</span>
            @endif
        </div>
    @endif
@endforeach

@foreach($operationalNoteNotifications ?? [] as $notification)
    @php
        $noteNumber = $notification->operationalNote?->note_number;
        $isUnread = $notification->read_at === null;
    @endphp
    @if($noteNumber)
        <div class="portal-notification-item care-note-notification-item {{ $isUnread ? 'is-unread' : 'is-read' }}"
            data-note="{{ $noteNumber }}"
            data-unread="{{ $isUnread ? '1' : '0' }}"
            data-created-at="{{ $notification->created_at?->timestamp ?? 0 }}"
            onclick="openOperationalNoteFromNotification(@json($noteNumber), event)">
            <div class="portal-notification-head">
                <p class="portal-notification-title">📝 {{ $notification->title }}</p>
                @if($isUnread)
                    <span class="notification-new-badge">جديد</span>
                @endif
            </div>
            <p class="portal-notification-message">{{ $notification->message }}</p>
            @if($notification->created_at)
                <span class="portal-notification-time">{{ $notification->created_at->diffForHumans() }}</span>
            @endif
        </div>
    @endif
@endforeach
