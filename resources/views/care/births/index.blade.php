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
    <form method="GET" action="{{ ($portalBase ?? '/care') }}/births" class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="بحث برقم الحيوان أو نوعه أو رقم الأم...">
        </div>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'selected' => $filters['group'] ?? '', 'withValues' => true])
        </select>
        <select class="filter-select" name="status" onchange="this.form.submit()">
            <option value="" @selected(empty($filters['status']))>كل الحالات</option>
            <option value="monitoring" @selected(($filters['status'] ?? '') === 'monitoring')>قيد المتابعة</option>
            <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>دائم داخل الحديقة</option>
        </select>
        <button type="submit" class="btn-submit" style="padding:10px 18px;">بحث</button>
    </form>
</div>

<div class="table-card">
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
                    <th>رقم الأم</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>تاريخ الولادة</th>
                    <th>الأيام المتبقية</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($newborns as $newborn)
                    @php
                        $monitoring = $newborn->status === \App\Enums\AnimalStatus::UnderBirthFollowUp->value;
                        $daysRemaining = app(\App\Services\BirthRegistrationService::class)->daysRemaining($newborn);
                        $daysClass = 'days-ok';
                        if ($monitoring && $daysRemaining !== null) {
                            if ($daysRemaining <= 1) {
                                $daysClass = 'days-danger';
                            } elseif ($daysRemaining <= 3) {
                                $daysClass = 'days-warn';
                            }
                        }
                    @endphp
                    <tr>
                        @include('partials.animal-table-cell', [
                            'emoji' => '🐾',
                            'animalId' => $newborn->code,
                            'name' => $newborn->name,
                            'image' => $newborn->photo_path
                                ? ($portalBase ?? '/care').'/births/'.$newborn->code.'/photo'
                                : null,
                        ])
                        <td>
                            @if($newborn->mother)
                                <span class="animal-id">{{ $newborn->mother->code }}</span>
                            @else
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>
                        <td style="font-weight:700;">{{ $newborn->species }}</td>
                        <td>{{ $newborn->group }}</td>
                        <td>{{ $newborn->birth_date?->format('Y-m-d') ?? '—' }}</td>
                        <td>
                            @if($monitoring)
                                <span class="{{ $daysClass }}">
                                    @if($daysRemaining === null)
                                        —
                                    @elseif($daysRemaining === 0)
                                        انتهت المدة ⚠️
                                    @elseif($daysRemaining === 1)
                                        يوم واحد
                                    @else
                                        {{ $daysRemaining }} يوماً@if($daysRemaining <= 3) ⚠️@endif
                                    @endif
                                </span>
                            @else
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($monitoring)
                                <span class="badge badge-status-monitoring"><span class="dot"></span>قيد المتابعة</span>
                            @else
                                <span class="badge badge-status-completed"><span class="dot"></span>دائم داخل الحديقة</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" onclick="openModal('{{ $newborn->code }}')" class="btn-tbl view" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:2.5rem;color:#64748b;font-weight:700;">
                            لا توجد مواليد مسجلة حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($newborns->hasPages())
        <div class="table-card-footer">{{ $newborns->links() }}</div>
    @endif
</div>
@endsection

@push('modals')
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
                        <div class="q-row" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
                            <span class="q-label">العلامة المميزة</span>
                            <span class="q-value" id="bMark">—</span>
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
                    <div class="q-attach-wrap" id="bAttachmentWrap">
                        <div class="q-attach-img" id="bAttachmentImg">🦁</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" id="bFooter"></div>
    </div>
</div>

@endpush

@section('scripts')
<script>
    const newborns = @json($newbornsForJs ?? []);

    function switchBTab(n) {
        document.getElementById('btab-1').style.display = n === 1 ? 'block' : 'none';
        document.getElementById('btab-2').style.display = n === 2 ? 'block' : 'none';
        document.getElementById('btab-btn-1').className = 'modal-tab' + (n === 1 ? ' active' : '');
        document.getElementById('btab-btn-2').className = 'modal-tab' + (n === 2 ? ' active' : '');
    }

    function openModal(code) {
        const d = newborns[code];
        if (!d) return;
        switchBTab(1);

        document.getElementById('modalBirthId').textContent = d.code;
        document.getElementById('bAnimalId').textContent = d.code;
        document.getElementById('bAnimalType').textContent = d.species;
        document.getElementById('bGroup').textContent = d.group;
        document.getElementById('bMother').textContent = d.mother_code ? (d.mother_name ? d.mother_code + ' — ' + d.mother_name : d.mother_code) : '—';
        document.getElementById('bDate').textContent = d.birth_date || '—';
        document.getElementById('bDays').textContent = d.days_label || '—';
        document.getElementById('bStatus').textContent = d.status_label;
        document.getElementById('bSupervisor').textContent = d.supervisor || '—';
        document.getElementById('bMark').textContent = d.mark || '—';
        document.getElementById('bNotes').textContent = d.notes || '—';

        const attachWrap = document.getElementById('bAttachmentWrap');
        if (d.has_photo && d.photo_url) {
            attachWrap.innerHTML = '<img src="' + d.photo_url + '" alt="صورة المولود" class="q-attach-img" style="object-fit:cover;width:180px;height:180px;border-radius:16px;">';
        } else {
            attachWrap.innerHTML = '<div class="q-attach-img" id="bAttachmentImg">🐾</div>';
        }

        document.getElementById('bFooter').innerHTML = '<button type="button" class="btn-cancel" onclick="closeModal()">إغلاق</button>';

        document.getElementById('birthModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('birthModal').classList.remove('open');
    }

    @if(!empty($highlightAnimal))
    document.addEventListener('DOMContentLoaded', function() {
        openModal(@json($highlightAnimal));
    });
    @endif
</script>
@endsection
