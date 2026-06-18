@extends($__layout ?? 'vet.layout')
@section('title', 'إدارة الحجر الصحي | المستشفى البيطري')
@section('page_title', 'إدارة الحجر الصحي')

@section('styles')
<style>
    .page-title-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
    .page-title-wrap { display:flex; align-items:center; gap:12px; }
    .title-icon { width:40px; height:40px; border-radius:12px; background:#e6f4ea; color:#1a4a2e; display:flex; align-items:center; justify-content:center; }
    .page-title-wrap h2 { font-size:1.3rem; font-weight:800; color:#0f172a; margin:0; }
    .btn-refresh { display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:700; color:#334155; cursor:pointer; transition:all 0.2s; }
    .btn-refresh:hover { background:#f8fafc; }

    .btn-add { display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#16a34a; border:none; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; color:#fff; cursor:pointer; transition:all 0.2s; box-shadow:0 2px 4px rgba(22,163,74,0.2); }
    .btn-add:hover { background:#15803d; box-shadow:0 4px 8px rgba(22,163,74,0.3); }

    .tabs-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:0.8rem 1.2rem; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; }
    .segmented-tabs { display:inline-flex; background:#f1f5f9; padding:5px; border-radius:10px; gap:4px; }
    .seg-tab { background:transparent; border:none; padding:9px 24px; border-radius:7px; font-family:'Cairo',sans-serif; font-size:0.9rem; font-weight:800; color:#64748b; cursor:pointer; transition:all 0.2s; }
    .seg-tab:hover { color:#1a4a2e; }
    .seg-tab.active { background:#fff; color:#1a4a2e; box-shadow:0 2px 4px rgba(0,0,0,0.07); }
    .tab-content { display:none; }
    .tab-content.active { display:block; animation:fadeIn 0.25s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(5px); } to { opacity:1; transform:translateY(0); } }

    .table-card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; margin-bottom:2rem; }
    .table-card-header { padding:1.1rem 1.5rem; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f1f5f9; }
    .table-card-title { font-size:1rem; font-weight:800; color:#0f172a; }

    .custom-table { width:100%; border-collapse:collapse; text-align:right; }
    .custom-table thead th { background:#F8FAFC; color:#64748b; font-size:0.78rem; font-weight:800; padding:13px 18px; border-bottom:1px solid #e2e8f0; }
    .custom-table tbody tr { transition:background 0.15s; }
    .custom-table tbody tr:hover { background:#fafbfc; }
    .custom-table tbody td { padding:15px 18px; border-bottom:1px solid #f1f5f9; font-size:0.9rem; font-weight:600; color:#1e293b; vertical-align:middle; }
    .custom-table tbody tr:last-child td { border-bottom:none; }

    .badge { padding:5px 11px; border-radius:999px; font-size:0.73rem; font-weight:700; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; }
    .badge .dot { width:6px; height:6px; border-radius:50%; }
    .badge-followup   { background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; }
    .badge-followup .dot { background:#3b82f6; }
    .badge-cleared    { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-cleared .dot { background:#22c55e; }
    .badge-failed     { background:#fef2f2; color:#e11d48; border:1px solid #fecdd3; }
    .badge-failed .dot { background:#ef4444; }

    .case-id { font-family:'Courier New',monospace; font-size:0.74rem; padding:3px 8px; border-radius:6px; font-weight:700; display:inline-block; }
    .case-id-open   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .case-id-closed { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }

    .btn-tbl { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:9px; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; text-decoration:none; transition:all 0.2s; cursor:pointer; box-shadow:0 1px 2px rgba(0,0,0,0.05); }
    .btn-tbl:hover { transform:translateY(-1px); box-shadow:0 3px 8px rgba(0,0,0,0.1); background:#e2e8f0; border-color:#cbd5e1; color:#0f172a; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
    .btn-tbl.edit:hover { color: #E8651A; background: #FFEDD5; border-color: #FED7AA; }
    .btn-tbl.end:hover { color: #DC2626; background: #FEE2E2; border-color: #FECACA; }

    .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); backdrop-filter:blur(5px); z-index:5000; align-items:flex-start; justify-content:center; padding:1.25rem; overflow-y:auto; }
    .modal-backdrop.open { display:flex; }
    .modal-box { background:#fff; border-radius:20px; width:100%; max-width:600px; max-height:none; margin:auto 0; display:flex; flex-direction:column; box-shadow:0 25px 50px rgba(0,0,0,0.15); animation:modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    #detailModal .modal-box { max-width:900px; max-height:calc(100vh - 2.5rem); }
    #detailModal .modal-body { overflow-y:auto; flex:1; min-height:0; }
    @media (max-width: 768px) {
        #detailModal .modal-body > div { grid-template-columns: 1fr !important; }
    }
    @keyframes modalIn { from { transform:translateY(24px) scale(0.97); opacity:0; } to { transform:translateY(0) scale(1); opacity:1; } }
    .modal-header { padding:1.4rem 1.8rem; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; background:#F8FAFC; border-radius:20px 20px 0 0; }
    .modal-header h3 { font-size:1.15rem; font-weight:800; color:#0f172a; margin:0; }
    .modal-close { width:32px; height:32px; border-radius:8px; background:#e2e8f0; border:none; color:#64748b; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.2rem; font-weight:700; transition:all 0.2s; line-height:1; }
    .modal-close:hover { background:#cbd5e1; color:#0f172a; }
    .modal-body { padding:1.8rem; }
    .modal-footer { padding:1.4rem 1.8rem; border-top:1px solid #e2e8f0; display:flex; gap:10px; justify-content:flex-end; background:#F8FAFC; border-radius:0 0 20px 20px; }
    
    .detail-section { margin-bottom:1.4rem; }
    .detail-section h4 { display:flex; align-items:center; gap:8px; font-size:0.9rem; font-weight:800; color:#0f172a; margin-bottom:0.9rem; padding-bottom:8px; border-bottom:2px solid #f1f5f9; }
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .detail-item { display:flex; flex-direction:column; gap:4px; }
    .detail-item label { font-size:0.73rem; color:#64748b; font-weight:700; }
    .detail-item span { font-size:0.86rem; color:#0f172a; font-weight:800; }
    .vet-note { background:#f8fafc; border-right:3px solid #3b82f6; padding:12px 14px; border-radius:8px 0 0 8px; margin-bottom:10px; }
    .note-date { font-size:0.73rem; color:#64748b; font-weight:700; margin-bottom:4px; }
    .note-text { font-size:0.83rem; color:#334155; font-weight:600; line-height:1.5; }
    
    .btn-action-release { padding:9px 18px; background:#16a34a; color:#fff; border:none; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-action-release:hover { background:#15803d; }
    .btn-action-close { padding:9px 18px; background:#e11d48; color:#fff; border:none; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-action-close:hover { background:#be123c; }
    .btn-cancel { padding:9px 18px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:800; cursor:pointer; transition:all 0.2s; }
    .btn-cancel:hover { background:#e2e8f0; }

    /* ═══ FORM ═══ */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: span 2; }
    .form-group label { font-size: 0.8rem; font-weight: 800; color: #374151; }
    .form-group label span.req { color: #ef4444; }
    .form-input, .form-select, .form-textarea { padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #0f172a; background: #fafbff; transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #2d7a47; box-shadow: 0 0 0 3px rgba(45,122,71,0.1); background: #fff; }
    .form-textarea { resize: vertical; min-height: 80px; }
    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(22,163,74,0.3); }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(22,163,74,0.35); }

    .modal-box.wide { max-width: 680px; }
    .form-grid.col-3 { grid-template-columns: 1fr 1fr 1fr; }
    .modal-section { margin-bottom: 1.25rem; grid-column: span 2; }
    .modal-section-title { display: flex; align-items: center; gap: 8px; font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; padding-bottom: 8px; border-bottom: 2px solid #f1f5f9; }
    .modal-section-title .sec-icon { width: 30px; height: 30px; border-radius: 8px; background: #e6f4ea; color: #1a4a2e; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .age-toggle { display: flex; border: 1.5px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
    .age-toggle-btn { flex: 1; padding: 9px 10px; text-align: center; cursor: pointer; font-family: 'Cairo', sans-serif; font-size: 0.78rem; font-weight: 700; color: #64748b; background: #f8fafc; border: none; border-left: 1px solid #e2e8f0; transition: all 0.2s; }
    .age-toggle-btn:last-child { border-left: none; }
    .age-toggle-btn.active { background: #1a4a2e; color: #fff; }
    .cond-block { display: none; flex-direction: column; gap: 1rem; margin-top: 0.75rem; }
    .cond-block.visible { display: flex; }
    .form-input.generated { background: #f0fdf4; color: #16a34a; font-weight: 800; border-color: #bbf7d0; }

    .dialog-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(4px); z-index: 6000; align-items: center; justify-content: center; padding: 1rem; }
    .dialog-backdrop.open { display: flex; }
    .dialog-box { background: #fff; border-radius: 18px; width: 100%; max-width: 460px; box-shadow: 0 30px 80px rgba(0,0,0,0.22); animation: modalIn 0.25s cubic-bezier(0.34,1.56,0.64,1); overflow: hidden; }
    .dialog-icon-wrap { width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.8rem; }
    .dialog-body { padding: 2rem 2rem 1.25rem; text-align: center; }
    .dialog-body h4 { font-size: 1.12rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
    .dialog-body p { font-size: 0.88rem; color: #64748b; font-weight: 600; line-height: 1.7; margin-bottom: 0; }
    .dialog-meta { margin-top: 1rem; padding: 12px 14px; background: #fef2f2; border: 1px solid #fecdd3; border-radius: 10px; text-align: right; }
    .dialog-meta-row { display: flex; justify-content: space-between; gap: 12px; font-size: 0.82rem; margin-bottom: 6px; }
    .dialog-meta-row:last-child { margin-bottom: 0; }
    .dialog-meta-label { color: #94a3b8; font-weight: 700; }
    .dialog-meta-value { color: #0f172a; font-weight: 800; }
    .dialog-footer { padding: 1rem 1.5rem 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: center; }
    .btn-submit-red { padding: 10px 22px; background: linear-gradient(135deg, #be123c, #e11d48); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(225,29,72,0.25); }
    .btn-submit-red:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(225,29,72,0.3); }
</style>
@endsection

@php $vetBase = ($readOnly ?? false) ? '/director/vet' : '/vet'; @endphp

@section('content')

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecdd3;color:#b91c1c;padding:12px 16px;border-radius:10px;margin-bottom:1rem;font-weight:700;">
    <ul style="margin:0;padding-right:1.2rem;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:1rem;font-weight:700;">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fecdd3;color:#b91c1c;padding:12px 16px;border-radius:10px;margin-bottom:1rem;font-weight:700;">
    {{ session('error') }}
</div>
@endif

{{-- ═══ TABS CARD ═══ --}}
<div class="tabs-card">
    <div class="segmented-tabs">
        <button class="seg-tab active" onclick="switchTab(event,'tab-followup')">قيد المتابعة</button>
        <button class="seg-tab" onclick="switchTab(event,'tab-cleared')">تم الإفراج الصحي</button>
        <button class="seg-tab" onclick="switchTab(event,'tab-failed')">لم تجتز الحجر</button>
    </div>
    
    @unless($readOnly ?? false)
    <button class="btn-add" onclick="openAddModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        إضافة للحجر
    </button>
    @endunless
</div>

{{-- ═══ FILTER CARD ═══ --}}
<div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.2rem; margin-bottom: 1.5rem; display: flex; justify-content: flex-start; align-items: center;">
    <div style="display: flex; gap: 15px; width: 100%; max-width: 800px;">
        <div style="flex: 1; position: relative;">
            <svg style="position: absolute; right: 12px; top: 11px; color: #94a3b8;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="بحث برقم الحيوان أو نوعه..." id="quarantineSearch" style="width: 100%; padding: 10px 35px 10px 15px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 600; outline: none; color: #0f172a;">
        </div>
    </div>
</div>

{{-- ════ TAB 1: قيد المتابعة ════ --}}
<div id="tab-followup" class="tab-content active">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">الحيوانات الخاضعة للحجر الصحي وتخضع للتقييم والمراقبة</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>الحيوان</th>
                        <th>النوع</th>
                        <th>المجموعة</th>
                        <th>الطبيب المسؤول</th>
                        <th>تاريخ التسجيل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($followup as $quarantine)
                    @php
                        $animal = $quarantine->animal;
                        $doctor = $quarantine->assignedDoctor($doctorsByGroup ?? collect());
                        $photoUrl = $animal->photo_path ? \Illuminate\Support\Facades\Storage::url($animal->photo_path) : null;
                    @endphp
                    <tr data-search="{{ $quarantine->case_number }} {{ $animal->code }} {{ $animal->name }} {{ $animal->species }}">
                        @include('partials.animal-table-cell', [
                            'name' => $animal->name,
                            'emoji' => '🐾',
                            'image' => $photoUrl,
                            'animalId' => $animal->code,
                        ])
                        <td>{{ $animal->species }}</td>
                        <td>{{ $animal->group }}</td>
                        <td>{{ $doctor?->name ?? '—' }}</td>
                        <td>{{ $quarantine->entry_date->format('Y-m-d') }}</td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="javascript:void(0)" onclick="openModal('{{ $quarantine->case_number }}', 'followup')" class="btn-tbl view" title="عرض التفاصيل">
                                    @include('partials.icon-eye-view')
                                </a>
                                @unless($readOnly ?? false)
                                <a href="javascript:void(0)" onclick="openEditModal('{{ $quarantine->case_number }}')" class="btn-tbl edit" title="تعديل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:#94a3b8;font-weight:700;padding:2rem;">لا توجد حالات قيد المتابعة حالياً</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ════ TAB 2: تم الإفراج الصحي ════ --}}
<div id="tab-cleared" class="tab-content">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">السجل التاريخي للحيوانات التي اجتازت فترة الحجر بنجاح</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>الحيوان</th>
                        <th>النوع</th>
                        <th>المجموعة</th>
                        <th>الطبيب المسؤول</th>
                        <th>تاريخ التسجيل</th>
                        <th>تاريخ الإفراج</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cleared as $quarantine)
                    @php
                        $animal = $quarantine->animal;
                        $doctor = $quarantine->assignedDoctor($doctorsByGroup ?? collect());
                        $photoUrl = $animal->photo_path ? \Illuminate\Support\Facades\Storage::url($animal->photo_path) : null;
                    @endphp
                    <tr data-search="{{ $quarantine->case_number }} {{ $animal->code }} {{ $animal->name }} {{ $animal->species }}">
                        @include('partials.animal-table-cell', [
                            'name' => $animal->name,
                            'emoji' => '🐾',
                            'image' => $photoUrl,
                            'animalId' => $animal->code,
                        ])
                        <td>{{ $animal->species }}</td>
                        <td>{{ $animal->group }}</td>
                        <td>{{ $doctor?->name ?? '—' }}</td>
                        <td>{{ $quarantine->entry_date->format('Y-m-d') }}</td>
                        <td>{{ $quarantine->released_at?->format('Y-m-d') ?? '—' }}</td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="javascript:void(0)" onclick="openModal('{{ $quarantine->case_number }}', 'cleared')" class="btn-tbl view" title="عرض التفاصيل">
                                    @include('partials.icon-eye-view')
                                </a>
                                @unless($readOnly ?? false)
                                <a href="javascript:void(0)" onclick="openEditModal('{{ $quarantine->case_number }}')" class="btn-tbl edit" title="تعديل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#94a3b8;font-weight:700;padding:2rem;">لا توجد حالات مفرج عنها بعد</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ════ TAB 3: لم تجتز الحجر ════ --}}
<div id="tab-failed" class="tab-content">
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">السجل التاريخي للحيوانات التي لم تجتز الحجر الصحي (نقلت للمستشفى أو غيره)</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>الحيوان</th>
                        <th>النوع</th>
                        <th>المجموعة</th>
                        <th>الطبيب المسؤول</th>
                        <th>تاريخ التسجيل</th>
                        <th>تاريخ الإغلاق</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($failed as $quarantine)
                    @php
                        $animal = $quarantine->animal;
                        $doctor = $quarantine->assignedDoctor($doctorsByGroup ?? collect());
                        $photoUrl = $animal->photo_path ? \Illuminate\Support\Facades\Storage::url($animal->photo_path) : null;
                    @endphp
                    <tr data-search="{{ $quarantine->case_number }} {{ $animal->code }} {{ $animal->name }} {{ $animal->species }}">
                        @include('partials.animal-table-cell', [
                            'name' => $animal->name,
                            'emoji' => '🐾',
                            'image' => $photoUrl,
                            'animalId' => $animal->code,
                        ])
                        <td>{{ $animal->species }}</td>
                        <td>{{ $animal->group }}</td>
                        <td>{{ $doctor?->name ?? '—' }}</td>
                        <td>{{ $quarantine->entry_date->format('Y-m-d') }}</td>
                        <td>{{ $quarantine->closed_at?->format('Y-m-d') ?? '—' }}</td>
                        <td>
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="javascript:void(0)" onclick="openModal('{{ $quarantine->case_number }}', 'failed')" class="btn-tbl view" title="عرض التفاصيل">
                                    @include('partials.icon-eye-view')
                                </a>
                                @unless($readOnly ?? false)
                                <a href="javascript:void(0)" onclick="openEditModal('{{ $quarantine->case_number }}')" class="btn-tbl edit" title="تعديل">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                                @endunless
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#94a3b8;font-weight:700;padding:2rem;">لا توجد حالات منتهية بهذه الفئة</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('modals')
{{-- ═══ DETAIL MODAL ═══ --}}
<div class="modal-backdrop" id="detailModal">
    <div class="modal-box" style="background: #f8fafc;">
        <div class="modal-header" style="background: transparent; border-bottom: none; display: flex; justify-content: center; position: relative; padding: 1.25rem 1.5rem 0.5rem; flex-shrink: 0;">
            <h3 style="font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0;">تفاصيل حيوان في الحجر — <span id="modalCaseId">QR-2025-001</span></h3>
            <button class="modal-close" onclick="closeModal()" style="position: absolute; left: 1.5rem; top: 1.5rem;">✕</button>
        </div>
        <div class="modal-body" style="padding: 1.5rem 2rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                
                <!-- Right Column (Animal Data) -->
                <div style="background: #fff; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center;">بيانات الحيوان</h4>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">رقم الحجر</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_id">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">الكود الفريد</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_code">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">الفصيلة / النوع</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_type">—</span>
                    </div>
                    <div id="mdl_nameRow" style="display: none; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">اسم الحيوان</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_animalName">—</span>
                    </div>
                    <div id="mdl_markRow" style="display: none; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">العلامة المميزة</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_mark">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">الجنس</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_gender">—</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">المجموعة الحيوانية</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_group">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">العمر</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_age">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">الحالة الصحية الأولية</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_initialHealth">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">مصدر الإحضار</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_source">—</span>
                    </div>
                    <div id="mdl_releasedRow" style="display: none; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">تاريخ الإفراج</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_releasedAt">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">الطبيب المسؤول</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_vet">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">تاريخ دخول الحجر</span>
                        <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_entryDate">—</span>
                    </div>

                    
                    <div style="margin-top: 1rem;">
                        <span style="color: #64748b; font-size: 0.85rem; font-weight: 700; display: block; text-align: center; margin-bottom: 0.5rem;">ملاحظات أولية</span>
                        <div style="background: #f8fafc; padding: 10px; border-radius: 8px; text-align: center; font-size: 0.9rem; font-weight: 700; color: #334155;" id="mdl_initialNotes">
                            —
                        </div>
                    </div>

                    <div id="mdl_failedSection" style="display: none; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 2px solid #fecdd3;">
                        <h4 style="font-size: 1rem; font-weight: 800; color: #be123c; margin-bottom: 1rem; text-align: center;">بيانات إنهاء الحجر</h4>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;">
                            <span style="color: #64748b; font-size: 0.9rem; font-weight: 700;">تاريخ الإغلاق</span>
                            <span style="color: #0f172a; font-size: 0.95rem; font-weight: 800;" id="mdl_closedAt">—</span>
                        </div>
                        <div style="margin-bottom: 0.9rem;">
                            <span style="color: #64748b; font-size: 0.85rem; font-weight: 700; display: block; margin-bottom: 0.45rem;">سبب الإنهاء</span>
                            <div style="background: #fef2f2; padding: 10px 12px; border-radius: 8px; font-size: 0.9rem; font-weight: 700; color: #991b1b; border: 1px solid #fecdd3;" id="mdl_closeReason">—</div>
                        </div>
                        <div>
                            <span style="color: #64748b; font-size: 0.85rem; font-weight: 700; display: block; margin-bottom: 0.45rem;">ملاحظات إضافية وتوثيق</span>
                            <div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; font-size: 0.9rem; font-weight: 700; color: #334155; border: 1px solid #e2e8f0; line-height: 1.6; white-space: pre-wrap;" id="mdl_closeNotes">—</div>
                        </div>
                    </div>
                </div>

                <!-- Left Column -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- Doses Card -->
                    <div style="background: #fff; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                        <h4 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center;">الجرعات الوقائية المسجلة</h4>
                        <div id="mdl_doses" style="text-align:center;color:#94a3b8;font-weight:700;font-size:0.9rem;padding:1rem;">
                            لا توجد جرعات مسجلة بعد
                        </div>
                        @if($canAddClinicalRecords ?? false)
                        <form method="POST" id="vaccineForm" style="display:none;margin-top:1rem;padding-top:1rem;border-top:1px dashed #e2e8f0;">
                            @csrf
                            <div class="form-group" style="margin-bottom:0.75rem;">
                                <label style="font-size:0.82rem;font-weight:800;color:#334155;display:block;margin-bottom:6px;">اسم الجرعة <span class="req">*</span></label>
                                <input type="text" name="name" class="form-input" placeholder="مثال: لقاح الحمى القلاعية" required>
                            </div>
                            <div class="form-group" style="margin-bottom:0.75rem;">
                                <label style="font-size:0.82rem;font-weight:800;color:#334155;display:block;margin-bottom:6px;">تاريخ الجرعة <span class="req">*</span></label>
                                <input type="date" name="administered_at" class="form-input" required>
                            </div>
                            <div class="form-group" style="margin-bottom:0.75rem;">
                                <label style="font-size:0.82rem;font-weight:800;color:#334155;display:block;margin-bottom:6px;">ملاحظة</label>
                                <textarea name="note" class="form-textarea" rows="2" placeholder="ملاحظات إضافية..."></textarea>
                            </div>
                            <button type="submit" class="btn-submit" style="width:100%;padding:10px;background:#2563eb;">تسجيل الجرعة</button>
                        </form>
                        @endif
                    </div>

                    <!-- Health Notes Card -->
                    <div style="background: #fff; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02); flex-grow: 1;">
                        <h4 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center;">الملاحظات الصحية</h4>
                        <div id="mdl_healthNotes" style="display: flex; flex-direction: column; gap: 0.8rem;">
                            <div style="background: #f8fafc; padding: 12px 14px; border-radius: 8px; font-size: 0.85rem; color: #334155; font-weight: 700; text-align: center; border: 1px solid #f1f5f9;">
                                —
                            </div>
                        </div>
                        @if($canAddClinicalRecords ?? false)
                        <form method="POST" id="noteForm" style="display:none;margin-top:1rem;padding-top:1rem;border-top:1px dashed #e2e8f0;">
                            @csrf
                            <div class="form-group" style="margin-bottom:0.75rem;">
                                <label style="font-size:0.82rem;font-weight:800;color:#334155;display:block;margin-bottom:6px;">ملاحظة صحية جديدة <span class="req">*</span></label>
                                <textarea name="note" class="form-textarea" rows="3" placeholder="سجل ملاحظة المتابعة الصحية..." required></textarea>
                            </div>
                            <button type="submit" class="btn-submit" style="width:100%;padding:10px;">إضافة الملاحظة</button>
                        </form>
                        @endif
                    </div>

                </div>

            </div>
        </div>
        <div class="modal-footer" id="modalFooterActions" style="background: transparent; border-top: 1px solid #e2e8f0; padding: 1rem 2rem 1.25rem; flex-shrink: 0;">
            <!-- سيتم حقن الأزرار هنا عبر الجافاسكريبت حسب حالة التبويب -->
            <button class="btn-cancel" onclick="closeModal()">إغلاق</button>
        </div>
    </div>
</div>

{{-- ═══ ADD MODAL ═══ --}}
<div class="modal-backdrop" id="addModal">
    <div class="modal-box wide">
        <form method="POST" action="{{ route('quarantine.store') }}" enctype="multipart/form-data" id="addQuarantineForm">
            @csrf
            <input type="hidden" name="age_method" id="add_age_method" value="{{ old('age_method', 'birth') }}">
        <div class="modal-header">
            <h3>📋 إضافة حيوان للحجر الصحي</h3>
            <button type="button" class="modal-close" onclick="closeAddModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>رقم الحيوان <span class="req">*</span></label>
                    <input type="text" class="form-input generated" value="#{{ $nextQuarantineCode }}" readonly>
                </div>
                <div class="form-group">
                    <label>الفصيلة / نوع الحيوان <span class="req">*</span></label>
                    <input type="text" name="species" class="form-input" placeholder="مثال: Gazella subgutturosa" value="{{ old('species') }}" required>
                </div>
                <div class="form-group">
                    <label>اسم الحيوان (اختياري)</label>
                    <input type="text" name="name" class="form-input" placeholder="مثال: غزال ريم" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label>المجموعة الحيوانية <span class="req">*</span></label>
                    <select name="group" class="form-select" required>
                        <option value="" disabled {{ old('group') ? '' : 'selected' }}>اختر المجموعة...</option>
                        @foreach($animalGroups as $group)
                            <option value="{{ $group }}" @selected(old('group') === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>الجنس <span class="req">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>اختر الجنس...</option>
                        <option value="ذكر" @selected(old('gender') === 'ذكر')>ذكر</option>
                        <option value="أنثى" @selected(old('gender') === 'أنثى')>أنثى</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>العلامة المميزة (اختياري)</label>
                    <input type="text" name="distinguishing_marks" class="form-input" placeholder="مثال: أذن يمين مقطوعة جزئياً..." value="{{ old('distinguishing_marks') }}">
                </div>
            </div>

            <div class="modal-section">
                <div class="modal-section-title">
                    <div class="sec-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    بيانات العمر
                </div>
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <label>طريقة تحديد العمر <span class="req">*</span></label>
                    <div class="age-toggle">
                        <button type="button" class="age-toggle-btn active" id="addBtnBirth" onclick="setAge('add','birth')">📅 تاريخ ميلاد معروف</button>
                        <button type="button" class="age-toggle-btn" id="addBtnApprox" onclick="setAge('add','approx')">🔢 عمر تقريبي عند التسجيل</button>
                    </div>
                </div>
                <div class="cond-block visible" id="addBlockBirth">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>تاريخ الميلاد <span class="req">*</span></label>
                            <input type="date" name="birth_date" class="form-input" id="add_birthDate" value="{{ old('birth_date') }}">
                        </div>
                        <div class="form-group" style="align-self: end;">
                            <label style="color:#64748b;">العمر المحسوب</label>
                            <input type="text" class="form-input generated" id="add_computedAge" placeholder="سيُحسب تلقائياً..." disabled>
                        </div>
                    </div>
                </div>
                <div class="cond-block" id="addBlockApprox">
                    <div class="form-grid col-3">
                        <div class="form-group">
                            <label>العمر التقريبي عند التسجيل <span class="req">*</span></label>
                            <input type="number" name="approx_age_value" class="form-input" id="add_approxValue" placeholder="مثال: 4" min="1" value="{{ old('approx_age_value') }}">
                        </div>
                        <div class="form-group">
                            <label>وحدة العمر <span class="req">*</span></label>
                            <select name="approx_age_unit" class="form-select" id="add_approxUnit">
                                <option value="أيام" @selected(old('approx_age_unit') === 'أيام')>أيام</option>
                                <option value="أشهر" @selected(old('approx_age_unit') === 'أشهر')>أشهر</option>
                                <option value="سنوات" @selected(old('approx_age_unit', 'سنوات') === 'سنوات')>سنوات</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="color:#64748b;">العمر الحالي التقريبي</label>
                            <input type="text" class="form-input generated" id="add_currentApproxAge" placeholder="سيُحسب تلقائياً..." disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>الحالة الصحية الأولية <span class="req">*</span></label>
                    <input type="text" name="initial_health_status" class="form-input" placeholder="مثال: إجهاد خفيف من السفر" value="{{ old('initial_health_status') }}" required>
                </div>
                <div class="form-group">
                    <label>مصدر الحيوان / جهة الإحضار <span class="req">*</span></label>
                    <input type="text" name="origin" class="form-input" placeholder="مثال: مركز الحياة البرية..." value="{{ old('origin') }}" required>
                </div>
                <div class="form-group">
                    <label>تاريخ الدخول للحجر <span class="req">*</span></label>
                    <input type="date" name="entry_date" class="form-input" value="{{ old('entry_date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>صورة الحيوان (اختياري)</label>
                    <input type="file" name="photo" class="form-input" accept="image/*" style="padding: 6px;">
                </div>
                <div class="form-group full">
                    <label>ملاحظات أولية (اختياري)</label>
                    <textarea name="initial_notes" class="form-textarea" placeholder="أدخل ملاحظات إضافية حول صحة الحيوان عند دخول الحجر...">{{ old('initial_notes') }}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeAddModal()">إلغاء</button>
            <button type="submit" class="btn-submit">تأكيد الإضافة</button>
        </div>
        </form>
    </div>
</div>

{{-- ═══ EDIT MODAL ═══ --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal-box wide">
        <form method="POST" action="" enctype="multipart/form-data" id="editQuarantineForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="age_method" id="edit_age_method" value="birth">
        <div class="modal-header">
            <h3>✏️ تعديل بيانات الحجر الصحي — <span id="editModalCaseId"></span></h3>
            <button type="button" class="modal-close" onclick="closeEditModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>نوع الحيوان <span class="req">*</span></label>
                    <input type="text" name="species" class="form-input" id="edit_type" required>
                </div>
                <div class="form-group">
                    <label>اسم الحيوان (اختياري)</label>
                    <input type="text" name="name" class="form-input" id="edit_animalName" placeholder="مثال: فهد...">
                </div>
                <div class="form-group">
                    <label>الجنس <span class="req">*</span></label>
                    <select name="gender" class="form-select" id="edit_gender" required>
                        <option value="ذكر">ذكر</option>
                        <option value="أنثى">أنثى</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>العلامة المميزة (اختياري)</label>
                    <input type="text" name="distinguishing_marks" class="form-input" id="edit_mark" placeholder="مثال: بقعة بيضاء على الجبهة...">
                </div>
            </div>

            <div class="modal-section">
                <div class="modal-section-title">
                    <div class="sec-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    بيانات العمر
                </div>
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <label>طريقة تحديد العمر <span class="req">*</span></label>
                    <div class="age-toggle">
                        <button type="button" class="age-toggle-btn active" id="editBtnBirth" onclick="setAge('edit','birth')">📅 تاريخ ميلاد معروف</button>
                        <button type="button" class="age-toggle-btn" id="editBtnApprox" onclick="setAge('edit','approx')">🔢 عمر تقريبي عند التسجيل</button>
                    </div>
                </div>
                <div class="cond-block visible" id="editBlockBirth">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>تاريخ الميلاد <span class="req">*</span></label>
                            <input type="date" name="birth_date" class="form-input" id="edit_birthDate">
                        </div>
                        <div class="form-group" style="align-self: end;">
                            <label style="color:#64748b;">العمر المحسوب</label>
                            <input type="text" class="form-input generated" id="edit_computedAge" placeholder="سيُحسب تلقائياً..." disabled>
                        </div>
                    </div>
                </div>
                <div class="cond-block" id="editBlockApprox">
                    <div class="form-grid col-3">
                        <div class="form-group">
                            <label>العمر التقريبي عند التسجيل <span class="req">*</span></label>
                            <input type="number" name="approx_age_value" class="form-input" id="edit_approxValue" placeholder="مثال: 4" min="1">
                        </div>
                        <div class="form-group">
                            <label>وحدة العمر <span class="req">*</span></label>
                            <select name="approx_age_unit" class="form-select" id="edit_approxUnit">
                                <option value="أيام">أيام</option>
                                <option value="أشهر">أشهر</option>
                                <option value="سنوات">سنوات</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="color:#64748b;">العمر الحالي التقريبي</label>
                            <input type="text" class="form-input generated" id="edit_currentApproxAge" placeholder="سيُحسب تلقائياً..." disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>مصدر الحيوان / جهة الإحضار <span class="req">*</span></label>
                    <input type="text" name="origin" class="form-input" id="edit_source" required>
                </div>
                <div class="form-group">
                    <label>تاريخ الدخول للحجر <span class="req">*</span></label>
                    <input type="date" name="entry_date" class="form-input" id="edit_entryDate" required>
                </div>
                <div class="form-group">
                    <label>تحديث صورة الحيوان</label>
                    <input type="file" name="photo" class="form-input" accept="image/*" style="padding: 6px;">
                </div>
                <div class="form-group full">
                    <label>الملاحظات الأولية</label>
                    <textarea name="initial_notes" class="form-textarea" id="edit_initialNotes" placeholder="ملاحظات إضافية حول صحة الحيوان عند دخول الحجر..."></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeEditModal()">إلغاء</button>
            <button type="submit" class="btn-submit" style="background: #E8651A; box-shadow: 0 4px 12px rgba(232,101,26,0.2);">حفظ التعديلات</button>
        </div>
        </form>
    </div>
</div>

{{-- ═══ END CASE MODAL ═══ --}}
<div class="modal-backdrop" id="endModal">
    <div class="modal-box" style="max-width: 500px;">
        <form method="POST" id="closeQuarantineForm" onsubmit="return confirmCloseQuarantine(event)">
            @csrf
            <div class="modal-header">
                <h3>🚫 إنهاء حالة الحجر الصحي — <span id="endModalCaseId"></span></h3>
                <button type="button" class="modal-close" onclick="closeEndModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full">
                        <label>سبب الإنهاء <span class="req">*</span></label>
                        <select class="form-select" name="close_reason" id="close_reason" required>
                            <option value="" disabled selected>اختر سبب الإنهاء...</option>
                            <option value="نفوق داخل الحجر">نفوق داخل الحجر</option>
                            <option value="إرجاع الحيوان">إرجاع الحيوان</option>
                            <option value="عدم التأقلم">عدم التأقلم</option>
                            <option value="إدخال بالخطأ">إدخال بالخطأ</option>
                            <option value="سبب آخر">سبب آخر</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label>ملاحظات إضافية وتوثيق</label>
                        <textarea class="form-textarea" name="close_notes" id="close_notes" placeholder="أدخل تفاصيل توثيق سبب إنهاء حالة الحجر..."></textarea>
                    </div>
                </div>
                <div style="margin-top: 15px; padding: 10px; background: #FEF2F2; border-left: 3px solid #EF4444; border-radius: 4px; font-size: 0.8rem; color: #991B1B;">
                    <strong>ملاحظة هامة:</strong> بإنهاء هذه الحالة، سينتقل الحيوان لقائمة الحالات المنتهية ولن يتم تخصيص رقم حيوان رسمي له، وسيبقى كمرجع إداري فقط.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEndModal()">إلغاء</button>
                <button type="submit" class="btn-submit" style="background: #E11D48; box-shadow: 0 4px 12px rgba(225,29,72,0.2);">تأكيد الإنهاء</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ CONFIRM CLOSE DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmCloseDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#fef2f2;">🚫</div>
            <h4>تأكيد إنهاء حالة الحجر</h4>
            <p>هل أنت متأكد من إنهاء حالة الحجر الصحي؟<br>سينتقل الحيوان إلى قائمة <strong>«لم تجتز الحجر»</strong> ولن يُخصَّص له رقم حيوان رسمي.</p>
            <div class="dialog-meta">
                <div class="dialog-meta-row">
                    <span class="dialog-meta-label">رقم الحالة</span>
                    <span class="dialog-meta-value" id="confirmCloseCaseId">—</span>
                </div>
                <div class="dialog-meta-row">
                    <span class="dialog-meta-label">سبب الإنهاء</span>
                    <span class="dialog-meta-value" id="confirmCloseReason">—</span>
                </div>
            </div>
        </div>
        <div class="dialog-footer">
            <button type="button" class="btn-cancel" onclick="closeConfirmCloseDialog()">إلغاء</button>
            <button type="button" class="btn-submit-red" onclick="submitCloseQuarantine()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                نعم، إنهاء الحالة
            </button>
        </div>
    </div>
</div>
@endpush

@section('scripts')
<script>
const quarantineDB = @json($quarantineRecords ?? []);
const quarantineReadOnly = @json($readOnly ?? false);
const quarantineCsrf = @json(csrf_token());
const quarantineReleaseUrl = @json(route('quarantine.release', ['quarantine' => '__CASE__']));
const quarantineCloseUrl = @json(route('quarantine.close', ['quarantine' => '__CASE__']));
const quarantineNoteUrl = @json(route('quarantine.notes.store', ['quarantine' => '__CASE__']));
const quarantineVaccineUrl = @json(route('quarantine.vaccines.store', ['quarantine' => '__CASE__']));
const quarantineCanAddClinical = @json($canAddClinicalRecords ?? false);
const quarantineUpdateUrl = @json(route('quarantine.update', ['quarantine' => '__CASE__']));
window.quarantineListUrl = @json($vetBase.'/quarantine');
window.quarantineReadUrl = @json($vetBase.'/quarantine');
window.quarantineNotificationsReadAllUrl = @json(route('quarantine.notifications.read-all'));

function resolveQuarantineTabType(caseId) {
    const d = quarantineDB[caseId];
    if (!d) return 'followup';
    if (d.status === 'under_followup') return 'followup';
    if (d.status === 'health_released') return 'cleared';
    return 'failed';
}

window.openQuarantineModal = function(caseId) {
    if (!quarantineDB[caseId]) return;
    openModal(caseId, resolveQuarantineTabType(caseId));
};

function setAge(prefix, method) {
    if (prefix === 'add') {
        document.getElementById('add_age_method').value = method;
    } else if (prefix === 'edit') {
        document.getElementById('edit_age_method').value = method;
    }
    ['Birth', 'Approx'].forEach(m => {
        document.getElementById(prefix + 'Btn' + m).classList.remove('active');
        const block = document.getElementById(prefix + 'Block' + m);
        block.classList.remove('visible');
        block.style.display = 'none';
    });
    document.getElementById(prefix + 'Btn' + (method === 'birth' ? 'Birth' : 'Approx')).classList.add('active');
    const active = document.getElementById(prefix + 'Block' + (method === 'birth' ? 'Birth' : 'Approx'));
    active.classList.add('visible');
    active.style.display = 'flex';
}

function fillAgeFields(prefix, record) {
    if (!record || typeof record === 'string') {
        const ageText = typeof record === 'string' ? record : '';
        const approxMatch = (ageText || '').match(/(\d+)/);
        const approxVal = approxMatch ? approxMatch[1] : '';
        const unit = (ageText || '').includes('شهر') ? 'أشهر' : ((ageText || '').includes('يوم') ? 'أيام' : 'سنوات');

        document.getElementById(prefix + '_approxValue').value = approxVal;
        document.getElementById(prefix + '_approxUnit').value = unit;
        document.getElementById(prefix + '_currentApproxAge').value = ageText || '';
        document.getElementById(prefix + '_computedAge').value = ageText || '';
        document.getElementById(prefix + '_birthDate').value = '';
        setAge(prefix, approxVal ? 'approx' : 'birth');
        return;
    }

    const method = record.ageMethod || 'birth';

    if (method === 'approx') {
        document.getElementById(prefix + '_approxValue').value = record.approxAgeValue || '';
        document.getElementById(prefix + '_approxUnit').value = record.approxAgeUnit || 'سنوات';
        document.getElementById(prefix + '_currentApproxAge').value = record.age || '';
        document.getElementById(prefix + '_birthDate').value = '';
        document.getElementById(prefix + '_computedAge').value = '';
        setAge(prefix, 'approx');
        return;
    }

    document.getElementById(prefix + '_birthDate').value = record.birthDate || '';
    document.getElementById(prefix + '_approxValue').value = '';
    document.getElementById(prefix + '_currentApproxAge').value = '';
    updateComputedAge(prefix);
    setAge(prefix, 'birth');
}

function calculateAgeFromBirthDate(birthDateValue) {
    if (!birthDateValue) {
        return '';
    }

    const birthDate = new Date(birthDateValue);
    if (Number.isNaN(birthDate.getTime())) {
        return '';
    }

    const today = new Date();
    let years = today.getFullYear() - birthDate.getFullYear();
    let months = today.getMonth() - birthDate.getMonth();
    let days = today.getDate() - birthDate.getDate();

    if (days < 0) {
        months -= 1;
    }
    if (months < 0) {
        years -= 1;
        months += 12;
    }

    if (years >= 1) {
        return `${years} سنوات`;
    }
    if (months >= 1) {
        return `${months} أشهر`;
    }

    const diffMs = today.getTime() - birthDate.getTime();
    const diffDays = Math.max(0, Math.floor(diffMs / (1000 * 60 * 60 * 24)));
    return `${diffDays} أيام`;
}

function updateComputedAge(prefix) {
    const birthDateInput = document.getElementById(prefix + '_birthDate');
    const output = document.getElementById(prefix + '_computedAge');

    if (!birthDateInput || !output) {
        return;
    }

    output.value = calculateAgeFromBirthDate(birthDateInput.value) || '';
}

function switchTab(evt, tabId) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.seg-tab').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}

function populateDetailModal(d) {
    document.getElementById('mdl_id').textContent = d.caseNumber || '—';
    document.getElementById('mdl_code').textContent = d.code || '—';
    document.getElementById('mdl_type').textContent = d.type || '—';
    document.getElementById('mdl_gender').textContent = d.gender || '—';
    document.getElementById('mdl_group').textContent = d.group || '—';
    document.getElementById('mdl_age').textContent = d.age || '—';
    document.getElementById('mdl_initialHealth').textContent = d.initialHealth || '—';
    document.getElementById('mdl_source').textContent = d.source || '—';
    document.getElementById('mdl_vet').textContent = d.vet || '—';
    document.getElementById('mdl_entryDate').textContent = d.entryDate || '—';
    document.getElementById('mdl_initialNotes').textContent = (d.initialNotes && String(d.initialNotes).trim()) ? d.initialNotes : '—';

    const healthNotes = document.getElementById('mdl_healthNotes');
    const notes = Array.isArray(d.notes) ? d.notes : [];
    if (notes.length) {
        healthNotes.innerHTML = notes.map(note => `
            <div style="background:#f8fafc;padding:12px 14px;border-radius:8px;font-size:0.85rem;color:#334155;font-weight:700;text-align:center;border:1px solid #f1f5f9;">
                ${note}
            </div>
        `).join('');
    } else {
        healthNotes.innerHTML = `
            <div style="background:#f8fafc;padding:12px 14px;border-radius:8px;font-size:0.85rem;color:#334155;font-weight:700;text-align:center;border:1px solid #f1f5f9;">
                لا توجد ملاحظات صحية مسجلة
            </div>
        `;
    }

    const dosesEl = document.getElementById('mdl_doses');
    const vaccines = Array.isArray(d.vaccines) ? d.vaccines : [];
    if (vaccines.length) {
        dosesEl.innerHTML = vaccines.map(vaccine => `
            <div style="background:#f8fafc;padding:12px 14px;border-radius:8px;font-size:0.85rem;color:#334155;font-weight:700;text-align:right;border:1px solid #f1f5f9;border-right:3px solid #3b82f6;margin-bottom:8px;">
                ${vaccine}
            </div>
        `).join('');
    } else {
        dosesEl.innerHTML = '<div style="text-align:center;color:#94a3b8;font-weight:700;font-size:0.9rem;padding:1rem;">لا توجد جرعات مسجلة بعد</div>';
    }

    const releasedRow = document.getElementById('mdl_releasedRow');
    if (d.releasedAt) {
        document.getElementById('mdl_releasedAt').textContent = d.releasedAt;
        releasedRow.style.display = 'flex';
    } else {
        releasedRow.style.display = 'none';
    }

    const nameRow = document.getElementById('mdl_nameRow');
    const markRow = document.getElementById('mdl_markRow');
    if (d.animalName) {
        document.getElementById('mdl_animalName').textContent = d.animalName;
        nameRow.style.display = 'flex';
    } else {
        nameRow.style.display = 'none';
    }
    if (d.mark) {
        document.getElementById('mdl_mark').textContent = d.mark;
        markRow.style.display = 'flex';
    } else {
        markRow.style.display = 'none';
    }

    const failedSection = document.getElementById('mdl_failedSection');
    const isFailed = d.status === 'failed';
    if (failedSection) {
        failedSection.style.display = isFailed ? 'block' : 'none';
    }
    if (isFailed) {
        document.getElementById('mdl_closedAt').textContent = d.closedAt || '—';
        document.getElementById('mdl_closeReason').textContent = (d.closeReason && String(d.closeReason).trim()) ? d.closeReason : '—';
        document.getElementById('mdl_closeNotes').textContent = (d.closeNotes && String(d.closeNotes).trim()) ? d.closeNotes : '—';
    }
}

function submitHealthRelease(caseId) {
    if (!confirm('هل أنت متأكد من إصدار قرار الإفراج الصحي؟\nسيصبح الحيوان ظاهراً في جميع سجلات الحديقة.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = quarantineReleaseUrl.replace('__CASE__', encodeURIComponent(caseId));

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = quarantineCsrf;
    form.appendChild(csrf);

    document.body.appendChild(form);
    form.submit();
}

function openModal(caseId, tabType) {
    document.getElementById('modalCaseId').textContent = caseId;
    document.getElementById('mdl_id').textContent = caseId;

    const d = quarantineDB[caseId];
    if (d) populateDetailModal(d);
    
    const footer = document.getElementById('modalFooterActions');
    const noteForm = document.getElementById('noteForm');
    const vaccineForm = document.getElementById('vaccineForm');
    const showClinicalForms = tabType === 'followup' && !quarantineReadOnly && quarantineCanAddClinical;

    if (noteForm) {
        noteForm.style.display = showClinicalForms ? 'block' : 'none';
        if (showClinicalForms) {
            noteForm.action = quarantineNoteUrl.replace('__CASE__', encodeURIComponent(caseId));
            noteForm.reset();
        }
    }

    if (vaccineForm) {
        vaccineForm.style.display = showClinicalForms ? 'block' : 'none';
        if (showClinicalForms) {
            vaccineForm.action = quarantineVaccineUrl.replace('__CASE__', encodeURIComponent(caseId));
            vaccineForm.reset();
            const dateInput = vaccineForm.querySelector('[name="administered_at"]');
            if (dateInput && !dateInput.value) {
                dateInput.value = new Date().toISOString().slice(0, 10);
            }
        }
    }
    
    if (tabType === 'followup' && !quarantineReadOnly) {
        footer.innerHTML = `
            <button class="btn-cancel" onclick="closeModal()">إغلاق</button>
            <button class="btn-action-close" onclick="closeModal(); openEndModal('${caseId}')" style="background:#E11D48; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:700; cursor:pointer;">إنهاء الحالة</button>
            <button class="btn-action-release" onclick="submitHealthRelease('${caseId}')" style="background:#16a34a; color:#fff; border:none; padding:8px 16px; border-radius:8px; font-family:'Cairo',sans-serif; font-size:0.85rem; font-weight:700; cursor:pointer;">اصدار قرار الافراج الصحي</button>
        `;
    } else {
        footer.innerHTML = `
            <button class="btn-cancel" onclick="closeModal()">إغلاق</button>
        `;
    }
    
    document.getElementById('detailModal').classList.add('open');
}

function closeModal() {
    document.getElementById('detailModal').classList.remove('open');
}

function openAddModal() {
    setAge('add', 'birth');
    document.getElementById('add_birthDate').value = '';
    document.getElementById('add_computedAge').value = '';
    document.getElementById('add_approxValue').value = '';
    document.getElementById('add_currentApproxAge').value = '';
    document.getElementById('addModal').classList.add('open');
}

function closeAddModal() {
    document.getElementById('addModal').classList.remove('open');
}

function openEditModal(caseId) {
    document.getElementById('editModalCaseId').textContent = caseId;
    document.getElementById('editQuarantineForm').action = quarantineUpdateUrl.replace('__CASE__', encodeURIComponent(caseId));
    const d = quarantineDB[caseId];
    if (d) {
        document.getElementById('edit_type').value = d.type;
        document.getElementById('edit_animalName').value = d.animalName || '';
        document.getElementById('edit_mark').value = d.mark || '';
        document.getElementById('edit_gender').value = d.gender;
        fillAgeFields('edit', d);
        document.getElementById('edit_source').value = d.source;
        document.getElementById('edit_entryDate').value = d.entryDate;
        document.getElementById('edit_initialNotes').value = d.initialNotes || '';
    }
    document.getElementById('editModal').classList.add('open');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}

function openEndModal(caseId) {
    document.getElementById('endModalCaseId').textContent = caseId;
    const form = document.getElementById('closeQuarantineForm');
    form.action = quarantineCloseUrl.replace('__CASE__', encodeURIComponent(caseId));
    form.reset();
    document.getElementById('endModal').classList.add('open');
}

function confirmCloseQuarantine(event) {
    event.preventDefault();

    const reasonSelect = document.getElementById('close_reason');
    if (!reasonSelect?.value) {
        reasonSelect?.reportValidity();
        return false;
    }

    document.getElementById('confirmCloseCaseId').textContent = document.getElementById('endModalCaseId').textContent;
    document.getElementById('confirmCloseReason').textContent = reasonSelect.value;
    document.getElementById('confirmCloseDialog').classList.add('open');

    return false;
}

function closeConfirmCloseDialog() {
    document.getElementById('confirmCloseDialog').classList.remove('open');
}

function submitCloseQuarantine() {
    closeConfirmCloseDialog();
    document.getElementById('closeQuarantineForm').submit();
}

function closeEndModal() {
    document.getElementById('endModal').classList.remove('open');
}

document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
document.getElementById('endModal').addEventListener('click', function(e) {
    if (e.target === this) closeEndModal();
});
document.getElementById('confirmCloseDialog').addEventListener('click', function(e) {
    if (e.target === this) closeConfirmCloseDialog();
});

document.getElementById('add_birthDate')?.addEventListener('change', () => updateComputedAge('add'));
document.getElementById('edit_birthDate')?.addEventListener('change', () => updateComputedAge('edit'));

document.getElementById('quarantineSearch')?.addEventListener('input', function(e) {
    const q = e.target.value.trim().toLowerCase();
    document.querySelectorAll('.custom-table tbody tr[data-search]').forEach(row => {
        const hay = (row.getAttribute('data-search') || '').toLowerCase();
        row.style.display = !q || hay.includes(q) ? '' : 'none';
    });
});

@if($errors->any())
openAddModal();
setAge('add', @json(old('age_method', 'birth')));
@endif
updateComputedAge('add');

@if(!empty($openCase))
document.addEventListener('DOMContentLoaded', function () {
    window.openQuarantineModal(@json($openCase));
    if (window.history.replaceState) {
        const url = new URL(window.location.href);
        url.searchParams.delete('open');
        window.history.replaceState({}, '', url.pathname + url.search);
    }
});
@endif
</script>
@endsection
