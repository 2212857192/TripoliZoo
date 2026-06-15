<div class="topbar-user-menu" id="topbarUserMenu">
    <div class="user-menu-info">
        <div class="user-menu-name">{{ auth()->user()->name }}</div>
        <div class="user-menu-email">{{ auth()->user()->email }}</div>
    </div>
    <div class="user-menu-avatar" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>
    <button type="button" class="user-menu-chevron" id="userMenuChevron" onclick="toggleUserMenu(event)" aria-label="قائمة المستخدم" aria-expanded="false">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <div class="user-menu-dropdown" id="userMenuDropdown">
        @include('partials.portal-logout', ['variant' => 'dropdown'])
    </div>
</div>
