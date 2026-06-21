@extends($__layout ?? 'care.layout')
@php
    use App\Enums\OperationalNoteKind;
    use App\Enums\OperationalNoteStatus;
    $activeStatus = $activeStatus ?? 'new';
    $readOnly = $readOnly ?? false;
    $portalBase = $portalBase ?? (($readOnly ?? false) ? '/director/care' : '/care');
@endphp
@section('title', 'الملاحظات التشغيلية | الرعاية والتغذية')
@section('page_title', 'الملاحظات التشغيلية')

@section('styles')
<style>
    .top-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1.2rem; }
    .filter-bar { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
    .search-box { flex: 1; min-width: 250px; position: relative; }
    .search-box input { width: 100%; padding: 10px 40px 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; outline: none; }
    .search-box svg { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .filter-select { padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #334155; outline: none; cursor: pointer; }
    .segmented-tabs { display: inline-flex; background: #f1f5f9; padding: 5px; border-radius: 10px; gap: 4px; }
    .seg-tab { background: transparent; border: none; padding: 9px 24px; border-radius: 7px; font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800; color: #64748b; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
    .seg-tab:hover { color: #1a4a2e; }
    .seg-tab.active { background: #fff; color: #1a4a2e; box-shadow: 0 2px 4px rgba(0,0,0,0.07); }
    .table-card { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
    .table-card-footer { padding: 1.1rem 1.75rem; display: flex; align-items: center; justify-content: flex-end; border-top: 1px solid #f1f5f9; background: #FAFBFC; }
    .custom-table { width: 100%; border-collapse: collapse; text-align: right; }
    .custom-table thead th { background: #F8FAFC; color: var(--text-muted); font-size: 0.8rem; font-weight: 800; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .custom-table tbody tr:hover { background: #FAFBFC; }
    .custom-table tbody td { padding: 16px 20px; border-bottom: 1px solid #F1F5F9; font-size: 0.92rem; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .custom-table tbody tr:last-child td { border-bottom: none; }
    .note-id { font-family: 'Courier New', monospace; font-size: 0.75rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }
    .note-summary { max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }
    .badge-new { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-new .dot { background: #ef4444; }
    .badge-reviewed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-reviewed .dot { background: #22c55e; }
    .badge-type-feeding { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-type-feeding .dot { background: #f59e0b; }
    .badge-type-general { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .badge-type-general .dot { background: #3b82f6; }
    .btn-tbl { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .btn-tbl.view:hover { color: #0284C7; background: #E0F2FE; border-color: #BAE6FD; }
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(5px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    #noteModal .modal-box { background: #f8fafc; border-radius: 20px; width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px rgba(0,0,0,0.15); animation: modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
    @keyframes modalIn { from { transform: translateY(24px) scale(0.97); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
    #noteModal .modal-header { background: transparent; border-bottom: none; display: flex; justify-content: center; position: relative; padding: 2rem 1.5rem 0; }
    #noteModal .modal-header h3 { font-size: 1.4rem; font-weight: 800; color: #1e293b; margin: 0; text-align: center; }
    #noteModal .modal-close { position: absolute; left: 1.5rem; top: 1.5rem; width: 32px; height: 32px; border-radius: 8px; background: #e2e8f0; border: none; color: #64748b; cursor: pointer; font-size: 1.2rem; font-weight: 700; }
    #noteModal .modal-tabs-bar { display: flex; justify-content: center; gap: 0; padding: 1rem 2rem 0; }
    #noteModal .modal-tab { padding: 8px 20px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; color: #94a3b8; border-bottom: 3px solid transparent; }
    #noteModal .modal-tab.active { color: #16a34a; border-bottom-color: #16a34a; }
    #noteModal .modal-body { padding: 1.5rem 2rem; }
    #noteModal .q-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { #noteModal .q-grid { grid-template-columns: 1fr; } }
    #noteModal .q-card { background: #fff; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    #noteModal .q-card-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center; }
    #noteModal .q-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9; }
    #noteModal .q-label { color: #64748b; font-size: 0.9rem; font-weight: 700; }
    #noteModal .q-value { color: #0f172a; font-size: 0.95rem; font-weight: 800; }
    #noteModal .q-note-box { background: #f8fafc; padding: 12px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; color: #334155; border: 1px solid #f1f5f9; line-height: 1.7; white-space: pre-wrap; }
    #noteModal .q-attach-wrap { text-align: center; padding: 2rem 1rem; }
    #noteModal .q-attach-empty { width: 180px; height: 180px; border-radius: 16px; margin: 0 auto; background: #f8fafc; border: 2px dashed #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700; color: #94a3b8; }
    #noteModal .q-attach-img { width: 180px; height: 180px; border-radius: 16px; margin: 0 auto; object-fit: cover; border: 2px solid #e2e8f0; display: block; }
    #noteModal .modal-footer { background: transparent; border-top: none; padding: 0 2rem 1.5rem; display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap; }
    #noteModal .btn-action-release { padding: 8px 16px; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    #noteModal .btn-cancel { padding: 8px 16px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    .dialog-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45); backdrop-filter: blur(3px); z-index: 1100; align-items: center; justify-content: center; }
    .dialog-backdrop.open { display: flex; }
    .dialog-box { background: #fff; border-radius: 18px; width: 100%; max-width: 440px; box-shadow: 0 30px 80px rgba(0,0,0,0.2); overflow: hidden; }
    .dialog-body { padding: 2rem 2rem 1.5rem; text-align: center; }
    .dialog-footer { padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: center; }
    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; }
</style>
@endsection

@section('content')
<div class="top-card">
    <div class="segmented-tabs">
        <a href="{{ route('care.notes.index', array_merge($filters ?? [], ['status' => 'new'])) }}" class="seg-tab {{ $activeStatus === 'new' ? 'active' : '' }}">جديدة</a>
        <a href="{{ route('care.notes.index', array_merge($filters ?? [], ['status' => 'reviewed'])) }}" class="seg-tab {{ $activeStatus === 'reviewed' ? 'active' : '' }}">تمت المراجعة</a>
    </div>
    <form method="GET" action="{{ route('care.notes.index') }}" class="filter-bar">
        <input type="hidden" name="status" value="{{ $activeStatus }}">
        <div class="search-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="بحث برقم الملاحظة أو نصها...">
        </div>
        <select class="filter-select" name="kind" onchange="this.form.submit()">
            <option value="">نوع الملاحظة</option>
            <option value="feeding" @selected(($filters['kind'] ?? '') === 'feeding')>تغذية</option>
            <option value="general" @selected(($filters['kind'] ?? '') === 'general')>ملاحظة عامة</option>
        </select>
        <select class="filter-select" name="group" onchange="this.form.submit()">
            @include('partials.animal-group-options', ['emptyLabel' => 'كل المجموعات', 'withValues' => true, 'selected' => $filters['group'] ?? ''])
        </select>
        <button type="submit" style="padding:10px 20px;background:linear-gradient(135deg,#1a4a2e,#2d7a47);color:#fff;border:none;border-radius:10px;font-family:'Cairo',sans-serif;font-size:0.85rem;font-weight:800;cursor:pointer;">بحث</button>
    </form>
</div>

<div class="table-card">
    <div style="overflow-x:auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>رقم الملاحظة</th>
                    <th>نوع الملاحظة</th>
                    <th>المجموعة</th>
                    <th>المشرف</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $note)
                    @php
                        $typeBadge = $note->note_kind === OperationalNoteKind::Feeding ? 'badge-type-feeding' : 'badge-type-general';
                        $statusBadge = $note->status === OperationalNoteStatus::New ? 'badge-new' : 'badge-reviewed';
                    @endphp
                    <tr>
                        <td><span class="note-id">{{ $note->note_number }}</span></td>
                        <td><span class="badge {{ $typeBadge }}"><span class="dot"></span>{{ $note->note_kind->label() }}</span></td>
                        <td>{{ $note->group }}</td>
                        <td>{{ $note->supervisor?->name ?? '—' }}</td>
                        <td>{{ $note->noted_at?->format('Y-m-d') }}</td>
                        <td><span class="badge {{ $statusBadge }}"><span class="dot"></span>{{ $note->status->label() }}</span></td>
                        <td>
                            <button type="button"
                                class="btn-tbl view"
                                title="عرض التفاصيل"
                                data-note-number="{{ $note->note_number }}"
                                onclick="openModal(this.dataset.noteNumber)">
                                @include('partials.icon-eye-view')
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2.5rem;color:#64748b;font-weight:700;">
                            لا توجد ملاحظات تشغيلية في هذا القسم حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($notes->hasPages())
        <div class="table-card-footer">{{ $notes->links() }}</div>
    @endif
</div>
@endsection

@push('modals')
<div class="modal-backdrop" id="noteModal" onclick="if(event.target===this) closeModal()">
    <div class="modal-box">
        <div class="modal-header">
            <h3>تفاصيل الملاحظة — <span id="modalNoteId">—</span></h3>
            <button type="button" class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-tabs-bar">
            <button type="button" class="modal-tab active" id="ntab-btn-1" onclick="switchNTab(1)">بيانات الملاحظة</button>
            <button type="button" class="modal-tab" id="ntab-btn-2" onclick="switchNTab(2)">المرفقات</button>
        </div>
        <div class="modal-body">
            <div id="ntab-1">
                <div class="q-grid">
                    <div class="q-card">
                        <h4 class="q-card-title">بيانات الملاحظة</h4>
                        <div class="q-row"><span class="q-label">رقم الملاحظة</span><span class="q-value" id="nNoteId">—</span></div>
                        <div class="q-row"><span class="q-label">نوع الملاحظة</span><span class="q-value" id="nType">—</span></div>
                        <div class="q-row"><span class="q-label">المجموعة</span><span class="q-value" id="nGroup">—</span></div>
                        <div class="q-row"><span class="q-label">المشرف</span><span class="q-value" id="nSupervisor">—</span></div>
                        <div class="q-row"><span class="q-label">تاريخ الملاحظة</span><span class="q-value" id="nDate">—</span></div>
                        <div class="q-row" style="border-bottom:none;margin-bottom:0;padding-bottom:0;"><span class="q-label">الحالة</span><span class="q-value" id="nStatus">—</span></div>
                    </div>
                    <div class="q-card">
                        <h4 class="q-card-title">نص الملاحظة</h4>
                        <div class="q-note-box" id="nContent">—</div>
                        <div id="nDetailsWrap" style="margin-top:1rem;display:none;">
                            <h4 class="q-card-title" style="font-size:0.95rem;">تفاصيل إضافية</h4>
                            <div class="q-note-box" id="nDetails">—</div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="ntab-2" style="display:none;">
                <div class="q-card">
                    <h4 class="q-card-title">المرفقات</h4>
                    <div class="q-attach-wrap" id="nAttachWrap">
                        <div class="q-attach-empty">لا توجد مرفقات</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" id="nFooter"></div>
    </div>
</div>

@if(!$readOnly)
<div class="dialog-backdrop" id="confirmReviewDialog" onclick="if(event.target===this) closeDialog('confirmReviewDialog')">
    <div class="dialog-box">
        <div class="dialog-body">
            <h4>تأكيد المراجعة</h4>
            <p>هل أنت متأكد من تحديد هذه الملاحظة التشغيلية كـ <strong>تمت المراجعة</strong>؟</p>
        </div>
        <div class="dialog-footer">
            <button type="button" class="btn-cancel" onclick="closeDialog('confirmReviewDialog')">إلغاء</button>
            <form method="POST" id="reviewForm" action="#">
                @csrf
                <button type="submit" class="btn-submit">نعم، تحديد كمراجعة</button>
            </form>
        </div>
    </div>
</div>
@endif
@endpush

@section('scripts')
<script>
    const notes = @json($notesForJs ?? []);

    function switchNTab(n) {
        document.getElementById('ntab-1').style.display = n === 1 ? 'block' : 'none';
        document.getElementById('ntab-2').style.display = n === 2 ? 'block' : 'none';
        document.getElementById('ntab-btn-1').className = 'modal-tab' + (n === 1 ? ' active' : '');
        document.getElementById('ntab-btn-2').className = 'modal-tab' + (n === 2 ? ' active' : '');
    }

    function openModal(noteNumber) {
        const d = notes[noteNumber];
        if (!d) return;
        switchNTab(1);

        document.getElementById('modalNoteId').textContent = d.note_number;
        document.getElementById('nNoteId').textContent = d.note_number;
        document.getElementById('nType').textContent = d.note_kind_label;
        document.getElementById('nGroup').textContent = d.group;
        document.getElementById('nSupervisor').textContent = d.supervisor || '—';
        document.getElementById('nDate').textContent = d.noted_at_display || d.noted_at || '—';
        document.getElementById('nStatus').textContent = d.status_label;
        document.getElementById('nContent').textContent = d.summary;

        const detailsWrap = document.getElementById('nDetailsWrap');
        if (d.details) {
            detailsWrap.style.display = 'block';
            document.getElementById('nDetails').textContent = d.details;
        } else {
            detailsWrap.style.display = 'none';
        }

        const attachWrap = document.getElementById('nAttachWrap');
        if (d.has_attachment && d.attachment_url) {
            attachWrap.innerHTML = '<a href="' + d.attachment_url + '" target="_blank" rel="noopener"><img src="' + d.attachment_url + '" alt="مرفق الملاحظة" class="q-attach-img"></a>';
        } else {
            attachWrap.innerHTML = '<div class="q-attach-empty">لا توجد مرفقات</div>';
        }

        const footer = document.getElementById('nFooter');
        const closeBtn = '<button type="button" class="btn-cancel" onclick="closeModal()">إغلاق</button>';
        if (d.can_review && d.review_url) {
            footer.innerHTML = closeBtn + '<button type="button" class="btn-action-release" onclick="markReviewed()">تحديد كمراجعة</button>';
            const form = document.getElementById('reviewForm');
            if (form) form.action = d.review_url;
        } else {
            footer.innerHTML = closeBtn;
        }

        document.getElementById('noteModal').classList.add('open');
    }

    function closeModal() { document.getElementById('noteModal').classList.remove('open'); }
    function openDialog(id) { document.getElementById(id)?.classList.add('open'); }
    function closeDialog(id) { document.getElementById(id)?.classList.remove('open'); }
    function markReviewed() { openDialog('confirmReviewDialog'); }

    window.openOperationalNoteModal = openModal;

    @if(!empty($highlightNote))
        document.addEventListener('DOMContentLoaded', () => openModal(@json($highlightNote)));
    @endif
</script>
@endsection
