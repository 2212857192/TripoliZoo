@extends($__layout ?? 'admin.layout')
@section('title', 'المحتوى التعريفي للحيوانات | Tripoli Zoo')
@section('page_title', 'المحتوى التعريفي للحيوانات')

@section('styles')
<style>
    .top-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.4rem 1.8rem;
        margin-bottom: 1.5rem;
    }

    .top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.2rem;
    }

    .top-row h2 { font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0; }
    .top-row p  { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin: 4px 0 0; }

    .btn-add {
        background: var(--orange);
        color: white;
        border: none;
        padding: 11px 22px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(232,101,26,0.25);
    }

    .btn-add:hover { background: #c0510d; transform: translateY(-1px); }

    .filter-bar {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        padding-top: 1.2rem;
        border-top: 1px solid var(--border);
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 220px;
    }

    .search-box svg {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }

    .search-box input {
        width: 100%;
        padding: 10px 42px 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .search-box input:focus { border-color: var(--orange); }

    /* Grid */
    .animals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 1.5rem;
    }

    .animal-card {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.25s, box-shadow 0.25s;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
    }

    .animal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.08);
    }

    .animal-img-wrap {
        position: relative;
        height: 180px;
        overflow: hidden;
        background: #F1F5F9;
    }

    .animal-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .animal-img-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4.5rem;
        background: linear-gradient(135deg, #FFF7ED, #FFEDD5);
    }

    .vis-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 800;
        backdrop-filter: blur(8px);
    }

    .vis-badge.visible    { background: rgba(220,252,231,0.92); color: #166534; }
    .vis-badge.hidden-app { background: rgba(254,226,226,0.92); color: #991B1B; }

    .animal-body { padding: 1.2rem; flex: 1; display: flex; flex-direction: column; }

    .animal-name { font-size: 1.05rem; font-weight: 800; color: var(--brown); margin: 0 0 2px; }
    .animal-sci  { font-size: 0.78rem; color: var(--text-muted); font-style: italic; margin: 0 0 10px; font-weight: 500; }

    .animal-desc-preview {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .animal-actions {
        display: flex;
        gap: 6px;
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid var(--bg-color);
    }

    .btn-act {
        flex: 1;
        padding: 8px 4px;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all 0.2s;
        color: var(--text-muted);
        text-decoration: none;
    }

    .btn-act:hover              { background: var(--bg-color); color: var(--text-main); }
    .btn-act.view-btn:hover     { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
    .btn-act.edit-btn:hover     { color: var(--orange); background: #FFEDD5; border-color: #FED7AA; }
    .btn-act.vis-btn:hover      { color: #7C3AED; background: #EDE9FE; border-color: #DDD6FE; }
    .btn-act.qr-btn:hover       { color: #059669; background: #D1FAE5; border-color: #A7F3D0; }

    /* QR Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.55);
        backdrop-filter: blur(5px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.25s;
    }

    .modal-overlay.show { opacity: 1; visibility: visible; }

    .modal-box {
        background: var(--white);
        width: 100%;
        max-width: 380px;
        border-radius: 20px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.18);
        transform: translateY(24px) scale(0.97);
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        overflow: hidden;
    }

    .modal-overlay.show .modal-box { transform: translateY(0) scale(1); }

    .modal-head {
        padding: 1.2rem 1.6rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #FAFBFC;
    }

    .modal-head h3 { font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0; }

    .btn-close-x {
        width: 30px; height: 30px;
        background: var(--border); border: none; border-radius: 8px;
        font-size: 1.1rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); transition: all 0.2s;
    }

    .btn-close-x:hover { background: #E2E8F0; }

    .qr-container {
        text-align: center;
        padding: 1.5rem;
    }

    .qr-container h4 { font-size: 1rem; font-weight: 800; color: var(--text-main); margin-bottom: 4px; }
    .qr-container p  { font-size: 0.82rem; color: var(--text-muted); margin-bottom: 1.5rem; }

    #qrCanvas {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 12px;
        background: white;
        margin-bottom: 1.2rem;
    }

    .qr-actions { display: flex; gap: 10px; justify-content: center; }

    .btn-qr-dl {
        padding: 9px 18px;
        background: var(--green);
        color: white; border: none;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.2s;
    }

    .btn-qr-dl:hover { background: #1B5E20; }

    .btn-cancel {
        padding: 9px 16px;
        background: var(--bg-color); color: var(--text-muted);
        border: 1.5px solid var(--border); border-radius: 8px;
        font-family: 'Cairo', sans-serif; font-weight: 700; cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel:hover { background: #E2E8F0; }

    .empty-state {
        text-align: center; padding: 4rem 2rem;
        color: var(--text-muted); display: none;
    }

    .empty-state svg { margin-bottom: 1rem; opacity: 0.25; }
    .empty-state h3  { font-weight: 700; margin-bottom: 6px; }

    /* Toast */
    .toast {
        position: fixed; bottom: 2rem; left: 50%;
        transform: translateX(-50%) translateY(80px);
        background: #1E293B; color: white;
        padding: 12px 24px; border-radius: 50px;
        font-weight: 700; font-size: 0.9rem;
        z-index: 9999;
        transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
        white-space: nowrap;
    }

    .toast.show { transform: translateX(-50%) translateY(0); }
</style>
@endsection

@section('content')

<!-- Top Card -->
<div class="top-card">
    <div class="top-row">
        <div>
            <h2>المحتوى التعريفي للحيوانات</h2>
            <p>إجمالي <strong id="animalCount">2</strong> محتوى تعريفي مسجل</p>
        </div>
        <a href="/admin/animals/create" class="btn-add">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            إضافة محتوى جديد
        </a>
    </div>
    <div class="filter-bar">
        <div class="search-box">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="searchInput" placeholder="ابحث باسم الحيوان المعرّف...">
        </div>
    </div>
</div>

<!-- Animals Grid -->
<div class="animals-grid" id="animalsGrid">

    <!-- Card 1: Lion -->
    <div class="animal-card" data-vis="visible" data-name="الأسد الإفريقي">
        <div class="animal-img-wrap">
            <img src="/zoo_lion.png" alt="الأسد" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="animal-img-placeholder" style="display:none">🦁</div>
            <span class="vis-badge visible">ظاهر للزوار</span>
        </div>
        <div class="animal-body">
            <h3 class="animal-name">الأسد الإفريقي</h3>
            <p class="animal-sci">Panthera leo</p>
            <p class="animal-desc-preview">الأسد الإفريقي من أكبر القطط البرية في العالم. يعيش في مجموعات تُعرف بـ (الفخر). يتميز الذكر بعُرفه الكثيف الذي يزداد قتامةً مع التقدم في السن.</p>
            <div class="animal-actions">
                <a href="/admin/animals/1" class="btn-act view-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    عرض
                </a>
                <a href="/admin/animals/1/edit" class="btn-act edit-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    تعديل
                </a>
                <button class="btn-act vis-btn" onclick="toggleVis(this)" data-vis="visible">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    إخفاء
                </button>
                <button class="btn-act qr-btn" onclick="openQR('الأسد الإفريقي','Panthera leo','L-01')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    QR
                </button>
            </div>
        </div>
    </div>

    <!-- Card 2: Elephant -->
    <div class="animal-card" data-vis="visible" data-name="الفيل الآسيوي">
        <div class="animal-img-wrap">
            <img src="/zoo_elephant.png" alt="الفيل" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="animal-img-placeholder" style="display:none">🐘</div>
            <span class="vis-badge visible">ظاهر للزوار</span>
        </div>
        <div class="animal-body">
            <h3 class="animal-name">الفيل الآسيوي</h3>
            <p class="animal-sci">Elephas maximus</p>
            <p class="animal-desc-preview">الفيل الآسيوي أصغر حجماً من أفريقي، ويتميز بأذنين أصغر ورأس أكثر تحدباً. يُعدّ من أكثر الحيوانات ذكاءً في العالم، ويمتلك ذاكرة استثنائية.</p>
            <div class="animal-actions">
                <a href="/admin/animals/2" class="btn-act view-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    عرض
                </a>
                <a href="/admin/animals/2/edit" class="btn-act edit-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    تعديل
                </a>
                <button class="btn-act vis-btn" onclick="toggleVis(this)" data-vis="visible">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    إخفاء
                </button>
                <button class="btn-act qr-btn" onclick="openQR('الفيل الآسيوي','Elephas maximus','E-04')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    QR
                </button>
            </div>
        </div>
    </div>

</div>

<div class="empty-state" id="emptyState">
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21.5 12H16c-.7 2-2 3-4 3s-3.3-1-4-3H2.5"/></svg>
    <h3>لا توجد نتائج</h3>
    <p>جرب تعديل معايير البحث</p>
</div>

<!-- QR Modal -->
<div class="modal-overlay" id="qrModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>رمز QR التعريفي</h3>
            <button class="btn-close-x" onclick="document.getElementById('qrModal').classList.remove('show')">&times;</button>
        </div>
        <div class="qr-container">
            <h4 id="qrName">-</h4>
            <p id="qrCode">-</p>
            <canvas id="qrCanvas" width="200" height="200"></canvas>
            <div class="qr-actions">
                <button class="btn-qr-dl" onclick="downloadQR()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    تحميل
                </button>
                <button class="btn-cancel" onclick="document.getElementById('qrModal').classList.remove('show')">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    let currentQRName = '';

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2800);
    }

    /* Filter */
    function filterAnimals() {
        const q   = document.getElementById('searchInput').value.trim().toLowerCase();
        const cards = document.querySelectorAll('.animal-card');
        let count = 0;

        cards.forEach(card => {
            const okQ   = !q || card.dataset.name.toLowerCase().includes(q);
            card.style.display = okQ ? '' : 'none';
            if (okQ) count++;
        });

        document.getElementById('emptyState').style.display = count === 0 ? 'block' : 'none';
    }

    document.getElementById('searchInput').addEventListener('input', filterAnimals);

    /* Toggle visibility */
    function toggleVis(btn) {
        const card   = btn.closest('.animal-card');
        const isVis  = card.dataset.vis === 'visible';
        card.dataset.vis = isVis ? 'hidden' : 'visible';

        const badge = card.querySelector('.vis-badge');
        badge.textContent = isVis ? 'مخفي' : 'ظاهر للزوار';
        badge.className   = 'vis-badge ' + (isVis ? 'hidden-app' : 'visible');

        btn.innerHTML = isVis
            ? `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><line x1="1" y1="1" x2="23" y2="23"></line></svg> إظهار`
            : `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> إخفاء`;

        filterAnimals();
        showToast(isVis ? '🚫 تم الإخفاء من تطبيق الزوار' : '👁️ تم الإظهار في تطبيق الزوار');
    }

    /* QR */
    function openQR(name, sci, code) {
        currentQRName = name;
        document.getElementById('qrName').textContent = name;
        document.getElementById('qrCode').textContent = 'رمز: ' + code + ' | ' + sci;

        QRCode.toCanvas(document.getElementById('qrCanvas'),
            JSON.stringify({ name, scientific: sci, code, zoo: 'حديقة حيوان طرابلس' }),
            { width: 200, margin: 1, color: { dark: '#1E293B', light: '#FFFFFF' } }
        );
        document.getElementById('qrModal').classList.add('show');
    }

    function downloadQR() {
        const canvas = document.getElementById('qrCanvas');
        const link   = document.createElement('a');
        link.download = 'QR-' + currentQRName + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        showToast('⬇️ تم تحميل رمز QR');
    }

    document.getElementById('qrModal').addEventListener('click', e => {
        if (e.target === document.getElementById('qrModal'))
            document.getElementById('qrModal').classList.remove('show');
    });
</script>
@endsection
