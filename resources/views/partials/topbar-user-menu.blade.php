<div class="topbar-user-menu" id="topbarUserMenu">
    <div class="user-menu-info">
        <div class="user-menu-name">{{ $userName ?? 'Jehad' }}</div>
        <div class="user-menu-email">{{ $userEmail ?? 'admin@wameedh.com' }}</div>
    </div>
    <div class="user-menu-avatar" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>
    <button type="button" class="user-menu-chevron" id="userMenuChevron" onclick="toggleUserMenu(event)" aria-label="قائمة المستخدم" aria-expanded="false">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <div class="user-menu-dropdown" id="userMenuDropdown">
        <a href="/login">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            تسجيل الخروج
        </a>
    </div>
</div>
