<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tripoli Zoo - Smart App</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #121212;
            --white: #ffffff;
            --card-bg: #FCFAF6;
            --grid-card-bg: #FFFFFF;
            --text-main: #1C1C1E;
            --text-muted: #8E8E93;
            --accent: #B4CC1D; /* Lime green */
            --accent-light: #E9EFB9;
            --light-green: #E8F5E9;
            --light-red: #FCE4EC;
            --light-gray: #F0F4F8;
            --icon-green: #2E7D32;
            --icon-red: #C2185B;
            --icon-gray: #455A64;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #f1f5f9; /* App background outside safe area */
            display: flex;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .app-container {
            width: 100%;
            max-width: 480px; /* Mobile constraint for desktop viewing */
            height: 100%;
            position: relative;
            background: url('/zoo_lion.png') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 50px rgba(0,0,0,0.15);
        }

        /* Overlay for text readability */
        .app-container::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.4) 30%, rgba(0,0,0,0.7) 100%);
            z-index: 1;
        }

        /* Top Bar */
        .top-bar {
            position: absolute;
            top: 24px;
            left: 20px;
            right: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .badge-open {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
        }
        .badge-open .dot {
            width: 8px;
            height: 8px;
            background: var(--accent);
            border-radius: 50%;
        }

        .lang-selector {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 8px 14px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
            cursor: pointer;
        }

        /* Hero Text */
        .hero-text {
            position: absolute;
            bottom: calc(54%); /* Keep it just above the white bottom sheet */
            left: 24px;
            right: 24px;
            z-index: 10;
        }

        .brand-subtitle {
            color: rgba(255,255,255,0.95);
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hero-title {
            color: var(--white);
            font-size: 3.4rem;
            font-weight: 500;
            line-height: 1.05;
            letter-spacing: -1.5px;
            font-family: 'Times New Roman', Times, serif;
        }

        .hero-title span {
            color: var(--accent);
            font-style: italic;
        }

        /* Bottom Sheet Overlay */
        .bottom-sheet {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 52%;
            background: var(--card-bg);
            border-radius: 36px 36px 0 0;
            z-index: 20;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Grid Cards */
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 16px;
            height: 100%;
            padding-bottom: 20px;
        }

        .grid-card {
            background: var(--grid-card-bg);
            border-radius: 28px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: transform 0.2s;
            cursor: pointer;
        }
        .grid-card:active {
            transform: scale(0.96);
        }

        .icon-wrap {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: auto;
        }
        
        .icon-wrap svg {
            width: 24px;
            height: 24px;
        }

        .c-qr { background: #E9F2D8; color: #435200; }
        .c-tour { background: #E8F5E9; color: #2E7D32; }
        .c-map { background: #FCE4EC; color: #880E4F; }
        .c-info { background: #E8ECEF; color: #37474F; }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-top: 18px;
            margin-bottom: 4px;
        }

        .card-desc {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* Media queries for adjustments */
        @media (max-width: 380px) {
            .hero-title { font-size: 2.8rem; }
            .grid-card { padding: 18px; }
            .card-title { font-size: 1rem; }
            .icon-wrap { width: 44px; height: 44px; }
            .bottom-sheet { height: 55%; }
        }
    </style>
</head>
<body>

<div class="app-container">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="badge-open">
            <div class="dot"></div>
            Open today
        </div>
        <div class="lang-selector">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            EN
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    <!-- Hero Text -->
    <div class="hero-text">
        <div class="brand-subtitle">TRIPOLI • SMART ZOO</div>
        <h1 class="hero-title">Explore wildlife<br><span>today.</span></h1>
    </div>

    <!-- Bottom Sheet -->
    <div class="bottom-sheet">
        <div class="grid-container">
            <!-- QR Scanner -->
            <div class="grid-card">
                <div class="icon-wrap c-qr">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="5" y="5" width="3" height="3" fill="currentColor"/>
                        <rect x="16" y="5" width="3" height="3" fill="currentColor"/>
                        <rect x="16" y="16" width="3" height="3" fill="currentColor"/>
                        <rect x="5" y="16" width="3" height="3" fill="currentColor"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">QR Scanner</div>
                    <div class="card-desc">Tap any enclosure</div>
                </div>
            </div>

            <!-- Virtual Tour -->
            <div class="grid-card">
                <div class="icon-wrap c-tour">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Virtual Tour</div>
                    <div class="card-desc">360° panorama</div>
                </div>
            </div>

            <!-- Interactive Map -->
            <div class="grid-card">
                <div class="icon-wrap c-map">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4z"/>
                        <line x1="8" y1="2" x2="8" y2="18"/>
                        <line x1="16" y1="6" x2="16" y2="22"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Interactive Map</div>
                    <div class="card-desc">Live wayfinding</div>
                </div>
            </div>

            <!-- Visitor Info -->
            <div class="grid-card">
                <div class="icon-wrap c-info">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Visitor Info</div>
                    <div class="card-desc">Open 09:00 - 17:00</div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
