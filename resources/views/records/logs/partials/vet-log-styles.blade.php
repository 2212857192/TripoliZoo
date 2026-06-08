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
    .hero-stats { display: flex; gap: 0.8rem; align-items: center; flex-wrap: wrap; }
    .hero-stat {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 8px 14px;
        text-align: center;
    }
    .hero-stat .num { font-size: 1.4rem; font-weight: 900; color: #1E293B; line-height: 1; }
    .hero-stat .lbl { font-size: 0.65rem; color: #64748B; font-weight: 700; }

    .info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-right: 3px solid #2E7D32;
        border-radius: 10px;
        padding: 14px 18px;
    }
    .info-box-title {
        font-size: 0.88rem;
        font-weight: 800;
        color: #2E7D32;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .info-box-text {
        font-size: 0.82rem;
        color: #334155;
        font-weight: 600;
        line-height: 1.6;
        margin: 0;
    }
    .flow-steps {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
    }
    .flow-step {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .flow-arrow {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 800;
    }
    .info-list {
        margin: 10px 0 0;
        padding-right: 18px;
        color: #334155;
        font-size: 0.82rem;
        font-weight: 600;
        line-height: 1.8;
    }

    .filter-bar {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        padding-top: 1.2rem;
        border-top: 1px solid #F1F5F9;
    }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        outline: none;
        transition: all 0.2s;
    }
    .search-box input:focus {
        border-color: #2E7D32;
        box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
    }
    .search-box svg {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .filter-select {
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-select:focus { border-color: #2E7D32; }

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
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
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
    .badge-completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-completed .dot { background: #15803d; }
    .badge-none { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .badge-none .dot { background: #94a3b8; }

    .cause-text { font-size: 0.85rem; color: #475569; max-width: 200px; line-height: 1.4; }

    .btn-tbl {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-tbl:hover { background: #f8fafc; border-color: #cbd5e1; }
    .btn-tbl-view:hover { border-color: #94a3b8; }
</style>
