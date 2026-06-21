<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة تحكم الإدارة | Tripoli Zoo')</title>
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
    @include('partials.dashboard-shell-styles')
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
            <a href="/admin/dashboard" class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span class="nav-item-text">لوحة التحكم</span>
            </a>

            <a href="/admin/employees" class="nav-item {{ request()->is('admin/employees') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span class="nav-item-text">حسابات الموظفين</span>
            </a>

            <a href="/admin/animals" class="nav-item {{ request()->is('admin/animals') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 12H16c-.7 2-2 3-4 3s-3.3-1-4-3H2.5"/><path d="M5.5 5.1L2 12v6c0 1.1.9 2 2 2h16a2 2 0 002-2v-6l-3.4-6.9A2 2 0 0017 5H7a2 2 0 00-1.5.1z"/></svg>
                <span class="nav-item-text">المحتوى التعريفي للحيوانات</span>
            </a>

            <a href="/admin/map-locations" class="nav-item {{ request()->is('admin/map-locations*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon><line x1="9" y1="3" x2="9" y2="18"></line><line x1="15" y1="6" x2="15" y2="21"></line></svg>
                <span class="nav-item-text">خريطة الحديقة</span>
            </a>

            <a href="/admin/tickets" class="nav-item {{ request()->is('admin/tickets') || request()->is('admin/tickets/create') || request()->is('admin/tickets/*/edit') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span class="nav-item-text">إدارة التذاكر</span>
            </a>


            <a href="/admin/visit-info" class="nav-item {{ request()->is('admin/visit-info*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span class="nav-item-text">معلومات الزيارة</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <header class="topbar">
            @include('partials.topbar-page-info', ['sectionLabel' => 'الإدارة', 'defaultTitle' => 'لوحة التحكم'])
            <div class="topbar-actions">
                @include('partials.topbar-notifications', ['notificationCount' => 0])
                @include('partials.topbar-user-menu')
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            @include('partials.flash-messages')
            @yield('content')
        </div>
    </main>

    @include('partials.admin-confirm-dialog')
    @include('partials.admin-ui-scripts')
    @yield('scripts')
    @include('partials.dashboard-shell-scripts')
    
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>
