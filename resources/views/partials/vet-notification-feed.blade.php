@foreach($vetNotificationFeed as $item)
    <div class="portal-notification-item vet-notification-feed-item {{ $item['is_unread'] ? 'is-unread' : 'is-read' }}"
        data-notification-kind="{{ $item['kind'] }}"
        data-reference="{{ $item['reference'] }}"
        data-unread="{{ $item['is_unread'] ? '1' : '0' }}"
        data-created-at="{{ $item['created_timestamp'] }}"
        onclick="handleVetNotificationClick(this, event)">
        <div class="portal-notification-head">
            <p class="portal-notification-title">{{ $item['icon'] }} {{ $item['title'] }}</p>
            @if($item['is_unread'])
                <span class="notification-new-badge">جديد</span>
            @endif
        </div>
        <p class="portal-notification-message">{{ $item['message'] }}</p>
        @if(!empty($item['time_label']))
            <span class="portal-notification-time">{{ $item['time_label'] }}</span>
        @endif
        <div class="portal-notification-footer">
            @if($item['is_unread'])
                <button type="button" class="notification-mark-read-btn" onclick="markPortalNotificationItemRead(event, this)">تمت القراءة</button>
            @else
                <span class="notification-read-badge">مقروء</span>
            @endif
        </div>
    </div>
@endforeach
