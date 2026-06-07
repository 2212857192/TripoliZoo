@extends('vet.layout')
@section('title', 'إدارة حسابات الموظفين | Tripoli Zoo')
@section('page_title', 'إدارة الموظفين')

@section('styles')
<style>
    /* ── Top Card (Header + Filters) ── */
    .top-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.4rem 1.8rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    /* ── Page Header ── */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-header-info h2 {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .page-header-info p {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        margin: 4px 0 0;
    }

    .btn-add {
        background: var(--green);
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
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(46,125,50,0.25);
    }

    .btn-add:hover {
        background: #1B5E20;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(46,125,50,0.35);
    }

    /* ── Filter Bar ── */
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
        background: var(--white);
    }

    .search-box input:focus { border-color: var(--green); }

    .filter-select {
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        background: var(--white);
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
        color: var(--text-main);
    }

    .filter-select:focus { border-color: var(--green); }

    /* ── Table ── */
    .table-card {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .custom-table thead th {
        background: #F8FAFC;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
    }

    .custom-table tbody tr {
        transition: background 0.15s;
    }

    .custom-table tbody tr:hover {
        background: #FAFBFC;
    }

    .custom-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.92rem;
        vertical-align: middle;
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ── Employee Avatar Cell ── */
    .emp-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .emp-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        color: white;
        flex-shrink: 0;
    }

    .emp-avatar.c1 { background: linear-gradient(135deg, #E8651A, #c0510d); }
    .emp-avatar.c2 { background: linear-gradient(135deg, #2E7D32, #1B5E20); }
    .emp-avatar.c3 { background: linear-gradient(135deg, #0284C7, #01579B); }
    .emp-avatar.c4 { background: linear-gradient(135deg, #7C3AED, #5B21B6); }

    .emp-info strong {
        display: block;
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.92rem;
    }

    .emp-info span {
        font-size: 0.78rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* ── Badges ── */
    .badge {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-block;
    }

    .badge.active   { background: #DCFCE7; color: #166534; }
    .badge.inactive { background: #FEE2E2; color: #991B1B; }

    /* ── Action Buttons ── */
    .actions-cell {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        width: 34px;
        height: 34px;
        background: none;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        color: var(--text-muted);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-icon:hover               { background: var(--bg-color); color: var(--text-main); }
    .btn-icon.view:hover          { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
    .btn-icon.edit:hover          { color: var(--orange); background: #FFEDD5; border-color: #FED7AA; }
    .btn-icon.toggle-on:hover     { color: #DC2626; background: #FEE2E2; border-color: #FECACA; }
    .btn-icon.toggle-off:hover    { color: #059669; background: #D1FAE5; border-color: #A7F3D0; }

    /* ── Modals ── */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.55);
        backdrop-filter: blur(5px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.25s;
    }

    .modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-box {
        background: var(--white);
        width: 100%;
        max-width: 520px;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        transform: translateY(24px) scale(0.97);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .modal-overlay.show .modal-box {
        transform: translateY(0) scale(1);
    }

    .modal-head {
        padding: 1.4rem 1.8rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #FAFBFC;
    }

    .modal-head h3 {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .btn-close-x {
        width: 32px;
        height: 32px;
        background: var(--border);
        border: none;
        border-radius: 8px;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        transition: all 0.2s;
        line-height: 1;
    }

    .btn-close-x:hover { background: #E2E8F0; color: var(--text-main); }

    .modal-body-pad {
        padding: 1.8rem;
    }

    /* View Modal Details */
    .view-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.2rem;
    }

    .view-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .view-field label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .view-field span {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .view-emp-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 1.2rem 1.8rem;
        background: linear-gradient(135deg, #F0FDF4, #ECFDF5);
        border-bottom: 1px solid var(--border);
    }

    .view-emp-avatar {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 900;
        color: white;
    }

    .view-emp-title h4 {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .view-emp-title p {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin: 4px 0 0;
    }

    /* Form Fields */
    .form-group {
        margin-bottom: 1.3rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--text-main);
    }

    .form-input {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.92rem;
        outline: none;
        transition: border-color 0.2s;
        background: white;
    }

    .form-input:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(46,125,50,0.08); }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    .modal-footer {
        display: flex;
        gap: 10px;
        padding: 1rem 1.8rem 1.5rem;
        justify-content: flex-start;
    }

    .btn-save {
        flex: 1;
        padding: 11px;
        background: var(--green);
        color: white;
        border: none;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-save:hover { background: #1B5E20; }

    .btn-cancel {
        padding: 11px 20px;
        background: var(--bg-color);
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel:hover { background: #E2E8F0; color: var(--text-main); }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
        display: none;
    }

    .empty-state svg { margin-bottom: 1rem; opacity: 0.3; }
    .empty-state p { font-weight: 600; }

    /* Toast notification */
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

    .toast.show { transform: translateX(-50%) translateY(0); }
</style>
@endsection

@section('content')

<!-- Top Card: Header + Filters -->
<div class="top-card">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-info">
            <h2>حسابات الموظفين</h2>
            <p>إجمالي <strong id="empCount">3</strong> موظف مسجل في النظام</p>
        </div>
        <button class="btn-add" onclick="openModal('addModal')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            إضافة موظف جديد
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="searchInput" placeholder="ابحث بالاسم أو البريد...">
        </div>
        <select id="statusFilter" class="filter-select">
            <option value="all">كل الحالات</option>
            <option value="active">نشط</option>
            <option value="inactive">غير نشط</option>
        </select>
        <select id="roleFilter" class="filter-select">
            <option value="all">كل الأدوار</option>
            <option value="مشرف مجموعة">مشرف مجموعة</option>
            <option value="طبيب بيطري">طبيب بيطري</option>
            <option value="رئيس وحدة الرعاية">رئيس وحدة الرعاية</option>
            <option value="وحدة السجلات والتوثيق">وحدة السجلات والتوثيق</option>
        </select>
    </div>
</div>

<!-- Table Card -->
<div class="table-card">
    <table class="custom-table" id="empTable">
        <thead>
            <tr>
                <th>الموظف</th>
                <th>الدور الوظيفي</th>
                <th>رقم الهاتف</th>
                <th>تاريخ الانضمام</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody id="empTbody">
            <tr data-status="active" data-role="طبيب بيطري"
                data-name="أحمد سالم" data-email="ahmed@tripolizoo.ly"
                data-phone="+218 91 123 4567" data-join="2022-03-15" data-color="c1">
                <td>
                    <div class="emp-cell">
                        <div class="emp-avatar c1">أ</div>
                        <div class="emp-info">
                            <strong>أحمد سالم</strong>
                            <span>ahmed@tripolizoo.ly</span>
                        </div>
                    </div>
                </td>
                <td>طبيب بيطري</td>
                <td dir="ltr" style="text-align:right;">+218 91 123 4567</td>
                <td>15 مارس 2022</td>
                <td><span class="badge active">نشط</span></td>
                <td>
                    <div class="actions-cell">
                        <button class="btn-icon view" title="عرض التفاصيل" onclick="openView(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <button class="btn-icon edit" title="تعديل" onclick="openEdit(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <button class="btn-icon toggle-on" title="إيقاف الحساب" onclick="toggleStatus(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                        </button>
                    </div>
                </td>
            </tr>
            <tr data-status="active" data-role="رئيس وحدة الرعاية"
                data-name="سعاد مسعود" data-email="suad@tripolizoo.ly"
                data-phone="+218 92 987 6543" data-join="2023-01-10" data-color="c2">
                <td>
                    <div class="emp-cell">
                        <div class="emp-avatar c2">س</div>
                        <div class="emp-info">
                            <strong>سعاد مسعود</strong>
                            <span>suad@tripolizoo.ly</span>
                        </div>
                    </div>
                </td>
                <td>رئيس وحدة الرعاية</td>
                <td dir="ltr" style="text-align:right;">+218 92 987 6543</td>
                <td>10 يناير 2023</td>
                <td><span class="badge active">نشط</span></td>
                <td>
                    <div class="actions-cell">
                        <button class="btn-icon view" title="عرض التفاصيل" onclick="openView(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <button class="btn-icon edit" title="تعديل" onclick="openEdit(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <button class="btn-icon toggle-on" title="إيقاف الحساب" onclick="toggleStatus(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                        </button>
                    </div>
                </td>
            </tr>
            <tr data-status="inactive" data-role="وحدة السجلات والتوثيق"
                data-name="خالد منصور" data-email="khaled@tripolizoo.ly"
                data-phone="+218 94 444 5555" data-join="2021-06-20" data-color="c3">
                <td>
                    <div class="emp-cell">
                        <div class="emp-avatar c3">خ</div>
                        <div class="emp-info">
                            <strong>خالد منصور</strong>
                            <span>khaled@tripolizoo.ly</span>
                        </div>
                    </div>
                </td>
                <td>وحدة السجلات والتوثيق</td>
                <td dir="ltr" style="text-align:right;">+218 94 444 5555</td>
                <td>20 يونيو 2021</td>
                <td><span class="badge inactive">غير نشط</span></td>
                <td>
                    <div class="actions-cell">
                        <button class="btn-icon view" title="عرض التفاصيل" onclick="openView(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <button class="btn-icon edit" title="تعديل" onclick="openEdit(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <button class="btn-icon toggle-off" title="تفعيل الحساب" onclick="toggleStatus(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="empty-state" id="emptyState">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
        <p>لا توجد نتائج مطابقة للبحث</p>
    </div>
</div>

<!-- ══════════════════════════════════
     VIEW MODAL
══════════════════════════════════ -->
<div class="modal-overlay" id="viewModal">
    <div class="modal-box">
        <div class="view-emp-header">
            <div class="view-emp-avatar" id="viewAvatar" style="background: linear-gradient(135deg, #E8651A, #c0510d);">أ</div>
            <div class="view-emp-title">
                <h4 id="viewName">-</h4>
                <p id="viewEmail">-</p>
            </div>
        </div>
        <div class="modal-body-pad">
            <div class="view-grid">
                <div class="view-field">
                    <label>الدور الوظيفي</label>
                    <span id="viewRole">-</span>
                </div>
                <div class="view-field">
                    <label>الحالة</label>
                    <span id="viewStatus">-</span>
                </div>
                <div class="view-field">
                    <label>رقم الهاتف</label>
                    <span id="viewPhone" dir="ltr">-</span>
                </div>
                <div class="view-field">
                    <label>تاريخ الانضمام</label>
                    <span id="viewJoin">-</span>
                </div>
                <div class="view-field" id="viewGroupField" style="display: none;">
                    <label>المجموعة المسندة</label>
                    <span id="viewGroup">-</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('viewModal')">إغلاق</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════
     EDIT MODAL
══════════════════════════════════ -->
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>تعديل بيانات الموظف</h3>
            <button class="btn-close-x" onclick="closeModal('editModal')">&times;</button>
        </div>
        <div class="modal-body-pad">
            <div class="form-row">
                <div class="form-group">
                    <label>الاسم الكامل</label>
                    <input type="text" id="editName" class="form-input">
                </div>
                <div class="form-group">
                    <label>الدور الوظيفي</label>
                    <select id="editRole" class="form-input" onchange="toggleEditGroupField()">
                        <option value="">اختر الدور</option>
                        <option value="مشرف مجموعة">مشرف مجموعة</option>
                        <option value="طبيب بيطري">طبيب بيطري</option>
                        <option value="رئيس وحدة الرعاية">رئيس وحدة الرعاية</option>
                        <option value="وحدة السجلات والتوثيق">وحدة السجلات والتوثيق</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" id="editEmail" class="form-input" dir="ltr">
            </div>
            <div class="form-group">
                <label>رقم الهاتف</label>
                <input type="text" id="editPhone" class="form-input" dir="ltr">
            </div>
            <div class="form-group">
                <label>الحالة</label>
                <select id="editStatus" class="form-input">
                    <option value="active">نشط</option>
                    <option value="inactive">غير نشط</option>
                </select>
            </div>
            <div class="form-group" id="editGroupField" style="display: none;">
                <label>المجموعة المسندة</label>
                <select id="editGroup" class="form-input">
                    <option value="">اختر المجموعة</option>
                    <option value="مجموعة الثديات الكبرى">مجموعة الثديات الكبرى</option>
                    <option value="مجموعة قرود ورئيسات">مجموعة قرود ورئيسات</option>
                    <option value="مجموعة طيور">مجموعة طيور</option>
                    <option value="مجموعة الزواحف">مجموعة الزواحف</option>
                    <option value="مجموعة اكلات العشب">مجموعة اكلات العشب</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-save" onclick="saveEdit()">حفظ التعديلات</button>
            <button class="btn-cancel" onclick="closeModal('editModal')">إلغاء</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════
     ADD MODAL
══════════════════════════════════ -->
<div class="modal-overlay" id="addModal">
    <div class="modal-box">
        <div class="modal-head">
            <h3>إضافة موظف جديد</h3>
            <button class="btn-close-x" onclick="closeModal('addModal')">&times;</button>
        </div>
        <div class="modal-body-pad">
            <div class="form-row">
                <div class="form-group">
                    <label>الاسم الكامل</label>
                    <input type="text" id="addName" class="form-input" placeholder="مثال: محمد علي">
                </div>
                <div class="form-group">
                    <label>الدور الوظيفي</label>
                    <select id="addRole" class="form-input" onchange="toggleGroupField()">
                        <option value="">اختر الدور</option>
                        <option value="مشرف مجموعة">مشرف مجموعة</option>
                        <option value="طبيب بيطري">طبيب بيطري</option>
                        <option value="رئيس وحدة الرعاية">رئيس وحدة الرعاية</option>
                        <option value="وحدة السجلات والتوثيق">وحدة السجلات والتوثيق</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" id="addEmail" class="form-input" dir="ltr" placeholder="name@tripolizoo.ly">
            </div>
            <div class="form-group">
                <label>رقم الهاتف</label>
                <input type="text" id="addPhone" class="form-input" dir="ltr" placeholder="+218 9X XXX XXXX">
            </div>
            <div class="form-group">
                <label>الحالة</label>
                <select id="addStatus" class="form-input">
                    <option value="active">نشط</option>
                    <option value="inactive">غير نشط</option>
                </select>
            </div>
            <div class="form-group" id="addGroupField" style="display: none;">
                <label>المجموعة المسندة</label>
                <select id="addGroup" class="form-input">
                    <option value="">اختر المجموعة</option>
                    <option value="مجموعة الثديات الكبرى">مجموعة الثديات الكبرى</option>
                    <option value="مجموعة قرود ورئيسات">مجموعة قرود ورئيسات</option>
                    <option value="مجموعة طيور">مجموعة طيور</option>
                    <option value="مجموعة الزواحف">مجموعة الزواحف</option>
                    <option value="مجموعة اكلات العشب">مجموعة اكلات العشب</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-save" onclick="addEmployee()">إضافة الموظف</button>
            <button class="btn-cancel" onclick="closeModal('addModal')">إلغاء</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

@endsection

@section('scripts')
<script>
    const avatarColors = ['c1','c2','c3','c4'];
    const avatarGradients = {
        c1: 'linear-gradient(135deg, #E8651A, #c0510d)',
        c2: 'linear-gradient(135deg, #2E7D32, #1B5E20)',
        c3: 'linear-gradient(135deg, #0284C7, #01579B)',
        c4: 'linear-gradient(135deg, #7C3AED, #5B21B6)',
    };
    let currentEditRow = null;
    let colorIndex = 0;

    /* ── Modal helpers ── */
    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    /* ── Toast ── */
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2800);
    }

    /* ── Search & Filter ── */
    function filterTable() {
        const q = document.getElementById('searchInput').value.trim().toLowerCase();
        const st = document.getElementById('statusFilter').value;
        const rl = document.getElementById('roleFilter').value;
        const rows = document.querySelectorAll('#empTbody tr');
        let visible = 0;

        rows.forEach(row => {
            const name  = row.dataset.name.toLowerCase();
            const email = row.dataset.email.toLowerCase();
            const okQ   = !q || name.includes(q) || email.includes(q);
            const okSt  = st === 'all' || row.dataset.status === st;
            const okRl  = rl === 'all' || row.dataset.role === rl;
            const show  = okQ && okSt && okRl;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('emptyState').style.display = visible === 0 ? 'block' : 'none';
    }

    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);
    document.getElementById('roleFilter').addEventListener('change', filterTable);

    /* ── View ── */
    function openView(btn) {
        const r = btn.closest('tr');
        document.getElementById('viewName').textContent  = r.dataset.name;
        document.getElementById('viewEmail').textContent = r.dataset.email;
        document.getElementById('viewRole').textContent  = r.dataset.role;
        document.getElementById('viewPhone').textContent = r.dataset.phone;
        document.getElementById('viewJoin').textContent  = r.dataset.join;

        // Show group field if applicable
        const groupField = document.getElementById('viewGroupField');
        if (r.dataset.group) {
            groupField.style.display = 'flex';
            document.getElementById('viewGroup').textContent = r.dataset.group;
        } else {
            groupField.style.display = 'none';
        }

        const badge = r.querySelector('.badge');
        const isActive = r.dataset.status === 'active';
        document.getElementById('viewStatus').innerHTML =
            `<span class="badge ${isActive ? 'active' : 'inactive'}">${badge.textContent}</span>`;

        const av = document.getElementById('viewAvatar');
        av.textContent = r.dataset.name[0];
        av.style.background = avatarGradients[r.dataset.color] || avatarGradients.c1;

        openModal('viewModal');
    }

    /* ── Toggle Edit Group Field ── */
    function toggleEditGroupField() {
        const role = document.getElementById('editRole').value;
        const groupField = document.getElementById('editGroupField');
        if (role === 'طبيب بيطري' || role === 'مشرف مجموعة') {
            groupField.style.display = 'block';
        } else {
            groupField.style.display = 'none';
        }
    }

    /* ── Edit ── */
    function openEdit(btn) {
        currentEditRow = btn.closest('tr');
        const r = currentEditRow;
        document.getElementById('editName').value  = r.dataset.name;
        document.getElementById('editEmail').value = r.dataset.email;
        document.getElementById('editPhone').value = r.dataset.phone;
        document.getElementById('editRole').value  = r.dataset.role;
        document.getElementById('editStatus').value = r.dataset.status || 'active';
        document.getElementById('editGroup').value = r.dataset.group || '';
        toggleEditGroupField();
        openModal('editModal');
    }

    function saveEdit() {
        if (!currentEditRow) return;
        const name  = document.getElementById('editName').value.trim();
        const email = document.getElementById('editEmail').value.trim();
        const phone = document.getElementById('editPhone').value.trim();
        const role  = document.getElementById('editRole').value;
        const status = document.getElementById('editStatus').value;
        const group = document.getElementById('editGroup').value;
        if (!name || !email || !role) { showToast('⚠️ الاسم والبريد والدور مطلوبة'); return; }
        if ((role === 'طبيب بيطري' || role === 'مشرف مجموعة') && !group) { showToast('⚠️ يجب اختيار المجموعة لهذا الدور'); return; }

        // Update data attrs
        currentEditRow.dataset.name  = name;
        currentEditRow.dataset.email = email;
        currentEditRow.dataset.phone = phone;
        currentEditRow.dataset.role  = role;
        currentEditRow.dataset.status = status;
        currentEditRow.dataset.group = group || '';

        // Update DOM
        currentEditRow.querySelector('.emp-info strong').textContent = name;
        currentEditRow.querySelector('.emp-info span').textContent   = email;
        currentEditRow.querySelector('.emp-avatar').textContent      = name[0];
        currentEditRow.cells[1].textContent = role;
        currentEditRow.cells[2].textContent = phone;

        // Update status badge
        const badge = currentEditRow.querySelector('.badge');
        badge.textContent = status === 'active' ? 'نشط' : 'غير نشط';
        badge.className = 'badge ' + status;

        closeModal('editModal');
        showToast('✅ تم حفظ التعديلات بنجاح');
    }

    /* ── Toggle Status ── */
    function toggleStatus(btn) {
        const row = btn.closest('tr');
        const isActive = row.dataset.status === 'active';
        row.dataset.status = isActive ? 'inactive' : 'active';

        const badge = row.querySelector('.badge');
        badge.textContent = isActive ? 'غير نشط' : 'نشط';
        badge.className   = 'badge ' + (isActive ? 'inactive' : 'active');

        btn.className = isActive ? 'btn-icon toggle-off' : 'btn-icon toggle-on';
        btn.title     = isActive ? 'تفعيل الحساب' : 'إيقاف الحساب';

        filterTable();
        showToast(isActive ? '🔴 تم إيقاف الحساب' : '🟢 تم تفعيل الحساب');
    }

    /* ── Toggle Group Field ── */
    function toggleGroupField() {
        const role = document.getElementById('addRole').value;
        const groupField = document.getElementById('addGroupField');
        if (role === 'طبيب بيطري' || role === 'مشرف مجموعة') {
            groupField.style.display = 'block';
        } else {
            groupField.style.display = 'none';
        }
    }

    /* ── Add Employee ── */
    function addEmployee() {
        const name  = document.getElementById('addName').value.trim();
        const email = document.getElementById('addEmail').value.trim();
        const phone = document.getElementById('addPhone').value.trim();
        const role  = document.getElementById('addRole').value;
        const status = document.getElementById('addStatus').value;
        const group = document.getElementById('addGroup').value;
        if (!name || !email || !role) { showToast('⚠️ الاسم والبريد والدور مطلوبة'); return; }
        if ((role === 'طبيب بيطري' || role === 'مشرف مجموعة') && !group) { showToast('⚠️ يجب اختيار المجموعة لهذا الدور'); return; }

        const color = avatarColors[colorIndex % avatarColors.length];
        colorIndex++;

        const today = new Date().toLocaleDateString('ar-LY', { day: 'numeric', month: 'long', year: 'numeric' });

        const tr = document.createElement('tr');
        tr.dataset.status = status;
        tr.dataset.role   = role;
        tr.dataset.name   = name;
        tr.dataset.email  = email;
        tr.dataset.phone  = phone;
        tr.dataset.join   = today;
        tr.dataset.color  = color;
        tr.dataset.group  = group || '';

        tr.innerHTML = `
            <td>
                <div class="emp-cell">
                    <div class="emp-avatar ${color}" style="background: ${avatarGradients[color]}">${name[0]}</div>
                    <div class="emp-info">
                        <strong>${name}</strong>
                        <span>${email}</span>
                    </div>
                </div>
            </td>
            <td>${role}</td>
            <td dir="ltr" style="text-align:right;">${phone}</td>
            <td>${today}</td>
            <td><span class="badge ${status}">${status === 'active' ? 'نشط' : 'غير نشط'}</span></td>
            <td>
                <div class="actions-cell">
                    <button class="btn-icon view" title="عرض" onclick="openView(this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                    <button class="btn-icon edit" title="تعديل" onclick="openEdit(this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </button>
                    <button class="btn-icon ${status === 'active' ? 'toggle-on' : 'toggle-off'}" title="${status === 'active' ? 'إيقاف الحساب' : 'تفعيل الحساب'}" onclick="toggleStatus(this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                    </button>
                </div>
            </td>`;

        document.getElementById('empTbody').appendChild(tr);

        // Update count
        const count = document.querySelectorAll('#empTbody tr').length;
        document.getElementById('empCount').textContent = count;

        // Reset form
        ['addName','addEmail','addPhone','addStatus','addGroup'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('addRole').selectedIndex = 0;
        document.getElementById('addGroupField').style.display = 'none';

        closeModal('addModal');
        filterTable();
        showToast('✅ تمت إضافة الموظف بنجاح');
    }

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(ov => {
        ov.addEventListener('click', e => { if (e.target === ov) ov.classList.remove('show'); });
    });
</script>
@endsection
