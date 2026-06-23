@extends($__layout ?? 'admin.layout')
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

    .modal-form-error {
        display: none;
        margin: 0 1.5rem 1rem;
        padding: 12px 14px;
        border-radius: 10px;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
        font-size: 0.88rem;
        font-weight: 700;
        line-height: 1.6;
    }

    .modal-form-error.show { display: block; }

    .form-input.input-error {
        border-color: #EF4444 !important;
        background: #FFF5F5;
    }

    .field-error {
        margin-top: 6px;
        font-size: 0.78rem;
        color: #DC2626;
        font-weight: 700;
    }
</style>
@endsection

@section('content')

<!-- Top Card: Header + Filters -->
<div class="top-card">
    <!-- Page Header -->
    <div class="page-header" style="justify-content:flex-end;">
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
            @foreach($roleOptions as $roleOption)
            <option value="{{ $roleOption }}">{{ $roleOption }}</option>
            @endforeach
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
                <th>تاريخ الانضمام</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody id="empTbody">
            @forelse($employees as $employee)
            <tr data-id="{{ $employee->id }}"
                data-status="{{ $employee->status }}"
                data-role="{{ $employee->role }}"
                data-name="{{ $employee->name }}"
                data-email="{{ $employee->email }}"
                data-join="{{ $employee->joined_at?->format('d/m/Y') ?? '—' }}"
                data-group="{{ $employee->assigned_group ?? '' }}"
                data-group-id="{{ $employee->animal_group_id ?? '' }}">
                <td>
                    <div class="emp-info">
                        <strong>{{ $employee->name }}</strong>
                        <span>{{ $employee->email }}</span>
                    </div>
                </td>
                <td>{{ $employee->role }}</td>
                <td>{{ $employee->joined_at?->format('d/m/Y') ?? '—' }}</td>
                <td><span class="badge {{ $employee->status }}">{{ $employee->status === 'active' ? 'نشط' : 'غير نشط' }}</span></td>
                <td>
                    <div class="actions-cell">
                        <button type="button" class="btn-icon view" title="عرض التفاصيل" onclick="openView(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <button type="button" class="btn-icon edit" title="تعديل" onclick="openEdit(this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <form method="POST" action="{{ route('admin.employees.toggle', $employee) }}" class="js-employee-toggle-form" style="display:inline;" data-active="{{ $employee->status === 'active' ? '1' : '0' }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-icon {{ $employee->status === 'active' ? 'toggle-on' : 'toggle-off' }}" title="{{ $employee->status === 'active' ? 'إيقاف الحساب' : 'تفعيل الحساب' }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem;">لا يوجد موظفون مسجلون بعد</td>
            </tr>
            @endforelse
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
            <button type="button" class="btn-cancel" onclick="closeModal('viewModal')">إغلاق</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════
     EDIT MODAL
══════════════════════════════════ -->
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <form method="POST" id="editForm" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_context" value="edit">
            <input type="hidden" name="_employee_id" id="editEmployeeId" value="{{ old('_employee_id') }}">
            <div class="modal-head">
                <h3>تعديل بيانات الموظف</h3>
                <button type="button" class="btn-close-x" onclick="closeModal('editModal')">&times;</button>
            </div>
            <div id="editFormError" class="modal-form-error @if($errors->any() && old('form_context') === 'edit') show @endif">
                @if($errors->any() && old('form_context') === 'edit')
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                @endif
            </div>
            <div class="modal-body-pad">
                <div class="form-row">
                    <div class="form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" name="name" id="editName" class="form-input @error('name') input-error @enderror" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>الدور الوظيفي</label>
                        <select name="role" id="editRole" class="form-input" onchange="toggleEditGroupField()" required>
                            <option value="">اختر الدور</option>
                            @foreach($roleOptions as $roleOption)
                            <option value="{{ $roleOption }}" @selected(old('role') === $roleOption)>{{ $roleOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" id="editEmail" class="form-input @error('email') input-error @enderror" dir="ltr" value="{{ old('email') }}" required>
                    @error('email')
                        @if(old('form_context') === 'edit')
                            <p class="field-error">{{ $message }}</p>
                        @endif
                    @enderror
                </div>
                <div class="form-group">
                    <label>الحالة</label>
                    <select name="status" id="editStatus" class="form-input">
                        <option value="active" @selected(old('status', 'active') === 'active')>نشط</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>غير نشط</option>
                    </select>
                </div>
                <div class="form-group" id="editGroupField" style="display: none;">
                    <label>المجموعة المسندة</label>
                    <select name="animal_group_id" id="editGroup" class="form-input">
                        <option value="">اختر المجموعة</option>
                        @foreach($groupRecords as $group)
                        <option value="{{ $group->id }}" @selected((string) old('animal_group_id') === (string) $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save">حفظ التعديلات</button>
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════
     ADD MODAL
══════════════════════════════════ -->
<div class="modal-overlay" id="addModal">
    <div class="modal-box">
        <form method="POST" action="{{ route('admin.employees.store') }}" id="addForm">
            @csrf
            <input type="hidden" name="form_context" value="add">
            <div class="modal-head">
                <h3>إضافة موظف جديد</h3>
                <button type="button" class="btn-close-x" onclick="closeModal('addModal')">&times;</button>
            </div>
            <div id="addFormError" class="modal-form-error @if($errors->any() && old('form_context') === 'add') show @endif">
                @if($errors->any() && old('form_context') === 'add')
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                @endif
            </div>
            <div class="modal-body-pad">
                <div class="form-row">
                    <div class="form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" name="name" id="addName" class="form-input @error('name') input-error @enderror" value="{{ old('name') }}" placeholder="مثال: محمد علي" required>
                    </div>
                    <div class="form-group">
                        <label>الدور الوظيفي</label>
                        <select name="role" id="addRole" class="form-input" onchange="toggleGroupField()" required>
                            <option value="">اختر الدور</option>
                            @foreach($createRoleOptions ?? $roleOptions as $roleOption)
                            <option value="{{ $roleOption }}" @selected(old('role') === $roleOption)>{{ $roleOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" id="addEmail" class="form-input @error('email') input-error @enderror" dir="ltr" value="{{ old('email') }}" placeholder="name@tripolizoo.ly" required>
                    @error('email')
                        @if(old('form_context') === 'add')
                            <p class="field-error">{{ $message }}</p>
                        @endif
                    @enderror
                    <p style="margin-top:6px;font-size:0.78rem;color:var(--text-muted);font-weight:600;">سيُرسل إلى هذا البريد رسالة ترحيب باسم المنصة مع بيانات الدخول.</p>
                </div>
                <div class="form-group">
                    <label>الحالة</label>
                    <select name="status" id="addStatus" class="form-input">
                        <option value="active" @selected(old('status', 'active') === 'active')>نشط</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>غير نشط</option>
                    </select>
                </div>
                <div class="form-group" id="addGroupField" style="display: none;">
                    <label>المجموعة المسندة</label>
                    <select name="animal_group_id" id="addGroup" class="form-input">
                        <option value="">اختر المجموعة</option>
                        @foreach($groupRecords as $group)
                        <option value="{{ $group->id }}" @selected((string) old('animal_group_id') === (string) $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-save">إضافة الموظف</button>
                <button type="button" class="btn-cancel" onclick="closeModal('addModal')">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast" style="display:none;"></div>

@endsection

@section('scripts')
<script>
    const ROLES_WITH_GROUP = ['الطبيب البيطري', 'مشرف المجموعة'];
    const employeeUpdateBase = @json(url('/admin/employees'));

    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    function filterTable() {
        const q = document.getElementById('searchInput').value.trim().toLowerCase();
        const st = document.getElementById('statusFilter').value;
        const rl = document.getElementById('roleFilter').value;
        const rows = document.querySelectorAll('#empTbody tr[data-id]');
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

        document.getElementById('emptyState').style.display = visible === 0 && rows.length > 0 ? 'block' : 'none';
    }

    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);
    document.getElementById('roleFilter').addEventListener('change', filterTable);

    function openView(btn) {
        const r = btn.closest('tr');
        document.getElementById('viewName').textContent  = r.dataset.name;
        document.getElementById('viewEmail').textContent = r.dataset.email;
        document.getElementById('viewRole').textContent  = r.dataset.role;
        document.getElementById('viewJoin').textContent  = r.dataset.join;

        const groupField = document.getElementById('viewGroupField');
        if (r.dataset.group) {
            groupField.style.display = 'flex';
            document.getElementById('viewGroup').textContent = r.dataset.group;
        } else {
            groupField.style.display = 'none';
        }

        const isActive = r.dataset.status === 'active';
        document.getElementById('viewStatus').innerHTML =
            `<span class="badge ${isActive ? 'active' : 'inactive'}">${isActive ? 'نشط' : 'غير نشط'}</span>`;

        openModal('viewModal');
    }

    function roleNeedsGroup(role) {
        return ROLES_WITH_GROUP.includes(role);
    }

    function toggleEditGroupField() {
        const role = document.getElementById('editRole').value;
        document.getElementById('editGroupField').style.display = roleNeedsGroup(role) ? 'block' : 'none';
    }

    function openEdit(btn) {
        const r = btn.closest('tr');
        document.getElementById('editEmployeeId').value = r.dataset.id;
        document.getElementById('editForm').action = employeeUpdateBase + '/' + r.dataset.id;
        document.getElementById('editName').value  = r.dataset.name;
        document.getElementById('editEmail').value = r.dataset.email;
        document.getElementById('editRole').value  = r.dataset.role;
        document.getElementById('editStatus').value = r.dataset.status || 'active';
        document.getElementById('editGroup').value = r.dataset.groupId || '';
        toggleEditGroupField();
        openModal('editModal');
    }

    function toggleGroupField() {
        const role = document.getElementById('addRole').value;
        document.getElementById('addGroupField').style.display = roleNeedsGroup(role) ? 'block' : 'none';
    }

    document.getElementById('addForm').addEventListener('submit', function(e) {
        const role = document.getElementById('addRole').value;
        const group = document.getElementById('addGroup').value;
        if (roleNeedsGroup(role) && !group) {
            e.preventDefault();
            showAdminToast('يجب اختيار المجموعة لهذا الدور', 'error');
        }
    });

    bindAdminConfirmForms('.js-employee-toggle-form', (form) => {
        return form.dataset.active === '1'
            ? 'هل أنت متأكد من تعطيل الحساب؟'
            : 'هل أنت متأكد من تفعيل الحساب؟';
    });

    document.querySelectorAll('.modal-overlay').forEach(ov => {
        ov.addEventListener('click', e => { if (e.target === ov) ov.classList.remove('show'); });
    });

    @if(old('form_context') === 'add')
    document.addEventListener('DOMContentLoaded', function () {
        openModal('addModal');
        toggleGroupField();
    });
    @elseif(old('form_context') === 'edit')
    document.addEventListener('DOMContentLoaded', function () {
        const employeeId = @json(old('_employee_id'));
        if (employeeId) {
            document.getElementById('editForm').action = employeeUpdateBase + '/' + employeeId;
        }
        openModal('editModal');
        toggleEditGroupField();
    });
    @endif
</script>
@endsection
