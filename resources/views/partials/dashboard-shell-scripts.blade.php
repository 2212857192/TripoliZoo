<script>
    function toggleUserMenu(e) {
        if (e) e.stopPropagation();
        var dropdown = document.getElementById('userMenuDropdown');
        var chevron = document.getElementById('userMenuChevron');
        if (!dropdown) return;
        var isOpen = dropdown.classList.contains('open');
        dropdown.classList.toggle('open', !isOpen);
        if (chevron) {
            chevron.classList.toggle('open', !isOpen);
            chevron.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
        }
        var menu = document.getElementById('notificationMenu');
        if (menu) menu.style.display = 'none';
    }

    function toggleNotifications(e) {
        if (e) e.stopPropagation();
        var menu = document.getElementById('notificationMenu');
        if (!menu) return;
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        var dropdown = document.getElementById('userMenuDropdown');
        var chevron = document.getElementById('userMenuChevron');
        if (dropdown) dropdown.classList.remove('open');
        if (chevron) {
            chevron.classList.remove('open');
            chevron.setAttribute('aria-expanded', 'false');
        }
    }

    window.addEventListener('click', function (e) {
        var notifBtn = document.getElementById('notificationBtn');
        var notifMenu = document.getElementById('notificationMenu');
        if (notifBtn && notifMenu && !notifBtn.contains(e.target) && !notifMenu.contains(e.target)) {
            notifMenu.style.display = 'none';
        }

        var userMenu = document.getElementById('topbarUserMenu');
        var userDropdown = document.getElementById('userMenuDropdown');
        if (userMenu && userDropdown && !userMenu.contains(e.target)) {
            userDropdown.classList.remove('open');
            var chevron = document.getElementById('userMenuChevron');
            if (chevron) {
                chevron.classList.remove('open');
                chevron.setAttribute('aria-expanded', 'false');
            }
        }
    });
</script>
