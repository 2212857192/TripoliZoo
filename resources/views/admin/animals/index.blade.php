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

@php
    $readOnly = $readOnly ?? false;
    $stats = $stats ?? ['total' => 0, 'visible' => 0, 'hidden' => 0];
    $filters = $filters ?? ['q' => '', 'visibility' => '', 'group' => ''];
@endphp

<!-- Top Card -->
<div class="top-card">
    <div class="top-row">
        <div>
            <h2>المحتوى التعريفي للحيوانات</h2>
            <p>
                إجمالي <strong>{{ $stats['total'] }}</strong> محتوى —
                <span style="color:#166534;">{{ $stats['visible'] }} ظاهر</span> —
                <span style="color:#991B1B;">{{ $stats['hidden'] }} مخفي</span>
            </p>
        </div>
        @unless($readOnly)
        <a href="{{ route('admin.animals.create') }}" class="btn-add">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            إضافة محتوى جديد
        </a>
        @endunless
    </div>
    <form method="GET" action="{{ route('admin.animals.index') }}" class="filter-bar" id="animalsFilterForm">
        <div class="search-box">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="ابحث بالاسم، النوع، الرمز، أو الاسم العلمي...">
        </div>
        <select class="filter-select" name="visibility" onchange="this.form.submit()" style="padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:'Cairo',sans-serif;font-weight:700;">
            <option value="" @selected($filters['visibility'] === '')>كل الحالات</option>
            <option value="visible" @selected($filters['visibility'] === 'visible')>ظاهر للزوار</option>
            <option value="hidden" @selected($filters['visibility'] === 'hidden')>مخفي</option>
        </select>
        <select class="filter-select" name="group" onchange="this.form.submit()" style="padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:'Cairo',sans-serif;font-weight:700;">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'selected' => $filters['group']])
        </select>
    </form>
</div>

<!-- Animals Grid -->
<div class="animals-grid" id="animalsGrid">
    @foreach($profiles as $profile)
    @php
        $animal = $profile->animal;
        $displayName = $animal?->species ?? '—';
        $sci = $animal?->group ?? '';
        $img = $profile->imageUrl();
        $group = $animal?->group ?? '—';
        $mapLocation = $profile->mapLocations->first();
    @endphp
    <div class="animal-card" data-vis="{{ $profile->is_visible ? 'visible' : 'hidden' }}" data-name="{{ $displayName }}">
        <div class="animal-img-wrap">
            @if($img)
            <img src="{{ $img }}" alt="{{ $displayName }}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            @endif
            <div class="animal-img-placeholder" style="{{ $img ? 'display:none' : '' }}">🦁</div>
            <span class="vis-badge {{ $profile->is_visible ? 'visible' : 'hidden-app' }}">{{ $profile->is_visible ? 'ظاهر للزوار' : 'مخفي' }}</span>
        </div>
        <div class="animal-body">
            <h3 class="animal-name">{{ $displayName }}</h3>
            @if($sci)<p class="animal-sci">{{ $sci }}</p>@endif
            <p class="animal-desc-preview">{{ Str::limit($profile->description, 120) }}</p>
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                <span style="font-size:.72rem;font-weight:800;padding:3px 8px;border-radius:6px;background:#F8FAFC;border:1px solid #E2E8F0;color:#64748B;">{{ $group }}</span>
                @if($mapLocation)
                <span style="font-size:.72rem;font-weight:800;padding:3px 8px;border-radius:6px;background:#ECFDF5;border:1px solid #BBF7D0;color:#166534;">{{ $mapLocation->name }}</span>
                @endif
            </div>
            <div class="animal-actions">
                <a href="{{ route('admin.animals.show', $profile) }}" class="btn-act view-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    عرض
                </a>
                @unless($readOnly)
                <a href="{{ route('admin.animals.edit', $profile) }}" class="btn-act edit-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    تعديل
                </a>
                <form method="POST" action="{{ route('admin.animals.visibility', $profile) }}" class="js-animal-visibility-form" style="display:inline;" data-visible="{{ $profile->is_visible ? '1' : '0' }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-act vis-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        {{ $profile->is_visible ? 'إخفاء' : 'إظهار' }}
                    </button>
                </form>
                @endunless
                @php
                    $qrButtonData = [
                        'name' => $displayName,
                        'subtitle' => $sci,
                        'code' => $profile->display_code ?? '',
                        'group' => $group !== '—' ? $group : '',
                        'image' => $img,
                        'scanUrl' => $profile->visitorQrUrl(),
                        'publicUrl' => config('app.visitor_public_url') ?: '',
                        'qrImageUrl' => route('admin.animals.qr', $profile, absolute: false),
                        'payload' => $profile->qrPayload(),
                    ];
                @endphp
                <button
                    type="button"
                    class="btn-act qr-btn js-animal-qr-trigger"
                    data-qr='@json($qrButtonData)'
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    QR
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="empty-state" id="emptyState" @if(($profiles ?? collect())->isNotEmpty()) style="display:none;" @endif>
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21.5 12H16c-.7 2-2 3-4 3s-3.3-1-4-3H2.5"/></svg>
    <h3>لا توجد نتائج</h3>
    <p>جرب تعديل معايير البحث</p>
</div>

@include('partials.admin-animal-qr-modal')

@endsection

@section('scripts')
@include('partials.admin-animal-qr-scripts')
<script>
    bindAdminConfirmForms('.js-animal-visibility-form', (form) => {
        return form.dataset.visible === '1'
            ? 'هل أنت متأكد من إخفاء هذا المحتوى عن الزوار؟'
            : 'هل أنت متأكد من إظهار هذا المحتوى للزوار؟';
    });
</script>
@endsection
