<div class="notification-dropdown-wrapper">
    <button type="button" class="action-btn" id="notificationBtn" onclick="toggleNotifications(event)" aria-label="الإشعارات" style="position:relative;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        @if(($notificationCount ?? 0) > 0)
            <span class="notification-badge"></span>
        @endif
    </button>
    <div class="notification-dropdown" id="notificationMenu" style="display:none;">
        <h4 style="font-size:0.95rem;font-weight:800;border-bottom:1px solid var(--border);padding-bottom:10px;margin-bottom:12px;color:var(--text-main);display:flex;justify-content:space-between;align-items:center;">
            الإشعارات
            <span style="background:#fef2f2;color:#ef4444;font-size:0.7rem;padding:2px 8px;border-radius:20px;">{{ $notificationCount ?? 0 }} جديدة</span>
        </h4>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @isset($notificationBody)
                {!! $notificationBody !!}
            @else
                <div style="font-size:0.8rem;text-align:center;padding:20px 0;color:var(--text-muted);">
                    لا توجد إشعارات جديدة
                </div>
            @endisset
        </div>
    </div>
</div>
