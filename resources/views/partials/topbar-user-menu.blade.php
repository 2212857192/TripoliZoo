<div class="topbar-user-menu" id="topbarUserMenu">
    <button type="button" class="user-menu-trigger" onclick="toggleUserMenu(event)" aria-haspopup="true" aria-expanded="false" id="userMenuTrigger">
        <div class="user-menu-info">
            <div class="user-menu-name">{{ auth()->user()->name }}</div>
            <div class="user-menu-email">{{ auth()->user()->role }}</div>
        </div>
        <div class="user-menu-avatar" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <span class="user-menu-chevron" id="userMenuChevron" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </span>
    </button>
    <div class="user-menu-dropdown" id="userMenuDropdown">
        <button type="button" class="user-menu-action user-menu-change-password" onclick="openChangePasswordModal(event)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            تغيير كلمة المرور
        </button>
        @include('partials.portal-logout', ['variant' => 'dropdown'])
    </div>
</div>

@include('partials.change-password-modal')
