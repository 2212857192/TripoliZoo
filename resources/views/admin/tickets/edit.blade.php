@extends('admin.layout')
@section('title', 'تعديل سعر التذكرة | Tripoli Zoo')
@section('page_title', 'تعديل سعر التذكرة')

@php
// Static demo ticket data based on id
$tickets = [
    '1' => ['name' => 'تذكرة الكبار', 'price' => '10.00', 'target' => 'كبار (فوق 12 سنة)', 'benefits' => 'الدخول العام للحديقة', 'active' => true],
    '2' => ['name' => 'تذكرة الأطفال', 'price' => '5.00', 'target' => 'أطفال (3 - 12 سنة)', 'benefits' => 'الدخول العام للحديقة', 'active' => true],
    '3' => ['name' => 'تذكرة كبار الشخصيات VIP', 'price' => '25.00', 'target' => 'العائلات وVIP', 'benefits' => 'سيارة جولف + مرشد', 'active' => true],
    '4' => ['name' => 'تذكرة السياح الأجانب', 'price' => '50.00', 'target' => 'الزوار غير الليبيين', 'benefits' => 'مرشد سياحي خاص', 'active' => false],
];
$ticket = $tickets[$id] ?? $tickets['1'];
@endphp

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
    
    <a href="/admin/tickets" class="page-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        العودة إلى قائمة التذاكر
    </a>

    <!-- Header Hero -->
    <div class="page-hero">
        <h2>تعديل تعرفة سعر التذكرة</h2>
        <p>قم بتعديل وتحديث الأسعار المطبقة للبيع لفئة التذكرة المحددة.</p>
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
            <div class="form-row">
                <div class="form-group">
                    <label>فئة التذكرة</label>
                    <input type="text" id="name" class="form-input" value="{{ $ticket['name'] }}" disabled style="background:#F1F5F9;cursor:not-allowed;font-weight:700;">
                </div>
                <div class="form-group">
                    <label>السعر الجديد بالدينار الليبي (د.ل) <span style="color:#EF4444">*</span></label>
                    <input type="number" id="price" class="form-input" value="{{ $ticket['price'] }}" step="0.5">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>الفئة المستهدفة بالدخول</label>
                    <input type="text" id="target" class="form-input" value="{{ $ticket['target'] }}" disabled style="background:#F1F5F9;cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label>الخدمات والمزايا المشمولة</label>
                    <input type="text" id="benefits" class="form-input" value="{{ $ticket['benefits'] }}" disabled style="background:#F1F5F9;cursor:not-allowed;">
                </div>
            </div>

            <!-- Divider -->
            <div class="form-divider"></div>

            <!-- Activation Switch inside same card container -->
            <div class="toggle-row">
                <label>تفعيل فئة التذكرة للبيع فوراً في النظام</label>
                <label class="switch">
                    <input type="checkbox" id="active" {{ $ticket['active'] ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>

            <!-- Action buttons inside the same container -->
            <div class="actions-row">
                <a href="/admin/tickets" class="btn-cancel-premium">إلغاء وتراجع</a>
                <button class="btn-submit-premium" onclick="submitForm()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    تحديث السعر الحالي
                </button>
            </div>
        </div>
    </div>

</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function submitForm() {
        const price = document.getElementById('price').value.trim();

        if (!price) {
            showToast('⚠️ يرجى تحديد سعر التذكرة الجديد');
            return;
        }

        const btn = document.querySelector('.btn-submit-premium');
        btn.textContent = '⏳ جاري الحفظ...';
        btn.disabled = true;

        setTimeout(() => {
            showToast('✅ تم تحديث السعر بنجاح ونشره للبيع');
            btn.textContent = '✅ تم التعديل!';
            setTimeout(() => { window.location.href = '/admin/tickets'; }, 1000);
        }, 800);
    }
</script>
@endsection
