@extends($__layout ?? 'vet.layout')
@php
    use App\Enums\TreatmentReferralStatus;
    $portalBase = $portalBase ?? '/vet';
    $isCarePortal = str_contains($portalBase, 'care');
@endphp
@section('title', $isCarePortal ? 'إحالات العلاج | الرعاية والتغذية' : 'إحالات العلاج | المستشفى البيطري')
@section('page_title', 'إحالات العلاج')

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
    .filter-bar .btn-search {
        padding: 10px 20px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none;
        border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800;
        cursor: pointer; transition: all 0.2s; white-space: nowrap;
    }
    .filter-bar .btn-search:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26,74,46,0.25); }

    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-header { padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #FAFBFC; }
    .table-card-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.8rem; font-weight: 800; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.92rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .custom-table tbody tr:last-child td { border-bottom: none; }

    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .badge-pending  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-pending .dot { background: #f59e0b; }
    .badge-approved { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-approved .dot { background: #22c55e; }
    .badge-rejected { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-rejected .dot { background: #ef4444; }

    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }

    .animal-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    /* ═══ MODAL ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 5000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 720px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }

    .modal-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1.2rem 1.5rem 0; display: flex; justify-content: space-between; align-items: flex-end; }
    .modal-title-wrap { padding-bottom: 0.8rem; }
    .modal-title-wrap h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .modal-title-wrap span { font-size: 0.8rem; color: #64748b; font-weight: 600; }
    .modal-tabs-wrap { display: flex; align-items: center; gap: 20px; }
    .modal-tabs { display: flex; }
    .modal-tab { padding: 10px 22px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent; transition: all 0.2s; }
    .modal-tab.active { color: #1a4a2e; border-bottom-color: #1a4a2e; }
    .modal-close { width: 32px; height: 32px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.1rem; font-weight: 700; transition: all 0.2s; margin-bottom: 10px; }
    .modal-close:hover { background: #f8fafc; color: #0f172a; }

    .modal-body { padding: 1.5rem; overflow-y: auto; max-height: 65vh; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; }
    .info-cell { background: #fff; padding: 12px 16px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
    .info-cell-value { font-size: 0.9rem; color: #0f172a; font-weight: 800; }

    .summary-layout { display: grid; grid-template-columns: 280px 1fr; gap: 1.25rem; align-items: start; }
    @media (max-width: 700px) { .summary-layout { grid-template-columns: 1fr; } }
    .animal-card { background: #fff; border-radius: 12px; padding: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .animal-card-title { font-size: 1rem; font-weight: 800; color: #1e293b; margin-bottom: 1rem; text-align: center; }
    .animal-photo-wrap { display: flex; justify-content: center; margin-bottom: 1rem; }
    .animal-photo { width: 68px; height: 68px; border-radius: 14px; background: linear-gradient(135deg, #E8F5E9, #C8E6C9); border: 2px solid #bbf7d0; display: flex; align-items: center; justify-content: center; font-size: 2rem; }
    .q-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.7rem; padding-bottom: 0.7rem; border-bottom: 1px solid #f1f5f9; gap: 8px; }
    .q-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .q-label { color: #64748b; font-size: 0.78rem; font-weight: 700; flex-shrink: 0; }
    .q-value { color: #0f172a; font-size: 0.85rem; font-weight: 800; text-align: left; }
    .section-title { font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; }

    .content-box { background: #fff; padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; color: #1e293b; font-weight: 600; line-height: 1.6; border: 1px solid #e2e8f0; border-right: 4px solid #3b82f6; margin-bottom: 1rem; }
    .section-label { font-size: 0.85rem; color: #0f172a; font-weight: 800; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }

    .status-section { border-radius: 12px; padding: 1.2rem; margin-top: 1.5rem; border: 1px solid transparent; }
    .status-section-title { font-size: 0.95rem; font-weight: 800; margin-bottom: 1rem; padding-bottom: 10px; border-bottom: 2px solid rgba(0,0,0,0.05); display: flex; align-items: center; gap: 8px; }
    .status-pending { background: #f8fafc; border-color: #e2e8f0; text-align: center; color: #475569; font-weight: 700; }
    .status-approved { background: #f0fdf4; border-color: #bbf7d0; }
    .status-approved .status-section-title { color: #15803d; }
    .status-rejected { background: #fef2f2; border-color: #fecaca; }
    .status-rejected .status-section-title { color: #dc2626; }
    .status-box { background: #fff; padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; color: #1e293b; font-weight: 700; border: 1px solid rgba(0,0,0,0.05); }

    .modal-footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 1.2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap; }
    .btn-cancel { padding: 10px 20px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #f8fafc; }
    .btn-secondary { padding: 10px 20px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
    .btn-secondary:hover { background: #dbeafe; }
    .btn-approve { padding: 10px 20px; background: #15803d; color: #fff; border: 1px solid #15803d; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-approve:hover { background: #166534; }
    .btn-reject { padding: 10px 20px; background: #fff; color: #dc2626; border: 1px solid #fecaca; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-reject:hover { background: #fef2f2; }

    .confirm-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(4px); z-index: 6000; align-items: center; justify-content: center; }
    .confirm-backdrop.open { display: flex; }
    .confirm-box { background: #fff; border-radius: 16px; width: 100%; max-width: 440px; padding: 1.8rem; box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
    .confirm-box h4 { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 10px; }
    .confirm-box p { font-size: 0.88rem; color: #64748b; font-weight: 600; line-height: 1.6; margin-bottom: 1.2rem; }
    .confirm-textarea { width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 600; resize: vertical; min-height: 90px; outline: none; margin-bottom: 1rem; }
    .confirm-textarea:focus { border-color: #2E7D32; }
    .confirm-actions { display: flex; gap: 10px; justify-content: flex-end; }
    .btn-confirm { padding: 10px 20px; background: #15803d; color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; }
    .btn-confirm-danger { padding: 10px 20px; background: #dc2626; color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; }
</style>
@endsection

@section('content')

<form method="GET" action="{{ $portalBase }}/referrals/treatment" class="top-card">
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="بحث برقم الحيوان أو نوعه أو رقم الإحالة...">
        </div>
        <select class="filter-select" name="status" onchange="this.form.submit()">
            <option value="">كل الحالات</option>
            <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>قيد المراجعة</option>
            <option value="approved" @selected(($filters['status'] ?? '') === 'approved')>معتمدة</option>
            <option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>مرفوضة</option>
        </select>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'withValues' => true, 'selected' => $filters['group'] ?? ''])
        </select>
    </div>
</form>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            إحالات العلاج
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>الحيوان</th>
                    <th>نوع الحيوان</th>
                    <th>المجموعة</th>
                    <th>تاريخ الإحالة</th>
                    <th>الحالة</th>
                    <th class="col-actions">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($referrals as $referral)
                    @php
                        $animal = $referral->animal;
                        $statusBadge = match ($referral->status) {
                            TreatmentReferralStatus::Approved => 'badge-approved',
                            TreatmentReferralStatus::Rejected => 'badge-rejected',
                            default => 'badge-pending',
                        };
                    @endphp
                    <tr>
                        @include('partials.animal-table-cell', [
                            'name' => $animal?->name,
                            'emoji' => '🐾',
                            'animalId' => $animal?->code,
                        ])
                        <td style="font-weight:700;">{{ $animal?->species ?? '—' }}</td>
                        <td>{{ $referral->group }}</td>
                        <td>{{ $referral->referred_at?->format('Y-m-d') }}</td>
                        <td><span class="badge {{ $statusBadge }}"><span class="dot"></span>{{ $referral->status->label() }}</span></td>
                        <td class="col-actions">
                            <button type="button"
                                class="btn-tbl view"
                                title="عرض التفاصيل"
                                data-referral-number="{{ $referral->referral_number }}"
                                onclick="openModal(this.dataset.referralNumber)">
                                @include('partials.icon-eye-view')
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2.5rem;color:#64748b;font-weight:700;">
                            لا توجد إحالات علاج مسجّلة حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($referrals->hasPages())
        <div style="padding:1rem 1.75rem;border-top:1px solid #f1f5f9;background:#FAFBFC;">
            {{ $referrals->links() }}
        </div>
    @endif
</div>

@endsection

@push('modals')
<div class="modal-backdrop" id="referralModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <h3>تفاصيل إحالة علاج</h3>
                <span id="mSubtitle">—</span>
            </div>
            <div class="modal-tabs-wrap">
                <div class="modal-tabs">
                    <button class="modal-tab active" id="mtab-btn-1" onclick="switchMTab(1)">بيانات الإحالة</button>
                    <button class="modal-tab" id="mtab-btn-2" onclick="switchMTab(2)">الحالة الصحية المرتبطة</button>
                </div>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
        </div>

        <div class="modal-body">
            <div id="mtab-1">
                <div class="summary-layout">
                    <div class="animal-card">
                        <h4 class="animal-card-title">بيانات الحيوان</h4>
                        <div class="animal-photo-wrap">
                            <div class="animal-photo" id="mAnimalPhoto">🐒</div>
                        </div>
                        <div style="text-align:center; margin-bottom:1rem;">
                            <div style="font-size:0.95rem;font-weight:800;color:#0f172a;" id="mAnimalName">—</div>
                            <div style="font-size:0.75rem;color:#64748b;font-weight:600;margin-top:4px;" id="mAnimalSub">—</div>
                        </div>
                        <div class="q-row">
                            <span class="q-label">رقم الحيوان</span>
                            <span class="q-value" id="mAnimalId">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">نوع الحيوان</span>
                            <span class="q-value" id="mAnimalType">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">الجنس</span>
                            <span class="q-value" id="mGender">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">العمر</span>
                            <span class="q-value" id="mAge">—</span>
                        </div>
                        <div class="q-row">
                            <span class="q-label">المجموعة</span>
                            <span class="q-value" id="mGroup">—</span>
                        </div>
                    </div>

                    <div>
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:1rem; flex-wrap:wrap;">
                            <h3 class="section-title" style="margin-bottom:0;">معلومات الإحالة</h3>
                            <span id="mStatusBadge"></span>
                        </div>

                        <div class="info-grid" style="margin-bottom:0;">
                            <div class="info-cell span-2">
                                <div class="info-cell-label">تاريخ إرسال الإحالة</div>
                                <div class="info-cell-value" id="mDate">—</div>
                            </div>
                        </div>

                        <div id="statusPending" class="status-section status-pending" style="display:none !important;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" style="margin-bottom:8px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div>الإحالة بانتظار مراجعة رئيس قسم المستشفى البيطري.</div>
                </div>

                <div id="statusApproved" class="status-section status-approved" style="display:none;">
                    <div class="status-section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        تم اعتماد الإحالة
                    </div>
                    <div class="info-grid" style="margin-bottom:0;background:transparent;border:none;">
                        <div class="info-cell" style="background:transparent;padding:0 0 10px 0;">
                            <div class="info-cell-label">تاريخ الاعتماد</div>
                            <div class="info-cell-value" id="appDate">—</div>
                        </div>
                        <div class="info-cell" style="background:transparent;padding:0 0 10px 0;">
                            <div class="info-cell-label">الحالة داخل المستشفى</div>
                            <div class="info-cell-value" style="color:#15803d;">تم فتح حالة علاجية</div>
                        </div>
                    </div>
                </div>

                <div id="statusRejected" class="status-section status-rejected" style="display:none;">
                    <div class="status-section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        تم رفض الإحالة
                    </div>
                    <div class="info-grid" style="margin-bottom:10px;background:transparent;border:none;">
                        <div class="info-cell" style="background:transparent;padding:0;">
                            <div class="info-cell-label">تاريخ الرفض</div>
                            <div class="info-cell-value" id="rejDate">—</div>
                        </div>
                    </div>
                    <div>
                        <div class="info-cell-label" style="margin-bottom:6px;">سبب الرفض</div>
                        <div class="status-box" id="rejReason">—</div>
                    </div>
                </div>
                    </div>
                </div>
            </div>

            <div id="mtab-2" style="display:none;">
                <div class="section-label">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    بيانات الحالة الصحية الأصلية
                </div>

                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ تسجيل الحالة</div>
                        <div class="info-cell-value" id="hDate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">المشرف المسجل</div>
                        <div class="info-cell-value" id="hSupervisor">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">نوع المتابعة</div>
                        <div class="info-cell-value" style="color:#dc2626;">تحتاج إحالة</div>
                    </div>
                    <div class="info-cell span-2">
                        <div class="info-cell-label">وصف الحالة (من المشرف)</div>
                        <div class="info-cell-value" id="hDesc" style="font-weight:600;line-height:1.6;">—</div>
                    </div>
                </div>

                <div style="margin-top:1rem;">
                    <div class="info-cell-label" style="margin-bottom:6px;">الملاحظات المسجلة عن الحيوان</div>
                    <div class="content-box" id="hNotes">—</div>
                </div>
            </div>
        </div>

        <div class="modal-footer" id="mFooter"></div>
    </div>
</div>

@unless($readOnly ?? false)
<div class="confirm-backdrop" id="approveConfirm">
    <div class="confirm-box">
        <h4>اعتماد إحالة علاج</h4>
        <p>سيتم اعتماد الإحالة وتسجيلها كمعتمدة لهذا الحيوان.</p>
        <form id="approveForm" method="POST" class="confirm-actions" style="margin:0;">
            @csrf
            <button type="button" class="btn-cancel" onclick="closeConfirm('approveConfirm')">تراجع</button>
            <button type="submit" class="btn-confirm">تأكيد الاعتماد</button>
        </form>
    </div>
</div>
@endunless

@unless($readOnly ?? false)
<div class="confirm-backdrop" id="rejectConfirm">
    <div class="confirm-box">
        <h4>رفض إحالة علاج</h4>
        <p>يرجى إدخال سبب الرفض قبل المتابعة.</p>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea class="confirm-textarea" name="rejection_reason" id="rejectReasonInput" placeholder="سبب الرفض..." required></textarea>
            <div class="confirm-actions">
                <button type="button" class="btn-cancel" onclick="closeConfirm('rejectConfirm')">تراجع</button>
                <button type="submit" class="btn-confirm-danger">تأكيد الرفض</button>
            </div>
        </form>
    </div>
</div>
@endunless
@endpush

@section('scripts')
<script>
    const portalBase = @json($portalBase);
    const isCarePortal = @json($isCarePortal);
    const isReadOnly = @json($readOnly ?? false);
    const canAct = @json($canAct ?? false);
    const referrals = @json($referralsForJs ?? []);
    let currentReferralNumber = '';

    function switchMTab(n) {
        document.getElementById('mtab-1').style.display = n === 1 ? 'block' : 'none';
        document.getElementById('mtab-2').style.display = n === 2 ? 'block' : 'none';
        document.getElementById('mtab-btn-1').className = 'modal-tab' + (n === 1 ? ' active' : '');
        document.getElementById('mtab-btn-2').className = 'modal-tab' + (n === 2 ? ' active' : '');
    }

    function openModal(refId) {
        const d = referrals[refId];
        if (!d) return;

        currentReferralNumber = refId;
        switchMTab(1);

        const displayName = d.animal_name || d.animal_species || '—';
        document.getElementById('mSubtitle').innerText = displayName + ' — ' + (d.group || '—');
        document.getElementById('mAnimalPhoto').textContent = '🐾';
        document.getElementById('mAnimalName').textContent = displayName;
        document.getElementById('mAnimalSub').textContent = (d.animal_code || '—') + ' · ' + (d.animal_species || '—');
        document.getElementById('mAnimalId').textContent = d.animal_code || '—';
        document.getElementById('mAnimalType').textContent = d.animal_species || '—';
        document.getElementById('mGender').textContent = d.animal_gender || '—';
        document.getElementById('mAge').textContent = d.animal_age || '—';
        document.getElementById('mGroup').textContent = d.group || '—';
        document.getElementById('mDate').textContent = d.date || '—';

        document.getElementById('hDate').textContent = d.case_date || '—';
        document.getElementById('hSupervisor').textContent = d.supervisor || '—';
        document.getElementById('hDesc').textContent = d.description || '—';
        document.getElementById('hNotes').textContent = d.animal_notes?.trim()
            ? d.animal_notes
            : 'لا توجد ملاحظات مسجلة عن الحيوان.';

        document.getElementById('statusPending').style.display = 'none';
        document.getElementById('statusApproved').style.display = 'none';
        document.getElementById('statusRejected').style.display = 'none';

        const footer = document.getElementById('mFooter');
        const closeBtn = `<button type="button" class="btn-cancel" onclick="closeModal()">إغلاق</button>`;
        const status = d.status;

        if (status === 'pending') {
            if (isCarePortal) {
                document.getElementById('mStatusBadge').innerHTML = `<span class="badge badge-pending"><span class="dot"></span>${d.status_label}</span>`;
            } else {
                document.getElementById('mStatusBadge').innerHTML = '';
            }
            if (canAct) {
                footer.innerHTML = `
                <button type="button" class="btn-reject" onclick="showRejectConfirm()">رفض إحالة علاج</button>
                <button type="button" class="btn-approve" onclick="showApproveConfirm()">اعتماد إحالة علاج</button>
                ${closeBtn}`;
            } else {
                footer.innerHTML = closeBtn;
            }
        } else if (status === 'approved') {
            document.getElementById('mStatusBadge').innerHTML = `<span class="badge badge-approved"><span class="dot"></span>${d.status_label}</span>`;
            document.getElementById('appDate').textContent = d.reviewed_at || '—';
            document.getElementById('statusApproved').style.display = 'block';
            footer.innerHTML = closeBtn;
        } else {
            document.getElementById('mStatusBadge').innerHTML = `<span class="badge badge-rejected"><span class="dot"></span>${d.status_label}</span>`;
            document.getElementById('rejDate').textContent = d.reviewed_at || '—';
            document.getElementById('rejReason').textContent = d.rejection_reason || '—';
            document.getElementById('statusRejected').style.display = 'block';
            footer.innerHTML = closeBtn;
        }

        document.getElementById('referralModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('referralModal').classList.remove('open');
    }

    function showApproveConfirm() {
        if (!canAct || !currentReferralNumber) return;
        const form = document.getElementById('approveForm');
        if (form) form.action = `${portalBase}/referrals/treatment/${currentReferralNumber}/approve`;
        document.getElementById('approveConfirm').classList.add('open');
    }

    function showRejectConfirm() {
        if (!canAct || !currentReferralNumber) return;
        document.getElementById('rejectReasonInput').value = '';
        const form = document.getElementById('rejectForm');
        if (form) form.action = `${portalBase}/referrals/treatment/${currentReferralNumber}/reject`;
        document.getElementById('rejectConfirm').classList.add('open');
    }

    function closeConfirm(id) {
        document.getElementById(id).classList.remove('open');
    }

    window.onclick = function(e) {
        if (e.target === document.getElementById('referralModal')) closeModal();
        if (e.target === document.getElementById('approveConfirm')) closeConfirm('approveConfirm');
        if (e.target === document.getElementById('rejectConfirm')) closeConfirm('rejectConfirm');
    };

    window.openTreatmentReferralModal = openModal;

    @if(!empty($highlightReferral))
        document.addEventListener('DOMContentLoaded', () => openModal(@json($highlightReferral)));
    @endif
</script>
@endsection
