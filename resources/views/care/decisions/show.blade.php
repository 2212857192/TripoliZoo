@extends($__layout ?? 'care.layout')
@section('title', 'تفاصيل قرار طبي | الرعاية والتغذية')
@section('page_title', 'تفاصيل قرار طبي')

@section('styles')
<style>
    .header-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
    .header-info h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; display: flex; align-items: center; gap: 12px; }
    
    /* ═══ BADGES ═══ */
    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    
    .type-discharge { background: #f0fdfa; color: #0f766e; border: 1px solid #ccfbf1; }
    .type-discharge .dot { background: #14b8a6; }
    .type-release { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .type-release .dot { background: #3b82f6; }
    .type-slaughter { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .type-slaughter .dot { background: #ef4444; }

    .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    .status-received { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce3; }
    .status-failed { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }

    /* ── TABS ── */
    .tabs-container { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; }
    .tabs-header { display: flex; background: #FAFBFC; border-bottom: 1px solid #e2e8f0; padding: 0 1rem; }
    .tab-btn { padding: 16px 24px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.95rem; font-weight: 800; color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .tab-btn:hover { color: var(--green); }
    .tab-btn.active { color: var(--green); border-bottom-color: var(--green); background: #fff; }

    .tab-content { padding: 2rem; display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
    .info-cell { background: #fff; padding: 16px 20px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.8rem; color: #64748b; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .info-cell-value { font-size: 1rem; color: #0f172a; font-weight: 800; }
    
    .content-box { background: #f8fafc; padding: 16px 20px; border-radius: 10px; font-size: 0.95rem; color: #334155; font-weight: 600; line-height: 1.7; border: 1px solid #e2e8f0; }
    .treatment-list { margin: 0; padding: 16px 20px 16px 0; list-style: disc; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; }
    .treatment-list li { margin: 0 1.5rem 0.6rem 0; font-size: 0.95rem; color: #334155; font-weight: 600; line-height: 1.6; }
    .treatment-list li:last-child { margin-bottom: 0; }
    .section-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }

    .id-tag { font-family: 'Courier New', monospace; font-size: 0.85rem; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

    .animal-avatar { width: 56px; height: 56px; border-radius: 14px; background: #f1f5f9; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; }
</style>
@endsection

@php
    $careBase = $portalBase ?? (($readOnly ?? false) ? '/director/care' : '/care');
    $isSlaughter = ($decisionKind ?? 'receiving') === 'slaughter';
    $animal = $isSlaughter ? $slaughterCase->animal : $task->animal;
    $typeKey = $isSlaughter ? 'slaughter' : $task->task_type->careDecisionTypeKey();
    $typeClass = match ($typeKey) {
        'release' => 'type-release',
        'slaughter' => 'type-slaughter',
        default => 'type-discharge',
    };
    $typeLabel = $isSlaughter ? 'ذبح اضطراري' : $task->task_type->careDecisionLabel();
    $issuer = $isSlaughter ? $slaughterCase->admitter : $task->decisionIssuer;
    $issuerLabel = $issuer ? $issuer->name.' ('.$issuer->role.')' : '—';
    $statusKey = $isSlaughter ? 'not-required' : $task->status->careStatusKey();
    $photoUrl = $animal?->photo_path ? \Illuminate\Support\Facades\Storage::url($animal->photo_path) : null;
    $treatments = $resolvedTreatments ?? ($isSlaughter
        ? []
        : array_values(array_filter($task->decision_treatments ?? [])));
    $sourceLabel = $isSlaughter
        ? \App\Enums\ReceivingTaskSource::Hospital->fromLabel()
        : $task->source->fromLabel();
    $decisionDate = $isSlaughter
        ? ($slaughterCase->closed_at?->format('Y-m-d') ?? '—')
        : ($task->decision_date?->format('Y-m-d') ?? '—');
    $decisionNotes = $isSlaughter
        ? ($slaughterCase->closing_outcome ?: '—')
        : ($task->decision_notes ?: '—');
@endphp

@section('content')

<div class="header-card header-card-stacked">
    <nav class="page-breadcrumb">
        <a href="{{ $careBase }}/decisions">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            القرارات الطبية
        </a>
        <span>/</span>
        <span class="current" id="breadId">تفاصيل القرار {{ $id }}</span>
    </nav>
    <div class="header-card-row">
        <div class="header-info">
            <h2>
                تفاصيل قرار طبي
                <span class="badge {{ $typeClass }}"><span class="dot"></span>{{ $typeLabel }}</span>
            </h2>
            <div style="font-size:0.9rem; color:#64748b; font-weight:700; margin-top:8px;">
                رقم القرار: <span class="id-tag" id="topId">{{ $id }}</span>
            </div>
        </div>
    </div>
</div>

<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab(1, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            بيانات القرار
        </button>
        <button class="tab-btn" onclick="switchTab(2, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            بيانات الحيوان
        </button>
        <button class="tab-btn" onclick="switchTab(3, this)" id="tab3-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            حالة الاستلام
        </button>
    </div>

    {{-- TAB 1: Decision Data --}}
    <div class="tab-content active" id="tab-1">
        <h3 class="section-title">المعلومات الأساسية للقرار</h3>
        <div class="info-grid" style="margin-bottom:1.5rem;">
            <div class="info-cell">
                <div class="info-cell-label">رقم القرار</div>
                <div class="info-cell-value id-tag">{{ $id }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">نوع القرار</div>
                <div class="info-cell-value">{{ $typeLabel }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">مصدر القرار</div>
                <div class="info-cell-value">{{ $sourceLabel }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">تاريخ إصدار القرار</div>
                <div class="info-cell-value">{{ $decisionDate }}</div>
            </div>
            <div class="info-cell span-2">
                <div class="info-cell-label">صادر بواسطة</div>
                <div class="info-cell-value">{{ $issuerLabel }}</div>
            </div>
        </div>

        <h3 class="section-title" style="margin-top:2rem;">التفاصيل الطبية المرفقة</h3>
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div>
                <div class="info-cell-label">العلاجات</div>
                @if(count($treatments))
                    <ul class="treatment-list">
                        @foreach($treatments as $treatment)
                            <li>{{ $treatment }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="content-box">—</div>
                @endif
            </div>
            <div>
                <div class="info-cell-label">ملاحظة</div>
                <div class="content-box">{{ $decisionNotes }}</div>
            </div>
        </div>
    </div>

    {{-- TAB 2: Animal Data --}}
    <div class="tab-content" id="tab-2">
        <h3 class="section-title">بيانات الحيوان المرتبط بالقرار</h3>
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-cell-label">اسم الحيوان</div>
                <div class="info-cell-value">{{ $animal?->name ?: '—' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">صورة</div>
                <div class="info-cell-value">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="" style="width:56px;height:56px;border-radius:14px;object-fit:cover;border:1px solid #e2e8f0;">
                    @else
                        <div class="animal-avatar">🐾</div>
                    @endif
                </div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">{{ $typeKey === 'release' ? 'رقم الحيوان في الحجر' : 'رقم الحيوان' }}</div>
                <div class="info-cell-value id-tag">{{ $animal?->code ?? '—' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">العلامة المميزة</div>
                <div class="info-cell-value">{{ $animal?->distinguishing_marks ?: '—' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">المجموعة المرتبطة</div>
                <div class="info-cell-value">{{ $animal?->group ?? '—' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">نوع الحيوان</div>
                <div class="info-cell-value">{{ $animal?->species ?? '—' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">الجنس</div>
                <div class="info-cell-value">{{ $animal?->gender ?? '—' }}</div>
            </div>
            <div class="info-cell span-2">
                <div class="info-cell-label">العمر الموثق</div>
                <div class="info-cell-value">{{ $animal?->formattedAge() ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- TAB 3: Reception Status --}}
    <div class="tab-content" id="tab-3">
        <div id="receptionContent">
            <h3 class="section-title">متابعة مهمة الاستلام</h3>
            @if($isSlaughter)
            <div class="info-grid">
                <div class="info-cell span-2">
                    <div class="info-cell-label">حالة الاستلام الحالية</div>
                    <div class="info-cell-value">
                        <span class="badge status-pending" style="font-size:0.9rem; padding:6px 14px; background:#f8fafc; color:#475569; border-color:#e2e8f0;">
                            لا يتطلب استلام
                        </span>
                    </div>
                </div>
            </div>
            @else
            <div class="info-grid">
                <div class="info-cell span-2">
                    <div class="info-cell-label">حالة الاستلام الحالية</div>
                    <div class="info-cell-value">
                        <span class="badge {{ $task->status->careStatusBadgeClass() }}" style="font-size:0.9rem; padding:6px 14px;">
                            {{ $task->status->label() }}
                        </span>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-label">المشرف المسؤول</div>
                    <div class="info-cell-value">{{ $task->supervisor?->name ?? '—' }}</div>
                </div>
                @if($statusKey === 'received' && $task->received_at)
                <div class="info-cell span-2">
                    <div class="info-cell-label">تاريخ الاستلام</div>
                    <div class="info-cell-value">{{ $task->received_at->format('Y-m-d') }}</div>
                </div>
                @endif
                @if($statusKey === 'received' && $task->receipt_note)
                <div class="info-cell span-2">
                    <div class="info-cell-label">ملاحظة الاستلام</div>
                    <div class="info-cell-value">{{ $task->receipt_note }}</div>
                </div>
                @endif
                @if($statusKey === 'failed' && $task->delay_recorded_at)
                <div class="info-cell span-2">
                    <div class="info-cell-label">تاريخ تسجيل التعذر</div>
                    <div class="info-cell-value">{{ $task->delay_recorded_at->format('Y-m-d') }}</div>
                </div>
                @endif
            </div>
            @endif

            @if(! $isSlaughter && $statusKey === 'failed' && $task->delay_reason)
            <div style="margin-top:1.5rem;">
                <div class="info-cell-label" style="color:#dc2626;">سبب التعذر المؤقت</div>
                <div class="content-box" style="border-color:#fecaca; background:#fef2f2; color:#b91c1c;">
                    {{ $task->delay_reason }}{{ $task->delay_extra_note ? ' — '.$task->delay_extra_note : '' }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function switchTab(n, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + n).classList.add('active');
        btn.classList.add('active');
    }
</script>
@endsection
