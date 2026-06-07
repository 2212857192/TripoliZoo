@extends('care.layout')
@section('title', 'تفاصيل حالة صحية | الرعاية والتغذية')
@section('page_title', 'تفاصيل حالة صحية')

@section('styles')
<style>
    /* ═══ PAGE HEADER ═══ */
    .page-header-box {
        background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 40%, #43A047 100%);
        border-radius: 20px;
        padding: 1.8rem 2.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(46, 125, 50, 0.35);
    }
    .page-header-box::before {
        content: '';
        position: absolute;
        top: -80px;
        left: -80px;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .header-left { position: relative; z-index: 2; }
    .header-left h2 { font-size: 1.45rem; font-weight: 900; color: #fff; margin-bottom: 5px; }
    
    .header-right { display: flex; align-items: center; gap: 1rem; position: relative; z-index: 2; }
    .case-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 800;
        color: #fff;
    }
    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px;
        background: rgba(255,255,255,0.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 12px;
        font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800;
        cursor: pointer; transition: all 0.25s;
        text-decoration: none;
    }
    .btn-back:hover { background: rgba(255,255,255,0.25); }

    /* ═══ DETAILS CARD ═══ */
    .summary-card {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    .summary-header {
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-bottom: 1px solid #e8edf5;
        display: flex;
        align-items: center;
        gap: 1.2rem;
    }
    .animal-avatar {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
        border: 2px solid #2E7D32;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }
    .animal-info h3 { font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-bottom: 4px; }
    .animal-info p { font-size: 0.8rem; color: #64748b; font-weight: 600; }
    .summary-body { padding: 2rem; }
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem 1rem;
    }
    .info-item { padding: 0.8rem 0; }
    .info-item-label { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; }
    .info-item-value { font-size: 0.9rem; font-weight: 800; color: #0f172a; }
    .full-width { grid-column: span 3; }

    .content-box {
        font-size: 0.9rem;
        font-weight: 600;
        color: #334155;
        line-height: 1.7;
        background: #f8fafc;
        padding: 1.2rem 1.4rem;
        border-radius: 10px;
        border-right: 3px solid #2E7D32;
        margin-top: 5px;
    }

    /* ═══ ACTION BUTTONS ═══ */
    .actions-bar {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }
    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid;
        text-decoration: none;
    }
    .btn-review {
        background: #f0fdf4;
        color: #15803d;
        border-color: #bbf7d0;
    }
    .btn-review:hover { background: #dcfce7; }
    
    .btn-refer {
        background: #fff1f2;
        color: #e11d48;
        border-color: #fecdd3;
    }
    .btn-refer:hover { background: #ffe4e6; }

    .btn-view-referral {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
    .btn-view-referral:hover { background: #dbeafe; }

</style>
@endsection

@section('content')

@php
    // Dummy logic to mock the different states requested
    $id = request()->route('id') ?? 1;
    
    $status = 'جديدة'; // Default
    $followUp = 'تحتاج إحالة';
    
    if ($id == 2) {
        $followUp = 'لا تحتاج إحالة';
    } elseif ($id == 3) {
        $status = 'تمت المراجعة';
        $followUp = 'لا تحتاج إحالة';
    } elseif ($id == 4) {
        $status = 'محالة للعلاج';
        $followUp = 'تحتاج إحالة';
    }
@endphp

{{-- ═══ PAGE HEADER ═══ --}}
<div class="page-header-box">
    <div class="header-left">
        <h2>تفاصيل حالة صحية</h2>
    </div>
    <div class="header-right">
        {{-- Badges --}}
        <div class="case-badge" style="background: rgba(255,255,255,0.1);">
            <span style="width:8px;height:8px;border-radius:50%;background: {{ $followUp == 'تحتاج إحالة' ? '#ef4444' : '#94a3b8' }};"></span>
            {{ $followUp }}
        </div>
        <div class="case-badge">
            <span style="width:8px;height:8px;border-radius:50%;background: {{ $status == 'جديدة' ? '#3b82f6' : ($status == 'تمت المراجعة' ? '#22c55e' : '#d97706') }};"></span>
            {{ $status }}
        </div>
        <a href="/care/health" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            رجوع
        </a>
    </div>
</div>

{{-- ═══ DETAILS CARD ═══ --}}
<div class="summary-card">
    <div class="summary-header">
        <div class="animal-avatar">🦁</div>
        <div class="animal-info">
            <h3>{{ $id == 3 ? 'نعامة إفريقية' : ($id == 4 ? 'غزال الريم' : 'أسد إفريقي') }}</h3>
            <p>HC-2026-00{{ $id }} — #ANL-0041-2026</p>
        </div>
    </div>
    <div class="summary-body">
        <div class="summary-grid">
            <div class="info-item">
                <div class="info-item-label">رقم الحالة</div>
                <div class="info-item-value" style="font-family:'Courier New',monospace; color:#334155;">HC-2026-00{{ $id }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">الرقم الرسمي للحيوان</div>
                <div class="info-item-value" style="font-family:'Courier New',monospace; color:#64748b;">#ANL-0041-2026</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">نوع الحيوان</div>
                <div class="info-item-value">{{ $id == 3 ? 'نعامة إفريقية' : ($id == 4 ? 'غزال الريم' : 'أسد إفريقي') }}</div>
            </div>
            
            <div class="info-item">
                <div class="info-item-label">الجنس</div>
                <div class="info-item-value">{{ $id == 3 ? 'أنثى' : 'ذكر' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">العمر</div>
                <div class="info-item-value">6 سنوات</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">المجموعة</div>
                <div class="info-item-value">{{ $id == 3 ? 'الطيور' : ($id == 4 ? 'العواشب' : 'السباع') }}</div>
            </div>
            
            <div class="info-item">
                <div class="info-item-label">تاريخ الحالة</div>
                <div class="info-item-value">2026-06-07</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">المشرف</div>
                <div class="info-item-value">خالد منصور</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">نوع المتابعة</div>
                <div class="info-item-value" style="color: {{ $followUp == 'تحتاج إحالة' ? '#e11d48' : '#475569' }};">{{ $followUp }}</div>
            </div>

            <div class="info-item full-width">
                <div class="info-item-label">وصف الحالة الصحية</div>
                <div class="content-box">
                    الحيوان يرفض تناول الطعام لليوم الثاني على التوالي، مع وجود جرح عميق بالقدم الأمامية اليسرى يعيقه عن الحركة الطبيعية.
                </div>
            </div>
            
            <div class="info-item full-width">
                <div class="info-item-label">الملاحظات المسجلة عن الحيوان</div>
                <div class="content-box" style="border-right-color: #64748B; background: #fff;">
                    تم تنظيف الجرح مبدئياً باستخدام المعقمات الأساسية من قبل الممرض المناوب، ولكن يتطلب تدخلاً جراحياً وتخييطاً.
                </div>
            </div>

            <div class="info-item full-width">
                <div class="info-item-label">المرفقات</div>
                <div class="info-item-value">
                    <div style="display:flex; gap:10px; margin-top:5px;">
                        <div style="width:80px; height:80px; background:#e2e8f0; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ ACTION BUTTONS ═══ --}}
@if($status == 'جديدة')
    <div class="actions-bar">
        <button class="btn-action btn-review" onclick="markAsReviewed()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            تحديد كمراجعة
        </button>
        <button class="btn-action btn-refer" onclick="referToTreatment()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13"></path><path d="M22 2L15 22L11 13L2 9L22 2Z"></path></svg>
            إحالة حالة صحية للعلاج
        </button>
    </div>
@elseif($status == 'محالة للعلاج')
    <div class="actions-bar">
        <a href="/care/referrals/treatment/1" class="btn-action btn-view-referral">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            عرض إحالة العلاج
        </a>
    </div>
@else
    {{-- تمت المراجعة - لا تظهر أزرار الإجراءات - القراءة فقط --}}
@endif

@endsection

@section('scripts')
<script>
    function markAsReviewed() {
        if(confirm("هل أنت متأكد من مراجعة الحالة وإنهاء الإجراء دون إحالة؟")) {
            alert("تم تغيير حالة الحالة إلى 'تمت المراجعة'. لم يتم إنشاء إحالة علاج.");
            window.location.href = "/care/health";
        }
    }

    function referToTreatment() {
        if(confirm("هل أنت متأكد من إحالة هذه الحالة للمستشفى البيطري للعلاج؟")) {
            alert("تم تحويل الحالة إلى 'محالة للعلاج' وتم إرسال الإحالة إلى قسم المستشفى البيطري.");
            window.location.href = "/care/health";
        }
    }
</script>
@endsection
