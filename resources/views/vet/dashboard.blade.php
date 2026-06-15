@extends($__layout ?? 'vet.layout')
@section('title', 'الرئيسية | المستشفى البيطري')
@section('page_title', 'الرئيسية')

@section('styles')
<style>
    /* ═══ STATS GRID ═══ */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        padding: 1.3rem 1.2rem;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: block;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        border-radius: 0 16px 16px 0;
        transition: width 0.3s;
        background: #1a4a2e;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.1);
        border-color: transparent;
    }

    .stat-card:hover::before { width: 6px; }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 1rem;
        color: #16a34a;
    }

    .stat-num {
        font-size: 2.2rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        line-height: 1.4;
    }

    /* ── Table ── */
    .table-card {
        background: var(--white);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-card-header {
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        background: #FAFBFC;
    }

    .table-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .custom-table thead th {
        background: #F8FAFC;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
    }

    .custom-table tbody tr { transition: background 0.15s; }
    .custom-table tbody tr:hover { background: #FAFBFC; }

    .custom-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-main);
        vertical-align: middle;
    }

    .custom-table tbody tr:last-child td { border-bottom: none; }

    /* ═══ BADGES ═══ */
    .badge {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        white-space: nowrap;
    }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }

    .badge-treatment  { background: #eff6ff; color: #2563eb; }
    .badge-treatment .dot { background: #3b82f6; }
    .badge-autopsy    { background: #fef2f2; color: #dc2626; }
    .badge-autopsy .dot { background: #ef4444; }
    .badge-quarantine { background: #fff7ed; color: #ea580c; }
    .badge-quarantine .dot { background: #f97316; }
    .badge-hospital   { background: #f0fdf4; color: #16a34a; }
    .badge-hospital .dot { background: #22c55e; }

    .badge-pending   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-pending .dot { background: #d97706; }
    .badge-rejected  { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
    .badge-rejected .dot { background: #ef4444; }
    .badge-approved  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-approved .dot { background: #22c55e; }
    .badge-ready     { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-ready .dot { background: #15803d; }
    .badge-review    { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-review .dot { background: #d97706; }

    .animal-id {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        background: #f8fafc;
        padding: 2px 6px;
        border-radius: 6px;
        color: #64748b;
        font-weight: 700;
        display: inline-block;
        margin-top: 4px;
        border: 1px solid #e2e8f0;
    }

    .view-all-link {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--green);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: var(--bg-color);
        border: 1px solid var(--border);
        border-radius: 8px;
        transition: all 0.2s;
    }
    .view-all-link:hover {
        background: #F1F5F9;
        border-color: #CBD5E1;
    }

    .title-icon {
        background: #e6f4ea;
        color: #1a4a2e;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .actions-cell { display: flex; gap: 6px; align-items: center; justify-content: flex-end; }
    .btn-tbl {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px; padding: 0; border-radius: 9px;
        cursor: pointer; text-decoration: none; transition: all 0.2s;
        border: 1px solid #e2e8f0; flex-shrink: 0;
        background: #f8fafc; color: #475569;
    }
    .btn-tbl:hover {
        transform: translateY(-1px);
        background: #e2e8f0; border-color: #94a3b8; color: #0f172a;
    }

    .two-col-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .alerts-list { display: flex; flex-direction: column; }
    .alert-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid #F1F5F9;
        transition: background 0.15s;
        text-decoration: none;
    }
    .alert-item:hover { background: #FAFBFC; }
    .alert-item:last-child { border-bottom: none; }
    .alert-content { display: flex; align-items: center; gap: 12px; }
    .alert-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F8FAFC;
        color: #64748B;
        border: 1px solid #E2E8F0;
    }
    .alert-text { font-size: 0.9rem; font-weight: 700; color: #0F172A; }
    .alert-time { font-size: 0.75rem; font-weight: 600; color: #64748B; margin-right: 15px; }
    .alert-arrow { color: #94A3B8; display: flex; align-items: center; }
</style>
@endsection

@section('content')

{{-- 1. SUMMARY CARDS --}}
<div class="stats-grid">
    <a href="/vet/referrals/treatment" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
        </div>
        <div class="stat-num">1</div>
        <div class="stat-label">إحالات علاج<br>قيد المراجعة</div>
    </a>

    <a href="/vet/referrals/treatment" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13"></path><path d="M22 2L15 22L11 13L2 9L22 2Z"></path></svg>
        </div>
        <div class="stat-num">1</div>
        <div class="stat-label">إحالات تحتاج<br>متابعة</div>
    </a>

    <a href="/vet/referrals/autopsy" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path></svg>
        </div>
        <div class="stat-num">2</div>
        <div class="stat-label">إحالات تشريح<br>بانتظار التوثيق</div>
    </a>

    <a href="/vet/cases/hospital" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div class="stat-num">3</div>
        <div class="stat-label">حالات داخل<br>المستشفى</div>
    </a>

    <a href="/vet/quarantine" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="stat-num">3</div>
        <div class="stat-label">حيوانات في<br>الحجر الصحي</div>
    </a>
</div>

{{-- 2. RECENT REFERRALS --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <div class="title-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
            آخر الإحالات الواردة
        </div>
        <a href="/vet/referrals/treatment" class="view-all-link">
            عرض الكل
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </a>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>نوع الإحالة</th>
                    <th>الحيوان</th>
                    <th>المجموعة</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-treatment">إحالة علاج</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">الفهد البري (صخر)</div>
                        <div class="animal-id">#ANM-109</div>
                    </td>
                    <td>السباع والضواري</td>
                    <td>2026-06-03</td>
                    <td><span class="badge badge-pending">قيد المراجعة</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/referrals/treatment" class="btn-tbl" title="الانتقال للواجهة">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-autopsy">إحالة تشريح</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">طائر العقاب الذهبي</div>
                        <div class="animal-id">#ANM-009-D</div>
                    </td>
                    <td>بيت الطيور الكبرى</td>
                    <td>2026-06-02</td>
                    <td><span class="badge badge-pending">انتظار التقرير</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/referrals/autopsy" class="btn-tbl" title="الانتقال للواجهة">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- 3. URGENT CASES --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <div class="title-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            حالات تتطلب إجراءً عاجلاً
        </div>
        <span style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; border-radius:20px; padding:4px 12px; font-size:0.75rem; font-weight:800;">
            4 حالات
        </span>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>نوع الحالة</th>
                    <th>الحيوان</th>
                    <th>الوضع الحالي</th>
                    <th>التاريخ</th>
                    <th>الوضع الإجرائي</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-hospital"><span class="dot"></span>حالة مستشفى</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">الأسد الإفريقي (سيمبا)</div>
                        <div class="animal-id">#ANM-101</div>
                    </td>
                    <td style="max-width:220px;">تماثل للشفاء الكامل بعد علاج جروح القدم</td>
                    <td>2026-05-30</td>
                    <td><span class="badge badge-ready">جاهز لإصدار الخروج</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/decisions" class="btn-tbl" title="الانتقال للواجهة">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-quarantine"><span class="dot"></span>حجر صحي</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">النمر البنغالي (رعد)</div>
                        <div class="animal-id">#ANM-204</div>
                    </td>
                    <td style="max-width:220px;">انتهاء مدة الملاحظة الوقائية بنجاح دون أعراض</td>
                    <td>2026-06-01</td>
                    <td><span class="badge badge-ready">جاهز للإفراج الصحي</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/quarantine" class="btn-tbl" title="الانتقال للواجهة">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-treatment">إحالة علاج</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">الفهد البري (صخر)</div>
                        <div class="animal-id">#ANM-109</div>
                    </td>
                    <td style="max-width:220px;">اشتباه بكسر كتف يحتاج قرار استدعاء للمستشفى</td>
                    <td>2026-06-03</td>
                    <td><span class="badge badge-review">قيد مراجعة رئيس القسم</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/referrals/treatment" class="btn-tbl" title="الانتقال للواجهة">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-autopsy">إحالة تشريح</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">طائر العقاب الذهبي</div>
                        <div class="animal-id">#ANM-009-D</div>
                    </td>
                    <td style="max-width:220px;">بانتظار إجراء التشريح وتوثيق التقرير النهائي للوفاة</td>
                    <td>2026-06-02</td>
                    <td><span class="badge badge-pending">بانتظار التوثيق</span></td>
                    <td>
                        <div class="actions-cell">
                            <a href="/vet/referrals/autopsy" class="btn-tbl" title="الانتقال للواجهة">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- 4 & 5. TWO COLUMN LAYOUT --}}
<div class="two-col-grid">

    {{-- 3. REFERRALS TRACKING --}}
    <div class="table-card" style="margin-bottom: 0;">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="title-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13"></path><path d="M22 2L15 22L11 13L2 9L22 2Z"></path></svg>
                </div>
                متابعة الإحالات
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="custom-table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>نوع الإحالة</th>
                        <th>الحالة</th>
                        <th>العدد</th>
                        <th>إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: 800; color:#1e293b;">إحالات العلاج</td>
                        <td><span class="badge badge-pending">قيد المراجعة</span></td>
                        <td style="font-weight: 800; font-size: 1rem;">1</td>
                        <td>
                            <a href="/vet/referrals/treatment" class="btn-tbl" title="عرض">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 800; color:#1e293b;">إحالات العلاج</td>
                        <td><span class="badge badge-approved">معتمدة</span></td>
                        <td style="font-weight: 800; font-size: 1rem;">2</td>
                        <td>
                            <a href="/vet/referrals/treatment" class="btn-tbl" title="عرض">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 800; color:#1e293b;">إحالات التشريح</td>
                        <td><span class="badge badge-pending">بانتظار التوثيق</span></td>
                        <td style="font-weight: 800; font-size: 1rem;">2</td>
                        <td>
                            <a href="/vet/referrals/autopsy" class="btn-tbl" title="عرض">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 800; color:#1e293b;">إحالات التشريح</td>
                        <td><span class="badge badge-approved">موثقة</span></td>
                        <td style="font-weight: 800; font-size: 1rem;">1</td>
                        <td>
                            <a href="/vet/referrals/autopsy" class="btn-tbl" title="عرض">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. RECENT IMPORTANT ALERTS --}}
    <div class="table-card" style="margin-bottom: 0;">
        <div class="table-card-header">
            <div class="table-card-title">
                <div class="title-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </div>
                آخر التنبيهات المهمة
            </div>
        </div>
        <div class="alerts-list">

            <a href="/vet/referrals/treatment" class="alert-item">
                <div class="alert-content">
                    <div class="alert-icon" style="color: #e11d48; background: #fff1f2; border-color: #fecdd3;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </div>
                    <div class="alert-text">إحالة علاج مرفوضة سابقاً تحتاج مراجعة — ANM-109</div>
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="alert-time">منذ 10 دقائق</div>
                    <div class="alert-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
                </div>
            </a>

            <a href="/vet/cases/hospital" class="alert-item">
                <div class="alert-content">
                    <div class="alert-icon" style="color: #d97706; background: #fffbeb; border-color: #fde68a;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="alert-text">تعذر استلام الحيوان ANM-030 مؤقتًا من الرعاية</div>
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="alert-time">منذ ساعة</div>
                    <div class="alert-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
                </div>
            </a>

            <a href="/vet/referrals/autopsy" class="alert-item">
                <div class="alert-content">
                    <div class="alert-icon" style="color: #15803d; background: #f0fdf4; border-color: #bbf7d0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div class="alert-text">تم توثيق نتيجة تشريح للحيوان ANM-009-D</div>
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="alert-time">منذ ساعتين</div>
                    <div class="alert-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
                </div>
            </a>

            <a href="/vet/decisions" class="alert-item">
                <div class="alert-content">
                    <div class="alert-icon" style="color: #2563eb; background: #eff6ff; border-color: #bfdbfe;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div class="alert-text">صدر قرار خروج بعد العلاج للحيوان ANM-101</div>
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="alert-time">أمس</div>
                    <div class="alert-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
                </div>
            </a>

        </div>
    </div>
</div>

@endsection
