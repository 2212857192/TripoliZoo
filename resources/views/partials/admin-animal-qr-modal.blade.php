{{-- نافذة QR التعريفي — مشتركة بين قائمة وعرض المحتوى التعريفي --}}
<style>
    .qr-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 1200;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        background: rgba(15, 23, 42, 0.62);
        backdrop-filter: blur(8px);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.28s ease, visibility 0.28s ease;
    }

    .qr-modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .qr-modal-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.28);
        transform: translateY(28px) scale(0.96);
        transition: transform 0.32s cubic-bezier(0.34, 1.2, 0.64, 1);
    }

    .qr-modal-overlay.show .qr-modal-card {
        transform: translateY(0) scale(1);
    }

    .qr-modal-hero {
        position: relative;
        padding: 1.6rem 1.5rem 3.4rem;
        background: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 55%, #3d7a35 100%);
        color: #fff;
        text-align: center;
    }

    .qr-modal-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 0%, rgba(255,255,255,0.12), transparent 45%);
        pointer-events: none;
    }

    .qr-modal-brand {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.18);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        margin-bottom: 0.8rem;
    }

    .qr-modal-close {
        position: absolute;
        top: 14px;
        left: 14px;
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 10px;
        background: rgba(255,255,255,0.14);
        color: #fff;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .qr-modal-close:hover { background: rgba(255,255,255,0.24); }

    .qr-modal-hero h3 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 900;
    }

    .qr-modal-hero p {
        margin: 6px 0 0;
        font-size: 0.8rem;
        font-weight: 600;
        opacity: 0.88;
    }

    .qr-modal-avatar-wrap {
        position: absolute;
        left: 50%;
        bottom: -34px;
        transform: translateX(-50%);
        width: 72px;
        height: 72px;
        border-radius: 18px;
        border: 4px solid #fff;
        background: linear-gradient(135deg, #FFF7ED, #FFEDD5);
        box-shadow: 0 10px 24px rgba(0,0,0,0.18);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    .qr-modal-avatar-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .qr-modal-body {
        padding: 3rem 1.5rem 1.5rem;
        text-align: center;
    }

    .qr-modal-animal-name {
        font-size: 1.2rem;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 4px;
    }

    .qr-modal-animal-meta {
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        margin: 0 0 1rem;
    }

    .qr-modal-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        margin-bottom: 1.2rem;
    }

    .qr-modal-badge {
        padding: 5px 10px;
        border-radius: 999px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        font-size: 0.74rem;
        font-weight: 800;
        color: #475569;
    }

    .qr-modal-frame {
        position: relative;
        display: inline-block;
        padding: 14px;
        border-radius: 18px;
        background: linear-gradient(180deg, #F8FAFC 0%, #fff 100%);
        border: 1px solid #E2E8F0;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.9), 0 8px 24px rgba(15,23,42,0.06);
        margin-bottom: 0.9rem;
    }

    .qr-modal-frame::before,
    .qr-modal-frame::after {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        border-color: #2d5a27;
        border-style: solid;
        opacity: 0.55;
    }

    .qr-modal-frame::before {
        top: 8px;
        right: 8px;
        border-width: 3px 3px 0 0;
        border-radius: 0 6px 0 0;
    }

    .qr-modal-frame::after {
        bottom: 8px;
        left: 8px;
        border-width: 0 0 3px 3px;
        border-radius: 0 0 0 6px;
    }

    .qr-modal-loading {
        width: 220px;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .qr-modal-spinner {
        width: 34px;
        height: 34px;
        border: 3px solid #E2E8F0;
        border-top-color: #2d5a27;
        border-radius: 50%;
        animation: qrSpin 0.8s linear infinite;
        margin-left: 10px;
    }

    @keyframes qrSpin { to { transform: rotate(360deg); } }

    #qrImage {
        display: block;
        width: 220px;
        height: 220px;
        border-radius: 10px;
    }

    .qr-modal-hint {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        line-height: 1.6;
        margin: 0 0 1.1rem;
        padding: 10px 12px;
        border-radius: 12px;
        background: #F8FAFC;
        border: 1px dashed #CBD5E1;
    }

    .qr-modal-hint.warn {
        background: #FEF3C7;
        border-color: #FCD34D;
        color: #92400E;
    }

    .qr-modal-scan-url {
        display: block;
        margin-top: 6px;
        font-size: 0.72rem;
        font-weight: 800;
        color: #1e3a1e;
        word-break: break-all;
        direction: ltr;
        text-align: left;
    }

    .qr-modal-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .qr-modal-actions .full { grid-column: 1 / -1; }

    .qr-btn-primary,
    .qr-btn-secondary,
    .qr-btn-ghost {
        padding: 12px 14px;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.88rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
    }

    .qr-btn-primary {
        background: linear-gradient(135deg, #1e3a1e, #2d5a27);
        color: #fff;
        box-shadow: 0 6px 16px rgba(30,58,30,0.22);
    }

    .qr-btn-primary:hover { transform: translateY(-1px); }

    .qr-btn-secondary {
        background: #ECFDF5;
        color: #166534;
        border: 1.5px solid #BBF7D0;
    }

    .qr-btn-secondary:hover { background: #DCFCE7; }

    .qr-btn-ghost {
        background: #F8FAFC;
        color: #64748b;
        border: 1.5px solid #E2E8F0;
    }

    .qr-btn-ghost:hover { background: #E2E8F0; color: #334155; }
</style>

<div class="qr-modal-overlay" id="qrModal" aria-hidden="true">
    <div class="qr-modal-card" role="dialog" aria-modal="true" aria-labelledby="qrModalTitle">
        <div class="qr-modal-hero">
            <button type="button" class="qr-modal-close" onclick="closeAnimalQR()" aria-label="إغلاق">&times;</button>
            <div class="qr-modal-brand">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                حديقة حيوان طرابلس
            </div>
            <h3 id="qrModalTitle">رمز QR التعريفي</h3>
            <p>للزوار — امسح الرمز من تطبيق الحديقة</p>
            <div class="qr-modal-avatar-wrap" id="qrAvatarWrap">
                <span id="qrAvatarEmoji">🦁</span>
                <img id="qrAvatarImg" src="" alt="" style="display:none;">
            </div>
        </div>

        <div class="qr-modal-body">
            <h4 class="qr-modal-animal-name" id="qrAnimalName">—</h4>
            <p class="qr-modal-animal-meta" id="qrAnimalMeta">—</p>

            <div class="qr-modal-badges">
                <span class="qr-modal-badge" id="qrAnimalCode">رمز: —</span>
                <span class="qr-modal-badge" id="qrAnimalGroup" style="display:none;"></span>
            </div>

            <div class="qr-modal-frame">
                <div class="qr-modal-loading" id="qrLoading">
                    جاري توليد الرمز
                    <div class="qr-modal-spinner"></div>
                </div>
                <img id="qrImage" src="" alt="رمز QR التعريفي" width="220" height="220" style="display:none;">
            </div>

            <p class="qr-modal-hint" id="qrModalHint">
                <span id="qrHintText">عند مسح الرمز من كاميرا الموبايل تُفتح صفحة تعريفية منسّقة بكل معلومات الحيوان.</span>
                <span class="qr-modal-scan-url" id="qrScanUrl"></span>
            </p>

            <div class="qr-modal-actions">
                <button type="button" class="qr-btn-primary full" onclick="downloadAnimalQR()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    تحميل صورة QR
                </button>
                <button type="button" class="qr-btn-ghost full" onclick="closeAnimalQR()">إغلاق</button>
            </div>
        </div>
    </div>
</div>
