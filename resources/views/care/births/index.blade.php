@extends($__layout ?? 'care.layout')
@section('title', 'الولادات الجديدة | الرعاية والتغذية')
@section('page_title', 'الولادات الجديدة')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.2rem; }

    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input { width: 100%; padding: 10px 40px 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; transition: all 0.2s; }
    .search-box input:focus { border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    .search-box svg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .filter-select { padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #334155; outline: none; cursor: pointer; }
    .filter-select:focus { border-color: #2E7D32; }

    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-footer {
        padding: 1.1rem 1.75rem;
        display: flex; align-items: center; justify-content: flex-end;
        border-top: 1px solid #f1f5f9; background: #FAFBFC;
    }
    .table-pagination {
        display: flex; align-items: center; gap: 8px;
        font-size: 0.82rem; font-weight: 700; color: #64748b;
    }
    .table-pagination .page-info { margin-left: 4px; white-space: nowrap; }
    .page-btn {
        width: 34px; height: 34px; border-radius: 9px;
        border: 1px solid #e2e8f0; background: #fff; color: #475569;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; font-family: 'Cairo', sans-serif;
    }
    .page-btn:hover:not(:disabled) { border-color: #2E7D32; color: #2E7D32; background: #f0fdf4; }
    .page-btn:disabled { opacity: 0.45; cursor: not-allowed; }
    .page-btn.active {
        background: linear-gradient(135deg, #1a4a2e, #2d7a47);
        border-color: transparent; color: #fff;
    }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.8rem; font-weight: 800; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.92rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .custom-table tbody tr:last-child td { border-bottom: none; }

    .badge { padding: 6px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge-status-monitoring { background: #eff6ff; color: #2563eb; }
    .badge-status-monitoring .dot { background: #3b82f6; }
    .badge-status-completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-status-completed .dot { background: #22c55e; }

    .days-ok { color: #16a34a; font-weight: 800; }
    .days-warn { color: #d97706; font-weight: 800; }
    .days-danger { color: #dc2626; font-weight: 800; }

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; padding: 0; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .temp-id, .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    /* ═══ MODAL — نفس نافذة تفاصيل الحجر الصحي ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    #birthModal .modal-box { background: #f8fafc; border-radius: 20px; width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    #birthModal .modal-header { background: transparent; border-bottom: none; display: flex; justify-content: center; position: relative; padding: 2rem 1.5rem 0; }
    #birthModal .modal-header h3 { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0; text-align: center; }
    #birthModal .modal-close {
        position: absolute; left: 1.5rem; top: 1.5rem;
        width: 32px; height: 32px; border-radius: 8px; background: #e2e8f0; border: none;
        color: #64748b; display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 1.2rem; font-weight: 700; line-height: 1;
    }
    #birthModal .modal-close:hover { background: #cbd5e1; color: #0f172a; }

    #birthModal .modal-tabs-bar { display: flex; justify-content: center; gap: 0; padding: 1rem 2rem 0; }
    #birthModal .modal-tab {
        padding: 8px 20px; border: none; background: transparent;
        font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800;
        cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent;
    }
    #birthModal .modal-tab.active { color: #16a34a; border-bottom-color: #16a34a; }

    #birthModal .modal-body { padding: 1.5rem 2rem; }
    #birthModal .q-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { #birthModal .q-grid { grid-template-columns: 1fr; } }

    #birthModal .q-card {
        background: #fff; border-radius: 12px; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    #birthModal .q-card-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center; }
    #birthModal .q-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;
    }
    #birthModal .q-row.sep { margin-bottom: 2rem; }
    #birthModal .q-label { color: #64748b; font-size: 0.9rem; font-weight: 700; }
    #birthModal .q-value { color: #0f172a; font-size: 0.95rem; font-weight: 800; }
    #birthModal .q-notes-list { display: flex; flex-direction: column; gap: 0.8rem; }
    #birthModal .q-note-item {
        background: #f8fafc; padding: 12px 14px; border-radius: 8px;
        font-size: 0.85rem; color: #334155; font-weight: 700; text-align: center; border: 1px solid #f1f5f9;
    }
    #birthModal .q-attach-wrap { text-align: center; padding: 2rem 1rem; }
    #birthModal .q-attach-img {
        width: 180px; height: 180px; border-radius: 16px; margin: 0 auto;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9); border: 2px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center; font-size: 5rem;
    }

    #birthModal .modal-footer { background: transparent; border-top: none; padding: 0 2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; }
    #birthModal .btn-action-release { padding: 8px 16px; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #birthModal .btn-action-release:hover { background: #15803d; }
    #birthModal .btn-action-close { padding: 8px 16px; background: #e11d48; color: #fff; border: none; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #birthModal .btn-action-close:hover { background: #be123c; }
    #birthModal .btn-cancel { padding: 8px 16px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #birthModal .btn-cancel:hover { background: #e2e8f0; }

    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-submit:hover { transform: translateY(-1px); }
    .btn-submit-red { padding: 10px 24px; background: linear-gradient(135deg, #991b1b, #dc2626); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-submit-red:hover { transform: translateY(-1px); }
    .btn-cancel { padding: 10px 20px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; }
    .btn-cancel:hover { background: #f8fafc; }

    /* ── Sub-dialog modals ── */
    .dialog-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(3px); z-index: 1100; align-items: center; justify-content: center; }
    .dialog-backdrop.open { display: flex; }
    .dialog-box { background: #fff; border-radius: 18px; width: 100%; max-width: 460px; box-shadow: 0 30px 80px rgba(0,0,0,0.2); animation: modalIn 0.25s cubic-bezier(0.34,1.56,0.64,1); overflow: hidden; }
    .dialog-icon-wrap { width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.8rem; }
    .dialog-body { padding: 2rem 2rem 1.5rem; text-align: center; }
    .dialog-body h4 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
    .dialog-body p { font-size: 0.85rem; color: #64748b; font-weight: 600; line-height: 1.6; margin-bottom: 0; }
    .dialog-footer { padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: center; }
    /* Toast */
    .toast { position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(20px); background: #0f172a; color: #fff; padding: 14px 24px; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); z-index: 2000; opacity: 0; transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1); pointer-events: none; }
    .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    .toast.green { background: linear-gradient(135deg, #1a4a2e, #2d7a47); }
    .toast.red { background: linear-gradient(135deg, #991b1b, #dc2626); }
</style>
@endsection

@section('content')

<div class="top-card">
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان أو نوعه أو رقم الأم...">
        </div>
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

<div class="table-card">
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الحيوان</th>
                    <th>المجموعة</th>
                    <th>تاريخ الولادة</th>
                    <th>الأيام المتبقية</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="animal-id">NB-26-001</span></td>
                    <td>السباع</td>
                    <td>2026-05-23</td>
                    <td><span class="days-danger">يوم واحد ⚠️</span></td>
                    <td><span class="badge badge-status-monitoring"><span class="dot"></span>قيد المتابعة</span></td>
                    <td>
                        <button onclick="openModal('monitoring','NB-26-001','🦁','أسد إفريقي','السباع','#ANL-0041-2022','2026-05-23','يوم واحد','قيد المتابعة','خالد منصور','—','1.4 كجم','المولود بصحة جيدة ونشط ويرضع بشكل طبيعي.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td><span class="animal-id">NB-26-002</span></td>
                    <td>الرئيسيات</td>
                    <td>2026-06-03</td>
                    <td><span class="days-ok">11 يوماً</span></td>
                    <td><span class="badge badge-status-monitoring"><span class="dot"></span>قيد المتابعة</span></td>
                    <td>
                        <button onclick="openModal('monitoring','NB-26-002','🐒','قرد المكاك','الرئيسيات','#ANL-0182-2023','2026-06-03','11 يوماً','قيد المتابعة','ياسر الغيثي','كدمة على الرسغ الأيسر','0.6 كجم','يتم إرضاع المولود بشكل طبيعي، حيوي ونشط.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td><span class="animal-id">NB-26-003</span></td>
                    <td>العواشب</td>
                    <td>2026-05-15</td>
                    <td><span style="color:#94a3b8;">—</span></td>
                    <td><span class="badge badge-status-completed"><span class="dot"></span>اكتملت المتابعة</span></td>
                    <td>
                        <button onclick="openModal('completed','NB-26-003','🦌','غزال الريم','العواشب','#ANL-0120-2024','2026-05-15','—','اكتملت المتابعة','أحمد الكواري','عرج واضح في الساق','3.2 كجم','تمت المتابعة بنجاح وتم اعتماده كحيوان دائم.')" class="btn-tbl view" title="عرض التفاصيل">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="table-card-footer">
        <div class="table-pagination">
            <button class="page-btn" disabled title="السابق">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            <button class="page-btn active">1</button>
            <button class="page-btn" title="التالي" disabled>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <span class="page-info">صفحة 1 من 1 — 3 مواليد</span>
        </div>
    </div>
</div>

{{-- ═══ MODAL ═══ --}}
<div class="modal-backdrop" id="birthModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>تفاصيل الولادة — <span id="modalBirthId">NB-26-001</span></h3>
            <button type="button" class="modal-close" onclick="closeModal()">✕</button>
        </div>

        <div class="modal-tabs-bar">
            <button class="modal-tab active" id="btab-btn-1" onclick="switchBTab(1)">بيانات المولود</button>
            <button class="modal-tab" id="btab-btn-2" onclick="switchBTab(2)">المرفقات</button>
        </div>

        <div class="modal-body">
            <div id="btab-1">
                <div class="q-grid">
                    <div class="q-card">
                        <h4 class="q-card-title">بيانات المولود</h4>

                        <div class="q-row">
                            <span class="q-label">رقم الحيوان</span>
                            <span class="q-value" id="bAnimalId">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">نوع الحيوان</span>
                            <span class="q-value" id="bAnimalType">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">المجموعة</span>
                            <span class="q-value" id="bGroup">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">رقم الأم</span>
                            <span class="q-value" id="bMother">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">تاريخ الولادة</span>
                            <span class="q-value" id="bDate">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">الأيام المتبقية</span>
                            <span class="q-value" id="bDays">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">الحالة</span>
                            <span class="q-value" id="bStatus">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">المشرف</span>
                            <span class="q-value" id="bSupervisor">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">العلامة المميزة</span>
                            <span class="q-value" id="bMark">—</span>
                        </div>
                        <div class="q-row" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
                            <span class="q-label">الوزن</span>
                            <span class="q-value" id="bWeight">—</span>
                        </div>
                    </div>

                    <div class="q-card" style="flex-grow:1;">
                        <h4 class="q-card-title">الملاحظات</h4>
                        <div class="q-notes-list">
                            <div class="q-note-item" id="bNotes">—</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="btab-2" style="display:none;">
                <div class="q-card">
                    <h4 class="q-card-title">المرفقات</h4>
                    <div class="q-attach-wrap">
                        <div class="q-attach-img" id="bAttachmentImg">🦁</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" id="bFooter"></div>
    </div>
</div>

{{-- ═══ CONFIRM FINISH DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmFinishDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#f0fdf4;">✅</div>
            <h4>تأكيد انتهاء فترة المتابعة</h4>
            <p>هل أنت متأكد من انتهاء فترة المتابعة واعتماد المولود رسمياً كحيوان دائم؟<br>يُرجى بعد ذلك تسجيله في <strong>إدارة الحيوانات</strong> ليحصل على رقمه الرسمي.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('confirmFinishDialog')">إلغاء</button>
            <button class="btn-submit" onclick="confirmFinish()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                نعم، انتهت فترة المتابعة
            </button>
        </div>
    </div>
</div>

{{-- ═══ CONFIRM HEALTH REFER DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmHealthReferDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#fff1f2;">🏥</div>
            <h4>تأكيد تحويل كحالة صحية</h4>
            <p>هل أنت متأكد من تحويل هذا المولود كحالة صحية تستدعي تدخل المستشفى البيطري؟<br>سيتم إنشاء حالة صحية وطلب استدعاء الطبيب فوراً.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('confirmHealthReferDialog')">إلغاء</button>
            <button class="btn-submit-red" onclick="confirmHealthRefer()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13"/><path d="M22 2L15 22L11 13L2 9L22 2Z"/></svg>
                نعم، تحويل كحالة صحية
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
    function switchBTab(n) {
        document.getElementById('btab-1').style.display = n === 1 ? 'block' : 'none';
        document.getElementById('btab-2').style.display = n === 2 ? 'block' : 'none';
        document.getElementById('btab-btn-1').className = 'modal-tab' + (n === 1 ? ' active' : '');
        document.getElementById('btab-btn-2').className = 'modal-tab' + (n === 2 ? ' active' : '');
    }

    function openModal(state, animalId, emoji, animalType, group, mother, date, days, status, supervisor, mark, weight, notes) {
        switchBTab(1);

        document.getElementById('modalBirthId').textContent = animalId;
        document.getElementById('bAnimalId').textContent = animalId;
        document.getElementById('bAnimalType').textContent = animalType;
        document.getElementById('bGroup').textContent = group;
        document.getElementById('bMother').textContent = mother;
        document.getElementById('bDate').textContent = date;
        document.getElementById('bDays').textContent = days;
        document.getElementById('bStatus').textContent = status;
        document.getElementById('bSupervisor').textContent = supervisor;
        document.getElementById('bMark').textContent = mark;
        document.getElementById('bWeight').textContent = weight;
        document.getElementById('bNotes').textContent = notes;
        document.getElementById('bAttachmentImg').textContent = emoji;

        const footer = document.getElementById('bFooter');
        const closeBtn = `<button class="btn-cancel" onclick="closeModal()">إغلاق</button>`;

        if (state === 'monitoring') {
            footer.innerHTML = closeBtn +
                `<button class="btn-action-close" onclick="referHealth()">تحويل كحالة صحية</button>
                <button class="btn-action-release" onclick="finishMonitoring()">انتهت فترة المتابعة</button>`;
        } else {
            footer.innerHTML = closeBtn;
        }

        document.getElementById('birthModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('birthModal').classList.remove('open');
    }

    // ── Custom dialogs ──
    function openDialog(id) {
        document.getElementById(id).classList.add('open');
    }
    function closeDialog(id) {
        document.getElementById(id).classList.remove('open');
    }
    function showToast(msg, type='green') {
        const t = document.getElementById('toastMsg');
        const tx = document.getElementById('toastText');
        t.className = 'toast ' + type;
        tx.innerText = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    function finishMonitoring() {
        openDialog('confirmFinishDialog');
    }
    function confirmFinish() {
        closeDialog('confirmFinishDialog');
        closeModal();
        showToast('✅ تم اعتماد المولود. يُرجى تسجيله في إدارة الحيوانات.', 'green');
    }

    function referHealth() {
        openDialog('confirmHealthReferDialog');
    }
    function confirmHealthRefer() {
        closeDialog('confirmHealthReferDialog');
        closeModal();
        showToast('🏥 تم إنشاء حالة صحية للمولود وطلب التدخل البيطري.', 'red');
    }

    window.onclick = function(e) {
        if (e.target === document.getElementById('birthModal')) closeModal();
    };
</script>
@endsection
