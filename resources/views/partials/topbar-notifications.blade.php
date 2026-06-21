<div class="notification-dropdown-wrapper">
    <button type="button" class="action-btn" id="notificationBtn" onclick="toggleNotifications(event)" aria-label="الإشعارات" style="position:relative;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <span class="notification-badge" id="notificationBadge" @if(($notificationCount ?? 0) <= 0) style="display:none;" @endif></span>
    </button>
    <div class="notification-dropdown" id="notificationMenu" style="display:none;">
        <h4 class="notification-dropdown-title">
            <span>الإشعارات</span>
            @if(($notificationCount ?? 0) > 0)
                <span id="notificationCountBadge" class="notification-count-pill">{{ $notificationCount }} جديدة</span>
            @else
                <span id="notificationCountBadge" class="notification-count-pill" style="display:none;"></span>
            @endif
        </h4>
        <div class="notification-filters" id="notificationFilters">
            <button type="button" class="notification-filter-btn active" data-filter="all">الكل</button>
            <button type="button" class="notification-filter-btn" data-filter="today">اليوم</button>
            <button type="button" class="notification-filter-btn" data-filter="yesterday">أمس</button>
            <button type="button" class="notification-filter-btn" data-filter="week">هذا الأسبوع</button>
            <button type="button" class="notification-filter-btn" data-filter="older">الأقدم</button>
        </div>
        <div id="notificationItemsList" class="notification-items-list">
            @isset($notificationBody)
                {!! $notificationBody !!}
            @else
                <div class="notification-empty-state">
                    لا توجد إشعارات
                </div>
            @endisset
        </div>
        <div id="notificationFilterEmpty" class="notification-empty-state" style="display:none;">
            لا توجد إشعارات في هذا التصفية
        </div>
    </div>
</div>
