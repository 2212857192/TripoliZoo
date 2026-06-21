@extends($__layout ?? 'vet.layout')
@php
    use App\Enums\AutopsyReferralStatus;
    $portalBase = $portalBase ?? '/vet';
    $isVetPortal = $isVetPortal ?? str_contains($portalBase, '/vet');
@endphp
@section('title', $isVetPortal ? 'إحالات التشريح | المستشفى البيطري' : 'إحالات التشريح | الرعاية والتغذية')
@section('page_title', 'إحالات التشريح')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; }
    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input { width: 100%; padding: 10px 40px 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; }
    .search-box input:focus { border-color: #2E7D32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    .search-box svg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .filter-select { padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #334155; outline: none; cursor: pointer; }
    .filter-bar .btn-search { padding: 10px 20px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800; cursor: pointer; }
    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-header { padding: 1.25rem 1.75rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #FAFBFC; }
    .table-card-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 800; color: #0f172a; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.8rem; font-weight: 800; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.92rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge-pending { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-pending .dot { background: #f59e0b; }
    .badge-documented { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-documented .dot { background: #3b82f6; }
    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 5000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 720px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
    .modal-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 1.5rem; overflow-y: auto; }
    .modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1rem; }
    .info-cell { background: #fff; padding: 12px 16px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
    .info-cell-value { font-size: 0.9rem; color: #0f172a; font-weight: 800; }
    .btn-cancel { padding: 10px 20px; background: #fff; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; }
</style>
@endsection

@section('content')
<form method="GET" action="{{ $portalBase }}/referrals/autopsy" class="top-card">
    <div class="filter-bar">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="بحث برقم الحيوان أو رقم الإحالة...">
        </div>
        <select class="filter-select" name="status" onchange="this.form.submit()">
            <option value="">كل الحالات</option>
            <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>بانتظار التوثيق</option>
            <option value="documented" @selected(($filters['status'] ?? '') === 'documented')>موثقة</option>
        </select>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'withValues' => true, 'selected' => $filters['group'] ?? ''])
        </select>
        <button type="submit" class="btn-search">بحث</button>
    </div>
</form>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
            إحالات التشريح
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
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($referrals as $referral)
                    @php
                        $animal = $referral->animal;
                        $mortality = $referral->mortalityCase;
                        $statusBadge = $referral->status === AutopsyReferralStatus::Documented ? 'badge-documented' : 'badge-pending';
                    @endphp
                    <tr>
                        @include('partials.animal-table-cell', [
                            'name' => $animal?->name,
                            'emoji' => '🐾',
                            'animalId' => $animal?->code ?? $mortality?->subject_code,
                        ])
                        <td style="font-weight:700;">{{ $animal?->species ?? $mortality?->subject_type ?? '—' }}</td>
                        <td>{{ $referral->group }}</td>
                        <td>{{ $referral->referred_at?->format('Y-m-d') }}</td>
                        <td><span class="badge {{ $statusBadge }}"><span class="dot"></span>{{ $referral->status->label() }}</span></td>
                        <td>
                            @if($isVetPortal)
                                <a href="{{ $portalBase }}/referrals/autopsy/{{ $referral->referral_number }}" class="btn-tbl view" title="عرض التفاصيل">
                                    @include('partials.icon-eye-view')
                                </a>
                            @else
                                <button type="button" class="btn-tbl view" title="عرض التفاصيل"
                                    data-referral-number="{{ $referral->referral_number }}"
                                    onclick="openModal(this.dataset.referralNumber)">
                                    @include('partials.icon-eye-view')
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2.5rem;color:#64748b;font-weight:700;">لا توجد إحالات تشريح مسجّلة حالياً.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($referrals->hasPages())
        <div style="padding:1rem 1.75rem;border-top:1px solid #f1f5f9;background:#FAFBFC;">{{ $referrals->links() }}</div>
    @endif
</div>
@endsection

@unless($isVetPortal)
@push('modals')
<div class="modal-backdrop" id="autopsyModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="margin:0;font-weight:800;">تفاصيل إحالة تشريح — <span id="mRefId">—</span></h3>
            <button type="button" class="btn-cancel" onclick="closeModal()" style="width:32px;height:32px;padding:0;">✕</button>
        </div>
        <div class="modal-body">
            <div class="info-grid">
                <div class="info-cell"><div class="info-cell-label">رقم الحيوان</div><div class="info-cell-value" id="mAnimalCode">—</div></div>
                <div class="info-cell"><div class="info-cell-label">نوع الحيوان</div><div class="info-cell-value" id="mAnimalType">—</div></div>
                <div class="info-cell"><div class="info-cell-label">حالة النفوق</div><div class="info-cell-value" id="mMortalityCase">—</div></div>
                <div class="info-cell"><div class="info-cell-label">تاريخ النفوق</div><div class="info-cell-value" id="mDeathDate">—</div></div>
                <div class="info-cell span-2"><div class="info-cell-label">سبب النفوق (من المشرف)</div><div class="info-cell-value" id="mDeathCause">—</div></div>
                <div class="info-cell span-2"><div class="info-cell-label">سبب الإحالة للتشريح</div><div class="info-cell-value" id="mTransferReason">—</div></div>
                <div class="info-cell"><div class="info-cell-label">الحالة</div><div class="info-cell-value" id="mStatus">—</div></div>
                <div class="info-cell"><div class="info-cell-label">المشرف</div><div class="info-cell-value" id="mSupervisor">—</div></div>
                <div class="info-cell span-2" id="mResultWrap" style="display:none;">
                    <div class="info-cell-label">سبب النفوق النهائي (بعد التشريح)</div>
                    <div class="info-cell-value" id="mFinalCause">—</div>
                </div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn-cancel" onclick="closeModal()">إغلاق</button></div>
    </div>
</div>
@endpush
@endunless

@section('scripts')
<script>
    const referrals = @json($referralsForJs ?? []);
    function openModal(refId) {
        const d = referrals[refId];
        if (!d) return;
        document.getElementById('mRefId').textContent = d.referral_number;
        document.getElementById('mAnimalCode').textContent = d.animal_code || '—';
        document.getElementById('mAnimalType').textContent = d.animal_species || '—';
        document.getElementById('mMortalityCase').textContent = d.mortality_case_number || '—';
        document.getElementById('mDeathDate').textContent = d.death_date || '—';
        document.getElementById('mDeathCause').textContent = d.death_cause || '—';
        document.getElementById('mTransferReason').textContent = d.transfer_reason || '—';
        document.getElementById('mStatus').textContent = d.status_label || '—';
        document.getElementById('mSupervisor').textContent = d.supervisor || '—';
        const resultWrap = document.getElementById('mResultWrap');
        if (d.status === 'documented' && d.final_death_cause) {
            resultWrap.style.display = 'block';
            document.getElementById('mFinalCause').textContent = d.final_death_cause;
        } else {
            resultWrap.style.display = 'none';
        }
        document.getElementById('autopsyModal').classList.add('open');
    }
    function closeModal() { document.getElementById('autopsyModal')?.classList.remove('open'); }
    @if($isVetPortal && !empty($highlightReferral))
        document.addEventListener('DOMContentLoaded', () => {
            window.location.href = @json($portalBase.'/referrals/autopsy/'.$highlightReferral);
        });
    @elseif(!$isVetPortal && !empty($highlightReferral))
        document.addEventListener('DOMContentLoaded', () => openModal(@json($highlightReferral)));
    @endif
</script>
@endsection
