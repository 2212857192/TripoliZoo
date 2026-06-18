@extends($__layout ?? 'admin.layout')
@section('title', 'تعديل فئة التذكرة | Tripoli Zoo')
@section('page_title', 'تعديل فئة التذكرة')

@section('styles')
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



    /* Page Hero header */
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
    
    <a href="{{ route('admin.tickets.index') }}" class="page-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        العودة إلى قائمة التذاكر
    </a>

    <!-- Header Hero -->
    <div class="page-hero">
        <h2>تعديل فئة التذكرة</h2>
        <p>حدّث السعر والحالة والبيانات المعروضة في تطبيق الزوار.</p>
    </div>

    <!-- Main Container -->
    <div class="premium-card">
        <div class="card-accent-header">
            <div class="icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
            </div>
            <h3>تحديث قيمة التذكرة</h3>
        </div>
        
        <div class="premium-card-body">
            <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group">
                        <label>اسم فئة التذكرة <span style="color:#EF4444">*</span></label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $ticket->name) }}" required>
                        @error('name')<div style="color:#EF4444;font-size:0.82rem;margin-top:6px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>السعر بالدينار الليبي (د.ل) <span style="color:#EF4444">*</span></label>
                        <input type="number" name="price" class="form-input" value="{{ old('price', $ticket->price) }}" step="0.5" min="0" required>
                        @error('price')<div style="color:#EF4444;font-size:0.82rem;margin-top:6px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>الفئة المستهدفة بالدخول</label>
                        <input type="text" name="target_description" class="form-input" value="{{ old('target_description', $ticket->target_description) }}">
                    </div>
                    <div class="form-group">
                        <label>نوع الزائر <span style="color:#EF4444">*</span></label>
                        <select name="visitor_nationality" class="form-input" required>
                            <option value="مواطن" @selected(old('visitor_nationality', $ticket->visitor_nationality) === 'مواطن')>مواطن</option>
                            <option value="أجنبي" @selected(old('visitor_nationality', $ticket->visitor_nationality) === 'أجنبي')>أجنبي</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>العمر <span style="color:#EF4444">*</span></label>
                        <select name="visitor_age_group" class="form-input" required>
                            <option value="بالغ" @selected(old('visitor_age_group', $ticket->visitor_age_group) === 'بالغ')>بالغ</option>
                            <option value="طفل" @selected(old('visitor_age_group', $ticket->visitor_age_group) === 'طفل')>طفل</option>
                            <option value="طالب" @selected(old('visitor_age_group', $ticket->visitor_age_group) === 'طالب')>طالب</option>
                        </select>
                    </div>
                </div>

                <div class="form-divider"></div>

                <div class="toggle-row">
                    <label>تفعيل فئة التذكرة للبيع في تطبيق الزوار</label>
                    <label class="switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $ticket->is_active))>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="actions-row">
                    <a href="{{ route('admin.tickets.index') }}" class="btn-cancel-premium">إلغاء وتراجع</a>
                    <button type="submit" class="btn-submit-premium">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
