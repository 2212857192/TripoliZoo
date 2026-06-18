@extends($__layout ?? 'vet.layout')
@section('title', 'تفاصيل الحالة | المستشفى البيطري')
@section('page_title', 'تفاصيل الحالة')

@section('styles')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 700; color: #64748b; }
    .breadcrumb a { color: #2E7D32; text-decoration: none; transition: color 0.2s; display: flex; align-items: center; gap: 4px; }
    .breadcrumb a:hover { color: #1b5e20; }

    .header-card {
        background: #fff; border: 1px solid var(--border); border-radius: 16px;
        padding: 1.5rem 2rem; margin-bottom: 1.5rem;
        display: flex; justify-content: space-between; align-items: center; gap: 12px;
        flex-wrap: wrap;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .header-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .header-info h2 {
        font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0;
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    }

    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-ready { background: #f0fdfa; color: #0f766e; border: 1px solid #ccfbf1; }
    .status-ready .dot { background: #14b8a6; }
    .status-watch { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    .status-watch .dot { background: #f59e0b; }
    .status-no-response { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .status-no-response .dot { background: #ef4444; }
    .status-handover { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .status-handover .dot { background: #3b82f6; }
    .status-unavailable { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .status-unavailable .dot { background: #f97316; }
    .status-discharged { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .status-discharged .dot { background: #22c55e; }
    .status-slaughter { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .status-slaughter .dot { background: #ef4444; }

    .btn-back {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; background: #f8fafc; color: #475569;
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-family: 'Cairo', sans-serif; font-size: 0.9rem; font-weight: 800;
        cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .btn-back:hover { background: #f1f5f9; color: #0f172a; }

    .tabs-container { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; }
    .tabs-header { display: flex; background: #FAFBFC; border-bottom: 1px solid #e2e8f0; padding: 0 1rem; }
    .tab-btn {
        padding: 16px 24px; border: none; background: transparent;
        font-family: 'Cairo', sans-serif; font-size: 0.95rem; font-weight: 800;
        color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s;
        display: flex; align-items: center; gap: 8px;
    }
    .tab-btn:hover { color: var(--green); }
    .tab-btn.active { color: var(--green); border-bottom-color: var(--green); background: #fff; }

    .tab-content { padding: 2rem; display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    .case-top-bar {
        display: flex; align-items: center; gap: 1.2rem;
        padding-bottom: 1.5rem; margin-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .animal-photo {
        width: 72px; height: 72px; border-radius: 16px; flex-shrink: 0;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
        border: 2px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem; overflow: hidden;
    }
    .animal-photo img { width: 100%; height: 100%; object-fit: cover; }
    .case-top-name { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
    .case-top-vet { font-size: 0.9rem; color: #64748b; font-weight: 700; }

    .summary-layout { display: grid; grid-template-columns: 1fr 320px; gap: 1.5rem; align-items: start; }
    @media (max-width: 900px) { .summary-layout { grid-template-columns: 1fr; } }

    .animal-card {
        background: #fff; border-radius: 12px; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .animal-card-title { font-size: 1.05rem; font-weight: 800; color: #1e293b; margin-bottom: 1.2rem; }
    .q-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;
    }
    .q-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .q-label { color: #64748b; font-size: 0.9rem; font-weight: 700; }
    .q-value { color: #0f172a; font-size: 0.95rem; font-weight: 800; text-align: left; }

    .notes-panel {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        position: sticky; top: 1rem;
    }
    .notes-panel-title { font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; }
    .notes-panel-body {
        background: #f8fafc; padding: 14px 16px; border-radius: 10px;
        font-size: 0.9rem; color: #334155; font-weight: 600; line-height: 1.7;
        border: 1px solid #e2e8f0; min-height: 120px;
    }

    .section-title { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 1.5rem 0 1rem; }
    .content-box {
        background: #f8fafc; padding: 16px 20px; border-radius: 10px;
        font-size: 0.95rem; color: #334155; font-weight: 600; line-height: 1.7;
        border: 1px solid #e2e8f0;
    }
    .id-tag {
        font-family: 'Courier New', monospace; font-size: 0.85rem;
        background: #f1f5f9; padding: 4px 10px; border-radius: 6px;
        color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0;
    }

    .follow-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        overflow: hidden;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .follow-card.is-open {
        border-color: #bbf7d0;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.08);
    }
    .follow-card-toggle {
        width: 100%;
        border: none;
        background: #FAFBFC;
        padding: 1rem 1.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        cursor: pointer;
        text-align: right;
        font-family: 'Cairo', sans-serif;
    }
    .follow-card-toggle:hover { background: #f4f7f4; }
    .follow-card.is-open .follow-card-toggle { background: #f0fdf4; border-bottom: 1px solid #e2e8f0; }
    .follow-card-main { flex: 1; min-width: 0; }
    .follow-card-top {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
        margin-bottom: 6px;
    }
    .follow-vet { font-size: 0.92rem; font-weight: 800; color: #0f172a; }
    .follow-date { font-size: 0.75rem; color: #94a3b8; font-weight: 700; white-space: nowrap; }
    .follow-card-preview {
        font-size: 0.84rem; color: #64748b; font-weight: 600; line-height: 1.5;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .follow-card-chevron {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        background: #fff; border: 1px solid #e2e8f0; color: #64748b;
        display: flex; align-items: center; justify-content: center;
        transition: transform 0.2s, background 0.2s, color 0.2s;
    }
    .follow-card.is-open .follow-card-chevron {
        transform: rotate(180deg);
        background: #e6f4ea;
        color: #2E7D32;
        border-color: #bbf7d0;
    }
    .follow-card-body {
        display: none;
        padding: 1.1rem 1.2rem 1.2rem;
        animation: fadeIn 0.25s ease;
    }
    .follow-card.is-open .follow-card-body { display: block; }
    .follow-field { margin-bottom: 1rem; }
    .follow-field:last-child { margin-bottom: 0; }
    .follow-field-label { font-size: 0.75rem; color: #64748b; font-weight: 800; margin-bottom: 6px; }
    .follow-field-label .req { color: #ef4444; }
    .follow-field-value {
        background: #f8fafc; padding: 12px 14px; border-radius: 8px;
        font-size: 0.88rem; color: #1e293b; font-weight: 700; line-height: 1.6;
        border: 1px solid #f1f5f9;
    }
    .nutrition-block { margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0; }
    .nutrition-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 0.5rem; }
    @media (max-width: 600px) { .nutrition-grid { grid-template-columns: 1fr; } }

    .follow-status-wrap { background: transparent !important; border: none !important; padding: 0 !important; }
    .follow-status-badge {
        padding: 5px 12px; border-radius: 999px; font-size: 0.78rem; font-weight: 700;
        display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;
    }
    .follow-status-badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .follow-status-ready { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .follow-status-ready .dot { background: #22c55e; }
    .follow-status-watch { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .follow-status-watch .dot { background: #f59e0b; }
    .follow-status-no-response { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .follow-status-no-response .dot { background: #ef4444; }

    .decision-panel {
        margin-top: 1.5rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 1.25rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .decision-panel-title { font-size: 1rem; font-weight: 800; color: #1e293b; margin-bottom: 1rem; }
    .treatment-list {
        margin: 0; padding: 12px 14px 12px 0; list-style: disc;
        background: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9;
    }
    .treatment-list li {
        margin: 0 1.4rem 0.5rem 0; font-size: 0.88rem; color: #1e293b;
        font-weight: 700; line-height: 1.6;
    }
    .treatment-list li:last-child { margin-bottom: 0; }

    .action-bar {
        margin-top: 1.5rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.2rem 1.4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
    .action-bar-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
    }
    .action-bar-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }
    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #2E7D32, #2d7a47);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.88rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(45,122,71,0.3);
        text-decoration: none;
    }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(45,122,71,0.35); }
    .btn-submit-red {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #be123c, #e11d48);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.88rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(225,29,72,0.25);
    }
    .btn-submit-red:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(225,29,72,0.3); }
    .btn-decision {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #c2410c, #ea580c);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.88rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(234,88,12,0.25);
    }
    .btn-decision:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(234,88,12,0.3); }

    .modal-backdrop {
        display: none; position: fixed; inset: 0;
        background: rgba(15,23,42,0.55); backdrop-filter: blur(5px);
        z-index: 1000; align-items: center; justify-content: center; padding: 20px;
    }
    .modal-backdrop.open { display: flex; }
    .decision-box {
        background: #fff; border-radius: 20px; width: 100%; max-width: 520px;
        max-height: 90vh; overflow-y: auto;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        animation: fadeIn 0.25s ease;
    }
    .decision-header {
        padding: 1.2rem 1.5rem; border-bottom: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: space-between;
        background: #F8FAFC; border-radius: 20px 20px 0 0;
    }
    .decision-header h3 { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0; }
    .modal-close {
        width: 32px; height: 32px; border-radius: 8px; background: #e2e8f0; border: none;
        color: #64748b; display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 1.1rem; font-weight: 700;
    }
    .decision-body { padding: 1.4rem 1.5rem; }
    .decision-footer {
        padding: 1.1rem 1.5rem; border-top: 1px solid #e2e8f0;
        display: flex; gap: 10px; justify-content: flex-end; background: #F8FAFC;
        border-radius: 0 0 20px 20px;
    }
    .decision-options { display: flex; flex-direction: column; gap: 10px; }
    .decision-option {
        display: flex; align-items: center; gap: 14px; padding: 1rem;
        border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.2s;
    }
    .decision-option:hover { border-color: #94a3b8; background: #f8fafc; }
    .decision-option.selected { border-color: #2E7D32; background: #F0FDF4; }
    .opt-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;
    }
    .opt-title { font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
    .opt-desc { font-size: 0.78rem; color: #64748b; font-weight: 600; line-height: 1.45; }
    .decision-hint {
        background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px;
        padding: 10px 12px; font-size: 0.82rem; color: #1d4ed8; font-weight: 700;
        margin-bottom: 1rem; line-height: 1.5;
    }
    .decision-note {
        width: 100%; padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600;
        resize: vertical; min-height: 72px; outline: none; margin-top: 1rem;
    }
    .btn-cancel {
        padding: 10px 18px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
        border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem;
        font-weight: 800; cursor: pointer;
    }
</style>
@endsection

@php $vetBase = ($readOnly ?? false) ? '/director/vet' : '/vet'; @endphp

@section('content')

<div class="breadcrumb">
    <a href="{{ $vetBase }}/cases/hospital">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        الحالات داخل المستشفى
    </a>
    <span>/</span>
    <span style="color:#0f172a;">تفاصيل الحالة</span>
</div>

<div class="header-card">
    <div class="header-info">
        <h2>
            تفاصيل الحالة
            <span id="headerBadge"></span>
        </h2>
    </div>
    <div class="header-actions">
        @if($canIssueDecision ?? false)
        <button type="button" class="btn-decision" onclick="openDecisionModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            إصدار قرار طبي
        </button>
        @endif
        <a href="{{ $vetBase }}/cases/hospital" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            العودة
        </a>
    </div>
</div>

<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab(1, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            ملخص الحالة
        </button>
        <button class="tab-btn" onclick="switchTab(2, this)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            المتابعة الطبية
        </button>
    </div>

    <div class="tab-content active" id="tab-1">
        <div class="case-top-bar">
            <div class="animal-photo" id="topAnimalPhoto">🦁</div>
            <div>
                <div class="case-top-name" id="topAnimalName">—</div>
                <div class="case-top-vet">الطبيب المشرف: <span id="topVet">—</span></div>
            </div>
        </div>

        <div class="summary-layout">
            <div class="animal-card">
                <h4 class="animal-card-title">ملخص الحالة</h4>
                <div class="q-row">
                    <span class="q-label">رقم الحيوان</span>
                    <span class="q-value id-tag" id="sAnimalId">—</span>
                </div>
                <div class="q-row">
                    <span class="q-label">نوع الحيوان</span>
                    <span class="q-value" id="sAnimalType">—</span>
                </div>
                <div class="q-row" id="sNameRow" style="display:none;">
                    <span class="q-label">اسم الحيوان</span>
                    <span class="q-value" id="sAnimalName">—</span>
                </div>
                <div class="q-row" id="sMarkRow" style="display:none;">
                    <span class="q-label">العلامة المميزة</span>
                    <span class="q-value" id="sMark">—</span>
                </div>
                <div class="q-row">
                    <span class="q-label">الجنس</span>
                    <span class="q-value" id="sGender">—</span>
                </div>
                <div class="q-row">
                    <span class="q-label">العمر</span>
                    <span class="q-value" id="sAge">—</span>
                </div>
                <div class="q-row">
                    <span class="q-label">المجموعة</span>
                    <span class="q-value" id="sGroup">—</span>
                </div>
                <div class="q-row">
                    <span class="q-label">تاريخ دخول المستشفى</span>
                    <span class="q-value" id="sAdmissionDate">—</span>
                </div>
                <div class="q-row" id="sDischargeDateRow" style="display:none;">
                    <span class="q-label">تاريخ الخروج</span>
                    <span class="q-value" id="sDischargeDate">—</span>
                </div>
            </div>

            <div>
                <div class="notes-panel">
                    <div class="notes-panel-title">الملاحظات المسجلة عن الحيوان</div>
                    <div class="notes-panel-body" id="sNotes">—</div>
                </div>

                <div class="decision-panel" id="decisionPanel" style="display:none;">
                    <div class="decision-panel-title">بيانات القرار الطبي</div>
                    <div class="q-row">
                        <span class="q-label">نوع القرار</span>
                        <span class="q-value" id="sDecisionType">—</span>
                    </div>
                    <div class="q-row">
                        <span class="q-label">تاريخ القرار</span>
                        <span class="q-value" id="sDecisionDate">—</span>
                    </div>
                    <div class="q-row" style="flex-direction:column; align-items:stretch; gap:8px;">
                        <span class="q-label">العلاجات المسجلة خلال فترة الإقامة</span>
                        <ul class="treatment-list" id="sDecisionTreatments"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="tab-2">
        <h3 class="section-title" style="margin-top:0;">سجل المتابعة الطبية</h3>
        <div id="followList"></div>
    </div>
</div>

@if($canIssueDecision ?? false)
<div class="modal-backdrop" id="decisionModal">
    <div class="decision-box">
        <div class="decision-header">
            <h3>إصدار قرار طبي — {{ $hospitalCase->animal?->name ?: $hospitalCase->animal?->species }}</h3>
            <button type="button" class="modal-close" onclick="closeDecisionModal()">✕</button>
        </div>
        <form method="POST" action="{{ $vetBase }}/cases/hospital/{{ $hospitalCase->case_number }}/issue-decision" id="decisionForm" onsubmit="return confirmDecisionSubmit(event)">
            @csrf
            <input type="hidden" name="decision" id="decisionInput" value="{{ $recommendedDecision ?? 'discharge' }}">
            <div class="decision-body">
                @if($recommendedDecision)
                <div class="decision-hint" id="decisionHint">
                    توصية الطبيب: {{ $recommendedDecision === 'discharge' ? 'جاهز للخروج' : 'لا يستجيب للعلاج' }} — يمكنك اختيار أي قرار تراه مناسباً.
                </div>
                @endif
                <p style="font-size:0.83rem; color:#64748b; font-weight:600; margin:0 0 1rem; line-height:1.6;">
                    اختر القرار الطبي النهائي للحالة. لن يُفرض عليك قرار محدد.
                </p>
                <div class="decision-options">
                    <div class="decision-option" data-decision="discharge" onclick="selectDecisionOption(this)">
                        <div class="opt-icon" style="background:#f0fdf4;">🏠</div>
                        <div>
                            <div class="opt-title">خروج بعد العلاج</div>
                            <div class="opt-desc">الحيوان تعافى وجاهز للعودة إلى موقعه في الحديقة</div>
                        </div>
                    </div>
                    <div class="decision-option" data-decision="slaughter" onclick="selectDecisionOption(this)">
                        <div class="opt-icon" style="background:#fef2f2;">⚠️</div>
                        <div>
                            <div class="opt-title">ذبح اضطراري</div>
                            <div class="opt-desc">الحالة تستدعي الذبح الاضطراري وفق الإجراءات الطبية</div>
                        </div>
                    </div>
                </div>
                <textarea name="note" class="decision-note" placeholder="ملاحظة القرار (اختياري)"></textarea>
            </div>
            <div class="decision-footer">
                <button type="button" class="btn-cancel" onclick="closeDecisionModal()">إلغاء</button>
                <button type="submit" class="btn-submit">تأكيد القرار</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    const caseId = '{{ $id ?? 'HC-2025-001' }}';
    const serverCase = @json($caseData ?? null);
    const recommendedDecision = @json($recommendedDecision ?? 'discharge');

    function openDecisionModal() {
        document.getElementById('decisionModal')?.classList.add('open');
        const initial = recommendedDecision || 'discharge';
        document.getElementById('decisionInput').value = initial;
        document.querySelectorAll('.decision-option').forEach(option => {
            option.classList.toggle('selected', option.dataset.decision === initial);
        });
    }

    function closeDecisionModal() {
        document.getElementById('decisionModal')?.classList.remove('open');
    }

    function selectDecisionOption(el) {
        document.querySelectorAll('.decision-option').forEach(option => option.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('decisionInput').value = el.dataset.decision;
    }

    function confirmDecisionSubmit(event) {
        const decision = document.getElementById('decisionInput').value;
        const labels = {
            discharge: 'خروج بعد العلاج',
            slaughter: 'ذبح اضطراري',
        };
        return confirm('تأكيد القرار: ' + (labels[decision] || decision) + '؟');
    }

    document.getElementById('decisionModal')?.addEventListener('click', function (e) {
        if (e.target === this) closeDecisionModal();
    });

    const hospitalDB = {
        'HC-2025-001': {
            statusClass: 'status-ready', statusText: 'جاهز للخروج',
            vet: 'د. خالد العربي',
            reason: 'إصابة في الطرف الأمامي مع التهاب موضعي يستدعي الرعاية داخل المستشفى.',
            notes: 'تم نقل الحيوان من حظيرة الأسود الرئيسية. يُراقب الشهية والحركة يومياً. آخر وزن: 185 كغ.',
            animalId: '#ANM-101', animalType: 'أسد إفريقي', animalName: 'سيمبا', mark: '',
            animalEmoji: '🦁', gender: 'ذكر', age: '6 سنوات', group: 'القططية',
            admissionDate: '2026-05-30', dischargeDate: '',
            followUps: [
                {
                    date: '2026-06-05 — 10:00 ص', vet: 'د. خالد العربي',
                    diagnosis: 'التئام الجرح بنسبة 90%. الحالة مستقرة وجاهزة للخروج.',
                    treatment: 'إيقاف المضاد الحيوي. متابعة الجرح موضعياً فقط.',
                    note: 'يُوصى بإعادة الحيوان إلى حظيرته خلال 48 ساعة.',
                    nutrition: { text: 'لحم مطبوخ جيداً مع مكملات فيتامين', start: '2026-06-06', end: '2026-06-12' },
                    status: 'جاهز للخروج', statusClass: 'follow-status-ready'
                },
                {
                    date: '2026-05-30 — 02:00 م', vet: 'د. فاطمة الزهراء',
                    diagnosis: 'جرح عميق في اليد اليسرى مفتوح مع عدوى بسيطة.',
                    treatment: 'تنظيف الجرح، ضمادات يومية، مضاد حيوي واسع الطيف.',
                    note: 'بدء العلاج داخل المستشفى.', nutrition: null,
                    status: 'قيد العلاج', statusClass: 'follow-status-watch'
                }
            ]
        },
        'HC-2025-002': {
            statusClass: 'status-watch', statusText: 'قيد العلاج',
            vet: 'د. فاطمة الزهراء',
            reason: 'خمول ورفض جزئي للطعام مع إصابة في الساق الخلفية.',
            notes: 'الحيوان تحت المراقبة المستمرة. يحتاج دعم غذائي.',
            animalId: '#ANM-154', animalType: 'زرافة نيلية', animalName: 'جميلة', mark: 'بقع بنية على الرقبة',
            animalEmoji: '🦒', gender: 'أنثى', age: '4 سنوات', group: 'الغزلان',
            admissionDate: '2026-06-02', dischargeDate: '',
            followUps: [
                { date: '2026-06-04', vet: 'د. فاطمة الزهراء', diagnosis: 'تحسن طفيف في الشهية.', treatment: 'مكملات غذائية ومسكنات.', note: '', nutrition: null, status: 'قيد العلاج', statusClass: 'follow-status-watch' }
            ]
        },
        'HC-2025-003': {
            statusClass: 'status-no-response', statusText: 'لا يستجيب للعلاج',
            vet: 'د. خالد العربي',
            reason: 'إصابة في الجناح مع عدوى متقدمة.',
            notes: 'الحالة لا تستجيب للبروتوكول العلاجي الحالي. يُراجع القرار الطبي.',
            animalId: '#ANM-088', animalType: 'نسر ذهبي', animalName: '', mark: '',
            animalEmoji: '🦅', gender: 'ذكر', age: '3 سنوات', group: 'الطيور',
            admissionDate: '2026-05-29', dischargeDate: '',
            followUps: [
                { date: '2026-06-03', vet: 'د. خالد العربي', diagnosis: 'لا تحسن ملحوظ.', treatment: 'تعديل الجرعة العلاجية.', note: 'مراجعة خلال 48 ساعة.', nutrition: null, status: 'لا يستجيب للعلاج', statusClass: 'follow-status-no-response' }
            ]
        },
        'HC-2025-006': {
            statusClass: 'status-discharged', statusText: 'خروج بعد العلاج',
            vet: 'د. خالد العربي',
            reason: 'التهاب معوي حاد.',
            notes: 'تم استلام الحيوان من مسؤول المجموعة.',
            animalId: '#ANM-042', animalType: 'شمبانزي', animalName: 'بونغو', mark: '',
            animalEmoji: '🐒', gender: 'ذكر', age: '8 سنوات', group: 'القرود',
            admissionDate: '2026-05-10', dischargeDate: '2026-05-20',
            decisionType: 'خروج بعد العلاج',
            followUps: [
                { date: '2026-05-20', vet: 'د. خالد العربي', diagnosis: 'شفاء تام.', treatment: 'إيقاف العلاج.', note: 'خروج بعد العلاج.', nutrition: null, status: 'جاهز للخروج', statusClass: 'follow-status-ready' },
                { date: '2026-05-15', vet: 'د. خالد العربي', diagnosis: 'تحسن في الأعراض المعوية.', treatment: 'مضاد حيوي ومحاليل وريدية.', note: '', nutrition: null, status: 'قيد العلاج', statusClass: 'follow-status-watch' },
                { date: '2026-05-10', vet: 'د. فاطمة الزهراء', diagnosis: 'التهاب معوي حاد.', treatment: 'عزل، سوائل وريدية، مضاد للقيء.', note: 'بدء العلاج داخل المستشفى.', nutrition: null, status: 'قيد العلاج', statusClass: 'follow-status-watch' }
            ]
        },
        'HC-2025-007': {
            statusClass: 'status-slaughter', statusText: 'ذبح اضطراري',
            vet: 'د. فاطمة الزهراء',
            reason: 'إصابة خطيرة في الحوض مع عدوى متقدمة.',
            notes: 'صدر قرار الذبح الاضطراري بعد فشل الاستجابة للعلاج.',
            animalId: '#ANM-042', animalType: 'مها أبو حراب', animalName: '', mark: '',
            animalEmoji: '🦌', gender: 'أنثى', age: '5 سنوات', group: 'الغزلان',
            admissionDate: '2026-05-25', dischargeDate: '2026-05-27',
            decisionType: 'ذبح اضطراري',
            followUps: [
                { date: '2026-05-27', vet: 'د. فاطمة الزهراء', diagnosis: 'تدهور الحالة ولا استجابة للعلاج.', treatment: 'مسكنات قوية ومحاليل داعمة.', note: 'توصية بالذبح الاضطراري.', nutrition: null, status: 'لا يستجيب للعلاج', statusClass: 'follow-status-no-response' },
                { date: '2026-05-26', vet: 'د. فاطمة الزهراء', diagnosis: 'عدوى موضعية متقدمة.', treatment: 'مضاد حيوي واسع الطيف وضمادات يومية.', note: '', nutrition: null, status: 'قيد العلاج', statusClass: 'follow-status-watch' }
            ]
        }
    };

    function collectTreatments(followUps) {
        const seen = new Set();
        const list = [];
        (followUps || []).forEach(f => {
            const treatment = (f.treatment || '').trim();
            if (treatment && !seen.has(treatment)) {
                seen.add(treatment);
                list.push(treatment);
            }
        });
        return list;
    }

    function renderDecisionPanel(d) {
        const panel = document.getElementById('decisionPanel');
        const treatments = collectTreatments(d.followUps);

        if (!d.dischargeDate || !treatments.length) {
            panel.style.display = 'none';
            return;
        }

        document.getElementById('sDecisionType').textContent = d.decisionType || d.statusText || '—';
        document.getElementById('sDecisionDate').textContent = d.dischargeDate;
        document.getElementById('sDecisionTreatments').innerHTML = treatments
            .map(t => `<li>${t}</li>`)
            .join('');
        panel.style.display = 'block';
    }

    function switchTab(n, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + n).classList.add('active');
        btn.classList.add('active');
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function toggleFollowCard(button) {
        const card = button.closest('.follow-card');
        if (!card) return;

        const isOpen = card.classList.contains('is-open');
        document.querySelectorAll('#followList .follow-card.is-open').forEach(item => {
            item.classList.remove('is-open');
            item.querySelector('.follow-card-toggle')?.setAttribute('aria-expanded', 'false');
        });

        if (!isOpen) {
            card.classList.add('is-open');
            button.setAttribute('aria-expanded', 'true');
        }
    }

    function renderFollowUps(list) {
        const container = document.getElementById('followList');
        if (!list || !list.length) {
            container.innerHTML = '<p style="color:#64748b;font-weight:700;text-align:center;padding:2rem;">لا توجد متابعات مسجلة.</p>';
            return;
        }
        container.innerHTML = list.map((f, index) => {
            let nutritionHtml = '';
            if (f.nutrition) {
                nutritionHtml = `
                    <div class="nutrition-block">
                        <div class="follow-field">
                            <div class="follow-field-label">التوصيات الغذائية العلاجية</div>
                            <div class="follow-field-value">${escapeHtml(f.nutrition.text)}</div>
                        </div>
                        <div class="nutrition-grid">
                            <div class="follow-field">
                                <div class="follow-field-label">تاريخ البداية</div>
                                <div class="follow-field-value">${escapeHtml(f.nutrition.start)}</div>
                            </div>
                            <div class="follow-field">
                                <div class="follow-field-label">تاريخ النهاية</div>
                                <div class="follow-field-value">${escapeHtml(f.nutrition.end)}</div>
                            </div>
                        </div>
                    </div>`;
            }

            const preview = f.diagnosis || f.treatment || 'متابعة طبية';
            const openClass = index === 0 ? ' is-open' : '';
            const statusHtml = f.status ? `
                <div class="follow-field">
                    <div class="follow-field-label">الحالة</div>
                    <div class="follow-field-value follow-status-wrap">
                        <span class="follow-status-badge ${f.statusClass || ''}"><span class="dot"></span>${escapeHtml(f.status)}</span>
                    </div>
                </div>` : '';

            return `
                <div class="follow-card${openClass}">
                    <button
                        type="button"
                        class="follow-card-toggle"
                        aria-expanded="${index === 0 ? 'true' : 'false'}"
                        onclick="toggleFollowCard(this)"
                    >
                        <div class="follow-card-main">
                            <div class="follow-card-top">
                                <div class="follow-vet">${escapeHtml(f.vet)}</div>
                                <div class="follow-date">${escapeHtml(f.date)}</div>
                            </div>
                            <div class="follow-card-preview">${escapeHtml(preview)}</div>
                        </div>
                        <span class="follow-card-chevron" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </span>
                    </button>
                    <div class="follow-card-body">
                        <div class="follow-field">
                            <div class="follow-field-label">التشخيص <span class="req">*</span></div>
                            <div class="follow-field-value">${escapeHtml(f.diagnosis)}</div>
                        </div>
                        <div class="follow-field">
                            <div class="follow-field-label">العلاج <span class="req">*</span></div>
                            <div class="follow-field-value">${escapeHtml(f.treatment)}</div>
                        </div>
                        ${f.note ? `
                        <div class="follow-field">
                            <div class="follow-field-label">الملاحظات</div>
                            <div class="follow-field-value">${escapeHtml(f.note)}</div>
                        </div>` : ''}
                        ${nutritionHtml}
                        ${statusHtml}
                    </div>
                </div>`;
        }).join('');
    }

    window.onload = function() {
        const d = serverCase || hospitalDB[caseId] || hospitalDB['HC-2025-001'];

        document.getElementById('headerBadge').innerHTML =
            '<span class="badge ' + d.statusClass + '"><span class="dot"></span>' + d.statusText + '</span>';

        document.getElementById('topAnimalPhoto').innerHTML = d.animalPhotoUrl
            ? `<img src="${escapeHtml(d.animalPhotoUrl)}" alt="">`
            : (d.animalEmoji || '🐾');
        document.getElementById('topAnimalName').textContent = d.animalName || d.animalType;
        document.getElementById('topVet').textContent = d.vet;

        document.getElementById('sAnimalId').textContent = d.animalId;
        document.getElementById('sAnimalType').textContent = d.animalType;
        document.getElementById('sGender').textContent = d.gender;
        document.getElementById('sAge').textContent = d.age;
        document.getElementById('sGroup').textContent = d.group;
        document.getElementById('sAdmissionDate').textContent = d.admissionDate;
        document.getElementById('sNotes').textContent = d.notes;

        if (d.animalName) {
            document.getElementById('sAnimalName').textContent = d.animalName;
            document.getElementById('sNameRow').style.display = 'flex';
        }
        if (d.mark) {
            document.getElementById('sMark').textContent = d.mark;
            document.getElementById('sMarkRow').style.display = 'flex';
        }
        if (d.dischargeDate) {
            document.getElementById('sDischargeDate').textContent = d.dischargeDate;
            document.getElementById('sDischargeDateRow').style.display = 'flex';
        }

        renderFollowUps(d.followUps);
        renderDecisionPanel(d);
    };
</script>
@endsection
