<style>
    .admin-confirm-overlay {
        position: fixed;
        inset: 0;
        z-index: 11000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.22s ease, visibility 0.22s ease;
    }

    .admin-confirm-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .admin-confirm-box {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        overflow: hidden;
        transform: translateY(16px) scale(0.97);
        transition: transform 0.24s cubic-bezier(0.34, 1.2, 0.64, 1);
    }

    .admin-confirm-overlay.show .admin-confirm-box {
        transform: translateY(0) scale(1);
    }

    .admin-confirm-head {
        padding: 1.25rem 1.4rem 0.75rem;
    }

    .admin-confirm-head h3 {
        margin: 0 0 8px;
        font-size: 1.05rem;
        font-weight: 900;
        color: #0f172a;
    }

    .admin-confirm-head p {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 700;
        color: #64748b;
        line-height: 1.6;
    }

    .admin-confirm-actions {
        display: flex;
        gap: 10px;
        padding: 1rem 1.4rem 1.25rem;
        justify-content: flex-end;
    }

    .admin-confirm-btn {
        padding: 10px 18px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.88rem;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }

    .admin-confirm-btn.cancel {
        background: #f1f5f9;
        color: #64748b;
        border: 1.5px solid #e2e8f0;
    }

    .admin-confirm-btn.cancel:hover { background: #e2e8f0; }

    .admin-confirm-btn.confirm {
        background: #2E7D32;
        color: #fff;
    }

    .admin-confirm-btn.confirm:hover { background: #1B5E20; }

    .admin-confirm-btn.confirm.danger {
        background: #DC2626;
    }

    .admin-confirm-btn.confirm.danger:hover { background: #B91C1C; }
</style>

<div class="admin-confirm-overlay" id="adminConfirmOverlay" aria-hidden="true">
    <div class="admin-confirm-box" role="dialog" aria-modal="true" aria-labelledby="adminConfirmTitle">
        <div class="admin-confirm-head">
            <h3 id="adminConfirmTitle">تأكيد العملية</h3>
            <p id="adminConfirmMessage">هل أنت متأكد؟</p>
        </div>
        <div class="admin-confirm-actions">
            <button type="button" class="admin-confirm-btn cancel" id="adminConfirmCancel">إلغاء</button>
            <button type="button" class="admin-confirm-btn confirm" id="adminConfirmOk">تأكيد</button>
        </div>
    </div>
</div>
