@extends('care.layout')
@section('title', 'الولادات الجديدة | الرعاية والتغذية')
@section('page_title', 'الولادات الجديدة')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.2rem; }
    .page-header { display: flex; justify-content: space-between; align-items: center; }
    .page-header-info h2 { font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0; }
    .page-header-info p { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin: 4px 0 0; }

    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; padding-top: 1.2rem; border-top: 1px solid #F1F5F9; }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input { width: 100%; padding: 10px 40px 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; transition: all 0.2s; }
    .search-box input:focus { border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    .search-box svg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .filter-select { padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #334155; outline: none; cursor: pointer; }
    .filter-select:focus { border-color: #2E7D32; }

    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-header { padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #FAFBFC; }
    .table-card-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
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

    /* ═══ MODAL — VET HOSPITAL STYLE ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 680px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
    .modal-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1.2rem 1.5rem 0; display: flex; justify-content: space-between; align-items: flex-end; }
    .modal-title-wrap { padding-bottom: 0.8rem; }
    .modal-title-wrap h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .modal-title-wrap span { font-size: 0.8rem; color: #64748b; font-weight: 600; }
    .modal-tabs-wrap { display: flex; align-items: center; gap: 20px; }
    .modal-tabs { display: flex; }
    .modal-tab { padding: 10px 24px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800; cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent; transition: all 0.2s; }
    .modal-tab.active { color: #1a4a2e; border-bottom-color: #1a4a2e; }
    .modal-close { width: 32px; height: 32px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.1rem; font-weight: 700; transition: all 0.2s; margin-bottom: 10px; }
    .modal-close:hover { background: #f8fafc; color: #0f172a; }
    .modal-body { padding: 1.5rem; overflow-y: auto; max-height: 65vh; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; }
    .info-cell { background: #fff; padding: 12px 16px; }
    .info-cell-label { font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
    .info-cell-value { font-size: 0.9rem; color: #0f172a; font-weight: 800; }

    .content-box { background: #fff; border-right: 4px solid #3b82f6; padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; color: #1e293b; font-weight: 700; line-height: 1.6; border: 1px solid #e2e8f0; border-right-width: 4px; }
    .section-label { font-size: 0.8rem; color: #64748b; font-weight: 800; margin-bottom: 8px; }

    .modal-footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 1.2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; }
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
    <div class="page-header">
        <div class="page-header-info">
            <h2>🍼 الولادات الجديدة</h2>
            <p>متابعة المواليد الجدد خلال فترة الرعاية الأولية قبل اعتمادهم رسمياً بالنظام.</p>
        </div>
    </div>
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث بنوع الحيوان، رقم الأم، أو معرف المولود...">
        </div>
        <select class="filter-select">
            <option value="">كل المجموعات</option>
            <option>السباع والضواري</option>
            <option>الرئيسيات</option>
            <option>العواشب</option>
            <option>الطيور</option>
        </select>
        <select class="filter-select">
            <option value="">كل الحالات</option>
            <option>قيد المتابعة</option>
            <option>اكتملت المتابعة</option>
        </select>
        <input type="date" class="filter-select">
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            قائمة المواليد
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>معرف المولود</th>
                    <th>نوع الحيوان</th>
                    <th>المجموعة</th>
                    <th>رقم الأم</th>
                    <th>تاريخ الولادة</th>
                    <th>الأيام المتبقية</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="temp-id">NB-26-001</span></td>
                    <td style="font-weight:700;">أسد إفريقي</td>
                    <td>السباع</td>
                    <td><span class="animal-id">#ANL-0041-2022</span></td>
                    <td>2026-05-23</td>
                    <td><span class="days-danger">يوم واحد ⚠️</span></td>
                    <td><span class="badge badge-status-monitoring"><span class="dot"></span>قيد المتابعة</span></td>
                    <td>
                        <button onclick="openModal('monitoring','NB-26-001','أسد إفريقي','السباع','#ANL-0041-2022','2026-05-23 / 04:30 ص','طبيعية','1.4 كجم','غير محدد','يوم واحد','المولود بصحة جيدة ونشط ويرضع بشكل طبيعي.')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td><span class="temp-id">NB-26-002</span></td>
                    <td style="font-weight:700;">قرد المكاك</td>
                    <td>الرئيسيات</td>
                    <td><span class="animal-id">#ANL-0182-2023</span></td>
                    <td>2026-06-03</td>
                    <td><span class="days-ok">11 يوماً</span></td>
                    <td><span class="badge badge-status-monitoring"><span class="dot"></span>قيد المتابعة</span></td>
                    <td>
                        <button onclick="openModal('monitoring','NB-26-002','قرد المكاك','الرئيسيات','#ANL-0182-2023','2026-06-03 / 09:15 ص','طبيعية','0.6 كجم','أنثى','11 يوماً','يتم إرضاع المولود بشكل طبيعي، حيوي ونشط.')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td><span class="temp-id">NB-26-003</span></td>
                    <td style="font-weight:700;">غزال الريم</td>
                    <td>العواشب</td>
                    <td><span class="animal-id">#ANL-0120-2024</span></td>
                    <td>2026-05-15</td>
                    <td><span style="color:#94a3b8;">—</span></td>
                    <td><span class="badge badge-status-completed"><span class="dot"></span>اكتملت المتابعة</span></td>
                    <td>
                        <button onclick="openModal('completed','NB-26-003','غزال الريم','العواشب','#ANL-0120-2024','2026-05-15 / 11:00 ص','طبيعية','3.2 كجم','ذكر','—','تمت المتابعة بنجاح وتم اعتماده كحيوان دائم.')" class="btn-tbl view">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ═══ MODAL ═══ --}}
<div class="modal-backdrop" id="birthModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <h3>تفاصيل المولود</h3>
                <span id="bSubtitle">—</span>
            </div>
            <div class="modal-tabs-wrap">
                <div class="modal-tabs">
                    <button class="modal-tab active" id="btab-btn-1" onclick="switchBTab(1)">بيانات المولود</button>
                    <button class="modal-tab" id="btab-btn-2" onclick="switchBTab(2)">الملاحظات</button>
                </div>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
        </div>

        <div class="modal-body">
            {{-- Tab 1 --}}
            <div id="btab-1">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:1.5rem;">
                    <span style="font-family:'Courier New',monospace; font-size:0.85rem; background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; padding:4px 12px; border-radius:6px; font-weight:700;" id="bTempId">NB-26-001</span>
                    <span style="font-size:0.8rem; color:#64748b; font-weight:700;">معرف المولود المؤقت</span>
                    <span id="bStatusBadge" style="margin-right:auto;"></span>
                </div>
                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-cell-label">نوع الحيوان</div>
                        <div class="info-cell-value" id="bAnimalType">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المجموعة</div>
                        <div class="info-cell-value" id="bGroup">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">رقم الأم</div>
                        <div class="info-cell-value" style="font-family:'Courier New',monospace; color:#64748b;" id="bMother">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ ووقت الولادة</div>
                        <div class="info-cell-value" id="bDate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">طبيعة الولادة</div>
                        <div class="info-cell-value" id="bType">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">الوزن الأولي</div>
                        <div class="info-cell-value" id="bWeight">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">الجنس</div>
                        <div class="info-cell-value" id="bGender">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">الأيام المتبقية</div>
                        <div class="info-cell-value" id="bDays">—</div>
                    </div>
                </div>
            </div>

            {{-- Tab 2 --}}
            <div id="btab-2" style="display:none;">
                <div style="margin-bottom:1.2rem;">
                    <div class="section-label">ملاحظات فترة المتابعة</div>
                    <div class="content-box" id="bNotes">—</div>
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
            <h4>تأكيد إنهاء فترة المتابعة</h4>
            <p>هل أنت متأكد من إنهاء فترة المتابعة واعتماد المولود رسمياً كحيوان دائم؟<br>يُرجى بعد ذلك تسجيله في <strong>إدارة الحيوانات</strong> ليحصل على رقمه الرسمي.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('confirmFinishDialog')">إلغاء</button>
            <button class="btn-submit" onclick="confirmFinish()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                نعم، اعتماد كحيوان
            </button>
        </div>
    </div>
</div>

{{-- ═══ CONFIRM HEALTH REFER DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmHealthReferDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#fff1f2;">🏥</div>
            <h4>تأكيد إحالة كحالة صحية</h4>
            <p>هل أنت متأكد من إحالة هذا المولود كحالة صحية تستدعي تدخل المستشفى البيطري؟<br>سيتم إنشاء حالة صحية وطلب استدعاء الطبيب فوراً.</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('confirmHealthReferDialog')">إلغاء</button>
            <button class="btn-submit-red" onclick="confirmHealthRefer()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13"/><path d="M22 2L15 22L11 13L2 9L22 2Z"/></svg>
                نعم، إحالة كحالة صحية
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

    function openModal(state, tempId, animalType, group, mother, date, btype, weight, gender, days, notes) {
        switchBTab(1);

        document.getElementById('bSubtitle').innerText = animalType + ' — ' + group;
        document.getElementById('bTempId').innerText = tempId;
        document.getElementById('bAnimalType').innerText = animalType;
        document.getElementById('bGroup').innerText = group;
        document.getElementById('bMother').innerText = mother;
        document.getElementById('bDate').innerText = date;
        document.getElementById('bType').innerText = btype;
        document.getElementById('bWeight').innerText = weight;
        document.getElementById('bGender').innerText = gender;
        document.getElementById('bDays').innerText = days;
        document.getElementById('bNotes').innerText = notes;

        const stMap = {
            'monitoring': 'background:#eff6ff;color:#2563eb;',
            'completed':  'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;'
        };
        const stLabel = state === 'completed' ? 'اكتملت المتابعة' : 'قيد المتابعة';
        document.getElementById('bStatusBadge').innerHTML = `<span style="padding:4px 10px;border-radius:999px;font-size:0.75rem;font-weight:800;${stMap[state]||''}">${stLabel}</span>`;

        const footer = document.getElementById('bFooter');
        const closeBtn = `<button class="btn-cancel" onclick="closeModal()">إغلاق</button>`;

        if (state === 'monitoring') {
            footer.innerHTML = closeBtn +
                `<button class="btn-submit-red" onclick="referHealth()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:6px;"><path d="M22 2L11 13"/><path d="M22 2L15 22L11 13L2 9L22 2Z"/></svg>
                    إحالة كحالة صحية
                </button>
                <button class="btn-submit" onclick="finishMonitoring()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:6px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    إنهاء المتابعة واعتماد كحيوان
                </button>`;
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
