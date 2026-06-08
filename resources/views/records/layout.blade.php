<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'السجلات والتوثيق | Tripoli Zoo')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --green:   #2E7D32;
            --green-light: #E8F5E9;
            --green-dark: #1B5E20;
            --brown:   #5A2D0C;
            --orange:  #E8651A;
            --white:   #FFFFFF;
            --bg-color: #F8FAFC;
            --border: #E2E8F0;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --sidebar-w: 280px;
            --ease:    cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            background-color: var(--white);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: -2px 0 10px rgba(0,0,0,0.04);
            transition: width 0.3s var(--ease);
            position: relative;
        }

        .toggle-btn-sidebar {
            position: absolute;
            left: -16px;
            top: 2rem;
            width: 32px;
            height: 32px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            z-index: 110;
            transition: all 0.3s var(--ease);
        }

        .toggle-btn-sidebar:hover {
            color: var(--orange);
            border-color: var(--orange);
        }

        .toggle-btn-sidebar svg {
            transition: transform 0.3s var(--ease);
        }

        .sidebar.collapsed .toggle-btn-sidebar svg {
            transform: rotate(180deg);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding: 1.5rem 0;
        }

        .sidebar.collapsed .title,
        .sidebar.collapsed .nav-label,
        .sidebar.collapsed .user-info,
        .sidebar.collapsed .nav-item-text {
            display: none;
        }

        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 12px;
        }

        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(46,125,50,0.15);
            background: linear-gradient(180deg, #e8f5e9 0%, #f0fdf4 60%, #ffffff 100%);
        }

        .sidebar-header .logo {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: var(--bg-color);
            padding: 3px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-header .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }

        .sidebar-header .title h2 {
            font-size: 1.5rem;
            color: var(--brown);
            font-weight: 800;
            letter-spacing: 4px;
            margin: 0;
            text-transform: uppercase;
            line-height: 1;
        }
        .sidebar-header .title h2 span {
            color: var(--green);
            font-weight: 900;
        }

        .sidebar-header .title span {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .sidebar-nav {
            padding: 1.5rem 1rem;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 10px 10px 5px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s var(--ease);
        }

        .nav-item svg {
            width: 36px;
            height: 36px;
            padding: 8px;
            border-radius: 10px;
            color: var(--text-muted);
            box-sizing: border-box;
            background: rgba(0, 0, 0, 0.02);
            transition: all 0.2s;
        }

        .nav-item:hover {
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        .nav-item:hover svg {
            background-color: rgba(0, 0, 0, 0.05);
            color: var(--text-main);
        }

        .nav-item.active {
            background: linear-gradient(135deg, #1a3d1a 0%, #2d6a30 60%, #3a7d3e 100%);
            color: #ffffff;
            font-weight: 700;
            position: relative;
            box-shadow: 0 4px 18px rgba(26, 61, 26, 0.35);
        }

        .nav-item.active svg {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.22);
            border-radius: 10px;
        }

        /* Dropdown Styles */
        .dropdown-menu {
            display: none;
            flex-direction: column;
            gap: 4px;
            padding-right: 20px;
            margin-top: 4px;
            transition: all 0.2s var(--ease);
        }
        .nav-dropdown.open .dropdown-menu {
            display: flex;
        }
        .dropdown-toggle {
            cursor: pointer;
            width: 100%;
            border: none;
            background: none;
            text-align: right;
            font-family: inherit;
        }
        .arrow-icon {
            margin-right: auto;
            transition: transform 0.2s;
        }
        .nav-dropdown.open .arrow-icon {
            transform: rotate(180deg);
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--border);
        }
        
        .sidebar.collapsed .sidebar-footer {
            padding: 1.5rem 10px;
        }
        
        .sidebar.collapsed .user-card {
            justify-content: center;
            padding: 10px 0;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: var(--bg-color);
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--orange);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
        }

        .user-info h4 {
            font-size: 0.9rem;
            color: var(--text-main);
            margin-bottom: 2px;
        }

        .user-info p {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* ── Main Content ── */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .topbar {
            height: 76px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 50;
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .page-title {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .page-title h1 {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            letter-spacing: -0.2px;
        }

        .page-title .breadcrumb {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .page-title .breadcrumb span {
            color: var(--orange);
            font-weight: 800;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .action-btn {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--bg-color);
            border: 1px solid var(--border);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: var(--white);
            color: var(--orange);
            border-color: var(--orange);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #FEE2E2;
            color: #DC2626;
            border: none;
            border-radius: 8px;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #FECACA;
        }

        .content-area {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Reusable Components for views */
        .card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--bg-color);
            padding-bottom: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-main);
        }

    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <button id="sidebarToggle" class="toggle-btn-sidebar">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <div class="sidebar-header">
            <div class="logo">
                <img src="/logo.jpg" alt="Logo">
            </div>
            <div class="title">
                <h2>TRIPOLI <span>ZOO</span></h2>
                <span>نظام الإدارة المتكامل</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="/records/dashboard" class="nav-item {{ request()->is('records/dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span class="nav-item-text">الرئيسية</span>
            </a>

            <a href="/records/animals" class="nav-item {{ request()->is('records/animals*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span class="nav-item-text">قائمة الحيوانات</span>
            </a>

            <!-- Dropdown Logs -->
            <div class="nav-dropdown {{ request()->is('records/logs*') ? 'open' : '' }}">
                <button class="nav-item dropdown-toggle" onclick="toggleDropdown(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span class="nav-item-text">السجلات الرسمية</span>
                    <svg class="arrow-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="dropdown-menu" style="{{ request()->is('records/logs*') ? 'display: flex;' : 'display: none;' }}">
                    <a href="/records/logs/births" class="nav-item {{ request()->is('records/logs/births') ? 'active' : '' }}">
                        <span style="font-size: 0.8rem; color: #64748b;">•</span>
                        <span class="nav-item-text">سجل الولادات</span>
                    </a>
                    <a href="/records/logs/stillbirths" class="nav-item {{ request()->is('records/logs/stillbirths') ? 'active' : '' }}">
                        <span style="font-size: 0.8rem; color: #64748b;">•</span>
                        <span class="nav-item-text">سجل الولادات النافقة</span>
                    </a>
                    <a href="/records/logs/entries" class="nav-item {{ request()->is('records/logs/entries') ? 'active' : '' }}">
                        <span style="font-size: 0.8rem; color: #64748b;">•</span>
                        <span class="nav-item-text">سجل الحيوانات الداخلة</span>
                    </a>
                    <a href="/records/logs/exits" class="nav-item {{ request()->is('records/logs/exits') ? 'active' : '' }}">
                        <span style="font-size: 0.8rem; color: #64748b;">•</span>
                        <span class="nav-item-text">سجل الحيوانات الخارجة</span>
                    </a>
                    <a href="/records/logs/mortality" class="nav-item {{ request()->is('records/logs/mortality') ? 'active' : '' }}">
                        <span style="font-size: 0.8rem; color: #64748b;">•</span>
                        <span class="nav-item-text">سجل النفوق</span>
                    </a>
                    <a href="/records/logs/slaughter" class="nav-item {{ request()->is('records/logs/slaughter') ? 'active' : '' }}">
                        <span style="font-size: 0.8rem; color: #64748b;">•</span>
                        <span class="nav-item-text">سجل الذبح الاضطراري</span>
                    </a>
                </div>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div style="padding: 1.2rem; border-top: 1px solid #e2e8f0;">
                <a href="/login" style="display: flex; align-items: center; gap: 8px; padding: 10px 12px; background: #fef2f2; color: #e11d48; border-radius: 8px; text-decoration: none; font-family: 'Cairo', sans-serif; font-size: 0.8rem; font-weight: 700; transition: all 0.2s;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    تسجيل خروج
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <header class="topbar">
            <div class="topbar-right">
                <div style="display:flex; align-items:center; justify-content:center; width:42px; height:42px; background:var(--green-light); color:var(--green); border-radius:12px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                </div>
                <div class="page-title">
                    <h1>@yield('page_title', 'لوحة التحكم')</h1>
                    <div class="breadcrumb">
                        السجلات والتوثيق <span>/</span> @yield('page_title', 'لوحة التحكم')
                    </div>
                </div>
            </div>
            
            <div class="topbar-actions">
                <div style="display: flex; align-items: center; gap: 10px; padding-left: 15px; border-left: 1px solid var(--border);">
                    <div style="text-align: left;">
                        <div style="font-size: 0.85rem; font-weight: 800; color: var(--text-main); line-height: 1;">أ. فاطمة سالم</div>
                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">موظف قسم السجلات</span>
                    </div>
                    <div style="width:38px; height:38px; border-radius:10px; background:#f1f5f9; color:#1e293b; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1rem;">ف.س</div>
                </div>
                
                <!-- Notification Dropdown -->
                <div class="notification-dropdown-wrapper" style="position: relative;">
                    <button class="action-btn" id="notificationBtn" onclick="toggleNotifications()" style="position: relative;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span class="notification-badge" style="position: absolute; top: 8px; right: 8px; background: var(--orange); width: 8px; height: 8px; border-radius: 50%; box-shadow: 0 0 0 2px var(--white);"></span>
                    </button>
                    <div class="notification-dropdown" id="notificationMenu" style="display: none; position: absolute; left: 0; top: 54px; width: 340px; background: white; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); z-index: 1000; padding: 18px;">
                        <h4 style="font-size: 0.95rem; font-weight: 800; border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-bottom: 12px; color: var(--text-main); display:flex; justify-content:space-between; align-items:center;">
                            الإشعارات
                            <span style="background:#fef2f2; color:#ef4444; font-size:0.7rem; padding:2px 8px; border-radius:20px;">0 جديدة</span>
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <div style="font-size: 0.8rem; text-align:center; padding: 20px 0; color:var(--text-muted);">
                                لا توجد إشعارات جديدة
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </main>

    @yield('scripts')
    
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });

        function toggleDropdown(btn) {
            const dropdown = btn.parentElement;
            const menu = dropdown.querySelector('.dropdown-menu');
            dropdown.classList.toggle('open');
            if (dropdown.classList.contains('open')) {
                menu.style.display = 'flex';
            } else {
                menu.style.display = 'none';
            }
        }

        function toggleNotifications() {
            const menu = document.getElementById('notificationMenu');
            if (menu.style.display === 'none') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }

        // Close notifications on clicking outside
        window.addEventListener('click', function(e) {
            const btn = document.getElementById('notificationBtn');
            const menu = document.getElementById('notificationMenu');
            if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    </script>
</body>
</html>
