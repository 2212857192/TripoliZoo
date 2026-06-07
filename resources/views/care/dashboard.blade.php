@extends('care.layout')
@section('title', 'الرئيسية | الرعاية والتغذية')
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

    .custom-table tbody tr {
        transition: background 0.15s;
    }

    .custom-table tbody tr:hover {
        background: #FAFBFC;
    }

    .custom-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-main);
        vertical-align: middle;
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

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

    .badge-health    { background: #eff6ff; color: #2563eb; }
    .badge-health .dot { background: #3b82f6; }
    .badge-mortality { background: #fef2f2; color: #dc2626; }
    .badge-mortality .dot { background: #ef4444; }
    .badge-note      { background: #fff7ed; color: #ea580c; }
    .badge-note .dot { background: #f97316; }
    .badge-birth     { background: #f0fdf4; color: #16a34a; }
    .badge-birth .dot { background: #22c55e; }
    
    .badge-pending   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-pending .dot { background: #d97706; }
    .badge-rejected  { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
    .badge-rejected .dot { background: #ef4444; }
    .badge-approved  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-approved .dot { background: #22c55e; }

    /* ═══ ACTION BUTTON ═══ */
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

    /* ── Two Column Layout ── */
    .two-col-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    /* ── Alerts List ── */
    .alerts-list {
        display: flex;
        flex-direction: column;
    }
    .alert-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid #F1F5F9;
        transition: background 0.15s;
        text-decoration: none;
    }
    .alert-item:hover {
        background: #FAFBFC;
    }
    .alert-item:last-child {
        border-bottom: none;
    }
    .alert-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
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
    .alert-text {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0F172A;
    }
    .alert-time {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748B;
        margin-right: 15px;
    }
    .alert-arrow {
        color: #94A3B8;
        display: flex;
        align-items: center;
    }

</style>
@endsection

@section('content')

{{-- 1. SUMMARY CARDS --}}
<div class="stats-grid">
    <a href="/care/health" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
        </div>
        <div class="stat-num">4</div>
        <div class="stat-label">حالات صحية<br>جديدة</div>
    </a>

    <a href="/care/health" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="stat-num">2</div>
        <div class="stat-label">حالات صحية<br>تحتاج إحالة</div>
    </a>

    <a href="/care/mortality" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path></svg>
        </div>
        <div class="stat-num">1</div>
        <div class="stat-label">حالات نفوق<br>جديدة</div>
    </a>

    <a href="/care/births" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
        <div class="stat-num">5</div>
        <div class="stat-label">مواليد<br>قيد المتابعة</div>
    </a>

    <a href="/care/notes" class="stat-card">
        <div class="stat-icon-wrap">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"></polyline></svg>
        </div>
        <div class="stat-num">3</div>
        <div class="stat-label">ملاحظات تشغيلية<br>جديدة</div>
    </a>
</div>

{{-- 2. ITEMS NEEDING REVIEW --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <div class="title-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            عناصر تحتاج مراجعة
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>النوع</th>
                    <th>الحيوان</th>
                    <th>المجموعة</th>
                    <th>وصف مختصر</th>
                    <th>التاريخ</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-health"><span class="dot"></span>حالة صحية</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">أسد إفريقي</div>
                        <div class="animal-id">#ANL-0041-2026</div>
                    </td>
                    <td>السباع</td>
                    <td>جرح عميق بالقدم الأمامية (تحتاج إحالة)</td>
                    <td>2026-06-07</td>
                    <td>
                        <div class="actions-cell">
                            <a href="/care/health" class="btn-tbl" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-mortality"><span class="dot"></span>حالة نفوق</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">غزال الريم</div>
                        <div class="animal-id">#ANL-0120-2026</div>
                    </td>
                    <td>العواشب</td>
                    <td>وفاة مفاجئة في الحظيرة (جديدة)</td>
                    <td>2026-06-06</td>
                    <td>
                        <div class="actions-cell">
                            <a href="/care/mortality" class="btn-tbl" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-note"><span class="dot"></span>ملاحظة تشغيلية</span></td>
                    <td>
                        <span style="color: #94a3b8; font-size: 0.85rem;">— غير مرتبط بحيوان —</span>
                    </td>
                    <td>الطيور الجارحة</td>
                    <td>تلف في شبك الحظيرة رقم 3 (جديدة)</td>
                    <td>2026-06-06</td>
                    <td>
                        <div class="actions-cell">
                            <a href="/care/notes" class="btn-tbl" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-birth"><span class="dot"></span>مولود</span></td>
                    <td>
                        <div style="font-weight: 700; color: #0f172a;">قرد المكاك</div>
                        <div class="animal-id">#ANL-0305-2026</div>
                    </td>
                    <td>الرئيسيات</td>
                    <td>قريب من إكمال مدة المتابعة الأولية</td>
                    <td>2026-06-05</td>
                    <td>
                        <div class="actions-cell">
                            <a href="/care/births" class="btn-tbl" title="عرض التفاصيل">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- 3 & 4. TWO COLUMN LAYOUT --}}
<div class="two-col-grid">
    
    {{-- 3. REFERRALS & RESULTS --}}
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
                        <td style="font-weight: 800; font-size: 1rem;">3</td>
                        <td>
                            <a href="/care/referrals/treatment" class="btn-tbl" title="عرض">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 800; color:#1e293b;">إحالات العلاج</td>
                        <td><span class="badge badge-rejected">مرفوضة</span></td>
                        <td style="font-weight: 800; font-size: 1rem; color: #e11d48;">1</td>
                        <td>
                            <a href="/care/referrals/treatment" class="btn-tbl" title="عرض">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 800; color:#1e293b;">إحالات التشريح</td>
                        <td><span class="badge badge-pending">بانتظار التوثيق</span></td>
                        <td style="font-weight: 800; font-size: 1rem;">2</td>
                        <td>
                            <a href="/care/referrals/autopsy" class="btn-tbl" title="عرض">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 800; color:#1e293b;">إحالات التشريح</td>
                        <td><span class="badge badge-approved">موثقة</span></td>
                        <td style="font-weight: 800; font-size: 1rem;">1</td>
                        <td>
                            <a href="/care/referrals/autopsy" class="btn-tbl" title="عرض">
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
            
            <a href="/care/referrals/treatment" class="alert-item">
                <div class="alert-content">
                    <div class="alert-icon" style="color: #e11d48; background: #fff1f2; border-color: #fecdd3;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </div>
                    <div class="alert-text">تم رفض إحالة علاج للحيوان 0041-2026-AN</div>
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="alert-time">منذ 10 دقائق</div>
                    <div class="alert-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
                </div>
            </a>

            <a href="/care/decisions" class="alert-item">
                <div class="alert-content">
                    <div class="alert-icon" style="color: #d97706; background: #fffbeb; border-color: #fde68a;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="alert-text">تعذر استلام الحيوان 0030-2026-AN مؤقتًا</div>
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="alert-time">منذ ساعة</div>
                    <div class="alert-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
                </div>
            </a>

            <a href="/care/referrals/autopsy" class="alert-item">
                <div class="alert-content">
                    <div class="alert-icon" style="color: #15803d; background: #f0fdf4; border-color: #bbf7d0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div class="alert-text">تم توثيق نتيجة تشريح للحيوان 0120-2026-AN</div>
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="alert-time">منذ ساعتين</div>
                    <div class="alert-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></div>
                </div>
            </a>

            <a href="/care/decisions" class="alert-item">
                <div class="alert-content">
                    <div class="alert-icon" style="color: #2563eb; background: #eff6ff; border-color: #bfdbfe;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div class="alert-text">صدر قرار إفراج صحي للحيوان 0101-2026-AN</div>
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
