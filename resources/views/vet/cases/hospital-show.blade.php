@extends($__layout ?? 'vet.layout')
@section('title', 'تفاصيل حالة المستشفى | Tripoli Zoo')
@section('page_title', 'تفاصيل حالة المستشفى')

@section('styles')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 700; color: #64748b; }
    .breadcrumb a { color: #2E7D32; text-decoration: none; transition: color 0.2s; display: flex; align-items: center; gap: 4px; }
    .breadcrumb a:hover { color: #1b5e20; }

    .header-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
    .header-info h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; display: flex; align-items: center; gap: 12px; }
    
    /* ═══ BADGES ═══ */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    
    .status-ready { background: #f0fdfa; color: #0f766e; border: 1px solid #ccfbf1; }
    .status-ready .dot { background: #14b8a6; }
    .status-watch { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    .status-watch .dot { background: #f59e0b; }
    .status-critical { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-critical .dot { background: #ef4444; }

    .btn-export { padding: 10px 20px; background: #fff; color: #16a34a; border: 1.5px solid #bbf7d0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(22,163,74,0.05); }
    .btn-export:hover { background: #f0fdf4; transform: translateY(-1px); }

    /* ── TABS ── */
    .tabs-container { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; }
    .tabs-header { display: flex; background: #FAFBFC; border-bottom: 1px solid #e2e8f0; padding: 0 1rem; }
    .tab-btn { padding: 16px 24px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.95rem; font-weight: 800; color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .tab-btn:hover { color: #0f172a; }
    .tab-btn.active { color: #1a4a2e; border-bottom-color: #1a4a2e; background: #fff; }

    .tab-content { padding: 2rem; display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
    .info-cell { background: #fff; padding: 16px 20px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.8rem; color: #64748b; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .info-cell-value { font-size: 1rem; color: #0f172a; font-weight: 800; }
    
    .content-box { background: #f8fafc; padding: 16px 20px; border-radius: 10px; font-size: 0.95rem; color: #334155; font-weight: 600; line-height: 1.7; border: 1px solid #e2e8f0; }
    .section-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }

    .id-tag { font-family: 'Courier New', monospace; font-size: 0.85rem; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    /* Timeline */
    .timeline { position:relative; padding-right:1rem; }
    .timeline::before { content:''; position:absolute; right:3px; top:0; bottom:0; width:2px; background:#e2e8f0; }
    .entry-card { position:relative; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:1.2rem; margin-bottom:1.5rem; box-shadow:0 2px 4px rgba(0,0,0,0.02); }
    .entry-card::before { content:''; position:absolute; right:-21px; top:20px; width:12px; height:12px; border-radius:50%; background:#fff; border:3px solid #3b82f6; z-index:1; }
    .entry-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:10px; border-bottom:1px solid #f1f5f9; }
    .entry-doctor { font-size:0.95rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:6px; }
    .entry-date { font-size:0.8rem; color:#64748b; font-weight:600; }
    
    .entry-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
    .entry-box { background:#f8fafc; padding:12px; border-radius:10px; border:1px solid #f1f5f9; }
    .entry-box.full { grid-column:span 2; }
    .box-title { font-size:0.75rem; color:#64748b; font-weight:800; margin-bottom:6px; display:block; }
    .box-text { font-size:0.85rem; color:#1e293b; font-weight:700; line-height:1.5; }
    
    .entry-footer { display:flex; justify-content:space-between; align-items:center; margin-top:1rem; padding-top:10px; border-top:1px dashed #e2e8f0; }
    .status-badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:6px; font-size:0.75rem; font-weight:800; }
    .status-better { background:#F0FDF4; color:#16A34A; }
    .status-worse { background:#FEF2F2; color:#DC2626; }
    .status-stable { background:#EFF6FF; color:#2563EB; }
</style>
@endsection

@section('content')

<div class="breadcrumb">
    <a href="/vet/cases/hospital">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        الحالات داخل المستشفى
    </a>
    <span>/</span>
    <span style="color:#0f172a;">تفاصيل الحالة HC-2025-001</span>
</div>

<div class="header-card">
    <div class="header-info">
        <h2>
            تفاصيل حالة المستشفى
            <span class="badge status-ready"><span class="dot"></span>جاهز للخروج</span>
        </h2>
        <div style="font-size:0.9rem; color:#64748b; font-weight:700; margin-top:8px;">
            رقم الحالة: <span class="id-tag">HC-2025-001</span>
        </div>
    </div>
    <div>
        <button class="btn-export" onclick="alert('جاري تصدير التقرير الطبي بتنسيق PDF...')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            تصدير تقرير طبي
        </button>
    </div>
</div>

<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab(1, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            ملخص الحالة
        </button>
        <button class="tab-btn" onclick="switchTab(2, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            بيانات الحيوان
        </button>
        <button class="tab-btn" onclick="switchTab(3, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            المتابعة الطبية
        </button>
    </div>

    {{-- TAB 1: Case Summary --}}
    <div class="tab-content active" id="tab-1">
        <h3 class="section-title">المعلومات الأساسية للحالة</h3>
        <div class="info-grid" style="margin-bottom:1.5rem;">
            <div class="info-cell">
                <div class="info-cell-label">رقم الحالة</div>
                <div class="info-cell-value id-tag">HC-2025-001</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">الحالة الحالية</div>
                <div class="info-cell-value"><span class="badge status-ready"><span class="dot"></span>جاهز للخروج</span></div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">تاريخ دخول المستشفى</div>
                <div class="info-cell-value">2026-06-05</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">الطبيب المسؤول</div>
                <div class="info-cell-value">د. خالد العربي</div>
            </div>
            <div class="info-cell span-2">
                <div class="info-cell-label">القسم المحوّل منه</div>
                <div class="info-cell-value">حظيرة الأسود الرئيسية</div>
            </div>
        </div>

        <h3 class="section-title" style="margin-top:2rem;">سبب الإحالة والملاحظات</h3>
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div>
                <div class="info-cell-label">سبب الإحالة</div>
                <div class="content-box">لوحظ خمول شديد ورفض لتناول وجبة اللحم لليوم الثاني على التوالي. هنالك جرح سطحي في الساق الخلفية اليمنى مع علامات التهاب أولية.</div>
            </div>
        </div>
    </div>

    {{-- TAB 2: Animal Data --}}
    <div class="tab-content" id="tab-2">
        <h3 class="section-title">بيانات الحيوان</h3>
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-cell-label">رقم الحيوان الرسمي</div>
                <div class="info-cell-value id-tag">#ANM-101</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">المجموعة المرتبطة</div>
                <div class="info-cell-value">القطط الكبرى</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">نوع الحيوان</div>
                <div class="info-cell-value">أسد إفريقي</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">الاسم</div>
                <div class="info-cell-value">سيمبا</div>
            </div>
            <div class="info-cell span-2">
                <div class="info-cell-label">العمر</div>
                <div class="info-cell-value">8 سنوات</div>
            </div>
        </div>
    </div>

    {{-- TAB 3: Medical Follow-up --}}
    <div class="tab-content" id="tab-3">
        <h3 class="section-title">المتابعة الطبية للحالة</h3>
        <div class="timeline">
            
            {{-- إدخال 2 --}}
            <div class="entry-card">
                <div class="entry-header">
                    <div class="entry-doctor">
                        <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; background:#e0e7ff; color:#4338ca; border-radius:50%; font-size:0.75rem;">د.خ</span>
                        د. خالد العربي
                    </div>
                    <div class="entry-date">اليوم — 09:30 صباحاً</div>
                </div>
                
                <div class="entry-grid">
                    <div class="entry-box">
                        <span class="box-title">التشخيص الحالي</span>
                        <span class="box-text">تحسن طفيف في النشاط. الالتهاب في الساق استجاب للمضاد الحيوي ولكن الشهية لا تزال ضعيفة.</span>
                    </div>
                    <div class="entry-box">
                        <span class="box-title">العلاج المتبع</span>
                        <span class="box-text">جرعة ثانية من Amoxicillin (500mg). تم إعطاء فيتامينات مقوية عن طريق الحقن العضلي.</span>
                    </div>
                    <div class="entry-box full">
                        <span class="box-title">الملاحظات الطبية</span>
                        <span class="box-text">يجب مراقبة مكان الجرح لمنع تلوثه. الحيوان يحاول لعق الجرح لذا تم تطبيق رذاذ مانع.</span>
                    </div>
                    <div class="entry-box full" style="border-right:3px solid #f59e0b; background:#fffbeb;">
                        <span class="box-title" style="color:#b45309;">التوصية الغذائية</span>
                        <span class="box-text">تقديم اللحم الطازج المفروم بقطع صغيرة جداً مع مرق الدجاج لترغيبه في الأكل وسهولة الهضم.</span>
                    </div>
                </div>
                
                <div class="entry-footer">
                    <span class="status-badge status-better">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                        تحسن ملحوظ
                    </span>
                    <button class="btn-action" style="padding:6px 12px; font-size:0.75rem; border:none; background:transparent;">تعديل الإدخال</button>
                </div>
            </div>

            {{-- إدخال 1 --}}
            <div class="entry-card">
                <div class="entry-header">
                    <div class="entry-doctor">
                        <span style="display:flex; align-items:center; justify-content:center; width:26px; height:26px; background:#e0e7ff; color:#4338ca; border-radius:50%; font-size:0.75rem;">د.خ</span>
                        د. خالد العربي
                    </div>
                    <div class="entry-date">2026-06-05 — 11:15 صباحاً (يوم الدخول)</div>
                </div>
                
                <div class="entry-grid">
                    <div class="entry-box full">
                        <span class="box-title">التشخيص المبدئي</span>
                        <span class="box-text">التهاب جرثومي ناتج عن خدش في الساق مع ارتفاع طفيف في درجة الحرارة (39.5 مئوية).</span>
                    </div>
                    <div class="entry-box">
                        <span class="box-title">العلاج المبدئي</span>
                        <span class="box-text">جرعة أولى من المضاد الحيوي وتطهير الجرح. إعطاء سوائل مغذية لتعويض النقص.</span>
                    </div>
                    <div class="entry-box">
                        <span class="box-title">الملاحظات الطبية</span>
                        <span class="box-text">تم عزله في الحظيرة الداخلية للمستشفى لضمان الهدوء.</span>
                    </div>
                </div>
                
                <div class="entry-footer">
                    <span class="status-badge status-stable">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        مستقر
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>

<div style="display:flex; justify-content:flex-end; margin-top:1.5rem;">
    <button class="btn-primary" style="background:linear-gradient(135deg, #c2410c, #ea580c); box-shadow:0 4px 12px rgba(234,88,12,0.25);" onclick="openDecisionModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        إصدار قرار
    </button>
</div>

@endsection

@section('scripts')
<script>
    function switchTab(n, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById('tab-' + n).classList.add('active');
        btn.classList.add('active');
    }

    function openDecisionModal() {
        alert('سيتم فتح مودال إصدار القرار');
    }
</script>
@endsection
