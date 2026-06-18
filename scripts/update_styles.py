import re

def update_layout():
    with open('resources/views/vet/layout.blade.php', 'r', encoding='utf-8') as f:
        content = f.read()

    new_styles = """    <style>
        :root {
            --green:   #2E7D32;
            --green2:  #388E3C;
            --brown:   #5A2D0C;
            --brown2:  #3B1A06;
            --orange:  #E8651A;
            --orange2: #BF4F10;
            --white:   #FFFFFF;
            --off:     #F8F3EC;
            --bg-color: #F8F3EC;
            --border: #E8DFD5;
            --text-main: #1A1A1A;
            --text-muted: #5A5A5A;
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
            direction: rtl;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            background: linear-gradient(135deg, var(--brown2) 0%, var(--brown) 60%, var(--green) 100%);
            border-left: 1px solid rgba(255,255,255,0.1);
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: -4px 0 15px rgba(0,0,0,0.1);
            transition: width 0.3s var(--ease);
            position: relative;
        }

        .toggle-btn-sidebar {
            position: absolute;
            left: -16px;
            top: 2rem;
            width: 32px;
            height: 32px;
            background: var(--orange);
            border: 2px solid var(--off);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--white);
            box-shadow: 0 4px 10px rgba(232,101,26,0.4);
            z-index: 110;
            transition: all 0.3s var(--ease);
        }

        .toggle-btn-sidebar:hover {
            background: var(--orange2);
            transform: scale(1.1);
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
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header .logo {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--white);
            padding: 3px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-header .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .sidebar-header .title h2 {
            font-size: 1.4rem;
            color: var(--white);
            font-weight: 800;
            margin: 0;
            line-height: 1;
        }
        .sidebar-header .title h2 span {
            color: var(--orange);
            font-weight: 900;
        }

        .sidebar-header .title span {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
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
            color: rgba(255,255,255,0.5);
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
            color: rgba(255,255,255,0.82);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s var(--ease);
        }

        .nav-item svg {
            width: 36px;
            height: 36px;
            padding: 8px;
            border-radius: 10px;
            color: rgba(255,255,255,0.82);
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.25s;
        }

        .nav-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: var(--white);
        }

        .nav-item:hover svg {
            background-color: rgba(255,255,255,0.15);
            color: var(--white);
        }

        .nav-item.active {
            background: var(--orange);
            color: var(--white);
            font-weight: 700;
            position: relative;
            box-shadow: 0 4px 14px rgba(232,101,26,0.45);
        }

        .nav-item.active svg {
            color: var(--white);
            background: rgba(255, 255, 255, 0.2);
        }

        /* Dropdown Styles */
        .dropdown-menu {
            display: none;
            flex-direction: column;
            gap: 4px;
            padding-right: 20px; /* RTL correct */
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
            margin-right: auto; /* RTL correct - pushes to left */
            transition: transform 0.2s;
        }
        .nav-dropdown.open .arrow-icon {
            transform: rotate(180deg);
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar.collapsed .sidebar-footer {
            padding: 1.5rem 10px;
        }

        /* ── Main Content ── */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: visible;
        }

        .topbar {
            height: 76px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 300;
            position: relative;
            overflow: visible;
            box-shadow: 0 2px 20px rgba(0,0,0,0.03);
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
            color: var(--brown);
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
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--white);
            border: 1px solid var(--border);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.25s var(--ease);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .action-btn:hover {
            background: var(--orange);
            color: var(--white);
            border-color: var(--orange);
            transform: translateY(-2px);
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
            padding: 2.5rem;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Reusable Components for views */
        .card {
            background: var(--white);
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }
        .card::after {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--orange), var(--green));
            transform: scaleX(0); transform-origin: right;
            transition: transform .4s var(--ease);
            border-radius: 20px 20px 0 0;
        }
        .card:hover::after { transform: scaleX(1); transform-origin: left; }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1rem;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--brown);
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>"""
    
    # We replace from <style> to </style> that comes right before @include('partials.dashboard-shell-styles')
    content = re.sub(r'<style>.*?</style>(?=\s*@include\(\'partials\.dashboard-shell-styles\'\))', new_styles, content, flags=re.DOTALL)
    
    with open('resources/views/vet/layout.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)

def update_dashboard():
    with open('resources/views/vet/dashboard.blade.php', 'r', encoding='utf-8') as f:
        content = f.read()

    new_styles = """<style>
    /* ═══ STATS GRID ═══ */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.2rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background: var(--white);
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        transition: transform .4s var(--ease), box-shadow .4s var(--ease);
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--orange), var(--green));
        transform: scaleX(0);
        transform-origin: right;
        transition: transform .4s var(--ease);
        border-radius: 20px 20px 0 0;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.08);
    }
    .stat-card:hover::after {
        transform: scaleX(1);
        transform-origin: left;
    }

    .stat-icon-wrap {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.2rem;
        border-radius: 14px;
        color: var(--orange);
        background: linear-gradient(135deg, rgba(232,101,26,0.15), rgba(232,101,26,0.06));
        transition: transform .3s var(--ease);
    }
    
    .stat-card:hover .stat-icon-wrap {
        transform: scale(1.1) rotate(-6deg);
    }

    .stat-card:nth-child(even) .stat-icon-wrap {
        color: var(--green);
        background: linear-gradient(135deg, rgba(46,125,50,0.15), rgba(46,125,50,0.06));
    }

    .stat-num {
        font-size: 2.4rem;
        font-weight: 900;
        color: var(--brown);
        line-height: 1;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--muted);
        line-height: 1.4;
    }

    /* ── Table ── */
    .table-card {
        background: var(--white);
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        position: relative;
    }
    
    .table-card::after {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--orange), var(--green));
        transform: scaleX(0); transform-origin: right;
        transition: transform .4s var(--ease);
        border-radius: 20px 20px 0 0;
    }
    .table-card:hover::after { transform: scaleX(1); transform-origin: left; }

    .table-card-header {
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border);
        background: var(--white);
    }

    .table-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--brown);
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .custom-table thead th {
        background: var(--off);
        color: var(--muted);
        font-size: 0.85rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
    }

    .custom-table tbody tr { transition: background 0.2s var(--ease); }
    .custom-table tbody tr:hover { background: var(--off); }

    .custom-table tbody td {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border);
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-main);
        vertical-align: middle;
    }

    .custom-table tbody tr:last-child td { border-bottom: none; }

    /* ═══ BADGES ═══ */
    .badge {
        padding: 8px 14px;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        border: 1px solid transparent;
    }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }

    .badge-treatment  { background: rgba(232,101,26,0.1); color: var(--orange2); border-color: rgba(232,101,26,0.2); }
    .badge-treatment .dot { background: var(--orange); }
    .badge-autopsy    { background: rgba(90,45,12,0.1); color: var(--brown); border-color: rgba(90,45,12,0.2); }
    .badge-autopsy .dot { background: var(--brown); }
    .badge-quarantine { background: rgba(232,101,26,0.15); color: var(--orange2); }
    .badge-quarantine .dot { background: var(--orange); }
    .badge-hospital   { background: rgba(46,125,50,0.1); color: var(--green); border-color: rgba(46,125,50,0.2); }
    .badge-hospital .dot { background: var(--green); }

    .badge-pending   { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .badge-pending .dot { background: #d97706; }
    .badge-rejected  { background: #fff1f2; color: #e11d48; border-color: #fecdd3; }
    .badge-rejected .dot { background: #ef4444; }
    .badge-approved  { background: rgba(46,125,50,0.1); color: var(--green); border-color: rgba(46,125,50,0.2); }
    .badge-approved .dot { background: var(--green); }
    .badge-ready     { background: rgba(46,125,50,0.1); color: var(--green); border-color: rgba(46,125,50,0.2); }
    .badge-ready .dot { background: var(--green); }
    .badge-review    { background: #fffbeb; color: #d97706; border-color: #fde68a; }
    .badge-review .dot { background: #d97706; }

    .animal-id {
        font-family: 'Courier New', monospace;
        font-size: 0.78rem;
        background: var(--white);
        padding: 4px 8px;
        border-radius: 8px;
        color: var(--muted);
        font-weight: 700;
        display: inline-block;
        margin-top: 6px;
        border: 1px solid var(--border);
    }

    .view-all-link {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--orange);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        background: var(--white);
        border: 2px solid var(--orange);
        border-radius: 50px;
        transition: all 0.3s var(--ease);
    }
    .view-all-link:hover {
        background: var(--orange);
        color: var(--white);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(232,101,26,0.3);
    }
    /* Flip arrow direction for RTL in view-all-link */
    .view-all-link svg {
        transform: rotate(180deg);
    }

    .title-icon {
        background: rgba(90,45,12,0.1);
        color: var(--brown);
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .actions-cell { display: flex; gap: 8px; align-items: center; justify-content: flex-end; }
    .btn-tbl {
        display: inline-flex; align-items: center; justify-content: center;
        width: 38px; height: 38px; padding: 0; border-radius: 10px;
        cursor: pointer; text-decoration: none; transition: all 0.25s var(--ease);
        border: 1px solid var(--border); flex-shrink: 0;
        background: var(--off); color: var(--muted);
    }
    .btn-tbl:hover {
        transform: translateY(-2px);
        background: var(--orange); border-color: var(--orange); color: var(--white);
        box-shadow: 0 4px 12px rgba(232,101,26,0.3);
    }
    /* Arrow inside action cell for RTL */
    .btn-tbl svg {
        transform: rotate(180deg);
    }

    .two-col-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .alerts-list { display: flex; flex-direction: column; }
    .alert-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.4rem 1.5rem;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s var(--ease);
        text-decoration: none;
    }
    .alert-item:hover { background: var(--off); }
    .alert-item:last-child { border-bottom: none; }
    .alert-content { display: flex; align-items: center; gap: 14px; }
    .alert-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--white);
        color: var(--muted);
        border: 1px solid var(--border);
    }
    .alert-text { font-size: 0.95rem; font-weight: 700; color: var(--text-main); }
    .alert-time { font-size: 0.8rem; font-weight: 600; color: var(--muted); margin-left: 15px; } /* Changed margin-right to margin-left for RTL */
    .alert-arrow { color: var(--muted); display: flex; align-items: center; transform: rotate(180deg); }
</style>"""

    content = re.sub(r'<style>.*?</style>', new_styles, content, flags=re.DOTALL)
    with open('resources/views/vet/dashboard.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)

update_layout()
update_dashboard()
print("Styles updated successfully.")
