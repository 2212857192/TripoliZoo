@extends('vet.layout')
@section('title', 'تفاصيل إحالة التشريح | المستشفى البيطري')
@section('page_title', 'تفاصيل إحالة التشريح')

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

/* ═══ RESULT CARD ═══ */
.result-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.result-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e8edf5;
}
.result-header h3 { font-size: 1.1rem; font-weight: 900; color: #0f172a; }
.result-body { padding: 2rem; }
.result-section { margin-bottom: 1.5rem; }
.result-section:last-child { margin-bottom: 0; }
.result-label { font-size: 0.8rem; font-weight: 800; color: #2E7D32; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
.result-content {
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
.btn-document {
    background: #2E7D32;
    color: #fff;
    border-color: #2E7D32;
}
.btn-document:hover { background: #1B5E20; }

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
    max-width: 600px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalIn 0.3s ease-out;
    max-height: 90vh;
    overflow-y: auto;
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
.modal-section { margin-bottom: 1.5rem; }
.modal-section:last-child { margin-bottom: 0; }
.modal-label { font-size: 0.8rem; font-weight: 800; color: #2E7D32; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
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
.modal-input {
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
}
.modal-input:focus {
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
.badge-documented { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.badge-documented .dot { background: #22c55e; }
</style>
@endsection

@section('content')
{{-- ═══ TABS ═══ --}}
<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab('animal')">بيانات الحيوان وحالة النفوق</button>
        <button class="tab-btn" onclick="switchTab('result')">نتيجة التشريح</button>
    </div>

    {{-- Tab 1: Animal Data --}}
    <div class="tab-content active" id="tab-animal">
        <div class="summary-card">
            <div class="summary-header">
                <div class="animal-avatar">🐒</div>
                <div class="animal-info">
                    <h3>شمبانزي أفريقي</h3>
                    <p>AR-2025-001 — #ANL-0871</p>
                </div>
            </div>
            <div class="summary-body">
                <div class="summary-grid">
                    <div class="info-item">
                        <div class="info-item-label">رقم الإحالة</div>
                        <div class="info-item-value">AR-2025-001</div>
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
                        <div class="info-item-label">تاريخ تسجيل النفوق</div>
                        <div class="info-item-value">2025-05-13</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">تاريخ إحالة التشريح</div>
                        <div class="info-item-value">2025-05-13</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">مرسل الإحالة</div>
                        <div class="info-item-value">رئيس قسم الرعاية والتغذية</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-label">سبب الإحالة</div>
                        <div class="info-item-value">الحاجة إلى تحديد سبب النفوق النهائي</div>
                    </div>
                    <div class="info-item" style="grid-column: span 2;">
                        <div class="info-item-label">الملاحظات المسجلة عند النفوق</div>
                        <div class="info-item-value">تم العثور على الحيوان نافقًا صباحًا دون أعراض واضحة مسبقة</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab 2: Result --}}
    <div class="tab-content" id="tab-result">
        <div class="result-card">
            <div class="result-header">
                <h3>نتيجة التشريح</h3>
            </div>
            <div class="result-body">
                <div id="pendingResult">
                    <p style="text-align: center; color: #64748b; font-weight: 600; padding: 2rem;">
                        لم يتم توثيق نتيجة التشريح بعد.
                    </p>
                    <div style="text-align: center; margin-top: 1rem;">
                        <button class="btn-action btn-document" onclick="showDocumentModal()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            توثيق نتيجة التشريح
                        </button>
                    </div>
                </div>
                <div id="documentedResult" style="display: none;">
                    <div class="result-section">
                        <div class="result-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            سبب النفوق النهائي
                        </div>
                        <div class="result-content">-</div>
                    </div>
                    <div class="result-section">
                        <div class="result-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            ملاحظات التشريح
                        </div>
                        <div class="result-content">-</div>
                    </div>
                    <div class="result-section">
                        <div class="result-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            تاريخ التوثيق
                        </div>
                        <div class="result-content">-</div>
                    </div>
                    <div class="result-section">
                        <div class="result-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            موثق النتيجة
                        </div>
                        <div class="result-content">-</div>
                    </div>
                    <div class="result-section">
                        <div class="result-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            تقرير الصفة التشريحية
                        </div>
                        <div class="result-content">
                            <button class="btn-action btn-document" style="padding: 8px 16px; font-size: 0.8rem;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                عرض / تحميل الملف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ═══ DOCUMENT MODAL ═══ --}}
<div class="modal-backdrop" id="documentModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>توثيق نتيجة التشريح</h3>
            <button class="modal-close" onclick="closeModal('documentModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-section">
                <div class="modal-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    سبب النفوق النهائي
                    <span style="color: #ef4444; font-size: 0.7rem; margin-right: 4px;">*</span>
                </div>
                <textarea class="modal-textarea" id="deathCause" placeholder="يرجى إدخال سبب النفوق النهائي..."></textarea>
            </div>
            <div class="modal-section">
                <div class="modal-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    ملاحظات التشريح
                </div>
                <textarea class="modal-textarea" id="autopsyNotes" placeholder="يرجى إدخال ملاحظات التشريح (اختياري)..."></textarea>
            </div>
            <div class="modal-section">
                <div class="modal-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    تقرير الصفة التشريحية
                </div>
                <div style="border: 1.5px dashed #e2e8f0; border-radius: 10px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#2E7D32'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <p style="color: #64748b; font-weight: 600; font-size: 0.85rem; margin-top: 0.5rem;">اضغط لرفع صورة أو ملف</p>
                    <input type="file" id="autopsyReport" style="display: none;">
                </div>
            </div>
            <div class="modal-section">
                <div class="modal-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    تاريخ التوثيق
                </div>
                <input type="date" class="modal-input" id="documentationDate" value="2025-05-14">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal btn-modal-cancel" onclick="closeModal('documentModal')">إلغاء</button>
            <button class="btn-modal btn-modal-confirm" onclick="confirmDocument()">حفظ النتيجة</button>
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

function showDocumentModal() {
    document.getElementById('documentModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function confirmDocument() {
    const deathCause = document.getElementById('deathCause').value;
    const autopsyNotes = document.getElementById('autopsyNotes').value;
    const documentationDate = document.getElementById('documentationDate').value;
    
    if(!deathCause.trim()) {
        alert('يرجى إدخال سبب النفوق النهائي');
        return;
    }
    
    closeModal('documentModal');
    
    // Show documented result
    document.getElementById('pendingResult').style.display = 'none';
    document.getElementById('documentedResult').style.display = 'block';
    
    // Update result content
    const resultSections = document.querySelectorAll('#documentedResult .result-content');
    resultSections[0].textContent = deathCause;
    resultSections[1].textContent = autopsyNotes || '-';
    resultSections[2].textContent = documentationDate;
    resultSections[3].textContent = 'رئيس قسم المستشفى البيطري';
    
    // Update status badge
    const statusBadge = document.querySelector('.case-status-badge');
    statusBadge.innerHTML = '<span style="width:8px;height:8px;border-radius:50%;background:#22c55e;"></span>موثقة';
    
    alert('تم حفظ نتيجة التشريح بنجاح');
}
</script>
@endsection
