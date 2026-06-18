@extends($__layout ?? 'vet.layout')
@section('title', 'تفاصيل حالة طبية ميدانية | المستشفى البيطري')
@section('page_title', 'تفاصيل حالة طبية ميدانية')

@section('styles')
<style>
    .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 700; color: #64748b; }
    .breadcrumb a { color: #2E7D32; text-decoration: none; transition: color 0.2s; display: flex; align-items: center; gap: 4px; }
    .breadcrumb a:hover { color: #1b5e20; }

    .header-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
    .header-info h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0 0 10px 0; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

    .badge { padding: 5px 12px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .status-open { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .status-open .dot { background: #3b82f6; }
    .status-closed { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .status-closed .dot { background: #94a3b8; }

    .tabs-container { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; }
    .tabs-header { display: flex; background: #FAFBFC; border-bottom: 1px solid #e2e8f0; padding: 0 1rem; }
    .tab-btn { padding: 16px 24px; border: none; background: transparent; font-family: 'Cairo', sans-serif; font-size: 0.95rem; font-weight: 800; color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
    .tab-btn:hover { color: var(--green); }
    .tab-btn.active { color: var(--green); border-bottom-color: var(--green); background: #fff; }

    .tab-content { padding: 2rem; display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    .summary-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { .summary-layout { grid-template-columns: 1fr; } }

    .animal-card {
        background: #fff; border-radius: 12px; padding: 1.5rem;
        border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .animal-card-title { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; text-align: center; }
    .animal-photo-wrap { display: flex; justify-content: center; margin-bottom: 1.2rem; }
    .animal-photo {
        width: 72px; height: 72px; border-radius: 16px;
        background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
        border: 2px solid #bbf7d0;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem; overflow: hidden;
    }
    .animal-photo img { width: 100%; height: 100%; object-fit: cover; }
    .q-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.8rem; padding-bottom: 0.8rem; border-bottom: 1px solid #f1f5f9;
    }
    .q-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .q-label { color: #64748b; font-size: 0.9rem; font-weight: 700; }
    .q-value { color: #0f172a; font-size: 0.95rem; font-weight: 800; text-align: left; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #e2e8f0; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
    .info-cell { background: #fff; padding: 16px 20px; }
    .info-cell.span-2 { grid-column: span 2; }
    .info-cell-label { font-size: 0.8rem; color: #64748b; font-weight: 800; margin-bottom: 6px; }
    .info-cell-value { font-size: 1rem; color: #0f172a; font-weight: 800; }
    .content-box { background: #f8fafc; padding: 16px 20px; border-radius: 10px; font-size: 0.95rem; color: #334155; font-weight: 600; line-height: 1.7; border: 1px solid #e2e8f0; }
    .section-title { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }
    .id-tag { font-family: 'Courier New', monospace; font-size: 0.85rem; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; color: #334155; font-weight: 800; display: inline-block; border: 1px solid #e2e8f0; }

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
    .nutrition-block {
        margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0;
    }
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
</style>
@endsection

@php $vetBase = ($readOnly ?? false) ? '/director/vet' : '/vet'; @endphp

@section('content')

<div class="breadcrumb">
    <a href="{{ $vetBase }}/cases/field">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        الحالات الطبية الميدانية
    </a>
    <span>/</span>
    <span style="color:#0f172a;" id="breadCaseId">تفاصيل الحالة {{ $id }}</span>
</div>

<div class="header-card">
    <div class="header-info">
        <h2>
            تفاصيل حالة طبية ميدانية
            <span id="headerBadge"></span>
        </h2>
        <div style="font-size:0.9rem; color:#64748b; font-weight:700; margin-top:8px;">
            رقم الحالة: <span class="id-tag" id="topCaseId">{{ $id }}</span>
        </div>
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

    {{-- TAB 1: Summary --}}
    <div class="tab-content active" id="tab-1">
        <div class="summary-layout">
            <div class="animal-card">
                <h4 class="animal-card-title">بيانات الحيوان</h4>
                <div class="animal-photo-wrap">
                    <div class="animal-photo" id="sAnimalPhoto">🐾</div>
                </div>
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
                    <span class="q-label">المجموعة</span>
                    <span class="q-value" id="sGroup">—</span>
                </div>
            </div>

            <div>
                <h3 class="section-title">معلومات الحالة</h3>
                <div class="info-grid" style="margin-bottom:1.5rem;">
                    <div class="info-cell">
                        <div class="info-cell-label">رقم الحالة</div>
                        <div class="info-cell-value id-tag" id="sCaseId">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">تاريخ فتح الحالة</div>
                        <div class="info-cell-value" id="sOpenDate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">آخر تحديث</div>
                        <div class="info-cell-value" id="sLastUpdate">—</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-cell-label">الطبيب المسؤول</div>
                        <div class="info-cell-value" id="sVet">—</div>
                    </div>
                    <div class="info-cell span-2" id="sCloseDateCell" style="display:none;">
                        <div class="info-cell-label">تاريخ الإغلاق</div>
                        <div class="info-cell-value" id="sCloseDate">—</div>
                    </div>
                    <div class="info-cell span-2">
                        <div class="info-cell-label">سبب فتح الحالة</div>
                        <div class="info-cell-value content-box" id="sReason" style="font-size:0.9rem;">—</div>
                    </div>
                    <div class="info-cell span-2" id="sInitialNoteCell" style="display:none;">
                        <div class="info-cell-label">ملاحظة أولية</div>
                        <div class="info-cell-value content-box" id="sInitialNote" style="font-size:0.9rem;">—</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: Medical Follow-up --}}
    <div class="tab-content" id="tab-2">
        <h3 class="section-title">سجل المتابعة الطبية</h3>
        <div id="followList"></div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const caseId = @json($id);
    const serverCase = @json($caseData ?? null);

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
                            <div class="follow-field-label">التوصية الغذائية العلاجية</div>
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
                            <div class="follow-field-label">ملاحظة</div>
                            <div class="follow-field-value">${escapeHtml(f.note)}</div>
                        </div>` : ''}
                        ${nutritionHtml}
                    </div>
                </div>`;
        }).join('');
    }

    window.onload = function() {
        const d = serverCase;
        if (!d) return;

        document.getElementById('breadCaseId').textContent = 'تفاصيل الحالة ' + caseId;
        document.getElementById('topCaseId').textContent = caseId;
        document.getElementById('sCaseId').textContent = caseId;

        const badge = document.getElementById('headerBadge');
        badge.innerHTML = '<span class="badge ' + d.statusClass + '"><span class="dot"></span>' + d.statusText + '</span>';

        if (d.status === 'closed' && d.closeDate) {
            document.getElementById('sCloseDateCell').style.display = 'block';
            document.getElementById('sCloseDate').textContent = d.closeDate;
        }

        document.getElementById('sOpenDate').textContent = d.openDate;
        document.getElementById('sLastUpdate').textContent = d.lastUpdate;
        document.getElementById('sVet').textContent = d.vet;
        document.getElementById('sReason').textContent = d.reason || '—';

        if (d.initialNote) {
            document.getElementById('sInitialNote').textContent = d.initialNote;
            document.getElementById('sInitialNoteCell').style.display = 'block';
        }

        const photoEl = document.getElementById('sAnimalPhoto');
        if (d.animalPhotoUrl) {
            photoEl.innerHTML = '<img src="' + d.animalPhotoUrl + '" alt="صورة الحيوان">';
        } else {
            photoEl.textContent = d.animalEmoji;
        }
        document.getElementById('sAnimalId').textContent = d.animalId;
        document.getElementById('sAnimalType').textContent = d.animalType;
        document.getElementById('sGroup').textContent = d.group;

        if (d.animalName) {
            document.getElementById('sAnimalName').textContent = d.animalName;
            document.getElementById('sNameRow').style.display = 'flex';
        }
        if (d.mark) {
            document.getElementById('sMark').textContent = d.mark;
            document.getElementById('sMarkRow').style.display = 'flex';
        }

        renderFollowUps(d.followUps);
    };
</script>
@endsection
