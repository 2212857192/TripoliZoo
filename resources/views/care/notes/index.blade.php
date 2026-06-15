@extends($__layout ?? 'care.layout')
@section('title', 'الملاحظات التشغيلية | الرعاية والتغذية')
@section('page_title', 'الملاحظات التشغيلية')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.2rem; }

    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
    .filter-select { padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #334155; outline: none; cursor: pointer; }
    .filter-select:focus { border-color: #2E7D32; }

    .segmented-tabs { display: inline-flex; background: #f1f5f9; padding: 5px; border-radius: 10px; gap: 4px; }
    .seg-tab { background: transparent; border: none; padding: 9px 24px; border-radius: 7px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800; color: #64748b; cursor: pointer; transition: all 0.2s; }
    .seg-tab:hover { color: #1a4a2e; }
    .seg-tab.active { background: #fff; color: #1a4a2e; box-shadow: 0 2px 4px rgba(0,0,0,0.07); }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ── Cards Grid ── */
    .notes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    
    .note-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; transition: all 0.2s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden; cursor: pointer; }
    .note-card:hover { transform: translateY(-4px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); border-color: #cbd5e1; }
    
    .note-card::before { content: ''; position: absolute; top: 0; right: 0; left: 0; height: 4px; background: #1a4a2e; }
    
    .note-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.2rem; }
    .note-card-id { font-family: 'Courier New', monospace; font-size: 0.8rem; font-weight: 800; color: #64748b; background: #f8fafc; padding: 4px 10px; border-radius: 6px; border: 1px solid #e2e8f0; }
    
    .note-card-meta { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: #64748b; font-weight: 700; margin-bottom: 0.2rem; }
    .note-card-meta svg { color: #94a3b8; }
    
    .note-card-content { font-size: 0.95rem; color: #1e293b; font-weight: 600; line-height: 1.7; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; flex-grow: 1; margin-bottom: 0.5rem; }
    
    .note-card-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 1rem; margin-top: auto; }

    /* ── Segmented Tabs (moved to top card) ── */

    /* ═══ BADGES ═══ */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    
    .badge-new      { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-new .dot { background: #ef4444; }
    .badge-reviewed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-reviewed .dot { background: #22c55e; }

    .badge-type-nutrition { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-type-nutrition .dot { background: #f59e0b; }
    .badge-type-general { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-type-general .dot { background: #3b82f6; }

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .note-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    /* ═══ MODAL — نفس نافذة تفاصيل الحجر الصحي ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    #noteModal .modal-box { background: #f8fafc; border-radius: 20px; width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    #noteModal .modal-header { background: transparent; border-bottom: none; display: flex; justify-content: center; position: relative; padding: 2rem 1.5rem 0; }
    #noteModal .modal-header h3 { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0; text-align: center; }
    #noteModal .modal-close {
        position: absolute; left: 1.5rem; top: 1.5rem;
        width: 32px; height: 32px; border-radius: 8px; background: #e2e8f0; border: none;
        color: #64748b; display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 1.2rem; font-weight: 700; line-height: 1;
    }
    #noteModal .modal-close:hover { background: #cbd5e1; color: #0f172a; }

    #noteModal .modal-tabs-bar { display: flex; justify-content: center; gap: 0; padding: 1rem 2rem 0; }
    #noteModal .modal-tab {
        padding: 8px 20px; border: none; background: transparent;
        font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800;
        cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent;
    }
    #noteModal .modal-tab.active { color: #16a34a; border-bottom-color: #16a34a; }

    #noteModal .modal-body { padding: 1.5rem 2rem; }
    #noteModal .q-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { #noteModal .q-grid { grid-template-columns: 1fr; } }

    #noteModal .q-card {
        background: #fff; border-radius: 12px; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    #noteModal .q-card-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center; }
    #noteModal .q-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;
    }
    #noteModal .q-label { color: #64748b; font-size: 0.9rem; font-weight: 700; }
    #noteModal .q-value { color: #0f172a; font-size: 0.95rem; font-weight: 800; }
    #noteModal .q-note-box {
        background: #f8fafc; padding: 12px 14px; border-radius: 8px; text-align: center;
        font-size: 0.85rem; font-weight: 700; color: #334155; border: 1px solid #f1f5f9; line-height: 1.7;
    }
    #noteModal .q-attach-wrap { text-align: center; padding: 2rem 1rem; }
    #noteModal .q-attach-empty {
        width: 180px; height: 180px; border-radius: 16px; margin: 0 auto;
        background: #f8fafc; border: 2px dashed #e2e8f0;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.9rem; font-weight: 700; color: #94a3b8;
    }

    #noteModal .modal-footer { background: transparent; border-top: none; padding: 0 2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap; }
    #noteModal .btn-action-release { padding: 8px 16px; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #noteModal .btn-action-release:hover { background: #15803d; }
    #noteModal .btn-cancel { padding: 8px 16px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #noteModal .btn-cancel:hover { background: #e2e8f0; }

    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-submit:hover { transform: translateY(-1px); }
    .btn-cancel { padding: 10px 20px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #f8fafc; }

    /* ── Sub-dialog modals ── */
    .dialog-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(3px); z-index: 1100; align-items: center; justify-content: center; }
    .dialog-backdrop.open { display: flex; }
    .dialog-box { background: #fff; border-radius: 18px; width: 100%; max-width: 440px; box-shadow: 0 30px 80px rgba(0,0,0,0.2); animation: modalIn 0.25s cubic-bezier(0.34,1.56,0.64,1); overflow: hidden; }
    .dialog-icon-wrap { width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.8rem; }
    .dialog-body { padding: 2rem 2rem 1.5rem; text-align: center; }
    .dialog-body h4 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
    .dialog-body p { font-size: 0.85rem; color: #64748b; font-weight: 600; line-height: 1.6; margin-bottom: 0; }
    .dialog-footer { padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: center; }

    /* Toast notification */
    .toast { position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(20px); background: #0f172a; color: #fff; padding: 14px 24px; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); z-index: 2000; opacity: 0; transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1); pointer-events: none; }
    .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    .toast.green { background: linear-gradient(135deg, #1a4a2e, #2d7a47); }
</style>
@endsection

@section('content')

{{-- ═══════ FILTERS ═══════ --}}
<div class="top-card">
    <div class="segmented-tabs">
        <button class="seg-tab active" onclick="switchTab(event, 'tab-new')">جديدة</button>
        <button class="seg-tab" onclick="switchTab(event, 'tab-reviewed')">تمت المراجعة</button>
    </div>
    <div class="filter-bar">
        <select class="filter-select">
            <option value="">نوع الملاحظة</option>
            <option>تغذية</option>
            <option>ملاحظة عامة</option>
        </select>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option>السباع والضواري</option>
            <option>الرئيسيات</option>
            <option>العواشب</option>
            <option>الطيور</option>
        </select>
        <input type="date" class="filter-select">
    </div>
</div>

{{-- ═══ TAB: جديدة ═══ --}}
<div id="tab-new" class="tab-content active">
<div class="notes-grid">
    {{-- Card 1: New / Nutrition --}}
    <div class="note-card type-nutrition" onclick="openModal('new', 'nutrition', 'NT-0842', 'السباع والضواري', 'خالد منصور', '2026-06-07 / 08:30 ص', 'الأسد الإفريقي (ANL-0041) لم يكمل وجبته لليوم الثاني على التوالي. يترك حوالي 30% من اللحم المقدم له. لا توجد عليه علامات خمول واضحة لكن شهيته منخفضة.')">
        <div class="note-card-header">
            <span class="note-card-id">NT-0842</span>
            <span class="badge badge-new"><span class="dot"></span>جديدة</span>
        </div>
        
        <div class="note-card-meta">
            <span class="badge badge-type-nutrition" style="padding: 3px 8px; font-size: 0.7rem;"><span class="dot"></span>تغذية</span>
            <span style="display:flex; align-items:center; gap:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> خالد منصور</span>
        </div>
        
        <div class="note-card-content">
            الأسد الإفريقي (ANL-0041) لم يكمل وجبته لليوم الثاني على التوالي. يترك حوالي 30% من اللحم المقدم له...
        </div>
        
        <div class="note-card-footer">
            <span style="display:flex; align-items:center; gap:4px; font-size:0.8rem; color:#64748b; font-weight:700;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> 2026-06-07</span>
            <span style="font-size:0.8rem; color:#475569; font-weight:800;">السباع والضواري</span>
        </div>
    </div>

    {{-- Card 2: New / General --}}
    <div class="note-card type-general" onclick="openModal('new', 'general', 'NT-0843', 'الرئيسيات', 'ياسر الغيثي', '2026-06-07 / 09:15 ص', 'يرجى إرسال فريق الصيانة. يوجد تمزق في الشبك الداخلي للمجموعة B (قردة المكاك) من الناحية الشمالية. التمزق صغير ولا يسمح بهروب الحيوانات حالياً لكن يجب إصلاحه قبل أن يتسع.')">
        <div class="note-card-header">
            <span class="note-card-id">NT-0843</span>
            <span class="badge badge-new"><span class="dot"></span>جديدة</span>
        </div>
        
        <div class="note-card-meta">
            <span class="badge badge-type-general" style="padding: 3px 8px; font-size: 0.7rem;"><span class="dot"></span>ملاحظة عامة</span>
            <span style="display:flex; align-items:center; gap:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> ياسر الغيثي</span>
        </div>
        
        <div class="note-card-content">
            يرجى إرسال فريق الصيانة. يوجد تمزق في الشبك الداخلي للمجموعة B (قردة المكاك) من الناحية الشمالية...
        </div>
        
        <div class="note-card-footer">
            <span style="display:flex; align-items:center; gap:4px; font-size:0.8rem; color:#64748b; font-weight:700;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> 2026-06-07</span>
            <span style="font-size:0.8rem; color:#475569; font-weight:800;">الرئيسيات</span>
        </div>
    </div>
</div>
</div>

{{-- ═══ TAB: تمت المراجعة ═══ --}}
<div id="tab-reviewed" class="tab-content">
<div class="notes-grid">
    <div class="note-card type-nutrition" onclick="openModal('reviewed', 'nutrition', 'NT-0830', 'الطيور', 'سالم عبدالله', '2026-06-06 / 10:00 ص', 'المورد تأخر في تسليم وجبة الأسماك اليومية. قمنا باستخدام الاحتياطي الموجود في المبردات وهو يكفي لليوم فقط.')">
        <div class="note-card-header">
            <span class="note-card-id">NT-0830</span>
            <span class="badge badge-reviewed"><span class="dot"></span>تمت المراجعة</span>
        </div>
        
        <div class="note-card-meta">
            <span class="badge badge-type-nutrition" style="padding: 3px 8px; font-size: 0.7rem;"><span class="dot"></span>تغذية</span>
            <span style="display:flex; align-items:center; gap:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> سالم عبدالله</span>
        </div>
        
        <div class="note-card-content">
            تأخر وصول الدفعة اليومية من الأسماك المخصصة للبطاريق. قمنا باستخدام الاحتياطي الموجود وهو يكفي لليوم...
        </div>
        
        <div class="note-card-footer">
            <span style="display:flex; align-items:center; gap:4px; font-size:0.8rem; color:#64748b; font-weight:700;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> 2026-06-06</span>
            <span style="font-size:0.8rem; color:#475569; font-weight:800;">الطيور</span>
        </div>
    </div>

    {{-- Card 4: Reviewed / General --}}
    <div class="note-card type-general" onclick="openModal('reviewed', 'general', 'NT-0822', 'العواشب', 'أحمد الكواري', '2026-06-05 / 07:30 ص', 'تم إجراء نقل روتيني لذكر المها لإتمام عملية تنظيف وتعقيم الحظيرة الرئيسية. تمت العملية بهدوء وبدون أي إصابات.')">
        <div class="note-card-header">
            <span class="note-card-id">NT-0822</span>
            <span class="badge badge-reviewed"><span class="dot"></span>تمت المراجعة</span>
        </div>
        
        <div class="note-card-meta">
            <span class="badge badge-type-general" style="padding: 3px 8px; font-size: 0.7rem;"><span class="dot"></span>ملاحظة عامة</span>
            <span style="display:flex; align-items:center; gap:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> أحمد الكواري</span>
        </div>
        
        <div class="note-card-content">
            نقل ذكر المها العربي إلى حظيرة العزل مؤقتاً لتنظيف الحظيرة الرئيسية. تمت العملية بهدوء...
        </div>
        
        <div class="note-card-footer">
            <span style="display:flex; align-items:center; gap:4px; font-size:0.8rem; color:#64748b; font-weight:700;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> 2026-06-05</span>
            <span style="font-size:0.8rem; color:#475569; font-weight:800;">العواشب</span>
        </div>
    </div>
</div>
</div>

{{-- ═══ MODAL ═══ --}}
<div class="modal-backdrop" id="noteModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>تفاصيل الملاحظة — <span id="modalNoteId">NT-0842</span></h3>
            <button type="button" class="modal-close" onclick="closeModal()">✕</button>
        </div>

        <div class="modal-tabs-bar">
            <button class="modal-tab active" id="ntab-btn-1" onclick="switchNTab(1)">بيانات الملاحظة</button>
            <button class="modal-tab" id="ntab-btn-2" onclick="switchNTab(2)">المرفقات</button>
        </div>

        <div class="modal-body">
            <div id="ntab-1">
                <div class="q-grid">
                    <div class="q-card">
                        <h4 class="q-card-title">بيانات الملاحظة</h4>

                        <div class="q-row">
                            <span class="q-label">رقم الملاحظة</span>
                            <span class="q-value" id="nNoteId">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">نوع الملاحظة</span>
                            <span class="q-value" id="nType">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">المجموعة</span>
                            <span class="q-value" id="nGroup">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">المشرف</span>
                            <span class="q-value" id="nSupervisor">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">تاريخ الملاحظة</span>
                            <span class="q-value" id="nDate">—</span>
                        </div>
                        <div class="q-row" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
                            <span class="q-label">الحالة</span>
                            <span class="q-value" id="nStatus">—</span>
                        </div>
                    </div>

                    <div class="q-card" style="flex-grow:1;">
                        <h4 class="q-card-title">نص الملاحظة</h4>
                        <div class="q-note-box" id="nContent">—</div>
                    </div>
                </div>
            </div>

            <div id="ntab-2" style="display:none;">
                <div class="q-card">
                    <h4 class="q-card-title">المرفقات</h4>
                    <div class="q-attach-wrap">
                        <div class="q-attach-empty">لا توجد مرفقات</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" id="nFooter"></div>
    </div>
</div>

{{-- ═══ CONFIRM REVIEW DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmReviewDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#f0fdf4;">✅</div>
            <h4>تأكيد المراجعة</h4>
            <p>هل أنت متأكد من تحديد هذه الملاحظة التشغيلية كـ <strong>"تمت المراجعة"</strong>؟<br>يدل هذا على اطلاعك عليها واتخاذك لما يلزم.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('confirmReviewDialog')">إلغاء</button>
            <button class="btn-submit" onclick="confirmReview()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                نعم، تحديد كمراجعة
            </button>
        </div>
    </div>
</div>

{{-- ═══ TOAST ═══ --}}
<div class="toast" id="toastMsg">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastText">تمت العملية بنجاح</span>
</div>

@endsection

@section('scripts')
<script>
    function switchTab(e, tabId) {
        document.querySelectorAll('.seg-tab').forEach(t => t.classList.remove('active'));
        e.currentTarget.classList.add('active');
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
    }

    function switchNTab(n) {
        document.getElementById('ntab-1').style.display = n === 1 ? 'block' : 'none';
        document.getElementById('ntab-2').style.display = n === 2 ? 'block' : 'none';
        document.getElementById('ntab-btn-1').className = 'modal-tab' + (n === 1 ? ' active' : '');
        document.getElementById('ntab-btn-2').className = 'modal-tab' + (n === 2 ? ' active' : '');
    }

    function openModal(status, type, noteId, group, supervisor, date, content) {
        switchNTab(1);

        const typeLabel = type === 'nutrition' ? 'تغذية' : 'ملاحظة عامة';
        const statusLabel = status === 'new' ? 'جديدة' : 'تمت المراجعة';

        document.getElementById('modalNoteId').textContent = noteId;
        document.getElementById('nNoteId').textContent = noteId;
        document.getElementById('nType').textContent = typeLabel;
        document.getElementById('nGroup').textContent = group;
        document.getElementById('nSupervisor').textContent = supervisor;
        document.getElementById('nDate').textContent = date;
        document.getElementById('nStatus').textContent = statusLabel;
        document.getElementById('nContent').textContent = content;

        const footer = document.getElementById('nFooter');
        const closeBtn = `<button class="btn-cancel" onclick="closeModal()">إغلاق</button>`;

        if (status === 'new') {
            footer.innerHTML = closeBtn +
                `<button class="btn-action-release" onclick="markReviewed()">تحديد كمراجعة</button>`;
        } else {
            footer.innerHTML = closeBtn;
        }

        document.getElementById('noteModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('noteModal').classList.remove('open');
    }

    // ── Dialogs ──
    function openDialog(id) {
        document.getElementById(id).classList.add('open');
    }
    function closeDialog(id) {
        document.getElementById(id).classList.remove('open');
    }
    function showToast(msg) {
        const t = document.getElementById('toastMsg');
        document.getElementById('toastText').innerText = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    function markReviewed() {
        openDialog('confirmReviewDialog');
    }
    
    function confirmReview() {
        closeDialog('confirmReviewDialog');
        closeModal();
        showToast('✅ تم تحديث حالة الملاحظة إلى "تمت المراجعة".');
    }

    window.onclick = function(e) {
        if (e.target === document.getElementById('noteModal')) closeModal();
    };
</script>
@endsection
