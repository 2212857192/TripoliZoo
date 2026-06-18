<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>خريطة الحديقة | Tripoli Zoo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }

        body {
            font-family: 'Cairo', sans-serif;
            background: #0f1a0f;
            color: #1a2e1a;
            height: 100dvh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── Top Bar ── */
        .topbar {
            position: relative;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: #0f1a0f;
            flex-shrink: 0;
        }
        .back-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #a8d5a2;
            text-decoration: none;
            font-weight: 700;
            font-size: .9rem;
        }
        .back-btn svg { width: 18px; height: 18px; }
        .topbar-title {
            color: #fff;
            font-size: 1rem;
            font-weight: 900;
            letter-spacing: -.3px;
        }
        .topbar-right { width: 70px; display: flex; justify-content: flex-end; }

        /* ── Legend pills ── */
        .legend {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: #0f1a0f;
            overflow-x: auto;
            flex-shrink: 0;
            scrollbar-width: none;
        }
        .legend::-webkit-scrollbar { display: none; }
        .legend-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
            border: 1.5px solid transparent;
            transition: .18s;
            color: #e2ede2;
            background: rgba(255,255,255,.08);
        }
        .legend-pill.active { background: rgba(255,255,255,.18); border-color: rgba(255,255,255,.3); }
        .legend-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .dot-enclosure { background: #22c55e; }
        .dot-service    { background: #38bdf8; }
        .dot-dining     { background: #fbbf24; }
        .dot-all        { background: linear-gradient(135deg, #22c55e 33%, #38bdf8 66%, #fbbf24 100%); }

        /* ── Map Container ── */
        .map-outer {
            flex: 1;
            position: relative;
            overflow: hidden;
            background: #1a2e1a;
        }

        .map-scroll {
            width: 100%;
            height: 100%;
            overflow: auto;
            position: relative;
            cursor: grab;
        }
        .map-scroll:active { cursor: grabbing; }
        .map-scroll::-webkit-scrollbar { display: none; }

        .map-inner {
            position: relative;
            display: inline-block;
            min-width: 100%;
            min-height: 100%;
        }

        .route-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 4;
        }

        .map-img {
            display: block;
            width: 100%;
            height: auto;
            min-width: 600px;
            user-select: none;
            -webkit-user-drag: none;
        }

        /* ── Pins ── */
        .pin {
            position: absolute;
            transform: translate(-50%, -50%);
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            z-index: 5;
            transition: transform .2s cubic-bezier(.34,1.56,.64,1);
        }
        .pin:hover, .pin.active { transform: translate(-50%, -50%) scale(1.25); }

        .pin-dot {
            width: 40px;
            height: 40px;
            border-radius: 50% 50% 50% 0;
            rotate: -45deg;
            border: 3px solid #fff;
            box-shadow: 0 6px 20px rgba(0,0,0,.5), 0 0 0 4px rgba(255,255,255,.15);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s;
            overflow: hidden;
        }
        .pin-dot svg {
            rotate: 45deg;
            width: 18px;
            height: 18px;
            color: #fff;
            flex-shrink: 0;
        }
        
        .pin-photo {
            width: 46px;
            height: 46px;
            object-fit: cover;
            border-radius: 50%;
            rotate: 45deg;
        }

        .pin.enclosure .pin-dot { background: #16a34a; }
        .pin.service    .pin-dot { background: #0284c7; }
        .pin.dining     .pin-dot { background: #d97706; }
        .pin.active .pin-dot {
            box-shadow: 0 8px 24px rgba(0,0,0,.6), 0 0 0 6px rgba(255,255,255,.3);
        }

        /* Pulse ring */
        .pin::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 56px; height: 56px;
            transform: translate(-50%, -50%) scale(0);
            border-radius: 50%;
            background: rgba(255,255,255,.25);
            animation: pulse-ring 2s ease-out infinite;
            pointer-events: none;
        }
        @keyframes pulse-ring {
            0%  { transform: translate(-50%,-50%) scale(0); opacity: 1; }
            70% { transform: translate(-50%,-50%) scale(1.4); opacity: 0; }
            100%{ transform: translate(-50%,-50%) scale(1.4); opacity: 0; }
        }

        /* ── Bottom Card ── */
        .pin-card {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            z-index: 30;
            transform: translateY(100%);
            transition: transform .35s cubic-bezier(.32,.72,0,1);
            border-radius: 24px 24px 0 0;
            background: #fff;
            box-shadow: 0 -8px 40px rgba(0,0,0,.3);
        }
        .pin-card.show { transform: translateY(0); }

        .card-handle {
            width: 40px; height: 4px;
            border-radius: 99px;
            background: #d1d5db;
            margin: 12px auto 0;
        }

        .card-body {
            padding: 16px 22px 28px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .card-top {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .card-icon {
            width: 48px; height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .card-icon svg { width: 22px; height: 22px; color: #fff; }
        .icon-enclosure { background: linear-gradient(135deg,#16a34a,#22c55e); }
        .icon-service    { background: linear-gradient(135deg,#0284c7,#38bdf8); }
        .icon-dining     { background: linear-gradient(135deg,#d97706,#fbbf24); }

        .card-info { flex: 1; }
        .card-name {
            font-size: 1.1rem;
            font-weight: 900;
            color: #111;
            line-height: 1.3;
        }
        .card-cat {
            font-size: .8rem;
            font-weight: 700;
            margin-top: 3px;
        }
        .cat-enclosure { color: #16a34a; }
        .cat-service    { color: #0284c7; }
        .cat-dining     { color: #d97706; }

        .card-desc {
            font-size: .88rem;
            color: #555;
            line-height: 1.8;
            font-weight: 600;
        }

        .card-close {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #f1f5f9;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #64748b;
        }
        .card-close svg { width: 16px; height: 16px; }

        .card-link-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #16a34a;
            color: #fff;
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: .95rem;
            margin-top: 5px;
            transition: .2s;
        }
        .card-link-btn:active { background: #15803d; }

        /* Backdrop */
        .backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.35);
            z-index: 25;
        }
        .backdrop.show { display: block; }

        /* Empty state */
        .empty-map {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: #a8d5a2;
            font-weight: 700;
            font-size: .95rem;
        }
        .empty-map svg { width: 48px; height: 48px; opacity: .5; }

        /* Zoom controls */
        .zoom-controls {
            position: absolute;
            bottom: 16px;
            left: 16px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            z-index: 10;
        }
        .zoom-btn {
            width: 40px; height: 40px;
            border-radius: 12px;
            border: none;
            background: rgba(15,26,15,.85);
            backdrop-filter: blur(8px);
            color: #a8d5a2;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,.1);
            transition: .15s;
        }
        .zoom-btn:active { background: rgba(34,197,94,.3); }

        /* Pin count badge */
        .pin-count {
            position: absolute;
            top: 12px;
            left: 16px;
            z-index: 10;
            background: rgba(15,26,15,.85);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 99px;
            padding: 5px 12px;
            color: #a8d5a2;
            font-size: .8rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

<!-- Top Bar -->
<header class="topbar">
    <a href="/app" class="back-btn" id="backBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
        العودة
    </a>
    <span class="topbar-title">خريطة الحديقة</span>
    <div class="topbar-right"></div>
</header>

<!-- Legend -->
<div class="legend">
    <button class="legend-pill active" data-filter="all" onclick="filterPins('all')">
        <span class="legend-dot" style="background:linear-gradient(135deg,#22c55e 33%,#38bdf8 66%,#fbbf24 100%)"></span>
        الكل
    </button>
    <button class="legend-pill" data-filter="enclosure" onclick="filterPins('enclosure')">
        <span class="legend-dot dot-enclosure"></span>
        أقفاص الحيوانات
    </button>
    <button class="legend-pill" data-filter="service" onclick="filterPins('service')">
        <span class="legend-dot dot-service"></span>
        الخدمات والمرافق
    </button>
    <button class="legend-pill" data-filter="dining" onclick="filterPins('dining')">
        <span class="legend-dot dot-dining"></span>
        المطاعم والمقاهي
    </button>
</div>

<!-- Map Area -->
<div class="map-outer">
    <div class="map-scroll" id="mapScroll">
        <div class="map-inner" id="mapInner">
            <img
                class="map-img"
                id="mapImg"
                src="{{ $mapImageUrl }}"
                alt="خريطة حديقة حيوان طرابلس"
                draggable="false"
            >

            @forelse($locations as $location)
                <button
                    class="pin {{ $location['category'] }}"
                    id="pin-{{ $location['id'] }}"
                    data-id="{{ $location['id'] }}"
                    data-name="{{ $location['name'] }}"
                    data-category="{{ $location['category'] }}"
                    data-description="{{ $location['description'] ?? '' }}"
                    data-profile-id="{{ $location['animal_profile_id'] ?? '' }}"
                    style="left: {{ $location['x'] * 100 }}%; top: {{ $location['y'] * 100 }}%;"
                    onclick="openPin(this)"
                    aria-label="{{ $location['name'] }}"
                    type="button"
                >
                    <div class="pin-dot">
                        @if(!empty($location['animal_photo_url']))
                            <img src="{{ $location['animal_photo_url'] }}" alt="" class="pin-photo">
                        @elseif($location['category'] === 'enclosure')
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                        @elseif($location['category'] === 'dining')
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                        @endif
                    </div>
                </button>
            @empty
            @endforelse

            <svg class="route-layer" id="routeLayer" aria-hidden="true"></svg>

            <!-- Pin count -->
            <div class="pin-count" id="pinCount">{{ count($locations) }} موقع</div>
        </div>
    </div>

    <!-- Zoom controls -->
    <div class="zoom-controls">
        <button class="zoom-btn" onclick="zoom(1.25)" title="تكبير">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </button>
        <button class="zoom-btn" onclick="zoom(0.8)" title="تصغير">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </button>
    </div>

    @if(count($locations) === 0)
    <div class="empty-map">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4z"/>
            <line x1="8" y1="2" x2="8" y2="18"/>
            <line x1="16" y1="6" x2="16" y2="22"/>
        </svg>
        لا توجد مواقع مفعّلة على الخريطة
    </div>
    @endif
</div>

<!-- Backdrop -->
<div class="backdrop" id="backdrop" onclick="closePin()"></div>

<!-- Pin Detail Card -->
<div class="pin-card" id="pinCard">
    <div class="card-handle"></div>
    <div class="card-body">
        <div class="card-top">
            <div class="card-icon" id="cardIcon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
            </div>
            <div class="card-info">
                <div class="card-name" id="cardName">—</div>
                <div class="card-cat" id="cardCat"></div>
            </div>
            <button class="card-close" onclick="closePin()" aria-label="إغلاق">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <p class="card-desc" id="cardDesc"></p>
        <a href="#" id="cardLink" class="card-link-btn" style="display: none;">عرض تفاصيل الحيوان</a>
    </div>
</div>

<script>
    const CATEGORY_LABELS = {
        enclosure: 'أقفاص وموائل الحيوانات',
        service:   'الخدمات والمرافق العامة',
        dining:    'المطاعم والمقاهي',
    };

    const ICON_SVG = {
        enclosure: `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>`,
        dining:    `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>`,
        service:   `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>`,
    };

    let activePin = null;
    let currentFilter = 'all';
    let scale = 1;

    function openPin(btn) {
        const cat = btn.dataset.category;
        const name = btn.dataset.name;
        const desc = btn.dataset.description || 'موقع داخل حديقة حيوان طرابلس';

        // Update card
        document.getElementById('cardName').textContent = name;
        document.getElementById('cardDesc').textContent = desc;

        const catEl = document.getElementById('cardCat');
        catEl.textContent = CATEGORY_LABELS[cat] || cat;
        catEl.className = 'card-cat cat-' + cat;

        const iconEl = document.getElementById('cardIcon');
        iconEl.className = 'card-icon icon-' + cat;
        iconEl.innerHTML = ICON_SVG[cat] || ICON_SVG.service;

        // Active state
        if (activePin) activePin.classList.remove('active');
        btn.classList.add('active');
        activePin = btn;

        const profileId = btn.dataset.profileId;
        const linkBtn = document.getElementById('cardLink');
        if (profileId) {
            linkBtn.href = '/app/animals/' + profileId;
            linkBtn.style.display = 'flex';
        } else {
            linkBtn.style.display = 'none';
        }

        document.getElementById('pinCard').classList.add('show');
        document.getElementById('backdrop').classList.add('show');
    }

    function closePin() {
        document.getElementById('pinCard').classList.remove('show');
        document.getElementById('backdrop').classList.remove('show');
        if (activePin) { activePin.classList.remove('active'); activePin = null; }
        clearRoute();
    }

    function filterPins(cat) {
        currentFilter = cat;

        // Update pills
        document.querySelectorAll('.legend-pill').forEach(p => {
            p.classList.toggle('active', p.dataset.filter === cat);
        });

        // Show/hide pins
        const allPins = document.querySelectorAll('.pin');
        let visible = 0;
        allPins.forEach(p => {
            const show = cat === 'all' || p.dataset.category === cat;
            p.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('pinCount').textContent = visible + ' موقع';

        // Close card if active pin is hidden
        if (activePin && currentFilter !== 'all' && activePin.dataset.category !== cat) {
            closePin();
        }
    }

    // Zoom via CSS transform on map-inner
    function zoom(factor) {
        const inner = document.getElementById('mapInner');
        scale = Math.min(3, Math.max(0.6, scale * factor));
        inner.style.transform = `scale(${scale})`;
        inner.style.transformOrigin = 'top right';
    }

    // Keyboard close
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closePin(); });

    const ENTRANCE = { x: 0.5, y: 0.92 };

    function drawRouteToPin(btn) {
        const layer = document.getElementById('routeLayer');
        const inner = document.getElementById('mapInner');
        if (!layer || !inner || !btn) return;

        const rect = inner.getBoundingClientRect();
        layer.setAttribute('viewBox', `0 0 ${rect.width} ${rect.height}`);
        layer.style.width = rect.width + 'px';
        layer.style.height = rect.height + 'px';

        const style = window.getComputedStyle(btn);
        const left = parseFloat(style.left) / 100 * rect.width;
        const top = parseFloat(style.top) / 100 * rect.height;
        const startX = ENTRANCE.x * rect.width;
        const startY = ENTRANCE.y * rect.height;

        layer.innerHTML = `
            <defs>
                <marker id="routeArrow" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto">
                    <polygon points="0 0, 8 3, 0 6" fill="#e8651a"/>
                </marker>
            </defs>
            <line x1="${startX}" y1="${startY}" x2="${left}" y2="${top}"
                stroke="#e8651a" stroke-width="5" stroke-dasharray="10 8"
                stroke-linecap="round" marker-end="url(#routeArrow)" opacity="0.95"/>
            <circle cx="${startX}" cy="${startY}" r="8" fill="#2d5a27" stroke="#fff" stroke-width="3"/>
        `;
    }

    function clearRoute() {
        const layer = document.getElementById('routeLayer');
        if (layer) layer.innerHTML = '';
    }

    function focusFromQuery() {
        const params = new URLSearchParams(window.location.search);
        const focusId = params.get('focus');
        const showRoute = params.get('route') === '1';
        if (!focusId) return;

        const pin = document.getElementById('pin-' + focusId);
        if (!pin) return;

        openPin(pin);
        if (showRoute) drawRouteToPin(pin);

        const scroll = document.getElementById('mapScroll');
        const inner = document.getElementById('mapInner');
        if (scroll && inner) {
            const rect = inner.getBoundingClientRect();
            const style = window.getComputedStyle(pin);
            const left = parseFloat(style.left) / 100 * rect.width;
            const top = parseFloat(style.top) / 100 * rect.height;
            scroll.scrollLeft = Math.max(0, left - scroll.clientWidth / 2);
            scroll.scrollTop = Math.max(0, top - scroll.clientHeight / 2);
        }
    }

    window.addEventListener('load', focusFromQuery);
    window.addEventListener('resize', () => {
        if (activePin && new URLSearchParams(window.location.search).get('route') === '1') {
            drawRouteToPin(activePin);
        }
    });

    // Touch drag on map
    const scroll = document.getElementById('mapScroll');
    let isDragging = false, startX, startY, scrollLeft, scrollTop;

    scroll.addEventListener('mousedown', e => {
        if (e.target.classList.contains('pin') || e.target.closest('.pin')) return;
        isDragging = true;
        startX = e.pageX - scroll.offsetLeft;
        startY = e.pageY - scroll.offsetTop;
        scrollLeft = scroll.scrollLeft;
        scrollTop = scroll.scrollTop;
        scroll.style.cursor = 'grabbing';
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
        scroll.style.cursor = 'grab';
    });

    document.addEventListener('mousemove', e => {
        if (!isDragging) return;
        e.preventDefault();
        scroll.scrollLeft = scrollLeft - (e.pageX - scroll.offsetLeft - startX);
        scroll.scrollTop  = scrollTop  - (e.pageY - scroll.offsetTop  - startY);
    });
</script>
</body>
</html>
