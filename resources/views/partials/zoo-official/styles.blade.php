<style>
    :root {
        --doc-black:   #111111;
        --doc-ink:     #1a1a1a;
        --doc-gray:    #4a4a4a;
        --doc-muted:   #6b7280;
        --doc-line:    #d1d5db;
        --doc-fill:    #f5f5f5;
        --doc-white:   #ffffff;
    }

    .zoo-official-doc {
        position: relative;
        background: var(--doc-white);
        border: 2px solid var(--doc-black);
        margin-bottom: 2rem;
        display: flex;
        flex-direction: column;
        min-height: auto;
    }

    .zoo-official-doc::before {
        content: 'حديقة حيوان طرابلس';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-28deg);
        font-size: clamp(2.5rem, 8vw, 5rem);
        font-weight: 800;
        color: rgba(0, 0, 0, 0.03);
        white-space: nowrap;
        pointer-events: none;
        z-index: 0;
        letter-spacing: 0.05em;
    }

    .zoo-official-doc > * { position: relative; z-index: 1; }

    /* ── Letterhead ── */
    .zoo-official-letterhead {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1.25rem;
        align-items: center;
        padding: 1.25rem 1.75rem 1rem;
        background: var(--doc-white);
        border-bottom: 1px solid var(--doc-black);
    }

    .zoo-official-brand {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .zoo-official-logo {
        width: 72px;
        height: 72px;
        object-fit: contain;
        border: 1px solid var(--doc-line);
        background: var(--doc-white);
        padding: 4px;
    }

    .zoo-official-org { text-align: center; }

    .zoo-official-org__name-ar {
        font-size: 1.35rem;
        font-weight: 900;
        color: var(--doc-black);
        line-height: 1.3;
        margin: 0;
    }

    .zoo-official-org__name-en {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--doc-gray);
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin: 4px 0 0;
    }

    .zoo-official-org__dept {
        display: inline-block;
        margin-top: 8px;
        padding: 4px 14px;
        border: 1px solid var(--doc-line);
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--doc-gray);
    }

    .zoo-official-meta-col {
        text-align: left;
        min-width: 140px;
    }

    .zoo-official-meta-row {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--doc-muted);
        margin-bottom: 6px;
        line-height: 1.5;
    }

    .zoo-official-meta-row strong {
        display: block;
        color: var(--doc-ink);
        font-size: 0.82rem;
        font-weight: 800;
    }

    .zoo-official-meta-row .ref-code {
        font-family: 'Courier New', monospace;
        color: var(--doc-black);
        background: var(--doc-fill);
        padding: 2px 8px;
        border: 1px solid var(--doc-line);
        display: inline-block;
    }

    /* ── Title bar ── */
    .zoo-official-titlebar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 0.85rem 1.75rem;
        background: var(--doc-fill);
        border-bottom: 2px solid var(--doc-black);
    }

    .zoo-official-titlebar__main h1 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 900;
        color: var(--doc-black);
        letter-spacing: 0.02em;
    }

    .zoo-official-titlebar__main p {
        margin: 4px 0 0;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--doc-gray);
    }

    .zoo-official-titlebar__extras {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .zoo-official-doc-type {
        font-size: 0.72rem;
        font-weight: 800;
        color: var(--doc-gray);
        background: var(--doc-white);
        border: 1px solid var(--doc-line);
        padding: 4px 12px;
        white-space: nowrap;
    }

    /* ── Body ── */
    .zoo-official-doc__body {
        padding: 1.5rem 1.75rem 1.75rem;
        background: var(--doc-white);
        flex: 1;
    }

    .zoo-official-doc__body .tabs-container {
        border: 1px solid var(--doc-line);
        box-shadow: none;
    }

    .zoo-official-doc__body .tabs-header {
        background: var(--doc-fill);
        border-bottom-color: var(--doc-line);
    }

    .zoo-official-doc__body .tab-btn.active {
        color: var(--doc-black);
        border-bottom-color: var(--doc-black);
    }

    .zoo-official-doc__body .info-card,
    .zoo-official-doc__body .table-card {
        border-color: var(--doc-line);
    }

    .zoo-official-titlebar__extras .badge {
        background: var(--doc-white);
        border-color: var(--doc-line);
        color: var(--doc-gray);
    }

    /* ── Footer ── */
    .zoo-official-footer {
        border-top: 1px solid var(--doc-black);
        background: var(--doc-fill);
        padding: 0.85rem 1.75rem;
        margin-top: auto;
    }

    .zoo-official-footer__note {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--doc-gray);
        line-height: 1.6;
        margin: 0 0 0.5rem;
        text-align: center;
    }

    .zoo-official-footer__meta {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--doc-muted);
        margin: 0;
        text-align: center;
        line-height: 1.6;
    }

    .zoo-official-footer__meta strong {
        color: var(--doc-black);
        font-weight: 800;
    }

    .zoo-official-footer__sep {
        margin: 0 0.65rem;
        color: var(--doc-line);
    }

    /* ── Print tables (export) ── */
    .zoo-official-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.25rem;
        font-size: 0.88rem;
    }

    .zoo-official-table th,
    .zoo-official-table td {
        border: 1px solid var(--doc-line);
        padding: 9px 12px;
        text-align: right;
        vertical-align: top;
    }

    .zoo-official-table th {
        background: var(--doc-fill);
        color: var(--doc-black);
        font-weight: 800;
        width: 30%;
    }

    .zoo-official-table td {
        font-weight: 700;
        color: var(--doc-ink);
    }

    .zoo-official-section-title {
        font-size: 0.95rem;
        font-weight: 900;
        color: var(--doc-black);
        margin: 1.25rem 0 0.65rem;
        padding-bottom: 0.4rem;
        border-bottom: 1px solid var(--doc-black);
    }

    .records-doc-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .records-doc-toolbar__actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .zoo-official-letterhead {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .zoo-official-meta-col { text-align: center; }

        .zoo-official-titlebar {
            flex-direction: column;
            align-items: flex-start;
        }

        .zoo-official-doc__body { padding: 1rem; }
    }

    @media print {
        .records-doc-toolbar,
        .no-print { display: none !important; }

        .zoo-official-doc {
            margin: 0;
            border-width: 1px;
        }

        .zoo-official-footer {
            page-break-inside: avoid;
            background: #fff;
        }

        body { background: #fff !important; padding: 0; }
    }
</style>
