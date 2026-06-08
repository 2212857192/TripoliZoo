@extends($__layout ?? 'admin.layout')
@section('title', 'إضافة موقع جديد على الخريطة | Tripoli Zoo')
@section('page_title', 'إدارة مواقع الخريطة')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.9);
        --glass-border: rgba(226, 232, 240, 0.8);
        --primary-gradient: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        --accent-gradient: linear-gradient(135deg, #E8651A 0%, #f97316 100%);
        --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
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



    /* Page Hero */
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

    /* Premium card design */
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
        display: flex;
        align-items: center;
        gap: 10px;
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

    .premium-card-body {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

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

    .form-divider {
        height: 1px;
        background: var(--border);
        margin: 1.5rem 0;
    }

    /* Toggle switches */
    .toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(248, 250, 252, 0.8);
        padding: 12px 20px;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 1.8rem;
    }

    .toggle-row label {
        font-weight: 800;
        font-size: 0.88rem;
        color: var(--text-main);
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 25px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

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

    .switch input:checked + .slider {
        background: var(--green);
    }

    .switch input:checked + .slider::before {
        transform: translateX(21px);
    }

    /* Actions Row */
    .actions-row {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-submit-premium {
        padding: 12px 30px;
        background: var(--accent-gradient);
        color: white;
        border: none;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.95rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 5px 15px rgba(232, 101, 26, 0.25);
        transition: all 0.3s;
    }

    .btn-submit-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(232, 101, 26, 0.35);
    }

    .btn-cancel-premium {
        padding: 12px 24px;
        background: var(--bg-color);
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel-premium:hover {
        background: #E2E8F0;
        color: var(--text-main);
    }

    .toast {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(80px);
        background: #1E293B;
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        z-index: 9999;
        transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
        white-space: nowrap;
    }

    .toast.show {
        transform: translateX(-50%) translateY(0);
    }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
        .actions-row { flex-direction: column; }
        .btn-submit-premium, .btn-cancel-premium { width: 100%; justify-content: center; }
    }
</style>
@endsection

@section('content')

<div class="ticket-single-layout">
    
    <a href="/admin/map-locations" class="page-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        العودة لخريطة الحديقة
    </a>

    <!-- Header Hero -->
    <div class="page-hero">
        <h2>إضافة موقع جديد على الخريطة</h2>
        <p>تسمح لك هذه اللوحة بإسقاط وتثبيت نقطة جغرافية جديدة لموقع أو مرفق داخل الحديقة.</p>
    </div>

    <!-- Main Container -->
    <div class="premium-card">
        <div class="card-accent-header">
            <div class="icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon></svg>
            </div>
            <h3>حدد الموقع الجغرافي على الخريطة التفاعلية</h3>
        </div>
        
        <div class="premium-card-body">
            
            <div class="form-row">
                <div class="form-group">
                    <label>اسم الموقع <span style="color:#EF4444">*</span></label>
                    <input type="text" id="name" class="form-input" placeholder="مثال: قفص الزرافة الاستوائية">
                </div>
                <div class="form-group">
                    <label>فئة وتصنيف الموقع <span style="color:#EF4444">*</span></label>
                    <select id="type" class="form-input">
                        <option value="enclosure">أقفاص وموائل الحيوانات</option>
                        <option value="service">الخدمات والمرافق العامة</option>
                        <option value="dining">المطاعم والمقاهي</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>إحداثي خط العرض (Latitude) <span style="color:#EF4444">*</span></label>
                    <input type="text" id="coord_lat" class="form-input" placeholder="اضغط على الخريطة لتحديده" readonly style="background:#F1F5F9;">
                </div>
                <div class="form-group">
                    <label>إحداثي خط الطول (Longitude) <span style="color:#EF4444">*</span></label>
                    <input type="text" id="coord_lng" class="form-input" placeholder="اضغط على الخريطة لتحديده" readonly style="background:#F1F5F9;">
                </div>
            </div>

            <!-- Leaflet Selection Map -->
            <div class="form-group">
                <label>انقر على نقطة في الخريطة لتحديد الإحداثيات الجغرافية بدقة:</label>
                <div id="leafletSelectMap" style="height: 300px; border-radius: 12px; border: 1.5px solid var(--border); overflow: hidden; z-index: 1;"></div>
            </div>

            <div class="form-group">
                <label>الوصف التعريفي بالموقع</label>
                <textarea id="desc" class="form-input" rows="4" placeholder="اكتب نبذة أو وصفاً تعريفياً موجزاً عن هذا الموقع..."></textarea>
            </div>

            <!-- Action buttons inside the same container -->
            <div class="actions-row">
                <a href="/admin/map-locations" class="btn-cancel-premium">إلغاء وتراجع</a>
                <button class="btn-submit-premium" onclick="submitForm()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    حفظ وإسقاط الموقع
                </button>
            </div>
        </div>
    </div>

</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<!-- Leaflet JS CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map;
    let marker;

    const zooCenter = [32.8485, 13.1785];

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function initSelectMap() {
        map = L.map('leafletSelectMap').setView(zooCenter, 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Map Click event
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            // Set coordinates inputs
            document.getElementById('coord_lat').value = lat.toFixed(6);
            document.getElementById('coord_lng').value = lng.toFixed(6);

            // Add or move marker
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        });
    }

    function submitForm() {
        const name = document.getElementById('name').value.trim();
        const lat = document.getElementById('coord_lat').value;

        if (!name) {
            showToast('⚠️ يرجى إدخال اسم الموقع');
            return;
        }

        if (!lat) {
            showToast('⚠️ يرجى النقر على الخريطة لتحديد الإحداثيات');
            return;
        }

        const btn = document.querySelector('.btn-submit-premium');
        btn.textContent = '⏳ جاري الإسقاط...';
        btn.disabled = true;

        setTimeout(() => {
            showToast('✅ تم إسقاط وتثبيت الموقع على الخريطة بنجاح');
            btn.textContent = '✅ تم الحفظ!';
            setTimeout(() => { window.location.href = '/admin/map-locations'; }, 1000);
        }, 800);
    }

    window.onload = initSelectMap;
</script>
@endsection
