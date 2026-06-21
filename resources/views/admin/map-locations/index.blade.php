@extends($__layout ?? 'admin.layout')
@section('title', 'إدارة الخريطة التفاعلية | Tripoli Zoo')
@section('page_title', 'إدارة الخريطة التفاعلية')

@php
    $categoryLabels = [
        'enclosure' => 'أقفاص وموائل الحيوانات',
        'service' => 'الخدمات والمرافق العامة',
        'dining' => 'المطاعم والمقاهي',
    ];
@endphp

@section('styles')
<style>
    .map-dashboard-grid {
        display: grid;
        grid-template-columns: 390px 1fr;
        gap: 1.5rem;
        align-items: stretch;
    }
    .panel-card, .map-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 20px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
        overflow: hidden;
    }
    .panel-head, .map-head {
        padding: 1.2rem 1.4rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .panel-head h3, .map-head h3 {
        margin: 0;
        color: #1e3a1e;
        font-size: 1.05rem;
        font-weight: 900;
    }
    .btn-add {
        padding: 10px 16px;
        border-radius: 12px;
        background: linear-gradient(135deg, #E8651A, #f97316);
        color: white;
        text-decoration: none;
        font-weight: 800;
        font-size: .86rem;
    }
    .panel-body { padding: 1rem; }
    .filters {
        display: grid;
        gap: .7rem;
        margin-bottom: 1rem;
    }
    .filter-input {
        width: 100%;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        padding: 11px 13px;
        font-family: 'Cairo', sans-serif;
        font-weight: 700;
        background: #f8fafc;
    }
    .locations-list {
        display: grid;
        gap: .75rem;
        max-height: 560px;
        overflow-y: auto;
        padding-left: 4px;
    }
    .location-card {
        border: 1.5px solid var(--border);
        border-right: 5px solid #94a3b8;
        border-radius: 15px;
        padding: .9rem;
        cursor: pointer;
        transition: .18s ease;
        background: white;
    }
    .location-card[data-category="enclosure"] { border-right-color: #10B981; }
    .location-card[data-category="service"] { border-right-color: #0EA5E9; }
    .location-card[data-category="dining"] { border-right-color: #F59E0B; }
    .location-card:hover, .location-card.active {
        border-color: #2d5a27;
        box-shadow: 0 10px 24px rgba(45, 90, 39, .10);
        transform: translateX(-3px);
    }
    .location-title-row {
        display: flex;
        justify-content: space-between;
        gap: .7rem;
        align-items: center;
    }
    .location-title {
        color: var(--text-main);
        font-size: .95rem;
        font-weight: 900;
    }
    .badge {
        border-radius: 999px;
        padding: 4px 10px;
        font-size: .72rem;
        font-weight: 900;
        white-space: nowrap;
        background: #f1f5f9;
        color: #475569;
    }
    .badge.on { background: #DCFCE7; color: #166534; }
    .badge.off { background: #fee2e2; color: #991b1b; }
    .meta {
        margin-top: .45rem;
        color: var(--text-muted);
        font-size: .78rem;
        font-weight: 700;
        line-height: 1.7;
    }
    .actions {
        display: flex;
        justify-content: flex-end;
        gap: .45rem;
        border-top: 1px solid #eef2f7;
        margin-top: .7rem;
        padding-top: .7rem;
    }
    .action-btn {
        border: 1.5px solid var(--border);
        background: white;
        color: var(--text-main);
        border-radius: 9px;
        padding: 6px 10px;
        font-family: 'Cairo', sans-serif;
        font-size: .78rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }
    .action-btn.danger { color: #b91c1c; }
    .map-body {
        position: relative;
        background: #edf4e9;
        min-height: 640px;
    }
    .map-image {
        display: block;
        width: 100%;
        height: 100%;
        min-height: 640px;
        object-fit: contain;
    }
    .map-pin {
        position: absolute;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 3px solid white;
        transform: translate(-50%, -50%);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .25);
        cursor: pointer;
        background: #0EA5E9;
    }
    .map-pin.enclosure { background: #10B981; }
    .map-pin.dining { background: #F59E0B; }
    .map-pin.inactive {
        background: #94a3b8;
        opacity: .7;
    }
    .empty-state {
        padding: 2rem 1rem;
        text-align: center;
        color: var(--text-muted);
        font-weight: 800;
    }
    @media (max-width: 980px) {
        .map-dashboard-grid { grid-template-columns: 1fr; }
        .map-body, .map-image { min-height: 420px; }
    }
</style>
@endsection

@section('content')
<div class="map-dashboard-grid">
    <section class="panel-card">
        <div class="panel-head">
            <h3>مواقع الخريطة</h3>
            <a href="{{ route('admin.map-locations.create') }}" class="btn-add">+ إضافة موقع</a>
        </div>
        <div class="panel-body">
            <div class="filters">
                <input type="text" id="mapSearch" class="filter-input" placeholder="ابحث باسم الموقع..." oninput="filterLocations()">
                <select id="mapFilter" class="filter-input" onchange="filterLocations()">
                    <option value="all">كل الفئات</option>
                    <option value="enclosure">أقفاص وموائل الحيوانات</option>
                    <option value="service">الخدمات والمرافق العامة</option>
                    <option value="dining">المطاعم والمقاهي</option>
                </select>
            </div>

            <div class="locations-list" id="locationsList">
                @forelse($locations as $location)
                    @php
                        $position = $location->mapPosition();
                        $xPct = $position ? round($position['x'] * 100, 1) : null;
                        $yPct = $position ? round($position['y'] * 100, 1) : null;
                    @endphp
                    <article
                        class="location-card"
                        id="location-card-{{ $location->id }}"
                        data-id="{{ $location->id }}"
                        data-name="{{ $location->name }}"
                        data-category="{{ $location->category }}"
                        onclick="selectLocation({{ $location->id }})"
                    >
                        <div class="location-title-row">
                            <span class="location-title">{{ $location->name }}</span>
                            <span class="badge {{ $location->is_active ? 'on' : 'off' }}">{{ $location->is_active ? 'ظاهر' : 'مخفي' }}</span>
                        </div>
                        <div class="meta">
                            {{ $categoryLabels[$location->category] ?? $location->category }}
                            @if($position)
                                <br>
                                الموضع: {{ $xPct }}% أفقي · {{ $yPct }}% عمودي
                            @else
                                <br>
                                <span style="color:#b45309;">يتطلب إعادة تحديد الموقع على الخريطة</span>
                            @endif
                        </div>
                        <div class="actions" onclick="event.stopPropagation()">
                            <form method="POST" action="{{ route('admin.map-locations.toggle', $location) }}" class="js-map-toggle-form" data-active="{{ $location->is_active ? '1' : '0' }}">
                                @csrf
                                @method('PATCH')
                                <button class="action-btn" type="submit">{{ $location->is_active ? 'إخفاء' : 'إظهار' }}</button>
                            </form>
                            <a class="action-btn" href="{{ route('admin.map-locations.edit', $location) }}">تعديل</a>
                            <form method="POST" action="{{ route('admin.map-locations.destroy', $location) }}" class="js-map-delete-form" data-confirm-title="تأكيد الحذف" data-confirm-danger="1">
                                @csrf
                                @method('DELETE')
                                <button class="action-btn danger" type="submit">حذف</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">لا توجد مواقع مسجلة بعد.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="map-card">
        <div class="map-head">
            <div>
                <h3>خريطة الحديقة</h3>
                <p style="margin:.25rem 0 0;color:var(--text-muted);font-weight:700;font-size:.82rem;">الدبابيس هنا هي نفس البيانات المعروضة في تطبيق الزائر والموقع.</p>
            </div>
        </div>
        <div class="map-body" id="mapBody">
            <img class="map-image" src="{{ asset('map.PNG') }}" alt="خريطة حديقة حيوان طرابلس">
            @foreach($locations as $location)
                @php $position = $location->mapPosition(); @endphp
                @if($position)
                <button
                    class="map-pin {{ $location->category }} {{ $location->is_active ? '' : 'inactive' }}"
                    id="map-pin-{{ $location->id }}"
                    type="button"
                    title="{{ $location->name }}"
                    data-x="{{ $position['x'] }}"
                    data-y="{{ $position['y'] }}"
                    style="display:none;"
                    onclick="selectLocation({{ $location->id }})"
                ></button>
                @endif
            @endforeach
        </div>
    </section>
</div>
@endsection

@section('scripts')
@include('partials.admin-map-picker-scripts')
@include('partials.admin-map-location-form-scripts')
<script>
    function selectLocation(id) {
        document.querySelectorAll('.location-card').forEach((item) => item.classList.remove('active'));
        document.querySelectorAll('.map-pin').forEach((pin) => pin.style.transform = 'translate(-50%, -50%) scale(1)');

        const card = document.getElementById(`location-card-${id}`);
        const pin = document.getElementById(`map-pin-${id}`);

        if (card) {
            card.classList.add('active');
            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        if (pin) {
            pin.style.transform = 'translate(-50%, -50%) scale(1.35)';
        }
    }

    function filterLocations() {
        const query = document.getElementById('mapSearch').value.trim().toLowerCase();
        const category = document.getElementById('mapFilter').value;

        document.querySelectorAll('.location-card').forEach((card) => {
            const id = card.dataset.id;
            const pin = document.getElementById(`map-pin-${id}`);
            const matchesName = card.dataset.name.toLowerCase().includes(query);
            const matchesCategory = category === 'all' || card.dataset.category === category;
            const visible = matchesName && matchesCategory;

            card.style.display = visible ? 'block' : 'none';
            if (pin) pin.style.display = visible ? 'block' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initAdminMapDisplay('mapBody', '.map-image');
        bindMapLocationForms();
    });
</script>
@endsection
