@extends($__layout ?? 'admin.layout')
@section('title', 'عرض المحتوى التعريفي | Tripoli Zoo')
@section('page_title', 'عرض المحتوى التعريفي')

@php
    $animal = $profile->animal;
    $animalName = $animal?->name ?: $animal?->species ?? '—';
    $sci = $profile->visitorSubtitle();
    $code = $profile->display_code ?? $animal?->code ?? '—';
    $img = $profile->imageUrl();
    $mapLocation = $mapLocation ?? null;
    $qrPayload = $qrPayload ?? $profile->qrPayload();
    $visitorUrl = $visitorUrl ?? route('visitor.animal', $profile);
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
    .img-display img  { width:100%; max-height:320px; height:auto; object-fit:contain; display:block; margin:0 auto; }
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

@if(session('success'))
<div class="notice-blue" style="margin-bottom:1rem;padding:12px 16px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;color:#1D4ED8;font-weight:700;">{{ session('success') }}</div>
@endif

<a href="{{ route('admin.animals.index') }}" class="page-back">
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
        <span class="vis-badge {{ $profile->is_visible ? 'on' : 'off' }}">
            {{ $profile->is_visible ? '👁 ظاهر للزوار' : '🚫 مخفي' }}
        </span>
    </div>
    <div class="animal-hero">
        <div class="animal-hero-img">
            @if($img)
                <img src="{{ $img }}" alt="{{ $animalName }}"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span style="display:none">🦁</span>
            @else
                <span>🦁</span>
            @endif
        </div>
        <div class="animal-hero-info">
            <h2>{{ $animalName }}</h2>
            @if($sci)<p>{{ $sci }}</p>@endif
            <div class="meta-pills">
                <span class="meta-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    الرمز: {{ $code }}
                </span>
                @if($animal?->group)
                <span class="meta-pill">{{ $animal->group }}</span>
                @endif
                @if($animal?->gender)
                <span class="meta-pill">{{ $animal->gender }}</span>
                @endif
                @if($animal?->formattedAge() && $animal->formattedAge() !== '—')
                <span class="meta-pill">{{ $animal->formattedAge() }}</span>
                @endif
                @if($mapLocation)
                <span class="meta-pill">📍 {{ $mapLocation->name }}</span>
                @endif
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
        <span style="font-size:.78rem;color:var(--text-muted);font-weight:700;">{{ mb_strlen($profile->description) }} حرف</span>
    </div>
    <div class="section-body">
        <p class="desc-block">{{ $profile->description }}</p>
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
                @if($img)
                    <img src="{{ $img }}" alt="{{ $animalName }}"
                         onerror="this.parentElement.innerHTML='<span>🦁</span>'">
                @else
                    <span>🦁</span>
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
                <a href="{{ route('admin.animals.edit', $profile) }}" class="btn-action edit">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    تعديل المحتوى التعريفي
                </a>
                <form method="POST" action="{{ route('admin.animals.visibility', $profile) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-action qr" style="width:100%;">
                        {{ $profile->is_visible ? 'إخفاء عن تطبيق الزائر' : 'إظهار في تطبيق الزائر' }}
                    </button>
                </form>
                @if($profile->is_visible)
                <a href="{{ $visitorUrl }}" target="_blank" rel="noopener" class="btn-action qr">
                    معاينة صفحة الزائر
                </a>
                @endif
                @php
                    $qrButtonData = [
                        'name' => $animalName,
                        'subtitle' => $sci,
                        'code' => $code,
                        'group' => $animal?->group,
                        'image' => $img,
                        'scanUrl' => $profile->visitorQrUrl(),
                        'publicUrl' => config('app.visitor_public_url') ?: '',
                        'qrImageUrl' => route('admin.animals.qr', $profile, absolute: false),
                        'payload' => $qrPayload,
                    ];
                @endphp
                <button
                    class="btn-action qr js-animal-qr-trigger"
                    type="button"
                    data-qr='@json($qrButtonData)'
                >
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    عرض رمز QR التعريفي
                </button>
                <a href="{{ route('admin.animals.index') }}" class="btn-action back">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

</div>

@include('partials.admin-animal-qr-modal')

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
@include('partials.admin-animal-qr-scripts')
@endsection
