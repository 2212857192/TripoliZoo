<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="حديقة حيوان طرابلس — نظام إدارة متكامل لحديقة الحيوان في طرابلس ليبيا.">
    <title>حديقة حيوان طرابلس | Tripoli Zoo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>

/* ═══════════════════════════════════════
   TOKENS — ألوان اللوقو الثلاثة
═══════════════════════════════════════ */
:root {
    --green:   #2E7D32;   /* أخضر داكن — اللوقو */
    --green2:  #388E3C;   /* أخضر متوسط */
    --brown:   #5A2D0C;   /* بني داكن — اللوقو */
    --brown2:  #3B1A06;   /* بني أعمق */
    --orange:  #E8651A;   /* برتقالي — اللوقو */
    --orange2: #BF4F10;   /* برتقالي داكن */
    --white:   #FFFFFF;
    --off:     #F8F3EC;   /* خلفية كريمية دافئة */
    --text:    #1A1A1A;
    --muted:   #5A5A5A;
    --ease:    cubic-bezier(.4,0,.2,1);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html  { scroll-behavior: smooth; }
body  {
    font-family: 'Cairo', sans-serif;
    background: var(--white);
    color: var(--text);
    direction: rtl;
    overflow-x: hidden;
}
a { text-decoration: none; color: inherit; }

/* ═══════════════════════════════════════
   NAVBAR
═══════════════════════════════════════ */
.nav {
    position: fixed;
    inset: 0 0 auto 0;
    z-index: 999;
    height: 76px;
    background: linear-gradient(135deg, var(--brown2) 0%, var(--brown) 60%, var(--green) 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 5%;
    box-shadow: 0 2px 20px rgba(0,0,0,0.3);
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 13px;
}
.nav-logo {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: var(--white);
    padding: 5px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    flex-shrink: 0;
}
.nav-logo img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }

.nav-name       { color: var(--white); font-size: 1rem; font-weight: 800; line-height: 1.25; }
.nav-name span  { display: block; font-size: 0.68rem; font-weight: 400; color: rgba(255,255,255,.6); }

.nav-links { display: flex; align-items: center; gap: 2rem; list-style: none; }
.nav-links a {
    color: rgba(255,255,255,.82);
    font-size: 0.9rem;
    font-weight: 600;
    transition: color .2s;
    border-bottom: 2px solid transparent;
    padding-bottom: 2px;
}
.nav-links a:hover { color: var(--white); border-bottom-color: var(--orange); }

.nav-cta {
    padding: 9px 26px;
    background: var(--orange);
    color: var(--white);
    font-family: 'Cairo', sans-serif;
    font-size: 0.88rem; font-weight: 700;
    border: none; border-radius: 50px; cursor: pointer;
    box-shadow: 0 4px 14px rgba(232,101,26,.45);
    transition: all .25s var(--ease);
}
.nav-cta:hover { background: var(--orange2); transform: translateY(-2px); }

/* ═══════════════════════════════════════
   HERO — SLIDER
═══════════════════════════════════════ */
.hero {
    position: relative;
    width: 100%;
    height: 100vh;
    min-height: 620px;
    overflow: hidden;
}

/* slides */
.slide {
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 1s ease;
}
.slide.on { opacity: 1; }

.slide img {
    width: 100%; height: 100%;
    object-fit: cover;
    transform: scale(1.06);
    transition: transform 7s ease;
}
.slide.on img { transform: scale(1); }

/* dark overlay — gradient from bottom */
.slide::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(
        to top,
        rgba(0,0,0,.82) 0%,
        rgba(0,0,0,.35) 50%,
        rgba(0,0,0,.12) 100%
    );
}

/* ── hero content ── */
.hero-body {
    position: absolute;
    inset: 0; z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 96px 6% 100px;
    gap: 0;
}



/* badge */
.hero-tag {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(232,101,26,.22);
    border: 1px solid rgba(232,101,26,.55);
    color: #FFAA70;
    padding: 5px 18px;
    border-radius: 50px;
    font-size: .8rem; font-weight: 700; letter-spacing: 1px;
    margin-bottom: 1.2rem;
    animation: popIn .6s var(--ease) .2s both;
}
.hero-tag-dot {
    width: 7px; height: 7px;
    background: var(--orange);
    border-radius: 50%;
    animation: blink 1.8s infinite;
}

/* title */
.hero-h1 {
    font-size: clamp(2.2rem, 5.5vw, 4rem);
    font-weight: 900;
    color: var(--white);
    line-height: 1.15;
    text-shadow: 0 3px 18px rgba(0,0,0,.5);
    margin-bottom: .5rem;
    animation: popIn .6s var(--ease) .3s both;
}
.hero-h1 em { font-style: normal; color: var(--orange); }

.hero-en {
    font-size: .78rem; font-weight: 600; letter-spacing: 4px;
    color: rgba(255,255,255,.55);
    text-transform: uppercase;
    margin-bottom: 1.2rem;
    animation: popIn .6s var(--ease) .35s both;
}

/* subtitle */
.hero-p {
    font-size: clamp(.95rem, 1.8vw, 1.1rem);
    color: rgba(255,255,255,.8);
    line-height: 1.85;
    max-width: 560px;
    margin-bottom: 2rem;
    animation: popIn .6s var(--ease) .45s both;
}

/* buttons */
.hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; animation: popIn .6s var(--ease) .55s both; }

.btn-or {
    padding: 13px 34px;
    background: var(--orange);
    color: var(--white);
    font-family: 'Cairo', sans-serif; font-size: .95rem; font-weight: 800;
    border: none; border-radius: 50px; cursor: pointer;
    box-shadow: 0 5px 20px rgba(232,101,26,.55);
    transition: all .3s var(--ease);
}
.btn-or:hover { background: var(--orange2); transform: translateY(-3px); box-shadow: 0 10px 28px rgba(232,101,26,.6); }

.btn-ghost {
    padding: 13px 34px;
    background: transparent;
    color: var(--white);
    font-family: 'Cairo', sans-serif; font-size: .95rem; font-weight: 700;
    border: 2px solid rgba(255,255,255,.45);
    border-radius: 50px; cursor: pointer;
    backdrop-filter: blur(8px);
    transition: all .3s var(--ease);
}
.btn-ghost:hover { background: rgba(255,255,255,.15); border-color: var(--white); transform: translateY(-3px); }

/* arrows */
.arrow {
    position: absolute; top: 50%; z-index: 20;
    transform: translateY(-50%);
    width: 50px; height: 50px;
    background: rgba(255,255,255,.12);
    border: 2px solid rgba(255,255,255,.3);
    border-radius: 50%;
    color: var(--white); font-size: 1.4rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; backdrop-filter: blur(8px);
    transition: all .25s var(--ease);
}
.arrow:hover { background: var(--orange); border-color: var(--orange); transform: translateY(-50%) scale(1.1); }
.arrow-r { right: 1.5rem; }
.arrow-l { left: 1.5rem; }

/* dots */
.dots {
    position: absolute; bottom: 1.8rem; left: 50%;
    transform: translateX(-50%);
    display: flex; gap: 9px; z-index: 20;
}
.dot {
    width: 9px; height: 9px; border-radius: 50px;
    background: rgba(255,255,255,.4); border: none; cursor: pointer;
    transition: all .3s var(--ease);
}
.dot.on { width: 28px; background: var(--orange); }

/* progress line */
.prog {
    position: absolute; bottom: 0; left: 0; z-index: 20;
    height: 3px; background: var(--orange); width: 0;
    transition: width linear;
}

/* slide counter */
.counter {
    position: absolute; bottom: 1.8rem; right: 4%;
    z-index: 20; color: rgba(255,255,255,.55);
    font-size: .78rem; font-weight: 600; letter-spacing: 2px;
}
.counter b { color: var(--orange); font-size: 1rem; }


/* ═══════════════════════════════════════
   SECTION COMMONS
═══════════════════════════════════════ */
.sec { padding: 5.5rem 5%; }
.sec-head { text-align: center; margin-bottom: 3.5rem; }
.sec-tag {
    display: inline-block;
    background: rgba(90,45,12,.07);
    border: 1px solid rgba(90,45,12,.18);
    color: var(--brown);
    padding: 4px 16px;
    border-radius: 50px;
    font-size: .78rem; font-weight: 700; letter-spacing: 1.5px;
    margin-bottom: .6rem;
}
.sec-h2 { font-size: clamp(1.8rem, 3.5vw, 2.5rem); font-weight: 900; color: var(--brown); }
.sec-h2 em { font-style: normal; color: var(--green); }
.sec-line { width: 52px; height: 4px; background: var(--orange); border-radius: 4px; margin: .8rem auto 0; }
.sec-p { color: var(--muted); font-size: .97rem; max-width: 520px; margin: .8rem auto 0; line-height: 1.85; }

/* ═══════════════════════════════════════
   FEATURES
═══════════════════════════════════════ */
.feat-bg { background: var(--off); }

/* ── grid: 3 top + 2 centered bottom ── */
.feat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.6rem;
}
.feat-grid .card:nth-child(4) { grid-column: 1; }
.feat-grid .card:nth-child(5) { grid-column: 2; }

@media (max-width: 900px) {
    .feat-grid { grid-template-columns: 1fr 1fr; }
    .feat-grid .card:nth-child(4),
    .feat-grid .card:nth-child(5) { grid-column: auto; }
}
@media (max-width: 560px) {
    .feat-grid { grid-template-columns: 1fr; }
}

.card {
    background: var(--white);
    border-radius: 20px;
    padding: 2.2rem 2rem;
    border: 1px solid rgba(0,0,0,.05);
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    transition: transform .4s var(--ease), box-shadow .4s var(--ease);
    position: relative; overflow: hidden;
    display: flex; flex-direction: column; gap: .5rem;
}
/* top coloured stripe on hover */
.card::after {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--orange), var(--green));
    transform: scaleX(0); transform-origin: right;
    transition: transform .4s var(--ease);
    border-radius: 20px 20px 0 0;
}
.card:hover { transform: translateY(-8px); box-shadow: 0 20px 48px rgba(0,0,0,.12); }
.card:hover::after { transform: scaleX(1); transform-origin: left; }

/* card number watermark */
.card-num {
    position: absolute; top: 1.1rem; left: 1.4rem;
    font-size: 3.5rem; font-weight: 900; line-height: 1;
    color: rgba(90,45,12,.06);
    font-variant-numeric: tabular-nums;
    user-select: none;
}

/* icon */
.card-ico {
    width: 60px; height: 60px; border-radius: 16px;
    font-size: 1.6rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: .6rem;
    transition: transform .3s var(--ease);
}
.card:hover .card-ico { transform: scale(1.1) rotate(-6deg); }
.ico-gr { background: linear-gradient(135deg, rgba(46,125,50,.14), rgba(46,125,50,.06)); }
.ico-or { background: linear-gradient(135deg, rgba(232,101,26,.15), rgba(232,101,26,.06)); }
.ico-br { background: linear-gradient(135deg, rgba(90,45,12,.13),  rgba(90,45,12,.05)); }

.card-h { font-size: 1.05rem; font-weight: 800; color: var(--brown); }
.card-p { font-size: .88rem; color: var(--muted); line-height: 1.72; }

/* ═══════════════════════════════════════
   GALLERY
═══════════════════════════════════════ */
.gal-bg { background: var(--white); }
.gal-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr 1fr;
    grid-template-rows: 230px 230px;
    gap: 10px;
    border-radius: 16px; overflow: hidden;
}
.gal-item { position: relative; overflow: hidden; cursor: pointer; }
.gal-item:first-child { grid-row: span 2; }
.gal-item img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s var(--ease); }
.gal-item:hover img { transform: scale(1.08); }
.gal-cap {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 10px 14px;
    background: linear-gradient(transparent, rgba(0,0,0,.6));
    color: var(--white); font-size: .85rem; font-weight: 700;
    transform: translateY(100%); transition: transform .3s var(--ease);
}
.gal-item:hover .gal-cap { transform: none; }

/* ═══════════════════════════════════════
   ABOUT SPLIT
═══════════════════════════════════════ */
.about { background: var(--off); display: grid; grid-template-columns: 1fr 1fr; overflow: hidden; }
.about-txt .sec-h2 em { color: var(--green); }
.about-img { position: relative; min-height: 500px; }
.about-img img { width: 100%; height: 100%; object-fit: cover; }
.about-badge {
    position: absolute; bottom: 2rem; right: 2rem;
    background: var(--orange);
    color: var(--white); text-align: center;
    padding: 14px 22px; border-radius: 14px;
    font-size: .85rem; font-weight: 700;
    box-shadow: 0 6px 20px rgba(0,0,0,.3);
}
.about-badge b { display: block; font-size: 2rem; font-weight: 900; line-height: 1; }
.about-txt {
    padding: 5rem 4rem;
    display: flex; flex-direction: column; justify-content: center;
    background: var(--off);
}
.about-txt .sec-tag  { align-self: flex-start; }
.about-txt .sec-h2   { text-align: right; margin-top: .8rem; }
.about-txt .sec-line { margin-right: 0; margin-left: auto; }
.chk { list-style: none; margin-top: 1.8rem; display: flex; flex-direction: column; gap: 0; }
.chk li {
    display: flex; align-items: flex-start; gap: 11px;
    padding: 10px 0; border-bottom: 1px solid rgba(0,0,0,.07);
    font-size: .9rem; color: var(--muted); line-height: 1.7;
}
.chk li:last-child { border: none; }
.chk-ico {
    width: 22px; height: 22px; flex-shrink: 0; margin-top: 1px;
    background: linear-gradient(135deg, var(--green), var(--green2));
    border-radius: 50%; color: var(--white); font-size: .62rem;
    display: flex; align-items: center; justify-content: center;
}

/* ═══════════════════════════════════════
   CTA
═══════════════════════════════════════ */
.cta {
    background: linear-gradient(135deg, var(--green) 0%, var(--green2) 45%, var(--brown) 100%);
    text-align: center; padding: 5.5rem 5%;
    position: relative; overflow: hidden;
}
.cta::before {
    content: ''; position: absolute;
    width: 600px; height: 600px; border-radius: 50%;
    background: rgba(255,255,255,.04);
    top: -200px; left: -150px;
}
.cta-h { font-size: clamp(1.9rem,4vw,2.8rem); font-weight: 900; color: var(--white); margin-bottom: 1rem; position: relative; z-index: 1; }
.cta-p { color: rgba(255,255,255,.75); font-size: .97rem; max-width: 480px; margin: 0 auto 2.2rem; line-height: 1.85; position: relative; z-index: 1; }
.cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; position: relative; z-index: 1; }
.btn-wh {
    padding: 13px 36px; background: var(--white); color: var(--green);
    font-family: 'Cairo', sans-serif; font-size: .95rem; font-weight: 800;
    border: none; border-radius: 50px; cursor: pointer;
    box-shadow: 0 5px 20px rgba(0,0,0,.2); transition: all .3s var(--ease);
}
.btn-wh:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,.28); }
.btn-wh-out {
    padding: 13px 36px; background: transparent; color: var(--white);
    font-family: 'Cairo', sans-serif; font-size: .95rem; font-weight: 700;
    border: 2px solid rgba(255,255,255,.45); border-radius: 50px; cursor: pointer;
    transition: all .3s var(--ease);
}
.btn-wh-out:hover { background: rgba(255,255,255,.14); border-color: var(--white); transform: translateY(-3px); }

/* ═══════════════════════════════════════
   FOOTER
═══════════════════════════════════════ */
footer {
    background: var(--brown2);
    color: rgba(255,255,255,.65);
    padding: 3.5rem 5% 1.5rem;
}
.ft-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 3rem; padding-bottom: 2.5rem; border-bottom: 1px solid rgba(255,255,255,.08); }
.ft-brand { display: flex; flex-direction: column; gap: 1rem; }
.ft-top   { display: flex; align-items: center; gap: 12px; }
.ft-ring  { width: 52px; height: 52px; background: var(--white); border-radius: 50%; padding: 5px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ft-ring img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
.ft-name  { color: var(--white); font-size: 1rem; font-weight: 800; line-height: 1.25; }
.ft-name small { display: block; font-size: .65rem; font-weight: 400; color: rgba(255,255,255,.45); }
.ft-desc  { font-size: .84rem; line-height: 1.78; }
.ft-col-h { color: var(--orange); font-size: .9rem; font-weight: 800; margin-bottom: 1.1rem; }
.ft-ul    { list-style: none; display: flex; flex-direction: column; gap: .5rem; }
.ft-ul a  { font-size: .83rem; color: rgba(255,255,255,.55); transition: color .2s; display: flex; align-items: center; gap: 6px; }
.ft-ul a::before { content: '›'; color: var(--orange); font-size: 1rem; }
.ft-ul a:hover { color: var(--orange); }
.ft-bot   { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem; padding-top: 1.5rem; }
.ft-copy  { font-size: .76rem; color: rgba(255,255,255,.35); }
.ft-copy b { color: var(--orange); }

/* scroll top */
#toTop {
    position: fixed; bottom: 2rem; left: 2rem; z-index: 500;
    width: 46px; height: 46px;
    background: var(--orange); color: var(--white);
    font-size: 1.1rem; border: none; border-radius: 50%; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(232,101,26,.5);
    opacity: 0; transform: translateY(16px);
    transition: opacity .3s, transform .3s;
    pointer-events: none;
}
#toTop.show { opacity: 1; transform: none; pointer-events: all; }
#toTop:hover { background: var(--orange2); transform: translateY(-2px); }

/* reveal */
.rv { opacity: 0; transform: translateY(28px); transition: opacity .6s ease, transform .6s ease; }
.rv.in { opacity: 1; transform: none; }

/* animations */
@keyframes popIn {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: none; }
}
@keyframes blink {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.3; transform:scale(1.5); }
}

/* responsive */
@media (max-width: 900px) {
    .nav-links { display: none; }
    .about     { grid-template-columns: 1fr; }
    .about-img { min-height: 280px; }
    .about-txt { padding: 3rem 1.8rem; }
    .gal-grid  { grid-template-columns: 1fr 1fr; grid-template-rows: 180px 180px; }
    .gal-item:first-child { grid-row: span 1; }
    .ft-grid   { grid-template-columns: 1fr; gap: 2rem; }

}
@media (max-width: 560px) {
    .gal-grid  { grid-template-columns: 1fr; grid-template-rows: auto; }
    .gal-item  { height: 200px; }
    .ft-bot    { flex-direction: column; text-align: center; }
}
    </style>
</head>
<body>

<!-- ══════════════ NAVBAR ══════════════ -->
<nav class="nav">
    <div class="nav-brand">
        <div class="nav-logo">
            <img src="/logo.jpg" alt="شعار حديقة حيوان طرابلس">
        </div>
        <div>
            <div class="nav-name">
                حديقة حيوان طرابلس
                <span>Tripoli Zoo Management</span>
            </div>
        </div>
    </div>

    <ul class="nav-links">
        <li><a href="#features">الخدمات</a></li>
        <li><a href="#gallery">معرض الصور</a></li>
        <li><a href="#about">عن الحديقة</a></li>
        <li><a href="#contact">تواصل</a></li>
    </ul>

    <button class="nav-cta" id="navLoginBtn" onclick="location.href='/login'">تسجيل الدخول</button>
</nav>

<!-- ══════════════ HERO ══════════════ -->
<section class="hero" id="home">
    <div class="slide on" id="sl0"><img src="/zoo_lion.png"     alt="الأسد"></div>
    <div class="slide"    id="sl1"><img src="/zoo_elephant.png" alt="الفيلة"></div>
    <div class="slide"    id="sl2"><img src="/zoo_birds.png"    alt="الطيور"></div>

    <div class="hero-body">

        <div class="hero-tag">
            <div class="hero-tag-dot"></div>
            نظام إدارة متكامل
        </div>

        <h1 class="hero-h1">
            حديقة حيوان <em>طرابلس</em>
        </h1>
        <p class="hero-en">TRIPOLI ZOO — MANAGEMENT SYSTEM</p>

        <p class="hero-p" id="hSub">
            اكتشف عالماً من الطبيعة والحياة البرية في قلب طرابلس.<br>
            نظام متكامل لإدارة الحديقة وضمان أفضل رعاية للحيوانات والزوار.
        </p>

        <div class="hero-btns">
            <button class="btn-or"    id="heroLoginBtn" onclick="location.href='/login'">🔑 دخول النظام</button>
            <button class="btn-ghost" id="heroExploreBtn" onclick="document.getElementById('features').scrollIntoView({behavior:'smooth'})">اكتشف المزيد ↓</button>
        </div>
    </div>

    <button class="arrow arrow-r" id="sPrev" aria-label="السابق">›</button>
    <button class="arrow arrow-l" id="sNext" aria-label="التالي">‹</button>

    <div class="dots" id="sDots">
        <button class="dot on" data-i="0"></button>
        <button class="dot"    data-i="1"></button>
        <button class="dot"    data-i="2"></button>
    </div>

    <div class="counter" id="sCounter"><b>01</b> / 03</div>
    <div class="prog"    id="sProg"></div>
</section>



<!-- ══════════════ FEATURES ══════════════ -->
<section class="sec feat-bg" id="features">
    <div class="sec-head rv">
        <div class="sec-tag">⚙️ نظام الإدارة</div>
        <h2 class="sec-h2">كل ما تحتاجه <em>في مكان واحد</em></h2>
        <div class="sec-line"></div>
        <p class="sec-p">منظومة متكاملة تغطي جميع احتياجات إدارة الحديقة من رعاية الحيوانات إلى إدارة الزوار.</p>
    </div>
    <div class="feat-grid">

        <div class="card rv">
            <span class="card-num">01</span>
            <div class="card-ico ico-or">🦒</div>
            <h3 class="card-h">إدارة الحيوانات</h3>
            <p class="card-p">تتبع شامل لصحة وتغذية وتاريخ كل حيوان، مع ملفات طبية كاملة ومحدّثة لحظياً.</p>
        </div>

        <div class="card rv">
            <span class="card-num">02</span>
            <div class="card-ico ico-gr">🎫</div>
            <h3 class="card-h">نظام التذاكر</h3>
            <p class="card-p">إصدار وإدارة تذاكر الدخول مع تتبع أعداد الزوار وتقارير الإيرادات اليومية.</p>
        </div>

        <div class="card rv">
            <span class="card-num">03</span>
            <div class="card-ico ico-br">👨‍⚕️</div>
            <h3 class="card-h">الرعاية البيطرية</h3>
            <p class="card-p">جدولة الفحوصات والتطعيمات وتسجيل السجلات الصحية لضمان صحة الحيوانات.</p>
        </div>

        <div class="card rv">
            <span class="card-num">04</span>
            <div class="card-ico ico-gr">🍖</div>
            <h3 class="card-h">إدارة التغذية</h3>
            <p class="card-p">خطط غذائية مخصصة لكل نوع مع إدارة المخزون والجداول الزمنية التلقائية.</p>
        </div>

        <div class="card rv">
            <span class="card-num">05</span>
            <div class="card-ico ico-or">👥</div>
            <h3 class="card-h">إدارة الموظفين</h3>
            <p class="card-p">تنظيم الجداول والمهام والأدوار الوظيفية لجميع كوادر الحديقة بكفاءة عالية.</p>
        </div>

    </div>
</section>

<!-- ══════════════ GALLERY ══════════════ -->
<section class="sec gal-bg" id="gallery">
    <div class="sec-head rv">
        <div class="sec-tag">📷 معرض الصور</div>
        <h2 class="sec-h2">اكتشف <em>عالمنا الرائع</em></h2>
        <div class="sec-line"></div>
    </div>
    <div class="gal-grid rv">
        <div class="gal-item"><img src="/zoo_lion.png"     alt="الأسد"><div class="gal-cap">🦁 الأسد الملكي</div></div>
        <div class="gal-item"><img src="/zoo_elephant.png" alt="الفيلة"><div class="gal-cap">🐘 عائلة الأفيال</div></div>
        <div class="gal-item"><img src="/zoo_birds.png"    alt="الطيور"><div class="gal-cap">🦜 الطيور الملونة</div></div>
        <div class="gal-item"><img src="/zoo_elephant.png" alt="طبيعة"><div class="gal-cap">🌿 البيئة الطبيعية</div></div>
        <div class="gal-item"><img src="/zoo_birds.png"    alt="استوائية"><div class="gal-cap">🌺 الطيور الاستوائية</div></div>
    </div>
</section>

<!-- ══════════════ ABOUT ══════════════ -->
<div class="about" id="about">
    <div class="about-img">
        <img src="/zoo_lion.png" alt="حديقة حيوان طرابلس">
        <div class="about-badge"><b>1994</b>تأسست</div>
    </div>
    <div class="about-txt rv">
        <div class="sec-tag">🏛️ عن الحديقة</div>
        <h2 class="sec-h2" style="margin-top:.8rem">
            أكثر من ٣٠ عاماً<br><em>من الشغف بالطبيعة</em>
        </h2>
        <div class="sec-line" style="margin-right:0;margin-left:auto"></div>
        <ul class="chk">
            <li><span class="chk-ico">✓</span><span>تأسست عام 1994 لتكون ملاذاً للحياة البرية والتعليم البيئي في ليبيا.</span></li>
            <li><span class="chk-ico">✓</span><span>أكثر من 200 نوع من الحيوانات البرية والنادرة من مختلف أنحاء العالم.</span></li>
            <li><span class="chk-ico">✓</span><span>نظام رقمي متطور يضمن أعلى معايير الرعاية والحفاظ على الحياة البرية.</span></li>
            <li><span class="chk-ico">✓</span><span>برامج تعليمية وترفيهية متخصصة للأطفال والعائلات طوال الأسبوع.</span></li>
            <li><span class="chk-ico">✓</span><span>فريق بيطري يعمل على مدار الساعة لضمان صحة وسعادة الحيوانات.</span></li>
        </ul>
        <div style="margin-top:2rem">
            <button class="btn-or" id="aboutLoginBtn" onclick="location.href='/login'">دخول النظام ←</button>
        </div>
    </div>
</div>

<!-- ══════════════ CTA ══════════════ -->
<section class="cta" id="contact">
    <h2 class="cta-h">🌿 انضم إلى نظام إدارة الحديقة</h2>
    <p class="cta-p">سجّل دخولك الآن وابدأ باستخدام النظام المتكامل لإدارة جميع جوانب الحديقة باحترافية.</p>
    <div class="cta-btns">
        <button class="btn-wh"     id="ctaLoginBtn"   onclick="location.href='/login'">🔑 تسجيل الدخول</button>
        <button class="btn-wh-out" id="ctaExploreBtn" onclick="document.getElementById('features').scrollIntoView({behavior:'smooth'})">🔍 الميزات</button>
    </div>
</section>

<!-- ══════════════ FOOTER ══════════════ -->
<footer id="footer">
    <div class="ft-grid">
        <div class="ft-brand">
            <div class="ft-top">
                <div class="ft-ring"><img src="/logo.jpg" alt="اللوقو"></div>
                <div class="ft-name">حديقة حيوان طرابلس <small>Tripoli Zoo Management System</small></div>
            </div>
            <p class="ft-desc">نظام إدارة متكامل مصمم خصيصاً لحديقة حيوان طرابلس، يجمع التقنية الحديثة برعاية الحياة البرية.</p>
        </div>
        <div>
            <h4 class="ft-col-h">روابط سريعة</h4>
            <ul class="ft-ul">
                <li><a href="#home">الصفحة الرئيسية</a></li>
                <li><a href="#features">الخدمات</a></li>
                <li><a href="#gallery">معرض الصور</a></li>
                <li><a href="#about">عن الحديقة</a></li>
            </ul>
        </div>
        <div>
            <h4 class="ft-col-h">النظام</h4>
            <ul class="ft-ul">
                <li><a href="/login">تسجيل الدخول</a></li>
                <li><a href="#features">إدارة الحيوانات</a></li>
                <li><a href="#features">نظام التذاكر</a></li>
                <li><a href="#features">التقارير</a></li>
            </ul>
        </div>
    </div>
    <div class="ft-bot">
        <p class="ft-copy">&copy; 2024 <b>حديقة حيوان طرابلس</b> — جميع الحقوق محفوظة</p>
        <p class="ft-copy">طُوِّر بـ 🤍 لخدمة الحياة البرية في ليبيا</p>
    </div>
</footer>

<button id="toTop" aria-label="للأعلى" onclick="scrollTo({top:0,behavior:'smooth'})">↑</button>

<script>
// ── SLIDER ───────────────────────────────────
const slides   = document.querySelectorAll('.slide');
const dots     = document.querySelectorAll('.dot');
const prog     = document.getElementById('sProg');
const counter  = document.getElementById('sCounter');
const DURATION = 5000;
let cur = 0;

const subs = [
    'اكتشف عالماً من الطبيعة والحياة البرية في قلب طرابلس.<br>نظام متكامل لإدارة الحديقة وضمان أفضل رعاية للحيوانات والزوار.',
    'شاهد مجموعتنا الرائعة من الأفيال في بيئة طبيعية آمنة تحاكي موطنهم الأصلي.',
    'استمتع بمشاهدة مئات الأنواع من الطيور الاستوائية والنادرة في أجواء ساحرة.'
];

function goTo(n) {
    slides[cur].classList.remove('on');
    dots[cur].classList.remove('on');
    cur = (n + slides.length) % slides.length;
    slides[cur].classList.add('on');
    dots[cur].classList.add('on');
    counter.innerHTML = `<b>0${cur+1}</b> / 03`;
    // fade subtitle
    const p = document.getElementById('hSub');
    p.style.opacity = 0;
    setTimeout(() => { p.innerHTML = subs[cur]; p.style.transition = 'opacity .5s'; p.style.opacity = 1; }, 200);
    // progress bar
    prog.style.transition = 'none'; prog.style.width = '0';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        prog.style.transition = `width ${DURATION}ms linear`;
        prog.style.width = '100%';
    }));
}

let timer = setInterval(() => goTo(cur + 1), DURATION);
function reset() { clearInterval(timer); timer = setInterval(() => goTo(cur + 1), DURATION); }

document.getElementById('sPrev').addEventListener('click', () => { goTo(cur - 1); reset(); });
document.getElementById('sNext').addEventListener('click', () => { goTo(cur + 1); reset(); });
dots.forEach(d => d.addEventListener('click', () => { goTo(+d.dataset.i); reset(); }));

// touch
let tx = 0;
document.querySelector('.hero').addEventListener('touchstart', e => tx = e.touches[0].clientX, {passive:true});
document.querySelector('.hero').addEventListener('touchend',   e => {
    const d = tx - e.changedTouches[0].clientX;
    if (Math.abs(d) > 50) { goTo(d > 0 ? cur+1 : cur-1); reset(); }
});

// start progress
goTo(0);

// ── SCROLL TOP ──────────────────────────────
window.addEventListener('scroll', () =>
    document.getElementById('toTop').classList.toggle('show', scrollY > 400)
);

// ── REVEAL ──────────────────────────────────
const obs = new IntersectionObserver(entries => {
    entries.forEach((e, i) => {
        if (e.isIntersecting) { setTimeout(() => e.target.classList.add('in'), i * 80); obs.unobserve(e.target); }
    });
}, { threshold: 0.1 });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
</body>
</html>
