@extends('vet.layout')
@section('title', 'تفاصيل الحجر الصحي | المستشفى البيطري')
@section('page_title', 'تفاصيل حيوان في الحجر')

@section('styles')
<style>
/* ═══ BREADCRUMB ═══ */
.breadcrumb {
    display: flex; align-items: center; gap: 8px;
    font-size: 0.8rem; font-weight: 700; color: #94a3b8;
    margin-bottom: 1.5rem;
}
.breadcrumb a { color: #64748b; text-decoration: none; transition: color 0.2s; }
.breadcrumb a:hover { color: #0f172a; }
.breadcrumb .sep { color: #cbd5e1; }
.breadcrumb .current { color: #0f172a; }

/* ═══ LAYOUT ═══ */
.detail-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.5rem;
    align-items: start;
}

/* ═══ MAIN CARD ═══ */
.main-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e8edf5;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.main-card-header {
    padding: 1.4rem 1.6rem;
    background: linear-gradient(135deg, #431407 0%, #7c2d12 60%, #c2410c 100%);
    display: flex; align-items: center; justify-content: space-between;
}
.animal-identity { display: flex; align-items: center; gap: 14px; }
.animal-avatar {
    width: 56px; height: 56px; border-radius: 14px;
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
}
.animal-info h3 { font-size: 1.2rem; font-weight: 900; color: #fff; margin-bottom: 3px; }
.animal-info p  { font-size: 0.78rem; color: rgba(255,255,255,0.65); font-weight: 600; }
.status-pill {
    padding: 7px 16px; border-radius: 30px;
    font-size: 0.78rem; font-weight: 800;
    display: inline-flex; align-items: center; gap: 7px;
}
.status-monitoring { background: rgba(251,191,36,0.2); color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
.status-ready      { background: rgba(52,211,153,0.2); color: #34d399; border: 1px solid rgba(52,211,153,0.3); }
.pulse { width: 7px; height: 7px; border-radius: 50%; animation: blinkPulse 2s infinite; }
.monitoring-pulse { background: #fbbf24; }
.ready-pulse      { background: #34d399; }
@keyframes blinkPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.4;transform:scale(0.8)} }

/* ═══ INFO SECTIONS ═══ */
.section-block { padding: 1.4rem 1.6rem; border-bottom: 1px solid #f1f5f9; }
.section-block:last-child { border-bottom: none; }
.section-title {
    font-size: 0.78rem; font-weight: 800; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 0.5px;
    margin-bottom: 1rem;
    display: flex; align-items: center; gap: 8px;
}
.section-title .icon {
    width: 26px; height: 26px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    background: #f1f5f9; color: #64748b;
}
.info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
.info-item label { font-size: 0.73rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 4px; }
.info-item span  { font-size: 0.9rem; font-weight: 800; color: #0f172a; }

/* ═══ VET NOTES TIMELINE ═══ */
.notes-timeline { display: flex; flex-direction: column; gap: 0; }
.note-entry {
    display: flex; gap: 14px; padding-bottom: 1.2rem;
    position: relative;
}
.note-entry:not(:last-child)::before {
    content: '';
    position: absolute;
    right: 16px; top: 32px;
    width: 2px; bottom: 0;
    background: linear-gradient(to bottom, #e2e8f0, transparent);
}
.note-dot-wrap { flex-shrink: 0; display: flex; flex-direction: column; align-items: center; }
.note-dot {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; flex-shrink: 0;
    border: 2px solid;
}
.note-dot.obs  { background: #fffbeb; border-color: #fde68a; }
.note-dot.dose { background: #eff6ff; border-color: #bfdbfe; }
.note-dot.exam { background: #f0fdf4; border-color: #bbf7d0; }
.note-content { flex: 1; }
.note-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 5px; }
.note-date { font-size: 0.72rem; font-weight: 700; color: #94a3b8; }
.note-author { font-size: 0.72rem; font-weight: 800; color: #475569; }
.note-type-tag {
    padding: 2px 8px; border-radius: 5px;
    font-size: 0.68rem; font-weight: 800;
}
.note-text { font-size: 0.83rem; font-weight: 600; color: #334155; line-height: 1.65; }

/* ═══ SIDE PANEL ═══ */
.side-panel { display: flex; flex-direction: column; gap: 1.2rem; }
.side-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e8edf5;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
}
.side-card-header {
    padding: 0.9rem 1.2rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem; font-weight: 800; color: #0f172a;
    display: flex; align-items: center; gap: 8px;
}
.side-card-body { padding: 1.1rem 1.2rem; }

/* ═══ PROGRESS BAR ═══ */
.progress-wrap { margin-bottom: 1rem; }
.progress-label {
    display: flex; justify-content: space-between;
    font-size: 0.76rem; font-weight: 700; color: #64748b;
    margin-bottom: 6px;
}
.progress-bar-bg { background: #f1f5f9; border-radius: 10px; height: 8px; overflow: hidden; }
.progress-bar-fill {
    height: 100%; border-radius: 10px;
    background: linear-gradient(to left, #22c55e, #86efac);
    transition: width 1s ease;
}

/* ═══ STAT MINI ═══ */
.stat-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; }
.stat-mini {
    background: #f8fafc;
    border-radius: 10px;
    padding: 0.8rem;
    text-align: center;
    border: 1px solid #f1f5f9;
}
.stat-mini .val { font-size: 1.4rem; font-weight: 900; color: #0f172a; line-height: 1; margin-bottom: 3px; }
.stat-mini .lbl { font-size: 0.68rem; font-weight: 700; color: #94a3b8; }

/* ═══ ACTION BUTTONS ═══ */
.action-btn-block { display: flex; flex-direction: column; gap: 8px; padding: 1.1rem 1.2rem; }
.btn-action-full {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 10px 16px;
    border-radius: 10px;
    font-family: 'Cairo', sans-serif; font-size: 0.83rem; font-weight: 800;
    cursor: pointer; transition: all 0.2s; text-decoration: none;
    border: none;
}
.btn-green { background: linear-gradient(135deg, #14532d, #15803d); color: #fff; box-shadow: 0 4px 12px rgba(21,128,61,0.25); }
.btn-green:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(21,128,61,0.3); }
.btn-outline { background: #fff; color: #334155; border: 1.5px solid #e2e8f0; }
.btn-outline:hover { background: #f8fafc; border-color: #94a3b8; }
.btn-red-outline { background: #fff1f2; color: #e11d48; border: 1.5px solid #fecdd3; }
.btn-red-outline:hover { background: #ffe4e6; }

/* ═══ MODAL ═══ */
.modal-backdrop { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); backdrop-filter:blur(5px); z-index:1000; align-items:center; justify-content:center; }
.modal-backdrop.open { display:flex; }
.modal-box { background:#fff; border-radius:20px; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px rgba(0,0,0,0.15); animation:modalIn 0.3s cubic-bezier(0.4,0,0.2,1); }
@keyframes modalIn { from { transform:translateY(24px) scale(0.97); opacity:0; } to { transform:translateY(0) scale(1); opacity:1; } }
.modal-header { padding:1.4rem 1.8rem; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; background:#F8FAFC; border-radius:20px 20px 0 0; }
.modal-header h3 { font-size:1.15rem; font-weight:800; color:#0f172a; margin:0; }
.modal-close { width:32px; height:32px; border-radius:8px; background:#e2e8f0; border:none; color:#64748b; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.2rem; font-weight:700; transition:all 0.2s; line-height:1; }
.modal-close:hover { background:#cbd5e1; color:#0f172a; }
.modal-body { padding:1.8rem; }
.modal-footer { padding:1.4rem 1.8rem; border-top:1px solid #e2e8f0; display:flex; gap:10px; justify-content:flex-end; background:#F8FAFC; border-radius:0 0 20px 20px; }
.form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 1rem; }
.form-group label { font-size: 0.78rem; font-weight: 800; color: #374151; }
.form-input, .form-select, .form-textarea {
    padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-family: 'Cairo', sans-serif; font-size: 0.84rem; font-weight: 600;
    color: #0f172a; background: #fafbff; transition: border-color 0.2s, box-shadow 0.2s; outline: none;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,0.1); background: #fff;
}
.form-textarea { resize: vertical; min-height: 90px; }
.btn-submit {
    padding: 9px 22px;
    background: linear-gradient(135deg, #431407, #c2410c);
    color: #fff; border: none; border-radius: 10px;
    font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800;
    cursor: pointer; transition: all 0.2s;
}
.btn-cancel-sm {
    padding: 9px 18px; background: #f1f5f9; color: #475569;
    border: 1px solid #e2e8f0; border-radius: 10px;
    font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800;
    cursor: pointer;
}

/* ═══ RELEASE CONFIRM ═══ */
.confirm-box {
    background: #fff; border-radius: 18px; width: 100%; max-width: 420px;
    padding: 2rem; text-align: center;
    box-shadow: 0 30px 80px rgba(0,0,0,0.2);
    animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
.confirm-icon { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.8rem; }
.confirm-box h3 { font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
.confirm-box p  { font-size: 0.82rem; color: #64748b; font-weight: 600; margin-bottom: 1.5rem; line-height: 1.6; }
.confirm-actions { display: flex; gap: 10px; justify-content: center; }
.btn-confirm-green { padding: 9px 22px; background: #16a34a; color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800; cursor: pointer; }
.btn-confirm-green:hover { background: #15803d; }
</style>
@endsection

@section('content')

{{-- ═══ BREADCRUMB ═══ --}}
<div class="breadcrumb">
    <a href="/vet/dashboard">لوحة التحكم</a>
    <span class="sep">›</span>
    <a href="/vet/quarantine">الحجر الصحي</a>
    <span class="sep">›</span>
    <span class="current">تفاصيل — رعد (#QRN-204)</span>
</div>

{{-- ═══ MAIN LAYOUT ═══ --}}
<div class="detail-layout">

    {{-- ─── LEFT: Main Content ─── --}}
    <div>
        {{-- Main Card --}}
        <div class="main-card" style="margin-bottom: 1.5rem;">
            {{-- Header --}}
            <div class="main-card-header">
                <div class="animal-identity">
                    <div class="animal-avatar">🐯</div>
                    <div class="animal-info">
                        <h3>رعد — النمر البنغالي</h3>
                        <p>رقم الحجر: #QRN-204 &nbsp;|&nbsp; الطبيب المسؤول: د. أسامة الورفلي</p>
                    </div>
                </div>
                <div class="status-pill status-ready">
                    <span class="pulse ready-pulse"></span>
                    جاهز للإفراج
                </div>
            </div>

            {{-- Basic Info --}}
            <div class="section-block">
                <div class="section-title">
                    <div class="icon">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    </div>
                    البيانات الأساسية
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>نوع الحيوان</label>
                        <span>نمر بنغالي</span>
                    </div>
                    <div class="info-item">
                        <label>الجنس</label>
                        <span>ذكر</span>
                    </div>
                    <div class="info-item">
                        <label>العمر التقريبي</label>
                        <span>4 سنوات</span>
                    </div>
                    <div class="info-item">
                        <label>مصدر الحيوان</label>
                        <span>حديقة حيوان تونس</span>
                    </div>
                    <div class="info-item">
                        <label>تاريخ دخول الحجر</label>
                        <span>2026-06-01</span>
                    </div>
                    <div class="info-item">
                        <label>أيام في الحجر</label>
                        <span style="color: #15803d; font-weight: 900;">2 أيام</span>
                    </div>
                </div>
            </div>

            {{-- Doctor Notes Timeline --}}
            <div class="section-block">
                <div class="section-title" style="margin-bottom: 1.2rem;">
                    <div class="icon">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    ملاحظات وإدخالات الطبيب البيطري
                </div>
                <div class="notes-timeline">
                    <div class="note-entry">
                        <div class="note-dot-wrap">
                            <div class="note-dot exam">✓</div>
                        </div>
                        <div class="note-content">
                            <div class="note-meta">
                                <span class="note-date">2026-06-03</span>
                                <span class="note-author">د. خالد العربي</span>
                                <span class="note-type-tag" style="background:#f0fdf4; color:#15803d;">فحص طبي</span>
                            </div>
                            <div class="note-text">انتهاء مدة الملاحظة الوقائية. لا أعراض جانبية على الإطلاق. درجة الحرارة طبيعية، وزن مستقر. يُوصى بإصدار قرار الإفراج الصحي.</div>
                        </div>
                    </div>
                    <div class="note-entry">
                        <div class="note-dot-wrap">
                            <div class="note-dot dose">💉</div>
                        </div>
                        <div class="note-content">
                            <div class="note-meta">
                                <span class="note-date">2026-06-02</span>
                                <span class="note-author">د. فاطمة الزهراء</span>
                                <span class="note-type-tag" style="background:#eff6ff; color:#2563eb;">جرعة وقائية</span>
                            </div>
                            <div class="note-text">تم إعطاء الجرعة الثانية من التطعيم الوقائي. الحيوان يستجيب بشكل ممتاز. لا توجد ردة فعل تحسسية.</div>
                        </div>
                    </div>
                    <div class="note-entry">
                        <div class="note-dot-wrap">
                            <div class="note-dot obs">👁</div>
                        </div>
                        <div class="note-content">
                            <div class="note-meta">
                                <span class="note-date">2026-06-01</span>
                                <span class="note-author">د. خالد العربي</span>
                                <span class="note-type-tag" style="background:#fffbeb; color:#b45309;">ملاحظة يومية</span>
                            </div>
                            <div class="note-text">استلام الحيوان من جهة التوريد. بدء فترة الحجر الصحي الوقائي المقررة. الحالة العامة جيدة، شهية طبيعية.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── RIGHT: Side Panel ─── --}}
    <div class="side-panel">

        {{-- Progress --}}
        <div class="side-card">
            <div class="side-card-header">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                تقدم فترة الحجر
            </div>
            <div class="side-card-body">
                <div class="progress-wrap">
                    <div class="progress-label">
                        <span>المدة المنقضية</span>
                        <span style="color:#15803d; font-weight:800;">2 / 3 أيام</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: 85%;"></div>
                    </div>
                    <div style="font-size:0.72rem; color:#94a3b8; font-weight:700; margin-top:5px; text-align: left;">85% مكتمل</div>
                </div>
                <div class="stat-mini-grid">
                    <div class="stat-mini">
                        <div class="val" style="color:#3b82f6;">2</div>
                        <div class="lbl">جرعات وقائية</div>
                    </div>
                    <div class="stat-mini">
                        <div class="val" style="color:#8b5cf6;">3</div>
                        <div class="lbl">ملاحظات مسجلة</div>
                    </div>
                    <div class="stat-mini">
                        <div class="val" style="color:#f97316;">0</div>
                        <div class="lbl">أعراض مسجلة</div>
                    </div>
                    <div class="stat-mini">
                        <div class="val" style="color:#22c55e;">✓</div>
                        <div class="lbl">جاهز للإفراج</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="side-card">
            <div class="side-card-header">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                إجراءات متاحة
            </div>
            <div class="action-btn-block">
                <button class="btn-action-full btn-green" onclick="openReleaseModal()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    إصدار قرار الإفراج الصحي
                </button>
                <button class="btn-action-full btn-outline" onclick="openEditModal()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    تعديل بيانات الحيوان
                </button>
                <a href="/vet/quarantine" class="btn-action-full btn-outline">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    العودة لقائمة الحجر
                </a>
                <button class="btn-action-full btn-red-outline" onclick="openEndModal()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    إنهاء حالة الحجر الصحي
                </button>
            </div>
        </div>

    </div>
</div>

{{-- ═══ RELEASE MODAL ═══ --}}
<div class="modal-backdrop" id="releaseModal">
    <div class="confirm-box">
        <div class="confirm-icon" style="background:#f0fdf4;">✅</div>
        <h3>تأكيد إصدار قرار الإفراج</h3>
        <p>سيتم إصدار قرار الإفراج الصحي للحيوان <strong>رعد (النمر البنغالي)</strong>.<br>
        سيُحوَّل القرار فوراً إلى قسم <strong>القرارات الطبية</strong> لإتمام إجراءات الاستلام الرسمي ودخوله للحديقة.</p>
        <div class="confirm-actions">
            <button class="btn-cancel-sm" onclick="closeModal('releaseModal')">إلغاء</button>
            <button class="btn-confirm-green">تأكيد الإفراج</button>
        </div>
    </div>
</div>

{{-- ═══ EDIT MODAL ═══ --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>✏️ تعديل بيانات رعد</h3>
            <button class="modal-close" onclick="closeModal('editModal')">✕</button>
        </div>
        <div class="modal-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label>نوع الحيوان</label>
                    <input type="text" class="form-input" value="نمر بنغالي">
                </div>
                <div class="form-group">
                    <label>الجنس</label>
                    <select class="form-select">
                        <option selected>ذكر</option>
                        <option>أنثى</option>
                        <option>غير محدد</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>العمر التقريبي</label>
                    <input type="text" class="form-input" value="4 سنوات">
                </div>
                <div class="form-group">
                    <label>مصدر الحيوان</label>
                    <input type="text" class="form-input" value="حديقة حيوان تونس">
                </div>
            </div>
            <div class="form-group">
                <label>ملاحظات إضافية</label>
                <textarea class="form-textarea" placeholder="أضف أي تحديثات..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel-sm" onclick="closeModal('editModal')">إلغاء</button>
            <button class="btn-submit">حفظ التعديلات</button>
        </div>
    </div>
</div>

{{-- ═══ END CASE MODAL ═══ --}}
<div class="modal-backdrop" id="endModal">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-header">
            <h3>🚫 إنهاء حالة الحجر الصحي</h3>
            <button class="modal-close" onclick="closeModal('endModal')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-group full" style="margin-bottom: 1rem;">
                <label>سبب الإنهاء <span class="req">*</span></label>
                <select class="form-select" style="width: 100%;">
                    <option value="" disabled selected>اختر سبب الإنهاء...</option>
                    <option>نفوق داخل الحجر</option>
                    <option>إرجاع الحيوان</option>
                    <option>عدم التأقلم</option>
                    <option>إدخال بالخطأ</option>
                    <option>سبب آخر</option>
                </select>
            </div>
            <div class="form-group full">
                <label>ملاحظات إضافية وتوثيق</label>
                <textarea class="form-textarea" style="width: 100%;" placeholder="أدخل تفاصيل توثيق سبب إنهاء حالة الحجر..."></textarea>
            </div>
            <div style="margin-top: 15px; padding: 10px; background: #FEF2F2; border-left: 3px solid #EF4444; border-radius: 4px; font-size: 0.8rem; color: #991B1B;">
                <strong>ملاحظة هامة:</strong> بإنهاء هذه الحالة، سينتقل الحيوان لقائمة الحالات المنتهية ولن يتم تخصيص رقم حيوان رسمي له، وسيبقى كمرجع إداري فقط.
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel-sm" onclick="closeModal('endModal')">إلغاء</button>
            <button class="btn-submit" style="background: #E11D48; box-shadow: 0 4px 12px rgba(225,29,72,0.2);">تأكيد الإنهاء</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openReleaseModal() { document.getElementById('releaseModal').classList.add('open'); }
function openEditModal()    { document.getElementById('editModal').classList.add('open'); }
function openEndModal()     { document.getElementById('endModal').classList.add('open'); }
function closeModal(id)     { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-backdrop').forEach(function(b) {
    b.addEventListener('click', function(e) { if (e.target === b) b.classList.remove('open'); });
});
</script>
@endsection
