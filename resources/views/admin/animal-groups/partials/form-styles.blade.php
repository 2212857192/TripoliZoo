<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.9);
        --glass-border: rgba(226, 232, 240, 0.8);
        --primary-gradient: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        --accent-gradient: linear-gradient(135deg, #E8651A 0%, #f97316 100%);
        --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
    }

    .group-form-layout {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .page-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 700;
        font-size: 0.88rem;
        margin-bottom: 1.5rem;
        transition: color 0.2s;
    }

    .page-back:hover { color: var(--orange); }

    .page-hero {
        background: var(--primary-gradient);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 25px -5px rgba(30, 58, 30, 0.25);
    }

    .page-hero h2 {
        font-size: 1.6rem;
        font-weight: 900;
        margin: 0 0 6px;
    }

    .page-hero p {
        font-size: 0.85rem;
        opacity: 0.85;
        margin: 0;
    }

    .premium-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .card-accent-header {
        padding: 1.3rem 1.8rem;
        background: linear-gradient(to left, rgba(45, 90, 39, 0.03), transparent);
        border-bottom: 1.5px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-accent-header h3 {
        font-size: 1.1rem;
        font-weight: 900;
        color: #1e3a1e;
        margin: 0;
    }

    .icon-wrapper {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: rgba(45, 90, 39, 0.08);
        color: #2d5a27;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .premium-card-body { padding: 2rem; }

    .form-group { margin-bottom: 1.5rem; }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 800;
        font-size: 0.88rem;
        color: #1e3a1e;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.92rem;
        outline: none;
        transition: all 0.2s;
        background: white;
    }

    .form-input:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 3px rgba(232, 101, 26, 0.08);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .form-hint {
        margin-top: 6px;
        font-size: 0.78rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(248, 250, 252, 0.8);
        padding: 12px 20px;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    .toggle-row label {
        font-weight: 800;
        font-size: 0.88rem;
        color: var(--text-main);
        margin: 0;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 25px;
    }

    .switch input { opacity: 0; width: 0; height: 0; }

    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #CBD5E1;
        border-radius: 50px;
        transition: 0.3s;
    }

    .slider::before {
        position: absolute;
        content: "";
        width: 19px;
        height: 19px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
    }

    .switch input:checked + .slider { background: var(--green); }
    .switch input:checked + .slider::before { transform: translateX(21px); }

    .meta-note {
        margin: 0 0 1.2rem;
        padding: 12px 16px;
        border-radius: 12px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .actions-row {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 0.5rem;
    }

    .btn-submit-premium,
    .btn-cancel-premium {
        padding: 12px 30px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.95rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border: none;
    }

    .btn-submit-premium {
        background: var(--accent-gradient);
        color: white;
        box-shadow: 0 5px 15px rgba(232, 101, 26, 0.25);
    }

    .btn-cancel-premium {
        background: var(--bg-color);
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        justify-content: center;
    }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
        .actions-row { flex-direction: column; }
        .btn-submit-premium, .btn-cancel-premium { width: 100%; justify-content: center; }
    }
</style>
