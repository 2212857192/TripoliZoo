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
    /* تصميم راقي وعصري لصفحة الملاحظات التشغيلية */
    .top-card {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 18px rgba(0,0,0,0.02);
    }
    .filter-bar {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    .search-box {
        flex: 1;
        min-width: 250px;
        position: relative;
    }
    .search-box input {
        width: 100%;
        padding: 12px 42px 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-box input:focus {
        border-color: #1a4a2e;
        box-shadow: 0 0 0 3px rgba(26, 74, 46, 0.1);
    }
    .search-box svg {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .filter-select {
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .filter-select:focus {
        border-color: #1a4a2e;
    }
    .segmented-tabs {
        display: inline-flex;
        background: #f1f5f9;
        padding: 6px;
        border-radius: 14px;
        gap: 4px;
    }
    .seg-tab {
        background: transparent;
        border: none;
        padding: 8px 24px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.88rem;
        font-weight: 800;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .seg-tab:hover {
        color: #1a4a2e;
    }
    .seg-tab.active {
        background: #fff;
        color: #1a4a2e;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    
    /* شبكة الكروت الاحترافية */
    .notes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .note-card {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 24px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1.2rem;
        position: relative;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.015);
    }
    .note-card:hover {
        transform: translateY(-4px);
        border-color: #cbd5e1;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }
    .note-card-highlight {
        border-color: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
    }
    .note-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .note-card-meta {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .note-id {
        font-family: 'Courier New', monospace;
        font-size: 0.78rem;
        background: #f8fafc;
        padding: 4px 10px;
        border-radius: 8px;
        color: #475569;
        font-weight: 800;
        border: 1px solid #e2e8f0;
    }
    .note-card-body {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.7;
        flex-grow: 1;
    }
    .note-attach-wrap {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
        position: relative;
    }
    .note-attach-thumb {
        width: 100%;
        max-height: 190px;
        object-fit: cover;
        transition: transform 0.3s ease;
        display: block;
    }
    .note-attach-wrap:hover .note-attach-thumb {
        transform: scale(1.03);
    }
    .note-card-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding-top: 1rem;
        border-top: 1px solid #f8fafc;
    }
    .note-card-author-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .note-card-author-info .group-name {
        font-size: 0.85rem;
        font-weight: 800;
        color: #334155;
    }
    .note-card-date {
        font-size: 0.78rem;
        font-weight: 700;
        color: #94a3b8;
    }
    .btn-review-note {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        background: #1a4a2e;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.8rem;
        font-weight: 800;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.15s;
    }
    .btn-review-note:hover {
        background: #123320;
    }
    .btn-review-note:active {
        transform: scale(0.97);
    }
    
    .badge {
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
    }
    .badge .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .badge-new {
        background: #fef2f2;
        color: #ef4444;
        border-color: #fee2e2;
    }
    .badge-new .dot {
        background: #ef4444;
    }
    .badge-reviewed {
        background: #f0fdf4;
        color: #22c55e;
        border-color: #dcfce7;
    }
    .badge-reviewed .dot {
        background: #22c55e;
    }
    .badge-type-feeding {
        background: #fffbeb;
        color: #d97706;
        border-color: #fef3c7;
    }
    .badge-type-feeding .dot {
        background: #f59e0b;
    }
    .badge-type-general {
        background: #eff6ff;
        color: #3b82f6;
        border-color: #dbeafe;
    }
    .badge-type-general .dot {
        background: #3b82f6;
    }
    /* تصميم المودال الاحترافي للتأكيد */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        animation: fadeIn 0.25s ease-out;
    }
    .modal-container {
        background: #fff;
        border-radius: 24px;
        padding: 2rem;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        border: 1px solid #e8edf5;
        text-align: center;
        animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .modal-icon {
        width: 56px;
        height: 56px;
        background: #f0fdf4;
        color: #22c55e;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
    }
    .modal-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    .modal-desc {
        font-size: 0.88rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 1.75rem;
    }
    .modal-actions {
        display: flex;
        gap: 0.75rem;
    }
    .modal-btn {
        flex: 1;
        padding: 12px;
        border-radius: 12px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.88rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    .modal-btn-confirm {
        background: #1a4a2e;
        color: #fff;
    }
    .modal-btn-confirm:hover {
        background: #123320;
    }
    .modal-btn-cancel {
        background: #f1f5f9;
        color: #475569;
    }
    .modal-btn-cancel:hover {
        background: #e2e8f0;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
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
    </form>
</div>

<div class="notes-grid">
    @forelse($notes as $note)
        @php
            $typeBadge = $note->note_kind === OperationalNoteKind::Feeding ? 'badge-type-feeding' : 'badge-type-general';
            $statusBadge = $note->status === OperationalNoteStatus::New ? 'badge-new' : 'badge-reviewed';
            $attachmentUrl = $note->attachment_path ? \Illuminate\Support\Facades\Storage::url($note->attachment_path) : null;
            $canReview = ! $readOnly && $note->status === OperationalNoteStatus::New;
        @endphp
        <article class="note-card" id="note-{{ $note->note_number }}">
            <div class="note-card-head">
                <span class="note-id">{{ $note->note_number }}</span>
                <div class="note-card-meta">
                    <span class="badge {{ $typeBadge }}"><span class="dot"></span>{{ $note->note_kind->label() }}</span>
                    <span class="badge {{ $statusBadge }}"><span class="dot"></span>{{ $note->status->label() }}</span>
                </div>
            </div>
            <div class="note-card-body">
                <div class="note-summary-text">{{ $note->summary }}</div>
                @if($note->details)
                    <div class="note-details-text" style="font-size:0.85rem;font-weight:600;color:#64748b;margin-top:6px;border-top:1px dashed #e2e8f0;padding-top:6px;">{{ $note->details }}</div>
                @endif
            </div>

            <div class="note-card-foot">
                <div>
                    <div style="font-size:0.82rem;font-weight:800;color:#475569;">{{ $note->group }} · {{ $note->supervisor?->name ?? '—' }}</div>
                    <div class="note-card-date">{{ $note->noted_at?->format('Y-m-d') }}</div>
                </div>
                @if($canReview)
                    <button type="button" class="btn-review-note" onclick="openReviewModal('{{ route('care.notes.review', $note->note_number) }}')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        تحديد كمراجعة
                    </button>
                @endif
            </div>
        </article>
    @empty
        <div class="notes-empty">لا توجد ملاحظات تشغيلية في هذا القسم حالياً.</div>
    @endforelse
</div>
@if($notes->hasPages())
    <div class="notes-pagination">{{ $notes->links() }}</div>
@endif

{{-- مودال التأكيد الاحترافي المشترك --}}
<div id="confirmModalOverlay" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
        <div class="modal-title">تأكيد مراجعة الملاحظة</div>
        <div class="modal-desc">هل أنت متأكد من تحديد هذه الملاحظة كـ "تمت المراجعة"؟ سيتم نقلها للأرشيف.</div>
        <form id="confirmModalForm" method="POST" action="">
            @csrf
            <div class="modal-actions">
                <button type="submit" class="modal-btn modal-btn-confirm">تأكيد المراجعة</button>
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeReviewModal()">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let activeActionUrl = '';

    function openReviewModal(actionUrl) {
        const modal = document.getElementById('confirmModalOverlay');
        const form = document.getElementById('confirmModalForm');
        form.action = actionUrl;
        modal.style.display = 'flex';
    }

    function closeReviewModal() {
        const modal = document.getElementById('confirmModalOverlay');
        modal.style.display = 'none';
    }

    // إغلاق المودال عند النقر خارج مساحة الحاوية
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('confirmModalOverlay');
        if (event.target === modal) {
            closeReviewModal();
        }
    });

    @if(!empty($highlightNote))
        document.addEventListener('DOMContentLoaded', () => {
            const card = document.getElementById('note-{{ $highlightNote }}');
            if (!card) return;
            card.classList.add('note-card-highlight');
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    @endif
</script>
@endsection
