@extends($__layout ?? 'admin.layout')
@section('title', 'إضافة موقع جديد على الخريطة | Tripoli Zoo')
@section('page_title', 'إدارة الخريطة التفاعلية')

@section('styles')
<style>
    .page-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 800;
        margin-bottom: 1.2rem;
    }
    .form-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 20px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
        overflow: hidden;
    }
    .form-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
    }
    .form-head h3 {
        margin: 0;
        color: #1e3a1e;
        font-size: 1.08rem;
        font-weight: 900;
    }
    .form-body {
        padding: 1.5rem;
        display: grid;
        gap: 1.2rem;
    }
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 7px;
        color: #1e3a1e;
        font-size: .86rem;
        font-weight: 900;
    }
    .form-input {
        width: 100%;
        border: 1.5px solid var(--border);
        border-radius: 11px;
        padding: 11px 13px;
        font-family: 'Cairo', sans-serif;
        font-weight: 700;
        background: #fff;
    }
    textarea.form-input {
        min-height: 96px;
        resize: vertical;
    }
    .toggle-row {
        display: flex;
        align-items: center;
        gap: .65rem;
        font-weight: 800;
        color: var(--text-main);
    }
    .map-picker {
        position: relative;
        overflow: hidden;
        border: 1.5px solid var(--border);
        border-radius: 16px;
        background: #edf4e9;
        cursor: crosshair;
        min-height: 420px;
    }
    .map-picker img {
        display: block;
        width: 100%;
        max-height: 620px;
        object-fit: contain;
    }
    .picker-pin {
        position: absolute;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #E8651A;
        border: 3px solid white;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .25);
        transform: translate(-50%, -50%);
        display: none;
        pointer-events: none;
        z-index: 2;
    }
    .actions {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
        padding-top: .5rem;
    }
    .btn-primary, .btn-secondary {
        border: 0;
        border-radius: 12px;
        padding: 12px 22px;
        font-family: 'Cairo', sans-serif;
        font-weight: 900;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-primary {
        background: linear-gradient(135deg, #E8651A, #f97316);
        color: white;
    }
    .btn-secondary {
        background: #f1f5f9;
        color: var(--text-muted);
    }
    @media (max-width: 780px) {
        .form-row { grid-template-columns: 1fr; }
        .actions { flex-direction: column; }
    }
</style>
@endsection

@section('content')
<a href="{{ route('admin.map-locations.index') }}" class="page-back">العودة لخريطة الحديقة</a>

<form method="POST" action="{{ route('admin.map-locations.store') }}" class="form-card" id="mapLocationForm">
    @csrf
    <div class="form-head">
        <h3>إضافة موقع جديد على خريطة الزائر</h3>
    </div>
    <div class="form-body">
        <div class="form-row">
            <div class="form-group">
                <label>اسم الموقع</label>
                <input class="form-input" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label>فئة الموقع</label>
                <select class="form-input" name="category" id="category">
                    <option value="enclosure" @selected(old('category') === 'enclosure')>أقفاص وموائل الحيوانات</option>
                    <option value="service" @selected(old('category') === 'service')>الخدمات والمرافق العامة</option>
                    <option value="dining" @selected(old('category') === 'dining')>المطاعم والمقاهي</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>حالة الظهور</label>
            <label class="toggle-row">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', '1') == '1')>
                ظاهر في تطبيق وموقع الزائر
            </label>
        </div>

        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" required>
        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" required>

        <div class="form-group">
            <label>اضغط على الخريطة لتحديد موضع الدبوس — نفس الموضع يظهر في موقع وتطبيق الزائر</label>
            <div class="map-picker" id="mapPicker">
                <img src="{{ asset('map.PNG') }}" alt="خريطة حديقة حيوان طرابلس">
                <span class="picker-pin" id="pickerPin"></span>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('admin.map-locations.index') }}" class="btn-secondary">إلغاء</a>
            <button class="btn-primary" type="submit">حفظ الموقع</button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
@include('partials.admin-map-picker-scripts')
@include('partials.admin-map-location-form-scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        initAdminMapPicker(
            'mapPicker',
            'latitude',
            'longitude',
            'pickerPin',
            @json(old('longitude') !== null ? (float) old('longitude') : null),
            @json(old('latitude') !== null ? (float) old('latitude') : null)
        );
    });
</script>
@endsection
