<style>
    .top-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.4rem 1.8rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .page-header-info h2 {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }
    .page-header-info p {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        margin: 4px 0 0;
    }
    .status-pill {
        background: #DCFCE7;
        border: 1px solid #BBF7D0;
        border-radius: 30px;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #166534;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22C55E;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        padding: 1.3rem 1.2rem;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: block;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        color: inherit;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        border-radius: 0 16px 16px 0;
        transition: width 0.3s;
        background: #1a4a2e;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.1);
        border-color: transparent;
    }
    .stat-card:hover::before { width: 6px; }
    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 1rem;
        color: #16a34a;
    }
    .stat-num {
        font-size: 2.2rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        line-height: 1.4;
    }
    .stat-unit {
        font-size: 0.68rem;
        font-weight: 800;
        color: #94a3b8;
        margin-top: 4px;
    }

    .dash-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1000px) { .dash-grid { grid-template-columns: 1fr; } }

    .table-card {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .table-card-header {
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        background: #FAFBFC;
    }
    .table-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }
    .title-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #e6f4ea;
        color: #1a4a2e;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }
    .custom-table thead th {
        background: #F8FAFC;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
    }
    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-main);
        vertical-align: middle;
    }
    .custom-table tbody tr:last-child td { border-bottom: none; }

    .badge {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge-green { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-green .dot { background: #22c55e; }
    .badge-blue { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-blue .dot { background: #3b82f6; }
    .badge-orange { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-orange .dot { background: #d97706; }
    .badge-red { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-red .dot { background: #ef4444; }
    .badge-gray { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .badge-gray .dot { background: #94a3b8; }

    .quick-links {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    }
    .quick-links-header {
        padding: 1.1rem 1.2rem;
        border-bottom: 1px solid #f1f5f9;
        background: #FAFBFC;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }
    .quick-link-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.9rem 1.2rem;
        text-decoration: none;
        border-bottom: 1px solid #f8fafc;
        transition: all 0.2s;
    }
    .quick-link-item:last-child { border-bottom: none; }
    .quick-link-item:hover { background: #f0fdf4; padding-right: 1.5rem; }
    .quick-link-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #e6f4ea;
        color: #1a4a2e;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
    .quick-link-text { flex: 1; font-size: 0.83rem; font-weight: 700; color: #334155; }
    .quick-link-arr { color: #cbd5e1; transition: color 0.2s, transform 0.2s; }
    .quick-link-item:hover .quick-link-arr { color: #1a4a2e; transform: translateX(-4px); }

    @keyframes pulse-green {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.3); }
    }
    .pulse-dot { animation: pulse-green 2s infinite; }

    .view-notice {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #166534;
        margin-bottom: 1.5rem;
    }
    .view-notice svg { flex-shrink: 0; color: #16a34a; }

    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1000px) { .two-col { grid-template-columns: 1fr; } }

    .unit-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    @media (max-width: 1100px) { .unit-cards { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .unit-cards { grid-template-columns: 1fr; } }

    .unit-card {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 1.25rem 1.4rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
    }
    .unit-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: #1a4a2e;
        border-radius: 0 16px 16px 0;
        transition: width 0.3s;
    }
    .unit-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.08);
        border-color: transparent;
    }
    .unit-card:hover::before { width: 6px; }
    .unit-card h4 {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .unit-card p {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 600;
        line-height: 1.5;
        margin-bottom: 12px;
    }
    .unit-card .arrow {
        font-size: 0.75rem;
        font-weight: 800;
        color: #16a34a;
    }

    /* ── Director dashboard extras ── */
    .section-heading {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 1rem;
        padding-bottom: 8px;
        border-bottom: 2px solid #e6f4ea;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-heading span {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
    }
    .stats-grid-8 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1200px) { .stats-grid-8 { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .stats-grid-8 { grid-template-columns: 1fr; } }

    .stat-sub {
        font-size: 0.72rem;
        font-weight: 700;
        color: #16a34a;
        margin-top: 6px;
        line-height: 1.4;
    }
    .stat-sub.warn { color: #d97706; }
    .stat-sub.danger { color: #dc2626; }
    .stat-sub.muted { color: #94a3b8; }

    .info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-right: 3px solid #1a4a2e;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .today-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 900px) { .today-grid { grid-template-columns: repeat(2, 1fr); } }
    .today-item {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 12px;
        padding: 12px 14px;
        text-align: center;
    }
    .today-item .num { font-size: 1.5rem; font-weight: 900; color: #0f172a; line-height: 1; }
    .today-item .lbl { font-size: 0.7rem; font-weight: 700; color: #64748b; margin-top: 4px; line-height: 1.3; }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 900px) { .charts-grid { grid-template-columns: 1fr; } }
    .chart-card {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 1.2rem 1.4rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .chart-card h4 {
        font-size: 0.88rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 1rem;
    }
    .bar-chart { display: flex; align-items: flex-end; gap: 8px; height: 120px; padding-top: 8px; }
    .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; justify-content: flex-end; }
    .bar-fill {
        width: 100%;
        max-width: 36px;
        background: linear-gradient(180deg, #16a34a, #1a4a2e);
        border-radius: 6px 6px 0 0;
        min-height: 4px;
        transition: height 0.3s;
    }
    .bar-fill.orange { background: linear-gradient(180deg, #f97316, #ea580c); }
    .bar-fill.blue { background: linear-gradient(180deg, #3b82f6, #2563eb); }
    .bar-fill.red { background: linear-gradient(180deg, #ef4444, #dc2626); }
    .bar-label { font-size: 0.62rem; font-weight: 700; color: #94a3b8; text-align: center; }
    .bar-val { font-size: 0.65rem; font-weight: 800; color: #64748b; }

    .donut-wrap { display: flex; align-items: center; gap: 1.2rem; flex-wrap: wrap; }
    .donut {
        width: 100px; height: 100px; border-radius: 50%;
        background: conic-gradient(#1a4a2e 0 26%, #16a34a 26% 50%, #22c55e 50% 68%, #4ade80 68% 82%, #86efac 82% 100%);
        position: relative; flex-shrink: 0;
    }
    .donut::after {
        content: ''; position: absolute; inset: 18px;
        background: #fff; border-radius: 50%;
    }
    .legend-list { flex: 1; min-width: 140px; }
    .legend-row {
        display: flex; align-items: center; justify-content: space-between;
        font-size: 0.78rem; font-weight: 700; color: #334155;
        padding: 4px 0; border-bottom: 1px solid #f1f5f9;
    }
    .legend-row:last-child { border-bottom: none; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; margin-left: 8px; }

    .alert-list { display: flex; flex-direction: column; gap: 8px; }
    .alert-item-row {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 14px;
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
    }
    .alert-item-row:hover { background: #f0fdf4; border-color: #bbf7d0; }
    .alert-item-row.high { border-right: 3px solid #ef4444; background: #fffbfb; }
    .alert-item-row.medium { border-right: 3px solid #f59e0b; }
    .alert-body { flex: 1; }
    .alert-body strong { display: block; font-size: 0.82rem; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
    .alert-body span { font-size: 0.72rem; font-weight: 600; color: #64748b; }
    .alert-action { font-size: 0.72rem; font-weight: 800; color: #16a34a; white-space: nowrap; }

    .decisions-list { display: flex; flex-direction: column; gap: 6px; }
    .decision-chip {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 12px; background: #f8fafc; border-radius: 8px;
        font-size: 0.78rem; font-weight: 700; color: #334155;
    }
    .ticket-types { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
    .ticket-type-pill {
        flex: 1; min-width: 70px; text-align: center;
        padding: 8px 10px; background: #f0fdf4; border: 1px solid #bbf7d0;
        border-radius: 10px; font-size: 0.75rem; font-weight: 800; color: #166534;
    }
    .ticket-type-pill span { display: block; font-size: 1.1rem; font-weight: 900; color: #0f172a; margin-bottom: 2px; }

    /* ── Dashboard tabs ── */
    .dashboard-tabs-card {
        background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
        border: 2px solid #bbf7d0;
        border-radius: 16px;
        padding: 0.85rem 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 14px rgba(26, 74, 46, 0.08);
        overflow-x: auto;
    }
    .dashboard-tabs-label {
        font-size: 0.72rem;
        font-weight: 800;
        color: #16a34a;
        margin-bottom: 8px;
        letter-spacing: 0.3px;
    }
    .segmented-tabs {
        display: flex;
        flex-wrap: wrap;
        background: #e2e8f0;
        padding: 6px;
        border-radius: 14px;
        gap: 6px;
        width: 100%;
    }
    .seg-tab {
        background: transparent;
        border: none;
        padding: 11px 22px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.88rem;
        font-weight: 800;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        display: inline-flex !important;
        align-items: center;
        gap: 6px;
        flex: 1 1 auto;
        justify-content: center;
        min-width: 120px;
    }
    .seg-tab:hover { color: #1a4a2e; background: rgba(255,255,255,0.5); }
    .seg-tab.active {
        background: #1a4a2e;
        color: #fff;
        box-shadow: 0 3px 10px rgba(26, 74, 46, 0.25);
    }
    .seg-tab.active .tab-badge { background: #fff; color: #dc2626; }
    .tab-badge {
        background: #ef4444;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 900;
        padding: 2px 7px;
        border-radius: 20px;
        line-height: 1.3;
    }
    .dash-tab-content { display: none; }
    .dash-tab-content.active { display: block; animation: dashFadeIn 0.25s ease; }
    @keyframes dashFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
