@extends($__layout ?? 'care.layout')
@section('title', 'حالات النفوق | الرعاية والتغذية')
@section('page_title', 'حالات النفوق')

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

    /* ── Table ── */
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

    /* ═══ BADGES ═══ */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-new      { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-new .dot { background: #ef4444; }
    .badge-approved { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-approved .dot { background: #22c55e; }
    .badge-autopsy  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-autopsy .dot { background: #f59e0b; }

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    /* ═══ MODAL — نفس نافذة تفاصيل الحجر الصحي ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    #mortalityModal .modal-box { background: #f8fafc; border-radius: 20px; width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    #mortalityModal .modal-header { background: transparent; border-bottom: none; display: flex; justify-content: center; position: relative; padding: 2rem 1.5rem 0; }
    #mortalityModal .modal-header h3 { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0; text-align: center; }
    #mortalityModal .modal-close {
        position: absolute; left: 1.5rem; top: 1.5rem;
        width: 32px; height: 32px; border-radius: 8px; background: #e2e8f0; border: none;
        color: #64748b; display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 1.2rem; font-weight: 700; line-height: 1;
    }
    #mortalityModal .modal-close:hover { background: #cbd5e1; color: #0f172a; }

    #mortalityModal .modal-tabs-bar { display: flex; justify-content: center; gap: 0; padding: 1rem 2rem 0; }
    #mortalityModal .modal-tab {
        padding: 8px 20px; border: none; background: transparent;
        font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800;
        cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent;
    }
    #mortalityModal .modal-tab.active { color: #16a34a; border-bottom-color: #16a34a; }

    #mortalityModal .modal-body { padding: 1.5rem 2rem; }
    #mortalityModal .q-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { #mortalityModal .q-grid { grid-template-columns: 1fr; } }

    #mortalityModal .q-card {
        background: #fff; border-radius: 12px; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    #mortalityModal .q-card-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center; }
    #mortalityModal .q-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;
    }
    #mortalityModal .q-label { color: #64748b; font-size: 0.9rem; font-weight: 700; }
    #mortalityModal .q-value { color: #0f172a; font-size: 0.95rem; font-weight: 800; }
    #mortalityModal .q-value.muted { color: #94a3b8; font-weight: 700; font-style: italic; }
    #mortalityModal .q-col { display: flex; flex-direction: column; gap: 1.5rem; }
    #mortalityModal .q-note-box {
        background: #f8fafc; padding: 12px 14px; border-radius: 8px; text-align: center;
        font-size: 0.85rem; font-weight: 700; color: #334155; border: 1px solid #f1f5f9;
    }
    #mortalityModal .q-notes-list { display: flex; flex-direction: column; gap: 0.8rem; }
    #mortalityModal .q-note-item {
        background: #f8fafc; padding: 12px 14px; border-radius: 8px;
        font-size: 0.85rem; color: #334155; font-weight: 700; text-align: center; border: 1px solid #f1f5f9;
    }
    #mortalityModal .q-attach-wrap { text-align: center; padding: 2rem 1rem; }
    #mortalityModal .q-attach-img {
        width: 180px; height: 180px; border-radius: 16px; margin: 0 auto;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9); border: 2px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center; font-size: 5rem;
    }

    #mortalityModal .modal-footer { background: transparent; border-top: none; padding: 0 2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap; }
    #mortalityModal .btn-action-release { padding: 8px 16px; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #mortalityModal .btn-action-release:hover { background: #15803d; }
    #mortalityModal .btn-action-close { padding: 8px 16px; background: #e11d48; color: #fff; border: none; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #mortalityModal .btn-action-close:hover { background: #be123c; }
    #mortalityModal .btn-cancel { padding: 8px 16px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #mortalityModal .btn-cancel:hover { background: #e2e8f0; }

    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-submit:hover { transform: translateY(-1px); }
    .btn-submit-orange { padding: 10px 24px; background: linear-gradient(135deg, #92400e, #d97706); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-submit-orange:hover { transform: translateY(-1px); }
    .btn-cancel { padding: 10px 20px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #f8fafc; }

    /* ── Sub-dialog modals (above main modal z=1000) ── */
    .dialog-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(3px); z-index: 1100; align-items: center; justify-content: center; }
    .dialog-backdrop.open { display: flex; }
    .dialog-box { background: #fff; border-radius: 18px; width: 100%; max-width: 480px; box-shadow: 0 30px 80px rgba(0,0,0,0.2); animation: modalIn 0.25s cubic-bezier(0.34,1.56,0.64,1); overflow: hidden; }
    .dialog-icon-wrap { width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.8rem; }
    .dialog-body { padding: 2rem 2rem 1.5rem; text-align: center; }
    .dialog-body h4 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
    .dialog-body p { font-size: 0.85rem; color: #64748b; font-weight: 600; line-height: 1.6; margin-bottom: 0; }
    .dialog-footer { padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: center; }

    /* Reason option cards */
    .reason-options { display: flex; flex-direction: column; gap: 8px; margin-top: 1.2rem; text-align: right; }
    .reason-option { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.2s; }
    .reason-option:hover { border-color: #d97706; background: #fffbeb; }
    .reason-option input[type=radio] { accent-color: #d97706; width: 16px; height: 16px; flex-shrink: 0; }
    .reason-option label { font-size: 0.88rem; font-weight: 700; color: #334155; cursor: pointer; }
    .reason-option.checked { border-color: #d97706; background: #fffbeb; }
    .reason-extra { margin-top: 10px; width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; }
    .reason-extra:focus { border-color: #d97706; }

    /* Toast notification */
    .toast { position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(20px); background: #0f172a; color: #fff; padding: 14px 24px; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); z-index: 2000; opacity: 0; transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1); pointer-events: none; }
    .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    .toast.green { background: linear-gradient(135deg, #1a4a2e, #2d7a47); }
    .toast.orange { background: linear-gradient(135deg, #92400e, #c2710c); }
</style>
@endsection

@section('content')
@php
    use App\Enums\MortalityCaseStatus;
    use App\Enums\UserRole;
    $portalBase = $portalBase ?? (($readOnly ?? false) ? '/director/care' : '/care');
    $canAct = empty($readOnly) && auth()->user()?->role === UserRole::CareHead->value;
@endphp

{{-- ═══════ FILTERS ═══════ --}}
<form method="GET" action="{{ $portalBase }}/mortality" class="top-card">
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="بحث برقم الحيوان أو نوعه أو رقم الحالة...">
        </div>
        <select class="filter-select" name="status" onchange="this.form.submit()">
            <option value="">كل الحالات</option>
            <option value="new" @selected(($filters['status'] ?? '') === 'new')>جديدة</option>
            <option value="approved" @selected(($filters['status'] ?? '') === 'approved')>معتمدة</option>
            <option value="referred_for_autopsy" @selected(($filters['status'] ?? '') === 'referred_for_autopsy')>محالة للتشريح</option>
        </select>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'withValues' => true, 'selected' => $filters['group'] ?? ''])
        </select>
    </div>
</form>

{{-- ═══ TABLE ═══ --}}
<div class="table-card">
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
                    <th>النوع</th>
                    <th>المجموعة</th>
                    <th>تاريخ النفوق</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cases as $case)
                    @php
                        $animal = $case->animal;
                        $statusBadge = match ($case->status) {
                            MortalityCaseStatus::Approved => 'badge-approved',
                            MortalityCaseStatus::ReferredForAutopsy => 'badge-autopsy',
                            default => 'badge-new',
                        };
                    @endphp
                    <tr>
                        @include('partials.animal-table-cell', [
                            'name' => $animal?->name,
                            'emoji' => '🐾',
                            'animalId' => $animal?->code ?? $case->subject_code,
                        ])
                        <td style="font-weight:700;">{{ $animal?->species ?? $case->subject_type ?? '—' }}</td>
                        <td>{{ $case->group }}</td>
                        <td>{{ $case->death_date?->format('Y-m-d') }}</td>
                        <td><span class="badge {{ $statusBadge }}"><span class="dot"></span>{{ $case->status->label() }}</span></td>
                        <td>
                            <button type="button"
                                class="btn-tbl view"
                                title="عرض التفاصيل"
                                data-case-number="{{ $case->case_number }}"
                                onclick="openModal(this.dataset.caseNumber)">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2.5rem;color:#64748b;font-weight:700;">
                            لا توجد حالات نفوق مسجّلة حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cases->hasPages())
        <div class="table-card-footer">
            {{ $cases->links() }}
        </div>
    @endif
</div>

@endsection

@push('modals')
<div class="modal-backdrop" id="mortalityModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>تفاصيل حالة النفوق — <span id="modalCaseId">MC-2026-001</span></h3>
            <button type="button" class="modal-close" onclick="closeModal()">✕</button>
        </div>

        <div class="modal-tabs-bar">
            <button class="modal-tab active" id="mtab-btn-1" onclick="switchMTab(1)">بيانات الحالة</button>
            <button class="modal-tab" id="mtab-btn-2" onclick="switchMTab(2)">المرفقات</button>
        </div>

        <div class="modal-body">
            <div id="mtab-1">
                <div class="q-grid">
                    <div class="q-card">
                        <h4 class="q-card-title">بيانات الحيوان</h4>

                        <div class="q-row">
                            <span class="q-label">رقم الحيوان</span>
                            <span class="q-value" id="mAnimalId">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">نوع الحيوان</span>
                            <span class="q-value" id="mAnimalType">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">اسم الحيوان</span>
                            <span class="q-value" id="mAnimalName">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">المجموعة</span>
                            <span class="q-value" id="mGroup">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">تاريخ النفوق</span>
                            <span class="q-value" id="mDate">—</span>
                        </div>
                        <div class="q-row" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
                            <span class="q-label">الحالة</span>
                            <span class="q-value" id="mStatus">—</span>
                        </div>
                    </div>

                    <div class="q-col">
                        <div class="q-card">
                            <h4 class="q-card-title">سبب النفوق</h4>
                            <div class="q-note-box" id="mCause">—</div>
                        </div>

                        <div class="q-card" style="flex-grow:1;">
                            <h4 class="q-card-title">الملاحظات المسجلة عن الحيوان</h4>
                            <div class="q-notes-list">
                                <div class="q-note-item" id="mNotes">—</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="mtab-2" style="display:none;">
                <div class="q-card">
                    <h4 class="q-card-title">المرفقات</h4>
                    <div class="q-attach-wrap">
                        <div class="q-attach-img" id="mAttachmentImg">🦁</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" id="mFooter"></div>
    </div>
</div>

{{-- ═══ BLOCK APPROVE (unknown cause) DIALOG ═══ --}}
<div class="dialog-backdrop" id="blockedApproveDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#fef2f2;">⚠️</div>
            <h4>لا يمكن الاعتماد</h4>
            <p>لا تستطيع اعتماد النتيجة لان سبب النفوق غير ظاهر</p>
        </div>
        <div class="dialog-footer">
            <button class="btn-cancel" onclick="closeDialog('blockedApproveDialog')">حسناً</button>
        </div>
    </div>
</div>

{{-- ═══ CONFIRM APPROVE DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmApproveDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#f0fdf4;">✅</div>
            <h4>تأكيد اعتماد حالة النفوق</h4>
            <p>هل أنت متأكد من اعتماد حالة النفوق رسمياً دون إحالة للتشريح؟<br>سيتم تحديث سجل الحيوان إلى <strong>نافق</strong> وإغلاق الحالة.</p>
        </div>
        <div class="dialog-footer">
            <button type="button" class="btn-cancel" onclick="closeDialog('confirmApproveDialog')">إلغاء</button>
            <form id="approveForm" method="POST" style="margin:0;display:inline-flex;">
                @csrf
                <button type="submit" class="btn-submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    نعم، اعتماد
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ═══ AUTOPSY REASON DIALOG ═══ --}}
<div class="dialog-backdrop" id="autopsyReasonDialog">
    <div class="dialog-box">
        <div class="dialog-body" style="text-align:right;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:1rem; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
                <div style="width:40px;height:40px;border-radius:10px;background:#fffbeb;display:flex;align-items:center;justify-content:center;">📋</div>
                <div>
                    <div style="font-size:1rem; font-weight:800; color:#0f172a;">إحالة للتشريح</div>
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600;">حدد سبب الإحالة للتشريح رغم وجود سبب ظاهر</div>
                </div>
            </div>
            <div class="reason-options">
                <div class="reason-option" onclick="selectReason(this,'للتأكد من سبب النفوق')">
                    <input type="radio" name="autopsyReason" value="للتأكد من سبب النفوق" id="r1">
                    <label for="r1">للتأكد من سبب النفوق</label>
                </div>
                <div class="reason-option" onclick="selectReason(this,'الاشتباه في مرض معدي')">
                    <input type="radio" name="autopsyReason" value="الاشتباه في مرض معدي" id="r2">
                    <label for="r2">الاشتباه في مرض معدي</label>
                </div>
                <div class="reason-option" onclick="selectReason(this,'تكرار حالات نفوق مشابهة')">
                    <input type="radio" name="autopsyReason" value="تكرار حالات نفوق مشابهة" id="r3">
                    <label for="r3">تكرار حالات نفوق مشابهة</label>
                </div>
                <div class="reason-option" onclick="selectReason(this,'طلب توثيق طبي إضافي')">
                    <input type="radio" name="autopsyReason" value="طلب توثيق طبي إضافي" id="r4">
                    <label for="r4">طلب توثيق طبي إضافي</label>
                </div>
            </div>
            <input type="text" class="reason-extra" id="extraReasonInput" placeholder="أو اكتب سبباً آخر...">
        </div>
        <div class="dialog-footer">
            <button type="button" class="btn-cancel" onclick="closeDialog('autopsyReasonDialog')">إلغاء</button>
            <form id="autopsyReasonForm" method="POST" style="margin:0;display:inline-flex;">
                @csrf
                <input type="hidden" name="autopsy_reason" id="autopsyReasonHidden">
                <button type="submit" class="btn-submit-orange" onclick="return prepareAutopsyReasonSubmit()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    تأكيد الإحالة للتشريح
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ═══ CONFIRM AUTOPSY (unknown cause) DIALOG ═══ --}}
<div class="dialog-backdrop" id="confirmAutopsyDialog">
    <div class="dialog-box">
        <div class="dialog-body">
            <div class="dialog-icon-wrap" style="background:#fffbeb;">🔬</div>
            <h4>تأكيد الإحالة للتشريح</h4>
            <p>سبب النفوق <strong>غير ظاهر</strong>.<br>هل تؤكد إحالة هذه الحالة للمستشفى البيطري لإجراء التشريح وتوثيق سبب النفوق النهائي؟</p>
        </div>
        <div class="dialog-footer">
            <button type="button" class="btn-cancel" onclick="closeDialog('confirmAutopsyDialog')">إلغاء</button>
            <form id="autopsyUnknownForm" method="POST" style="margin:0;display:inline-flex;">
                @csrf
                <button type="submit" class="btn-submit-orange">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    نعم، إحالة للتشريح
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ═══ TOAST ═══ --}}
<div class="toast" id="toastMsg">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastText">تمت العملية بنجاح</span>
</div>
@endpush

@section('scripts')
<script>
    const portalBase = @json($portalBase);
    const canAct = @json($canAct ?? false);
    const cases = @json($mortalityCasesForJs ?? []);
    let currentCaseNumber = '';
    let currentMortalityCause = '';
    let selectedReason = '';

    function switchMTab(n) {
        document.getElementById('mtab-1').style.display = n === 1 ? 'block' : 'none';
        document.getElementById('mtab-2').style.display = n === 2 ? 'block' : 'none';
        document.getElementById('mtab-btn-1').className = 'modal-tab' + (n === 1 ? ' active' : '');
        document.getElementById('mtab-btn-2').className = 'modal-tab' + (n === 2 ? ' active' : '');
    }

    function isCauseNotApparent(cause) {
        const value = (cause || '').trim();
        return value === '' || value === 'غير ظاهر' || value.startsWith('غير ظاهر');
    }

    function openModal(caseNumber) {
        const d = cases[caseNumber];
        if (!d) return;

        currentCaseNumber = caseNumber;
        currentMortalityCause = d.death_cause;
        switchMTab(1);

        const footer = document.getElementById('mFooter');
        const closeBtn = `<button type="button" class="btn-cancel" onclick="closeModal()">إغلاق</button>`;

        document.getElementById('modalCaseId').textContent = d.case_number;
        document.getElementById('mAnimalId').textContent = d.animal_code || '—';
        document.getElementById('mAnimalType').textContent = d.animal_species || '—';
        document.getElementById('mAnimalName').innerHTML = d.animal_name
            ? d.animal_name
            : '<span class="q-value muted">—</span>';
        document.getElementById('mGroup').textContent = d.group || '—';
        document.getElementById('mDate').textContent = d.death_date || '—';
        document.getElementById('mStatus').textContent = d.status_label || '—';
        document.getElementById('mNotes').textContent = d.notes || '—';

        const causeEl = document.getElementById('mCause');
        if (isCauseNotApparent(d.death_cause)) {
            causeEl.innerHTML = '<span class="q-value muted">غير ظاهر</span>';
        } else {
            causeEl.textContent = d.death_cause;
        }

        const attachImg = document.getElementById('mAttachmentImg');
        if (d.attachment_url) {
            attachImg.innerHTML = `<img src="${d.attachment_url}" alt="مرفق" style="width:180px;height:180px;object-fit:cover;border-radius:16px;">`;
        } else {
            attachImg.textContent = '🐾';
        }

        if (d.status === 'new' && canAct) {
            if (d.cause_apparent) {
                footer.innerHTML = closeBtn +
                    `<button type="button" class="btn-action-close" onclick="openAutopsyReasonDialog()">إحالة للتشريح</button>
                    <button type="button" class="btn-action-release" onclick="approveCase()">اعتماد حالة النفوق</button>`;
            } else {
                footer.innerHTML = closeBtn +
                    `<button type="button" class="btn-action-close" onclick="referAutopsy()">إحالة حالة نفوق للتشريح</button>
                    <button type="button" class="btn-action-release" onclick="approveCase()">اعتماد حالة النفوق</button>`;
            }
        } else {
            footer.innerHTML = closeBtn;
        }

        document.getElementById('mortalityModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('mortalityModal').classList.remove('open');
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

    function approveCase() {
        if (!canAct || !currentCaseNumber) return;
        if (isCauseNotApparent(currentMortalityCause)) {
            openDialog('blockedApproveDialog');
            return;
        }
        const form = document.getElementById('approveForm');
        if (form) form.action = `${portalBase}/mortality/${currentCaseNumber}/approve`;
        openDialog('confirmApproveDialog');
    }

    function referAutopsy() {
        if (!canAct || !currentCaseNumber) return;
        const form = document.getElementById('autopsyUnknownForm');
        if (form) form.action = `${portalBase}/mortality/${currentCaseNumber}/refer-autopsy`;
        openDialog('confirmAutopsyDialog');
    }

    function selectReason(el, val) {
        document.querySelectorAll('.reason-option').forEach(o => o.classList.remove('checked'));
        el.classList.add('checked');
        el.querySelector('input[type=radio]').checked = true;
        selectedReason = val;
        document.getElementById('extraReasonInput').value = '';
    }

    function openAutopsyReasonDialog() {
        if (!canAct || !currentCaseNumber) return;
        selectedReason = '';
        document.querySelectorAll('.reason-option').forEach(o => o.classList.remove('checked'));
        document.querySelectorAll('input[name=autopsyReason]').forEach(r => r.checked = false);
        document.getElementById('extraReasonInput').value = '';
        const form = document.getElementById('autopsyReasonForm');
        if (form) form.action = `${portalBase}/mortality/${currentCaseNumber}/refer-autopsy`;
        openDialog('autopsyReasonDialog');
    }

    function prepareAutopsyReasonSubmit() {
        const extra = document.getElementById('extraReasonInput').value.trim();
        const reason = extra || selectedReason;
        if (!reason) {
            document.getElementById('extraReasonInput').style.borderColor = '#ef4444';
            document.getElementById('extraReasonInput').placeholder = '⚠️ يرجى تحديد سبب الإحالة أولاً';
            return false;
        }
        document.getElementById('autopsyReasonHidden').value = reason;
        return true;
    }

    window.onclick = function(e) {
        const mainModal = document.getElementById('mortalityModal');
        if (e.target === mainModal) closeModal();
    };

    @if(!empty($highlightCase))
        document.addEventListener('DOMContentLoaded', () => openModal(@json($highlightCase)));
    @endif
</script>
@endsection
