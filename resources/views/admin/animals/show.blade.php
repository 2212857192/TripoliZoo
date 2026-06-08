@extends($__layout ?? 'admin.layout')
@section('title', 'عرض المحتوى التعريفي | Tripoli Zoo')
@section('page_title', 'عرض المحتوى التعريفي')

@php
$animals = [
    '1' => ['name'=>'الأسد الإفريقي',  'sci'=>'Panthera leo',           'emoji'=>'🦁', 'code'=>'L-01',
             'desc'=>'الأسد الإفريقي من أكبر القطط البرية في العالم. يعيش في مجموعات تُعرف بـ (الفخر). يتميز الذكر بعُرفه الكثيف الذي يزداد قتامةً مع التقدم في السن. يصل وزنه إلى 190 كجم ويمكنه الجري بسرعة تصل إلى 80 كم/ساعة لمسافات قصيرة.',
             'img'=>'/zoo_lion.png', 'vis'=>true],
    '2' => ['name'=>'الفيل الآسيوي',   'sci'=>'Elephas maximus',         'emoji'=>'🐘', 'code'=>'E-04',
             'desc'=>'الفيل الآسيوي أصغر حجماً من الأفريقي، ويتميز بأذنين أصغر ورأس أكثر تحدباً. يُعدّ من أكثر الحيوانات ذكاءً في العالم، ويمتلك ذاكرة استثنائية.',
             'img'=>'/zoo_elephant.png','vis'=>true],
    '3' => ['name'=>'النمر البنغالي',  'sci'=>'Panthera tigris',         'emoji'=>'🐯', 'code'=>'T-02',
             'desc'=>'النمر البنغالي أكبر أنواع القطط وأقواها. يسبح جيداً ويجيد تسلق الأشجار. يُهدَّد بالانقراض بسبب الصيد الجائر وفقدان موطنه.',
             'img'=>'', 'vis'=>true],
];
$animal = $animals[$id ?? '1'] ?? $animals['1'];
@endphp

@section('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg,#1e3a1e 0%,#2d5a27 100%);
        --card-shadow: 0 10px 30px -10px rgba(0,0,0,.07);
    }

    .page-back {
        display:inline-flex; align-items:center; gap:8px;
        color:var(--text-muted); text-decoration:none;
        font-weight:700; font-size:.9rem; margin-bottom:1.6rem; transition:color .2s;
    }
    .page-back:hover { color:var(--orange); }

    /* ── Section card ── */
    .section-card {
        background:white; border:1px solid var(--border);
        border-radius:20px; overflow:hidden;
        box-shadow:var(--card-shadow); margin-bottom:1.4rem;
        transition:box-shadow .3s;
    }
    .section-card:hover { box-shadow:0 15px 35px -10px rgba(45,90,39,.08); }

    .section-head {
        padding:1.2rem 1.6rem; border-bottom:1px solid var(--border);
        display:flex; align-items:center; justify-content:space-between;
        gap:12px; background:linear-gradient(to left,rgba(45,90,39,.02),transparent);
    }

    .section-head-left { display:flex; align-items:center; gap:12px; }

    .section-icon {
        width:38px; height:38px; border-radius:10px;
        background:rgba(45,90,39,.08); color:#2d5a27;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }

    .section-head h3 { font-size:1rem; font-weight:900; color:#1e3a1e; margin:0 0 2px; }
    .section-head p  { font-size:.78rem; color:var(--text-muted); font-weight:600; margin:0; }

    .section-body { padding:1.6rem; }

    /* ── Animal hero ── */
    .animal-hero {
        display:flex; align-items:center; gap:1.8rem;
        padding:1.6rem;
    }

    .animal-hero-img {
        width:120px; height:120px; border-radius:20px; flex-shrink:0;
        overflow:hidden; background:linear-gradient(135deg,#FFF7ED,#FFEDD5);
        display:flex; align-items:center; justify-content:center;
        border:2px solid var(--border);
        box-shadow:0 8px 20px rgba(0,0,0,.08);
    }
    .animal-hero-img img  { width:100%; height:100%; object-fit:cover; }
    .animal-hero-img span { font-size:4rem; }

    .animal-hero-info h2 { font-size:1.5rem; font-weight:900; color:#1e3a1e; margin:0 0 4px; }
    .animal-hero-info p  { font-size:.9rem; color:var(--text-muted); font-weight:600; margin:0 0 12px; font-style:italic; }

    .meta-pills { display:flex; gap:8px; flex-wrap:wrap; }

    .meta-pill {
        padding:5px 12px; border-radius:50px;
        font-size:.78rem; font-weight:800;
        background:var(--bg-color); color:var(--text-muted);
        border:1px solid var(--border);
        display:flex; align-items:center; gap:5px;
    }

    /* ── Description ── */
    .desc-block {
        font-size:.95rem; line-height:1.85;
        color:var(--text-main); font-weight:600;
    }

    /* ── Vis badge ── */
    .vis-badge {
        padding:5px 12px; border-radius:50px;
        font-size:.78rem; font-weight:800;
    }
    .vis-badge.on  { background:#DCFCE7; color:#166534; }
    .vis-badge.off { background:#FEE2E2; color:#991B1B; }

    /* ── Bottom row ── */
    .bottom-row { display:flex; flex-direction:column; gap:1.4rem; }

    .bottom-card {
        background:white; border:1px solid var(--border);
        border-radius:20px; overflow:hidden; box-shadow:var(--card-shadow);
    }

    .bottom-card-head {
        padding:1rem 1.4rem; border-bottom:1px solid var(--border);
        background:#FAFBFC; display:flex; align-items:center; gap:8px;
    }
    .bottom-card-head h3 { font-size:.92rem; font-weight:800; color:var(--text-main); margin:0; }
    .bottom-card-body    { padding:1.2rem 1.4rem; }

    /* ── Image display ── */
    .img-display {
        border-radius:14px; overflow:hidden;
        border:1.5px solid var(--border);
        background:linear-gradient(135deg,#FFF7ED,#FFEDD5);
        display:flex; align-items:center; justify-content:center;
        min-height:160px;
    }
    .img-display img  { width:100%; max-height:220px; object-fit:cover; display:block; }
    .img-display span { font-size:5rem; }

    /* ── Action buttons ── */
    .actions-grid { display:flex; flex-direction:column; gap:10px; }

    .btn-action {
        width:100%; padding:13px; border-radius:12px;
        font-family:'Cairo',sans-serif; font-weight:800; font-size:.95rem;
        cursor:pointer; transition:all .2s;
        display:flex; align-items:center; justify-content:center; gap:8px;
        text-decoration:none; border:none;
    }

    .btn-action.edit {
        background:var(--primary-gradient); color:white;
        box-shadow:0 6px 18px rgba(30,58,30,.22);
    }
    .btn-action.edit:hover { transform:translateY(-2px); box-shadow:0 10px 24px rgba(30,58,30,.32); }

    .btn-action.qr {
        background:rgba(45,90,39,.07); color:#1e3a1e;
        border:1.5px solid rgba(45,90,39,.2);
    }
    .btn-action.qr:hover { background:rgba(45,90,39,.12); }

    .btn-action.back {
        background:var(--bg-color); color:var(--text-muted);
        border:1.5px solid var(--border);
    }
    .btn-action.back:hover { background:#E2E8F0; color:var(--text-main); }

    /* ── QR modal ── */
    .modal-overlay {
        position:fixed; inset:0; background:rgba(15,23,42,.55);
        backdrop-filter:blur(6px); z-index:1000;
        display:flex; align-items:center; justify-content:center;
        opacity:0; visibility:hidden; transition:all .25s;
    }
    .modal-overlay.show { opacity:1; visibility:visible; }

    .modal-box {
        background:white; width:100%; max-width:360px;
        border-radius:24px; box-shadow:0 25px 60px rgba(0,0,0,.18);
        overflow:hidden;
        transform:translateY(24px) scale(.97); transition:all .3s cubic-bezier(.4,0,.2,1);
    }
    .modal-overlay.show .modal-box { transform:translateY(0) scale(1); }

    .modal-head {
        padding:1.2rem 1.6rem; border-bottom:1px solid var(--border);
        display:flex; justify-content:space-between; align-items:center;
        background:#FAFBFC;
    }
    .modal-head h3 { font-size:1.05rem; font-weight:800; color:var(--text-main); margin:0; }

    .btn-close-x {
        width:30px; height:30px; background:var(--border); border:none;
        border-radius:8px; font-size:1.1rem; cursor:pointer;
        display:flex; align-items:center; justify-content:center;
        color:var(--text-muted); transition:all .2s;
    }
    .btn-close-x:hover { background:#E2E8F0; }

    .qr-body { padding:1.8rem; text-align:center; }
    .qr-body h4 { font-size:1rem; font-weight:800; color:var(--text-main); margin:0 0 4px; }
    .qr-body p  { font-size:.8rem; color:var(--text-muted); font-weight:600; margin:0 0 1.2rem; }

    #qrCanvas { border:1px solid var(--border); border-radius:12px; padding:10px; background:white; margin-bottom:1rem; }

    .qr-actions { display:flex; gap:10px; justify-content:center; }
    .btn-qr-dl {
        padding:9px 18px; background:var(--green); color:white; border:none;
        border-radius:8px; font-family:'Cairo',sans-serif; font-weight:700;
        cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all .2s;
    }
    .btn-qr-dl:hover { background:#1B5E20; }
    .btn-cancel {
        padding:9px 16px; background:var(--bg-color); color:var(--text-muted);
        border:1.5px solid var(--border); border-radius:8px;
        font-family:'Cairo',sans-serif; font-weight:700; cursor:pointer; transition:all .2s;
    }
    .btn-cancel:hover { background:#E2E8F0; }

    /* ── Toast ── */
    .toast {
        position:fixed; bottom:2rem; left:50%;
        transform:translateX(-50%) translateY(80px);
        background:#1E293B; color:white;
        padding:12px 24px; border-radius:50px;
        font-weight:700; font-size:.9rem; z-index:9999;
        transition:transform .4s cubic-bezier(.4,0,.2,1); white-space:nowrap;
    }
    .toast.show { transform:translateX(-50%) translateY(0); }
</style>
@endsection

@section('content')

<a href="/admin/animals" class="page-back">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    العودة إلى قائمة المحتوى التعريفي
</a>

{{-- ── 1. Animal identity ── --}}
<div class="section-card">
    <div class="section-head">
        <div class="section-head-left">
            <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            </div>
            <div>
                <h3>بيانات الحيوان</h3>
                <p>معلومات الهوية الأساسية للحيوان المسجّل في الحديقة</p>
            </div>
        </div>
        <span class="vis-badge {{ $animal['vis'] ? 'on' : 'off' }}">
            {{ $animal['vis'] ? '👁 ظاهر للزوار' : '🚫 مخفي' }}
        </span>
    </div>
    <div class="animal-hero">
        <div class="animal-hero-img">
            @if($animal['img'])
                <img src="{{ $animal['img'] }}" alt="{{ $animal['name'] }}"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span style="display:none">{{ $animal['emoji'] }}</span>
            @else
                <span>{{ $animal['emoji'] }}</span>
            @endif
        </div>
        <div class="animal-hero-info">
            <h2>{{ $animal['name'] }}</h2>
            <p>{{ $animal['sci'] }}</p>
            <div class="meta-pills">
                <span class="meta-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    الرمز: {{ $animal['code'] }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ── 2. Description ── --}}
<div class="section-card">
    <div class="section-head">
        <div class="section-head-left">
            <div class="section-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div>
                <h3>الوصف التعريفي</h3>
                <p>المحتوى الذي يظهر للزوار عند مسح رمز QR</p>
            </div>
        </div>
        <span style="font-size:.78rem;color:var(--text-muted);font-weight:700;">{{ mb_strlen($animal['desc']) }} حرف</span>
    </div>
    <div class="section-body">
        <p class="desc-block">{{ $animal['desc'] }}</p>
    </div>
</div>

{{-- ── Bottom: Image + Actions ── --}}
<div class="bottom-row">

    {{-- Image --}}
    <div class="bottom-card">
        <div class="bottom-card-head">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <h3>صورة الحيوان</h3>
        </div>
        <div class="bottom-card-body">
            <div class="img-display">
                @if($animal['img'])
                    <img src="{{ $animal['img'] }}" alt="{{ $animal['name'] }}"
                         onerror="this.parentElement.innerHTML='<span>{{ $animal['emoji'] }}</span>'">
                @else
                    <span>{{ $animal['emoji'] }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="bottom-card">
        <div class="bottom-card-head">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
            <h3>الإجراءات المتاحة</h3>
        </div>
        <div class="bottom-card-body">
            <div class="actions-grid">
                <a href="/admin/animals/{{ $id ?? 1 }}/edit" class="btn-action edit">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    تعديل المحتوى التعريفي
                </a>
                <button class="btn-action qr" onclick="openQR()">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    عرض رمز QR التعريفي
                </button>
                <a href="/admin/animals" class="btn-action back">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

</div>

{{-- QR Modal --}}
<div class="modal-overlay" id="qrModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>رمز QR التعريفي</h3>
            <button class="btn-close-x" onclick="closeQR()">×</button>
        </div>
        <div class="qr-body">
            <h4>{{ $animal['name'] }}</h4>
            <p>{{ $animal['sci'] }} — رمز: {{ $animal['code'] }}</p>
            <canvas id="qrCanvas" width="180" height="180"></canvas>
            <div class="qr-actions">
                <button class="btn-qr-dl" onclick="downloadQR()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    تحميل
                </button>
                <button class="btn-cancel" onclick="closeQR()">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2800);
    }

    function openQR() {
        QRCode.toCanvas(document.getElementById('qrCanvas'),
            JSON.stringify({ name:'{{ $animal['name'] }}', sci:'{{ $animal['sci'] }}', code:'{{ $animal['code'] }}', zoo:'حديقة حيوان طرابلس' }),
            { width:180, margin:1, color:{dark:'#1E293B',light:'#FFFFFF'} }
        );
        document.getElementById('qrModal').classList.add('show');
    }

    function closeQR() { document.getElementById('qrModal').classList.remove('show'); }

    function downloadQR() {
        const link = document.createElement('a');
        link.download = 'QR-{{ $animal['name'] }}.png';
        link.href = document.getElementById('qrCanvas').toDataURL('image/png');
        link.click();
        showToast('⬇️ تم تحميل رمز QR');
    }

    document.getElementById('qrModal').addEventListener('click', e => {
        if (e.target === document.getElementById('qrModal')) closeQR();
    });
</script>
@endsection
