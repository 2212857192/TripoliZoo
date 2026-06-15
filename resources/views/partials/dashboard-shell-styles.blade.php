<style>
    /* ── Sidebar: تدرج أخضر تحت الشعار ── */
    .sidebar-header {
        background: linear-gradient(180deg, #e8f5e9 0%, #f0fdf4 60%, #ffffff 100%) !important;
        border-bottom: 1px solid rgba(46, 125, 50, 0.15) !important;
    }

    .sidebar-header .logo {
        border-radius: 50%;
        background: #fff;
    }

    .sidebar-header .logo img {
        border-radius: 50%;
    }

    /* ── Sidebar: أيقونات بدون مربعات ── */
    .nav-item svg:not(.arrow-icon) {
        width: 22px;
        height: 22px;
        min-width: 22px;
        padding: 0;
        border-radius: 0;
        background: transparent !important;
        box-sizing: content-box;
    }

    .nav-item:hover svg:not(.arrow-icon) {
        background: transparent !important;
    }

    .nav-item.active svg:not(.arrow-icon) {
        background: transparent !important;
        border-radius: 0;
    }

    .dropdown-toggle svg.arrow-icon {
        width: 16px;
        height: 16px;
        padding: 0;
        background: transparent !important;
    }

    /* ── الشريط العلوي ── */
    .topbar {
        height: 68px;
        background: #ffffff;
        backdrop-filter: none;
        border-bottom: 1px solid var(--border);
        box-shadow: none;
        padding: 0 1.75rem;
        justify-content: space-between;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 0;
    }

    .page-title h1 {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .page-title .breadcrumb {
        font-size: 0.72rem;
        color: #94a3b8;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .page-title .breadcrumb span {
        color: #94a3b8;
        font-weight: 600;
    }

    .topbar-actions {
        gap: 14px;
        align-items: center;
    }

    /* ── زر الإشعارات دائري ── */
    .action-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        color: #64748b;
        flex-shrink: 0;
    }

    .action-btn:hover {
        background: #f8fafc;
        color: #334155;
        border-color: #cbd5e1;
    }

    .notification-badge {
        position: absolute;
        top: 9px;
        right: 10px;
        background: #ef4444;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        box-shadow: 0 0 0 2px #fff;
    }

    .notification-dropdown {
        position: absolute;
        left: 0;
        top: calc(100% + 8px);
        width: 340px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        z-index: 1000;
        padding: 18px;
    }

    /* ── قائمة المستخدم ── */
    .topbar-user-menu {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-left: 14px;
        border-left: 1px solid var(--border);
    }

    .user-menu-info {
        text-align: left;
        line-height: 1.35;
    }

    .user-menu-name {
        font-size: 0.88rem;
        font-weight: 800;
        color: #0f172a;
    }

    .user-menu-email {
        font-size: 0.72rem;
        font-weight: 600;
        color: #94a3b8;
    }

    .user-menu-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .user-menu-chevron {
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .user-menu-chevron:hover {
        background: #f1f5f9;
        color: #475569;
    }

    .user-menu-chevron.open {
        color: #334155;
    }

    .user-menu-dropdown {
        display: none;
        position: absolute;
        left: 0;
        top: calc(100% + 10px);
        min-width: 180px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        z-index: 1001;
        overflow: hidden;
        padding: 6px;
    }

    .user-menu-dropdown.open {
        display: block;
    }

    .user-menu-dropdown a,
    .user-menu-dropdown button[type="submit"] {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-family: 'Cairo', sans-serif;
        font-size: 0.82rem;
        font-weight: 700;
        color: #dc2626;
        transition: background 0.2s;
        width: 100%;
        background: none;
        border: none;
        cursor: pointer;
    }

    .user-menu-dropdown a:hover,
    .user-menu-dropdown button[type="submit"]:hover {
        background: #fef2f2;
    }

    .sidebar .sidebar-footer {
        display: block;
        margin-top: auto;
    }

    .portal-logout-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: 'Cairo', sans-serif;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .portal-logout-btn.is-sidebar {
        width: 100%;
        padding: 10px 12px;
        background: #fef2f2;
        color: #e11d48;
        border: none;
        border-radius: 8px;
        font-size: 0.8rem;
    }

    .portal-logout-btn.is-sidebar:hover {
        background: #fee2e2;
    }

    .portal-logout-btn.is-dropdown {
        width: 100%;
        padding: 10px 12px;
        background: none;
        color: #e11d48;
        border: none;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .portal-logout-btn.is-dropdown:hover {
        background: #fef2f2;
    }

    /* ── تنقل داخل الصفحة (بدل الهيدر) ── */
    .page-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .page-breadcrumb a {
        color: #2E7D32;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: color 0.2s;
    }

    .page-breadcrumb a:hover {
        color: #1b5e20;
    }

    .page-breadcrumb .current {
        color: #0f172a;
        font-weight: 800;
    }

    .header-card-stacked {
        flex-direction: column;
        align-items: stretch;
    }

    .header-card-stacked .header-card-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    /* ── عمود الحيوان الموحّد (صورة + اسم) ── */
    .animal-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .animal-thumb {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        flex-shrink: 0;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
        border: 1.5px solid #bbf7d0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        overflow: hidden;
    }

    .animal-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .animal-cell-name {
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .animal-cell-name.is-muted {
        color: #94a3b8;
        font-style: italic;
        font-weight: 600;
    }

    .animal-cell-id {
        font-family: 'Courier New', monospace;
        font-size: 0.72rem;
        color: #475569;
        font-weight: 800;
        margin-top: 2px;
        letter-spacing: 0.02em;
    }

    .animal-cell-sub {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        margin-top: 2px;
    }

    /* ── فلتر التاريخ مع تاريخ محدد ── */
    .date-filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .date-filter-picker-wrap {
        display: none;
    }

    .date-filter-picker-wrap.show {
        display: block;
    }

    .date-filter-picker-wrap input {
        width: 100%;
    }

</style>
