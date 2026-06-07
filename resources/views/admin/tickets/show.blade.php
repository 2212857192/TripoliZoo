@extends('admin.layout')
@section('title', 'تفاصيل تذكرة الشراء | Tripoli Zoo')
@section('page_title', 'عرض تذكرة شراء')

@php
// Preset ticket types base info
$ticketId = $id ?? '1';
$types = [
    '1' => ['name' => 'تذكرة الكبار', 'price' => 10.00, 'target' => 'كبار (فوق 12 سنة)', 'benefits' => 'الدخول العام للحديقة'],
    '2' => ['name' => 'تذكرة الأطفال', 'price' => 5.00, 'target' => 'أطفال (3 - 12 سنة)', 'benefits' => 'الدخول العام للحديقة'],
    '3' => ['name' => 'تذكرة كبار الشخصيات VIP', 'price' => 25.00, 'target' => 'العائلات وVIP', 'benefits' => 'سيارة جولف + مرشد'],
];
$ticketType = $types[$ticketId] ?? $types['1'];

// Query params customization
$qty = request()->query('qty', 1);
$customerName = request()->query('name', 'عام');
$total = $ticketType['price'] * $qty;
$ticketNo = 'TK-' . rand(100000, 999999);
@endphp

@section('styles')
<style>
    .page-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        transition: color 0.2s;
    }

    .page-back:hover { color: var(--orange); }

    .ticket-details-layout {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem 0;
    }

    /* Ticket stub look */
    .ticket-stub {
        background: white;
        border: 1px solid var(--border);
        border-radius: 24px;
        width: 100%;
        max-width: 460px;
        overflow: hidden;
        box-shadow: 0 15px 40px -15px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    /* Top header area */
    .ticket-stub-top {
        background: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        color: white;
        padding: 2rem;
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
    }

    .ticket-stub-top h3 {
        margin: 0;
        font-weight: 900;
        font-size: 1.5rem;
        letter-spacing: 0.5px;
    }

    .ticket-stub-top p {
        font-size: 0.8rem;
        opacity: 0.85;
        margin: 5px 0 0;
    }

    .ticket-stub-body {
        padding: 2rem;
        background: white;
    }

    .ticket-number {
        text-align: center;
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--orange);
        letter-spacing: 1px;
        margin-bottom: 1.5rem;
        font-family: monospace;
    }

    .ticket-info-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 2rem;
    }

    .ticket-info-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-main);
        padding-bottom: 10px;
        border-bottom: 1px solid var(--bg-color);
    }

    .ticket-info-row:last-child {
        border-bottom: none;
    }

    .ticket-info-row label {
        color: var(--text-muted);
    }

    .ticket-info-row span {
        text-align: left;
    }

    /* Barcode/QR simulator placeholder */
    .barcode-area {
        text-align: center;
        padding: 1rem;
        background: var(--bg-color);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        margin-bottom: 1.5rem;
    }

    .barcode-line {
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* Stub buttons */
    .stub-actions {
        display: flex;
        gap: 12px;
    }

    .btn-stub {
        flex: 1;
        padding: 12px;
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

    @media print {
        body * { visibility: hidden; }
        .ticket-stub, .ticket-stub * { visibility: visible; }
        .ticket-stub { position: absolute; left: 0; top: 0; border: none; box-shadow: none; }
        .stub-actions { display: none; }
    }
</style>
@endsection

@section('content')
<a href="/admin/tickets" class="page-back">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    العودة لقائمة التذاكر
</a>

<div class="ticket-details-layout">
    
    <div class="ticket-stub">
        
        <!-- Top ticket section -->
        <div class="ticket-stub-top">
            <h3>حديقة حيوان طرابلس</h3>
            <p>تذكرة دخول رسمية معتمدة</p>
        </div>

        <!-- Body ticket section -->
        <div class="ticket-stub-body">
            
            <div class="ticket-number">{{ $ticketNo }}</div>

            <div class="ticket-info-grid">
                <div class="ticket-info-row">
                    <label>اسم الزائر</label>
                    <span>{{ $customerName }}</span>
                </div>
                <div class="ticket-info-row">
                    <label>فئة التذكرة</label>
                    <span>{{ $ticketType['name'] }}</span>
                </div>
                <div class="ticket-info-row">
                    <label>الكمية المصدرة</label>
                    <span>{{ $qty }} تذاكر</span>
                </div>
                <div class="ticket-info-row">
                    <label>سعر التذكرة الفردي</label>
                    <span>{{ number_format($ticketType['price'], 2) }} د.ل</span>
                </div>
                <div class="ticket-info-row">
                    <label>المبلغ الإجمالي المدفوع</label>
                    <span style="font-weight: 900; color: var(--orange);">{{ number_format($total, 2) }} د.ل</span>
                </div>
                <div class="ticket-info-row">
                    <label>تاريخ ووقت الشراء</label>
                    <span>2026-06-02 | 11:26 م</span>
                </div>
            </div>

            <!-- Custom simulated QR Code -->
            <div class="barcode-area">
                <canvas id="ticketQR" width="120" height="120"></canvas>
                <div class="barcode-line">امسح الرمز للبوابة الإلكترونية</div>
            </div>

            <div class="stub-actions">
                <button class="btn-stub print" onclick="window.print()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    طباعة التذكرة
                </button>
                <a href="/admin/tickets" class="btn-stub back">العودة للوحة</a>
            </div>

        </div>

    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    window.onload = function() {
        QRCode.toCanvas(document.getElementById('ticketQR'),
            JSON.stringify({
                ticketNo: "{{ $ticketNo }}",
                visitor: "{{ $customerName }}",
                type: "{{ $ticketType['name'] }}",
                qty: "{{ $qty }}",
                total: "{{ $total }} د.ل",
                time: "2026-06-02 23:26"
            }),
            { width: 120, margin: 1 }
        );
    }
</script>
@endsection
