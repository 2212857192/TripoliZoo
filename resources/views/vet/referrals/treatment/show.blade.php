@extends('vet.layout')
@section('title', 'تفاصيل إحالة العلاج | المستشفى البيطري')
@section('page_title', 'تفاصيل إحالة العلاج')

@section('styles')
<style>
/* ═══ PAGE HEADER ═══ */
.page-header {
    background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 40%, #43A047 100%);
    border-radius: 20px;
    padding: 1.8rem 2.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(46, 125, 50, 0.35);
}
.page-header::before {
    content: '';
    position: absolute;
    top: -80px;
    left: -80px;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
}
.header-left { position: relative; z-index: 2; }
.header-left h2 { font-size: 1.45rem; font-weight: 900; color: #fff; margin-bottom: 5px; }
.header-left p  { font-size: 0.85rem; color: rgba(255,255,255,0.6); font-weight: 500; }
.header-right { display: flex; align-items: center; gap: 1rem; position: relative; z-index: 2; }
.case-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.4);
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 800;
    color: #fff;
}
.btn-back {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 20px;
    background: rgba(255,255,255,0.15);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 12px;
    font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800;
    cursor: pointer; transition: all 0.25s;
    text-decoration: none;
}
.btn-back:hover { background: rgba(255,255,255,0.25); }

/* ═══ TABS ═══ */
.tabs-container {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e8edf5;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.tabs-header {
    display: flex;
    background: linear-gradient(to left, #f8fafc, #fff);
    border-bottom: 1px solid #e8edf5;
}
.tab-btn {
    flex: 1;
    padding: 1.2rem 2rem;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    font-family: 'Cairo', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
}
.tab-btn:hover { color: #2E7D32; background: rgba(46,125,50,0.03); }
.tab-btn.active { color: #2E7D32; border-bottom-color: #2E7D32; background: rgba(46,125,50,0.05); }
.tab-content { display: none; padding: 2rem; }
.tab-content.active { display: block; }

/* ═══ SUMMARY CARD ═══ */
.summary-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.summary-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e8edf5;
    display: flex;
    align-items: center;
    gap: 1.2rem;
}
.animal-avatar {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
    border: 2px solid #2E7D32;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}
.animal-info h3 { font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-bottom: 4px; }
.animal-info p { font-size: 0.8rem; color: #64748b; font-weight: 600; }
.summary-body { padding: 2rem; }
.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}
.info-item { padding: 0.8rem 0; }
.info-item-label { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 4px; }
.info-item-value { font-size: 0.85rem; font-weight: 700; color: #0f172a; }

/* ═══ REASON CARD ═══ */
.reason-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.reason-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e8edf5;
}
.reason-header h3 { font-size: 1.1rem; font-weight: 900; color: #0f172a; }
.reason-body { padding: 2rem; }
.reason-section { margin-bottom: 1.5rem; }
.reason-section:last-child { margin-bottom: 0; }
.reason-label { font-size: 0.8rem; font-weight: 800; color: #2E7D32; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
.reason-content {
    font-size: 0.9rem;
    font-weight: 600;
    color: #334155;
    line-height: 1.7;
    background: #f8fafc;
    padding: 1rem 1.4rem;
    border-radius: 10px;
    border-left: 3px solid #2E7D32;
}

/* ═══ ACTION BUTTONS ═══ */
.actions-bar {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-family: 'Cairo', sans-serif;
    font-size: 0.9rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid;
    text-decoration: none;
}
.btn-approve {
    background: #f0fdf4;
    color: #15803d;
    border-color: #bbf7d0;
}
.btn-approve:hover { background: #dcfce7; }
.btn-reject {
    background: #fff1f2;
    color: #e11d48;
    border-color: #fecdd3;
}
.btn-reject:hover { background: #ffe4e6; }

/* ═══ MODAL ═══ */
.modal-backdrop {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-backdrop.active { display: flex; }
.modal-box {
    background: #fff;
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalIn 0.3s ease-out;
}
@keyframes modalIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.modal-header h3 { font-size: 1.1rem; font-weight: 900; color: #0f172a; }
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #94a3b8;
    cursor: pointer;
    transition: color 0.2s;
}
.modal-close:hover { color: #64748b; }
.modal-body { padding: 2rem; }
.modal-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #334155;
    line-height: 1.7;
    margin-bottom: 1.5rem;
}
.modal-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-family: 'Cairo', sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
    color: #334155;
    outline: none;
    transition: all 0.2s;
    resize: vertical;
    min-height: 100px;
}
.modal-textarea:focus {
    border-color: #2E7D32;
    box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
}
.modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}
.btn-modal {
    padding: 10px 20px;
    border-radius: 8px;
    font-family: 'Cairo', sans-serif;
    font-size: 0.85rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid;
}
.btn-modal-confirm {
    background: #2E7D32;
    color: #fff;
    border-color: #2E7D32;
}
.btn-modal-confirm:hover { background: #1B5E20; }
.btn-modal-cancel {
    background: #fff;
    color: #64748b;
    border-color: #e2e8f0;
}
.btn-modal-cancel:hover { background: #f8fafc; }

/* ═══ STATUS BADGES ═══ */
.badge { padding: 4px 10px; border-radius: 18px; font-size: 0.72rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }
.badge .dot { width: 5px; height: 5px; border-radius: 50%; }
.badge-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.badge-pending .dot { background: #f59e0b; }
.badge-approved { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.badge-approved .dot { background: #22c55e; }
.badge-rejected { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }
.badge-rejected .dot { background: #ef4444; }
</style>
@endsection

@section('content')
{{-- ═══ PAGE HEADER ═══ --}}
<div class="page-header">
    <div class="header-left">
        <h2>🏥 تفاصيل إحالة علاج - 001-2025-TR</h2>
        <p>شمبانزي أفريقي — د. ريم الفيصل</p>
    </div>
    <div class="header-right">
        <div class="case-status-badge">
            <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;"></span>
            قيد المراجعة
        </div>
        <a href="/vet/referrals/treatment" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            رجوع
        </a>
    </div>
</div>

{{-- ═══ TABS ═══ --}}
<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab('animal')">بيانات الحيوان والإحالة</button>
        <button class="tab-btn" onclick="switchTab('reason')">سبب الإحالة والملاحظات</button>
    </div>

    {{-- Tab 1: Animal Data --}}
    <div class="tab-content active" id="tab-animal">
        <div class="summary-card">
            <div class="summary-header">
                <div class="animal-avatar">🐒</div>
                <div class="animal-info">
                    <h3>شمبانزي أفريقي</h3>
                    <p>001-2025-TR — #ANL-0871</p>
                </div>
            </div>
            <div class="summary-body">
                <div class="summary-grid">
                    <div class="info-item">
                        <div class="info-item-label">رقم الإحالة</div>
                        <div class="info-item-value">TR-2025-001</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">رقم الحيوان</div>
                        <div class="info-item-value">#ANL-0871</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">نوع الحيوان</div>
                        <div class="info-item-value">شمبانزي أفريقي</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">الجنس</div>
                        <div class="info-item-value">ذكر</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">العمر</div>
                        <div class="info-item-value">6 سنوات</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">المجموعة</div>
                        <div class="info-item-value">القرود</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">تاريخ الإحالة</div>
                        <div class="info-item-value">2025-05-13</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">حالة الإحالة</div>
                        <div class="info-item-value">
                            <span class="badge badge-pending"><span class="dot"></span>قيد المراجعة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab 2: Reason --}}
    <div class="tab-content" id="tab-reason">
        <div class="reason-card">
            <div class="reason-header">
                <h3>سبب الإحالة والملاحظات</h3>
            </div>
            <div class="reason-body">
                <div class="reason-section">
                    <div class="reason-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        سبب الإحالة
                    </div>
                    <div class="reason-content">إصابة في الطرف الأمامي</div>
                </div>
                <div class="reason-section">
                    <div class="reason-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        الملاحظات المسجلة قبل التحويل
                    </div>
                    <div class="reason-content">الحيوان لا يستخدم يده اليسرى، مع وجود جرح واضح</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ ACTION BUTTONS ═══ --}}
<div class="actions-bar" id="pendingActions">
    <button class="btn-action btn-approve" onclick="showApproveModal()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        اعتماد إحالة علاج
    </button>
    <button class="btn-action btn-reject" onclick="showRejectModal()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        رفض إحالة علاج
    </button>
</div>

{{-- ═══ APPROVE MODAL ═══ --}}
<div class="modal-backdrop" id="approveModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>اعتماد إحالة علاج</h3>
            <button class="modal-close" onclick="closeModal('approveModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p class="modal-text">سيتم اعتماد الإحالة وإنشاء حالة داخل المستشفى لهذا الحيوان.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-modal btn-modal-cancel" onclick="closeModal('approveModal')">تراجع</button>
            <button class="btn-modal btn-modal-confirm" onclick="confirmApprove()">تأكيد الاعتماد</button>
        </div>
    </div>
</div>

{{-- ═══ REJECT MODAL ═══ --}}
<div class="modal-backdrop" id="rejectModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>رفض إحالة علاج</h3>
            <button class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="reason-section">
                <div class="reason-label">سبب الرفض</div>
                <textarea class="modal-textarea" id="rejectReason" placeholder="يرجى إدخال سبب الرفض..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal btn-modal-cancel" onclick="closeModal('rejectModal')">تراجع</button>
            <button class="btn-modal btn-modal-confirm" onclick="confirmReject()">تأكيد الرفض</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    event.target.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

function showApproveModal() {
    document.getElementById('approveModal').classList.add('active');
}

function showRejectModal() {
    document.getElementById('rejectModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function confirmApprove() {
    closeModal('approveModal');
    alert('تم اعتماد الإحالة بنجاح');
    document.getElementById('pendingActions').style.display = 'none';
}

function confirmReject() {
    const reason = document.getElementById('rejectReason').value;
    if(!reason.trim()) {
        alert('يرجى إدخال سبب الرفض');
        return;
    }
    closeModal('rejectModal');
    alert('تم رفض الإحالة: ' + reason);
    document.getElementById('pendingActions').style.display = 'none';
}
</script>
@endsection
