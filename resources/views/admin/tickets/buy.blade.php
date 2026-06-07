@extends('admin.layout')
@section('title', 'شراء وإصدار تذكرة | Tripoli Zoo')
@section('page_title', 'إدارة التذاكر والمبيعات')

@section('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.9);
        --glass-border: rgba(226, 232, 240, 0.8);
        --primary-gradient: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        --accent-gradient: linear-gradient(135deg, #E8651A 0%, #f97316 100%);
        --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
    }

    .page-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 700;
        font-size: 0.88rem;
        margin-bottom: 1.5rem;
        transition: color 0.2s;
    }

    .page-back:hover { color: var(--orange); }

    /* Page Hero */
    .page-hero {
        background: var(--primary-gradient);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 25px -5px rgba(30, 58, 30, 0.25);
    }

    .page-hero h2 {
        font-size: 1.6rem;
        font-weight: 900;
        margin: 0 0 6px;
    }

    .page-hero p {
        font-size: 0.85rem;
        opacity: 0.85;
        margin: 0;
    }

    /* Premium card design */
    .premium-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .card-accent-header {
        padding: 1.3rem 1.8rem;
        background: linear-gradient(to left, rgba(45, 90, 39, 0.03), transparent);
        border-bottom: 1.5px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-accent-header h3 {
        font-size: 1.1rem;
        font-weight: 900;
        color: #1e3a1e;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .icon-wrapper {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: rgba(45, 90, 39, 0.08);
        color: #2d5a27;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .premium-card-body {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 800;
        font-size: 0.88rem;
        color: #1e3a1e;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.92rem;
        outline: none;
        transition: all 0.2s;
        background: white;
    }

    .form-input:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 3px rgba(232, 101, 26, 0.08);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .form-divider {
        height: 1px;
        background: var(--border);
        margin: 1.8rem 0;
    }

    /* Receipt Preview inside the single container */
    .receipt-preview-container {
        background: linear-gradient(180deg, #F8FAF6 0%, #F1F5F9 100%);
        border: 1.5px dashed rgba(45, 90, 39, 0.25);
        border-radius: 16px;
        padding: 1.8rem;
        margin-bottom: 1.8rem;
    }

    .receipt-header {
        text-align: center;
        padding-bottom: 1rem;
        border-bottom: 1.5px dashed rgba(0,0,0,0.15);
        margin-bottom: 1.2rem;
    }

    .receipt-header h4 {
        margin: 0 0 6px;
        font-weight: 900;
        color: #1e3a1e;
        font-size: 1.1rem;
    }

    .receipt-header p {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0;
    }

    .receipt-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.2rem;
    }

    .receipt-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .receipt-cell label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 700;
    }

    .receipt-cell span {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .receipt-cell.total-cell {
        border-right: 2px solid var(--border);
        padding-right: 1.2rem;
    }

    .receipt-cell.total-cell span {
        font-size: 1.3rem;
        color: var(--orange);
    }

    /* Actions Row */
    .actions-row {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-buy-premium {
        padding: 12px 30px;
        background: var(--accent-gradient);
        color: white;
        border: none;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.95rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 5px 15px rgba(232, 101, 26, 0.25);
        transition: all 0.3s;
    }

    .btn-buy-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(232, 101, 26, 0.35);
    }

    .btn-cancel-premium {
        padding: 12px 24px;
        background: var(--bg-color);
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel-premium:hover {
        background: #E2E8F0;
        color: var(--text-main);
    }

    /* Modal Styling */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-box {
        transform: scale(0.9) translateY(30px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        max-width: 440px;
        padding: 10px;
    }

    .modal-overlay.show .modal-box {
        transform: scale(1) translateY(0);
    }

    /* Ticket stub look inside modal */
    .ticket-stub {
        background: white;
        border: 1px solid var(--border);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        position: relative;
    }

    /* Top header area */
    .ticket-stub-top {
        background: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        color: white;
        padding: 1.8rem;
        text-align: center;
        position: relative;
    }

    .ticket-stub-top::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        right: 0;
        height: 20px;
        background: radial-gradient(circle, transparent, transparent 50%, white 50%, white);
        background-size: 20px 20px;
        z-index: 10;
    }

    .ticket-stub-top h3 {
        margin: 0;
        font-weight: 900;
        font-size: 1.4rem;
        letter-spacing: 0.5px;
    }

    .ticket-stub-top p {
        font-size: 0.8rem;
        opacity: 0.85;
        margin: 5px 0 0;
    }

    .ticket-stub-body {
        padding: 2rem 1.8rem 1.8rem;
        background: white;
    }

    .ticket-number {
        text-align: center;
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--orange);
        letter-spacing: 1px;
        margin-bottom: 1.3rem;
        font-family: monospace;
    }

    .ticket-info-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 1.5rem;
    }

    .ticket-info-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-main);
        padding-bottom: 8px;
        border-bottom: 1px solid var(--bg-color);
    }

    .ticket-info-row:last-child {
        border-bottom: none;
    }

    .ticket-info-row label {
        color: var(--text-muted);
    }

    /* Barcode/QR simulator placeholder */
    .barcode-area {
        text-align: center;
        padding: 0.8rem;
        background: var(--bg-color);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        margin-bottom: 1.5rem;
    }

    .barcode-line {
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Stub buttons */
    .stub-actions {
        display: flex;
        gap: 12px;
    }

    .btn-stub {
        flex: 1;
        padding: 11px;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        outline: none;
    }

    .btn-stub.print {
        background: var(--primary-gradient);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(30, 58, 30, 0.2);
    }

    .btn-stub.print:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(30, 58, 30, 0.3);
    }

    .btn-stub.back {
        background: var(--bg-color);
        color: var(--text-muted);
        border: 1.5px solid var(--border);
    }

    .btn-stub.back:hover {
        background: #E2E8F0;
    }

    .toast {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(80px);
        background: #1E293B;
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        z-index: 99999;
        transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
        white-space: nowrap;
    }

    .toast.show {
        transform: translateX(-50%) translateY(0);
    }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
        .receipt-grid { grid-template-columns: 1fr; }
        .receipt-cell.total-cell { border-right: none; border-top: 1px dashed var(--border); padding-right: 0; padding-top: 10px; }
        .actions-row { flex-direction: column; }
        .btn-buy-premium, .btn-cancel-premium { width: 100%; justify-content: center; }
        .stub-actions { flex-direction: column; }
    }

    @media print {
        body {
            background: white !important;
            color: black !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Ensure colors, shadows, and backgrounds print exactly as seen */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Hide all page layouts except the modal and stub */
        .sidebar, .topbar, .page-back, .page-hero, .premium-card, .toast {
            display: none !important;
        }

        .main-content, .content-area {
            padding: 0 !important;
            margin: 0 !important;
            background: transparent !important;
            display: block !important;
        }

        /* Adjust modal overlay for printing */
        .modal-overlay {
            position: absolute !important;
            inset: 0 !important;
            background: white !important;
            backdrop-filter: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: flex !important;
            justify-content: center !important;
            align-items: flex-start !important;
            padding-top: 10px !important;
        }

        .modal-box {
            transform: none !important;
            box-shadow: none !important;
            max-width: 440px !important;
            width: 100% !important;
            padding: 0 !important;
        }

        .ticket-stub {
            border: 1px solid #cbd5e1 !important;
            border-radius: 24px !important;
            box-shadow: none !important;
            background: white !important;
        }

        .ticket-stub-top {
            background: #1e3a1e !important;
            background: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%) !important;
            color: white !important;
            padding: 1.8rem !important;
        }

        .stub-actions {
            display: none !important;
        }
    }
</style>
@endsection

@section('content')

<a href="/admin/tickets" class="page-back">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    العودة لقائمة التذاكر
</a>

<!-- Header Hero -->
<div class="page-hero">
    <h2>شراء وإصدار تذاكر دخول</h2>
    <p>قم بتسجيل تفاصيل عملية بيع وإصدار التذاكر للزوار في شباك التذاكر الموحد.</p>
</div>

<!-- Single Main Container -->
<div class="premium-card">
    <div class="card-accent-header">
        <div class="icon-wrapper">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
        </div>
        <h3>تفاصيل إصدار المبيعات وتأكيد الفاتورة</h3>
    </div>
    
    <div class="premium-card-body">
        <!-- Form Inputs Grid -->
        <div class="form-group">
            <label>اسم العميل / الزائر (اختياري)</label>
            <input type="text" id="custName" class="form-input" placeholder="مثال: أحمد محمد سالم" onkeyup="updateReceipt()">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>فئة التذكرة</label>
                <select id="ticketType" class="form-input" onchange="updateReceipt()">
                    <option value="1" data-price="10.00">تذكرة الكبار (10.00 د.ل)</option>
                    <option value="2" data-price="5.00">تذكرة الأطفال (5.00 د.ل)</option>
                    <option value="3" data-price="25.00">تذكرة كبار الشخصيات VIP (25.00 د.ل)</option>
                </select>
            </div>
            <div class="form-group">
                <label>عدد التذاكر المطلوب</label>
                <input type="number" id="qty" class="form-input" min="1" max="50" value="1" onchange="updateReceipt()" onkeyup="updateReceipt()">
            </div>
        </div>

        <div class="form-group">
            <label>طريقة الدفع وسداد القيمة</label>
            <select id="payMethod" class="form-input">
                <option>نقداً (كاش)</option>
                <option>خدمة سداد الإلكترونية</option>
                <option>بطاقة تداول المصرفية</option>
            </select>
        </div>

        <!-- Divider -->
        <div class="form-divider"></div>

        <!-- Integrated Live Preview Receipt Box -->
        <div class="receipt-preview-container">
            <div class="receipt-header">
                <h4>فاتورة دخول حديقة حيوان طرابلس</h4>
                <p>تاريخ المعاملة: 2026-06-02 م</p>
            </div>
            <div class="receipt-grid">
                <div class="receipt-cell">
                    <label>اسم الزائر</label>
                    <span id="previewCust">عام</span>
                </div>
                <div class="receipt-cell">
                    <label>نوع التذكرة</label>
                    <span id="previewType">تذكرة الكبار</span>
                </div>
                <div class="receipt-cell">
                    <label>سعر التذكرة</label>
                    <span id="previewUnitPrice">10.00 د.ل</span>
                </div>
                <div class="receipt-cell">
                    <label>الكمية</label>
                    <span id="previewQty">1</span>
                </div>
                <div class="receipt-cell total-cell">
                    <label>الإجمالي المطلوب</label>
                    <span id="previewTotal">10.00 د.ل</span>
                </div>
            </div>
        </div>

        <!-- Actions Row inside same card -->
        <div class="actions-row">
            <a href="/admin/tickets" class="btn-cancel-premium">إلغاء وتراجع</a>
            <button class="btn-buy-premium" onclick="submitPurchase()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                تأكيد وإصدار التذكرة
            </button>
        </div>
    </div>
</div>

<!-- Beautiful Ticket Stub Popup Modal -->
<div class="modal-overlay" id="ticketModal">
    <div class="modal-box">
        <div class="ticket-stub">
            <div class="ticket-stub-top">
                <h3>حديقة حيوان طرابلس</h3>
                <p>تذكرة دخول رسمية معتمدة</p>
            </div>
            <div class="ticket-stub-body">
                <div class="ticket-number" id="modalTicketNo">TK-000000</div>
                
                <div class="ticket-info-grid">
                    <div class="ticket-info-row">
                        <label>اسم الزائر</label>
                        <span id="modalVisitorName">عام</span>
                    </div>
                    <div class="ticket-info-row">
                        <label>فئة التذكرة</label>
                        <span id="modalTicketType">تذكرة الكبار</span>
                    </div>
                    <div class="ticket-info-row">
                        <label>الكمية المصدرة</label>
                        <span id="modalQty">1 تذاكر</span>
                    </div>
                    <div class="ticket-info-row">
                        <label>سعر التذكرة الفردي</label>
                        <span id="modalUnitPrice">10.00 د.ل</span>
                    </div>
                    <div class="ticket-info-row">
                        <label>المبلغ الإجمالي المدفوع</label>
                        <span style="font-weight: 900; color: var(--orange);" id="modalTotalPaid">10.00 د.ل</span>
                    </div>
                    <div class="ticket-info-row">
                        <label>تاريخ ووقت الشراء</label>
                        <span>2026-06-02 | 11:26 م</span>
                    </div>
                </div>

                <div class="barcode-area">
                    <canvas id="modalQR" width="120" height="120"></canvas>
                    <div class="barcode-line">امسح الرمز للبوابة الإلكترونية</div>
                </div>

                <div class="stub-actions">
                    <button class="btn-stub print" onclick="window.print()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        طباعة التذكرة
                    </button>
                    <button class="btn-stub back" onclick="closeModal()">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    function updateReceipt() {
        const cust = document.getElementById('custName').value.trim() || 'عام';
        const typeSelect = document.getElementById('ticketType');
        const selectedOpt = typeSelect.options[typeSelect.selectedIndex];
        const typeName = selectedOpt.text.split('(')[0].trim();
        const price = parseFloat(selectedOpt.getAttribute('data-price'));
        const qty = parseInt(document.getElementById('qty').value) || 1;

        const total = price * qty;

        document.getElementById('previewCust').textContent = cust;
        document.getElementById('previewType').textContent = typeName;
        document.getElementById('previewUnitPrice').textContent = price.toFixed(2) + ' د.ل';
        document.getElementById('previewQty').textContent = qty;
        document.getElementById('previewTotal').textContent = total.toFixed(2) + ' د.ل';
    }

    function submitPurchase() {
        const btn = document.querySelector('.btn-buy-premium');
        btn.textContent = '⏳ جاري إتمام الشراء...';
        btn.disabled = true;

        setTimeout(() => {
            showToast('✅ تم تأكيد عملية الشراء وإصدار التذكرة');
            btn.textContent = '✅ تمت المعاملة!';
            
            // Gather values
            const cust = document.getElementById('custName').value.trim() || 'عام';
            const typeSelect = document.getElementById('ticketType');
            const selectedOpt = typeSelect.options[typeSelect.selectedIndex];
            const typeName = selectedOpt.text.split('(')[0].trim();
            const price = parseFloat(selectedOpt.getAttribute('data-price'));
            const qty = parseInt(document.getElementById('qty').value) || 1;
            const total = price * qty;
            const randNo = 'TK-' + Math.floor(100000 + Math.random() * 900000);

            // Populate Modal ticket elements
            document.getElementById('modalTicketNo').textContent = randNo;
            document.getElementById('modalVisitorName').textContent = cust;
            document.getElementById('modalTicketType').textContent = typeName;
            document.getElementById('modalQty').textContent = qty + ' تذاكر';
            document.getElementById('modalUnitPrice').textContent = price.toFixed(2) + ' د.ل';
            document.getElementById('modalTotalPaid').textContent = total.toFixed(2) + ' د.ل';

            // Generate QR Code inside modal
            QRCode.toCanvas(document.getElementById('modalQR'),
                JSON.stringify({
                    ticketNo: randNo,
                    visitor: cust,
                    type: typeName,
                    qty: qty,
                    total: total.toFixed(2) + " د.ل",
                    time: "2026-06-02 23:33"
                }),
                { width: 120, margin: 1 }
            );

            // Show Modal
            document.getElementById('ticketModal').classList.add('show');
        }, 800);
    }

    function closeModal() {
        document.getElementById('ticketModal').classList.remove('show');
        
        // Reset buy button
        const btn = document.querySelector('.btn-buy-premium');
        btn.textContent = 'تأكيد وإصدار التذكرة';
        btn.disabled = false;
    }

    document.getElementById('ticketModal').addEventListener('click', function(e) {
        if(e.target === this) {
            closeModal();
        }
    });

    updateReceipt();
</script>
@endsection
