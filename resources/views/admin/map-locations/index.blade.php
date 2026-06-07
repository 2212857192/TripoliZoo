@extends('admin.layout')
@section('title', 'إدارة خريطة الحديقة | Tripoli Zoo')
@section('page_title', 'إدارة مواقع الخريطة')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(226, 232, 240, 0.8);
        --primary-gradient: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        --accent-gradient: linear-gradient(135deg, #E8651A 0%, #f97316 100%);
        --card-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
        --card-hover-shadow: 0 25px 45px -12px rgba(45, 90, 39, 0.15);
    }

    .map-dashboard-grid {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 2rem;
        align-items: stretch;
        min-height: 680px;
    }

    /* Premium card design */
    .premium-panel-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: var(--card-shadow);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .premium-panel-card:hover {
        box-shadow: var(--card-hover-shadow);
        border-color: rgba(45, 90, 39, 0.15);
    }

    .panel-accent-header {
        padding: 1.5rem;
        background: linear-gradient(to left, rgba(45, 90, 39, 0.03), transparent);
        border-bottom: 1.5px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .panel-accent-header h3 {
        font-size: 1.15rem;
        font-weight: 900;
        color: #1e3a1e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .icon-wrapper {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(45, 90, 39, 0.1);
        color: #2d5a27;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .panel-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
    }

    /* List Controls */
    .controls-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 12px 42px 12px 16px;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        outline: none;
        transition: all 0.2s;
        background: #f8fafc;
    }

    .search-input:focus {
        border-color: var(--orange);
        background: white;
        box-shadow: 0 0 0 3px rgba(232, 101, 26, 0.06);
    }

    .search-icon-svg {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }

    .filter-select {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.88rem;
        font-weight: 700;
        background: #f8fafc;
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-select:focus {
        border-color: #2d5a27;
        background: white;
    }

    /* Location List */
    .locations-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        overflow-y: auto;
        padding-left: 4px;
        flex: 1;
        max-height: 480px;
    }

    .location-item-card {
        background: white;
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
        gap: 8px;
        border-right: 4px solid var(--border);
    }

    .location-item-card:hover, .location-item-card.active {
        border-color: #2d5a27;
        background: rgba(45, 90, 39, 0.01);
        box-shadow: 0 8px 20px -6px rgba(45, 90, 39, 0.12);
        transform: translateX(-4px);
    }

    .location-item-card.active {
        border-right-color: #2d5a27;
        background: rgba(45, 90, 39, 0.03);
    }

    .location-item-card[data-type="enclosure"] { border-right-color: #10B981; }
    .location-item-card[data-type="service"]   { border-right-color: #0EA5E9; }
    .location-item-card[data-type="dining"]    { border-right-color: #F59E0B; }

    .location-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .location-title {
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--text-main);
    }

    .category-badge {
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 900;
    }

    .category-badge.enclosure { background: #DCFCE7; color: #166534; }
    .category-badge.service   { background: #E0F2FE; color: #0369A1; }
    .category-badge.dining    { background: #FEF3C7; color: #92400E; }

    .location-details-row {
        display: flex;
        gap: 15px;
        font-size: 0.78rem;
        color: var(--text-muted);
        font-weight: 700;
    }

    .location-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 6px;
        border-top: 1px solid var(--bg-color);
        padding-top: 10px;
    }

    .btn-action-pill {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 800;
        border: 1.5px solid var(--border);
        background: white;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        color: var(--text-main);
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-action-pill.edit:hover {
        border-color: var(--orange);
        color: var(--orange);
        background: #FFFBF8;
    }

    .btn-action-pill.delete:hover {
        border-color: #EF4444;
        color: #EF4444;
        background: #FFF5F5;
    }

    /* Map container */
    .map-canvas-card {
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        position: relative;
        background: #F1F5F9;
        display: flex;
        flex-direction: column;
    }

    #leafletMap {
        width: 100%;
        flex: 1;
        z-index: 1;
    }

    /* Action buttons */
    .btn-add-location {
        padding: 10px 20px;
        background: var(--accent-gradient);
        color: white;
        border: none;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.88rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        box-shadow: 0 6px 15px rgba(232, 101, 26, 0.25);
        transition: all 0.2s;
    }

    .btn-add-location:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(232, 101, 26, 0.35);
    }

    /* Premium Custom Map Popups */
    .premium-popup-content {
        font-family: 'Cairo', sans-serif;
        padding: 4px;
        text-align: right;
    }

    .premium-popup-title {
        font-weight: 900;
        font-size: 0.95rem;
        color: #1e3a1e;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .premium-popup-code {
        font-size: 0.75rem;
        color: var(--orange);
        font-weight: 800;
        margin-bottom: 6px;
    }

    .premium-popup-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.5;
        font-weight: 600;
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

    @media (max-width: 900px) {
        .map-dashboard-grid { grid-template-columns: 1fr; }
        .map-canvas-card { height: 450px; }
    }
</style>
@endsection

@section('content')
<div class="map-dashboard-grid">

    <!-- Sidebar Panel: Locations List -->
    <div class="premium-panel-card">
        <div class="panel-accent-header">
            <h3>
                <div class="icon-wrapper">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon></svg>
                </div>
                مواقع الخريطة
            </h3>
            <a href="/admin/map-locations/create" class="btn-add-location">
                + إضافة موقع جديد
            </a>
        </div>
        
        <div class="panel-content">
            <!-- Search & Filters -->
            <div class="controls-grid">
                <div class="search-input-wrapper">
                    <input type="text" id="mapSearch" class="search-input" placeholder="ابحث بالاسم أو الرمز..." onkeyup="filterLocations()">
                    <svg class="search-icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <select id="mapFilter" class="filter-select" onchange="filterLocations()">
                    <option value="all">كل الفئات والتصنيفات</option>
                    <option value="enclosure">🦁 بيوت الحيوانات</option>
                    <option value="service">🎫 الخدمات والمرافق</option>
                    <option value="dining">🍕 المطاعم والمقاهي</option>
                </select>
            </div>

            <!-- Locations Scroll List -->
            <div class="locations-list" id="locationsList">
                
                <!-- Item 1 -->
                <div class="location-item-card" data-id="1" data-name="قفص الأسد الأفريقي" data-type="enclosure" onclick="selectLocation(1, 32.8492, 13.1782)" id="list-item-1">
                    <div class="location-item-header">
                        <span class="location-title">قفص الأسد الأفريقي</span>
                        <span class="category-badge enclosure">بيت حيوانات</span>
                    </div>
                    <div class="location-details-row">
                        <span>الرمز: L-01</span>
                        <span>شمال: 32.8492</span>
                        <span>شرق: 13.1782</span>
                    </div>
                    <div class="location-actions">
                        <button class="btn-action-pill" title="تفعيل على الخريطة" onclick="event.stopPropagation(); toggleMapVisibility(1)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <a href="/admin/map-locations/1/edit" class="btn-action-pill edit" title="تعديل">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </a>
                        <button class="btn-action-pill delete" title="حذف" onclick="event.stopPropagation(); deleteLocation(1)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="location-item-card" data-id="2" data-name="بحيرة الفيل الأسيوي" data-type="enclosure" onclick="selectLocation(2, 32.8480, 13.1800)" id="list-item-2">
                    <div class="location-item-header">
                        <span class="location-title">بحيرة الفيل الأسيوي</span>
                        <span class="category-badge enclosure">بيت حيوانات</span>
                    </div>
                    <div class="location-details-row">
                        <span>الرمز: E-04</span>
                        <span>شمال: 32.8480</span>
                        <span>شرق: 13.1800</span>
                    </div>
                    <div class="location-actions">
                        <button class="btn-action-pill" title="تفعيل على الخريطة" onclick="event.stopPropagation(); toggleMapVisibility(2)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <a href="/admin/map-locations/2/edit" class="btn-action-pill edit" title="تعديل">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </a>
                        <button class="btn-action-pill delete" title="حذف" onclick="event.stopPropagation(); deleteLocation(2)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="location-item-card" data-id="3" data-name="البوابة الرئيسية" data-type="service" onclick="selectLocation(3, 32.8465, 13.1788)" id="list-item-3">
                    <div class="location-item-header">
                        <span class="location-title">البوابة الرئيسية للدخول</span>
                        <span class="category-badge service">مرافق وخدمات</span>
                    </div>
                    <div class="location-details-row">
                        <span>الرمز: EN-01</span>
                        <span>شمال: 32.8465</span>
                        <span>شرق: 13.1788</span>
                    </div>
                    <div class="location-actions">
                        <button class="btn-action-pill" title="تفعيل على الخريطة" onclick="event.stopPropagation(); toggleMapVisibility(3)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <a href="/admin/map-locations/3/edit" class="btn-action-pill edit" title="تعديل">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </a>
                        <button class="btn-action-pill delete" title="حذف" onclick="event.stopPropagation(); deleteLocation(3)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="location-item-card" data-id="4" data-name="مطعم الواحة الأخضر" data-type="dining" onclick="selectLocation(4, 32.8485, 13.1765)" id="list-item-4">
                    <div class="location-item-header">
                        <span class="location-title">مطعم الواحة الأخضر</span>
                        <span class="category-badge dining">مطاعم ومقاهي</span>
                    </div>
                    <div class="location-details-row">
                        <span>الرمز: DN-03</span>
                        <span>شمال: 32.8485</span>
                        <span>شرق: 13.1765</span>
                    </div>
                    <div class="location-actions">
                        <button class="btn-action-pill" title="تفعيل على الخريطة" onclick="event.stopPropagation(); toggleMapVisibility(4)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <a href="/admin/map-locations/4/edit" class="btn-action-pill edit" title="تعديل">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </a>
                        <button class="btn-action-pill delete" title="حذف" onclick="event.stopPropagation(); deleteLocation(4)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Map Canvas Card -->
    <div class="map-canvas-card">
        <div id="leafletMap"></div>
    </div>

</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<!-- Leaflet JS CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map;
    let markers = {};

    const zooCenter = [32.8485, 13.1785];

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2800);
    }

    function initMap() {
        map = L.map('leafletMap').setView(zooCenter, 16);

        // Load premium grey map styling for a cleaner dashboard GIS look
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; CartoDB',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Custom premium colored icons
        const animalIcon = L.divIcon({
            className: 'custom-leaflet-icon',
            html: `<div style="background:#10B981; width:20px; height:20px; border-radius:50%; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3); position:relative;"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        const serviceIcon = L.divIcon({
            className: 'custom-leaflet-icon',
            html: `<div style="background:#0EA5E9; width:20px; height:20px; border-radius:50%; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3); position:relative;"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        const diningIcon = L.divIcon({
            className: 'custom-leaflet-icon',
            html: `<div style="background:#F59E0B; width:20px; height:20px; border-radius:50%; border:3px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.3); position:relative;"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        addMarker(1, [32.8492, 13.1782], `
            <div class="premium-popup-content">
                <div class="premium-popup-title">🦁 قفص الأسد الأفريقي</div>
                <div class="premium-popup-code">رمز الموقع: L-01</div>
                <div class="premium-popup-desc">يقع في المنطقة الشمالية للحديقة، بيت مخصص لحيوانات السافانا.</div>
            </div>
        `, animalIcon);

        addMarker(2, [32.8480, 13.1800], `
            <div class="premium-popup-content">
                <div class="premium-popup-title">🐘 بحيرة الفيل الأسيوي</div>
                <div class="premium-popup-code">رمز الموقع: E-04</div>
                <div class="premium-popup-desc">البحيرة الطبيعية والمظلات المخصصة للفيلة الآسيوية الكبرى.</div>
            </div>
        `, animalIcon);

        addMarker(3, [32.8465, 13.1788], `
            <div class="premium-popup-content">
                <div class="premium-popup-title">🎫 البوابة الرئيسية</div>
                <div class="premium-popup-code">رمز الموقع: EN-01</div>
                <div class="premium-popup-desc">مدخل ومخرج الزوار الرئيسي، ويضم مكتب إصدار التذاكر الموحد.</div>
            </div>
        `, serviceIcon);

        addMarker(4, [32.8485, 13.1765], `
            <div class="premium-popup-content">
                <div class="premium-popup-title">🍔 مطعم الواحة الأخضر</div>
                <div class="premium-popup-code">رمز الموقع: DN-03</div>
                <div class="premium-popup-desc">منطقة المطاعم الرئيسية وسط الحديقة، تقدّم الوجبات السريعة والعائلية.</div>
            </div>
        `, diningIcon);
    }

    function addMarker(id, coords, popupText, iconStyle) {
        const marker = L.marker(coords, { icon: iconStyle }).addTo(map);
        marker.bindPopup(popupText);
        
        marker.on('click', function() {
            selectLocation(id, coords[0], coords[1], false);
        });

        markers[id] = marker;
    }

    function selectLocation(id, lat, lng, panMap = true) {
        document.querySelectorAll('.location-item-card').forEach(el => el.classList.remove('active'));

        const item = document.getElementById('list-item-' + id);
        if(item) {
            item.classList.add('active');
            item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        if(markers[id]) {
            markers[id].openPopup();
        }

        if(panMap && map) {
            map.panTo([lat, lng]);
        }
    }

    function deleteLocation(id) {
        if(confirm('هل أنت متأكد من حذف هذا الموقع من الخريطة؟')) {
            const item = document.getElementById('list-item-' + id);
            if(item) item.remove();

            if(markers[id]) {
                map.removeLayer(markers[id]);
                delete markers[id];
            }

            showToast('🗑️ تم حذف الموقع بنجاح');
        }
    }

    function toggleMapVisibility(id) {
        if(markers[id]) {
            if(map.hasLayer(markers[id])) {
                map.removeLayer(markers[id]);
                showToast('👁️ تم إخفاء الموقع من الخريطة');
            } else {
                markers[id].addTo(map);
                showToast('👁️ تم إظهار الموقع على الخريطة');
            }
        }
    }

    function filterLocations() {
        const query = document.getElementById('mapSearch').value.toLowerCase();
        const category = document.getElementById('mapFilter').value;

        const items = document.querySelectorAll('.location-item-card');
        items.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const type = item.getAttribute('data-type');
            const id = item.getAttribute('data-id');

            const matchesQuery = name.includes(query);
            const matchesCat = (category === 'all') || (type === category);

            if(matchesQuery && matchesCat) {
                item.style.display = 'flex';
                if(markers[id] && !map.hasLayer(markers[id])) {
                    markers[id].addTo(map);
                }
            } else {
                item.style.display = 'none';
                if(markers[id] && map.hasLayer(markers[id])) {
                    map.closePopup();
                    map.removeLayer(markers[id]);
                }
            }
        });
    }

    window.onload = initMap;
</script>
@endsection
