@extends('admin.layout')
@section('title', 'إدارة التذاكر | Tripoli Zoo')
@section('page_title', 'إدارة التذاكر والمبيعات')

@section('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --primary-gradient: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        --accent-gradient: linear-gradient(135deg, #E8651A 0%, #f97316 100%);
        --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.06);
    }

    .tickets-container {
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
    }

    /* Top Stats Bar */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
    }

    .stat-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon.green  { background: rgba(45, 90, 39, 0.1); color: #2d5a27; }
    .stat-icon.orange { background: rgba(232, 101, 26, 0.1); color: #E8651A; }
    .stat-icon.blue   { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }

    .stat-info label {
        display: block;
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 700;
        margin-bottom: 4px;
    }

    .stat-info span {
        font-size: 1.6rem;
        font-weight: 900;
        color: var(--text-main);
    }

    /* Controls Panel */
    .control-panel {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .search-filter-box {
        display: flex;
        gap: 10px;
        flex: 1;
        max-width: 500px;
    }

    .search-input {
        flex: 1;
        padding: 10px 16px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        outline: none;
        transition: all 0.2s;
    }

    .search-input:focus {
        border-color: var(--orange);
    }

    .filter-select {
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.88rem;
        font-weight: 700;
        background: white;
        outline: none;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .btn-premium {
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-premium.primary {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(30, 58, 30, 0.2);
    }

    .btn-premium.primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(30, 58, 30, 0.3);
    }

    .btn-premium.accent {
        background: var(--accent-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(232, 101, 26, 0.2);
    }

    .btn-premium.accent:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(232, 101, 26, 0.3);
    }

    /* Ticket Grid / Cards */
    .tickets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .ticket-card-premium {
        background: white;
        border: 2px solid #B7D803;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .ticket-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px -10px rgba(183, 216, 3, 0.3);
        border-color: #96B302;
    }

    .ticket-header-gradient {
        background: white;
        padding: 1.5rem;
        color: #1e3a1e;
        text-align: center;
        position: relative;
        border-bottom: 1px solid #e2e8f0;
    }

    .ticket-card-premium.suspended .ticket-header-gradient {
        background: white;
        color: #64748B;
    }

    .ticket-name {
        font-size: 1.2rem;
        font-weight: 900;
        margin: 0 0 6px;
    }

    .ticket-price {
        font-size: 1.6rem;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .ticket-price span {
        font-size: 0.9rem;
        font-weight: 700;
        opacity: 0.9;
    }

    .ticket-details {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .detail-row-ticket {
        display: flex;
        justify-content: space-between;
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-main);
        padding-bottom: 8px;
        border-bottom: 1px solid var(--bg-color);
    }

    .detail-row-ticket:last-child {
        border-bottom: none;
    }

    .detail-row-ticket span:first-child {
        color: var(--text-muted);
    }

    .status-badge-ticket {
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 900;
    }

    .status-badge-ticket.active { background: #DCFCE7; color: #166534; }
    .status-badge-ticket.suspended { background: #FEE2E2; color: #991B1B; }

    .ticket-actions-bar {
        padding: 1rem 1.5rem;
        background: #FAFBFC;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        gap: 8px;
    }

    .btn-ticket-op {
        flex: 1;
        padding: 8px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        text-decoration: none;
        color: var(--text-main);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .btn-ticket-op:hover {
        border-color: var(--orange);
        color: var(--orange);
        background: white;
    }

    .btn-ticket-op.toggle-status.suspended-btn {
        color: #EF4444;
        border-color: #FEE2E2;
        background: #FFF5F5;
    }

    .btn-ticket-op.toggle-status.suspended-btn:hover {
        background: #FEE2E2;
    }

    .btn-ticket-op.toggle-status.activate-btn {
        color: var(--green);
        border-color: #DCFCE7;
        background: #F4FBF7;
    }

    .btn-ticket-op.toggle-status.activate-btn:hover {
        background: #DCFCE7;
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
        z-index: 9999;
        transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
        white-space: nowrap;
    }

    .toast.show {
        transform: translateX(-50%) translateY(0);
    }
</style>
@endsection

@section('content')
<div class="tickets-container">

    <!-- Metrics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
            </div>
            <div class="stat-info">
                <label>التذاكر النشطة المتاحة</label>
                <span id="activeCount">3 فئات</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <div class="stat-info">
                <label>إجمالي مبيعات اليوم</label>
                <span>4,250 د.ل</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
            </div>
            <div class="stat-info">
                <label>الزوار المسجلين اليوم</label>
                <span>1,240 زائر</span>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="control-panel">
        <div class="search-filter-box">
            <input type="text" id="searchInput" class="search-input" placeholder="البحث باسم التذكرة..." onkeyup="filterTickets()">
            <select id="statusFilter" class="filter-select" onchange="filterTickets()">
                <option value="all">كل الحالات</option>
                <option value="active">تذاكر نشطة</option>
                <option value="suspended">تذاكر موقوفة</option>
            </select>
        </div>
        <div class="action-buttons">
            <a href="/admin/tickets/create" class="btn-premium primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                إضافة فئة تذكرة
            </a>
        </div>
    </div>

    <!-- Tickets Grid -->
    <div class="tickets-grid" id="ticketsGrid">
        
        <!-- Ticket 1 -->
        <div class="ticket-card-premium" data-name="تذكرة الكبار" data-status="active" id="ticket-1">
            <div class="ticket-header-gradient">
                <h4 class="ticket-name">تذكرة الكبار</h4>
                <div class="ticket-price"><span id="price-val-1">10.00</span> <span>د.ل</span></div>
            </div>
            <div class="ticket-details">
                <div class="detail-row-ticket">
                    <span>الفئة المستهدفة</span>
                    <span>كبار (فوق 12 سنة)</span>
                </div>
                <div class="detail-row-ticket">
                    <span>حالة التذكرة</span>
                    <span class="status-badge-ticket active" id="badge-1">نشطة</span>
                </div>
                <div class="detail-row-ticket">
                    <span>شاملة الخدمات</span>
                    <span>الدخول العام فقط</span>
                </div>
            </div>
            <div class="ticket-actions-bar">
                <a href="/admin/tickets/1/edit" class="btn-ticket-op">
                    تعديل السعر
                </a>
                <button class="btn-ticket-op toggle-status suspended-btn" id="btn-toggle-1" onclick="toggleTicketStatus(1)">
                    إيقاف التذكرة
                </button>
            </div>
        </div>

        <!-- Ticket 2 -->
        <div class="ticket-card-premium" data-name="تذكرة الأطفال" data-status="active" id="ticket-2">
            <div class="ticket-header-gradient">
                <h4 class="ticket-name">تذكرة الأطفال</h4>
                <div class="ticket-price"><span id="price-val-2">5.00</span> <span>د.ل</span></div>
            </div>
            <div class="ticket-details">
                <div class="detail-row-ticket">
                    <span>الفئة المستهدفة</span>
                    <span>أطفال (3 - 12 سنة)</span>
                </div>
                <div class="detail-row-ticket">
                    <span>حالة التذكرة</span>
                    <span class="status-badge-ticket active" id="badge-2">نشطة</span>
                </div>
                <div class="detail-row-ticket">
                    <span>شاملة الخدمات</span>
                    <span>الدخول العام فقط</span>
                </div>
            </div>
            <div class="ticket-actions-bar">
                <a href="/admin/tickets/2/edit" class="btn-ticket-op">
                    تعديل السعر
                </a>
                <button class="btn-ticket-op toggle-status suspended-btn" id="btn-toggle-2" onclick="toggleTicketStatus(2)">
                    إيقاف التذكرة
                </button>
            </div>
        </div>

        <!-- Ticket 3 -->
        <div class="ticket-card-premium" data-name="تذكرة كبار الشخصيات vip" data-status="active" id="ticket-3">
            <div class="ticket-header-gradient">
                <h4 class="ticket-name">تذكرة كبار الشخصيات VIP</h4>
                <div class="ticket-price"><span id="price-val-3">25.00</span> <span>د.ل</span></div>
            </div>
            <div class="ticket-details">
                <div class="detail-row-ticket">
                    <span>الفئة المستهدفة</span>
                    <span>العائلات وVIP</span>
                </div>
                <div class="detail-row-ticket">
                    <span>حالة التذكرة</span>
                    <span class="status-badge-ticket active" id="badge-3">نشطة</span>
                </div>
                <div class="detail-row-ticket">
                    <span>شاملة الخدمات</span>
                    <span>سيارة جولف + مرشد</span>
                </div>
            </div>
            <div class="ticket-actions-bar">
                <a href="/admin/tickets/3/edit" class="btn-ticket-op">
                    تعديل السعر
                </a>
                <button class="btn-ticket-op toggle-status suspended-btn" id="btn-toggle-3" onclick="toggleTicketStatus(3)">
                    إيقاف التذكرة
                </button>
            </div>
        </div>

        <!-- Ticket 4 -->
        <div class="ticket-card-premium suspended" data-name="تذكرة السياح الأجانب" data-status="suspended" id="ticket-4">
            <div class="ticket-header-gradient">
                <h4 class="ticket-name">تذكرة السياح الأجانب</h4>
                <div class="ticket-price"><span id="price-val-4">50.00</span> <span>د.ل</span></div>
            </div>
            <div class="ticket-details">
                <div class="detail-row-ticket">
                    <span>الفئة المستهدفة</span>
                    <span>الزوار غير الليبيين</span>
                </div>
                <div class="detail-row-ticket">
                    <span>حالة التذكرة</span>
                    <span class="status-badge-ticket suspended" id="badge-4">موقوفة</span>
                </div>
                <div class="detail-row-ticket">
                    <span>شاملة الخدمات</span>
                    <span>مرشد سياحي خاص</span>
                </div>
            </div>
            <div class="ticket-actions-bar">
                <a href="/admin/tickets/4/edit" class="btn-ticket-op">
                    تعديل السعر
                </a>
                <button class="btn-ticket-op toggle-status activate-btn" id="btn-toggle-4" onclick="toggleTicketStatus(4)">
                    تفعيل التذكرة
                </button>
            </div>
        </div>

    </div>

</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2800);
    }

    function toggleTicketStatus(id) {
        const card = document.getElementById('ticket-' + id);
        const badge = document.getElementById('badge-' + id);
        const btn = document.getElementById('btn-toggle-' + id);
        const isCurrentlyActive = card.getAttribute('data-status') === 'active';

        if (isCurrentlyActive) {
            // Deactivate
            card.setAttribute('data-status', 'suspended');
            card.classList.add('suspended');
            badge.textContent = 'موقوفة';
            badge.className = 'status-badge-ticket suspended';
            btn.textContent = 'تفعيل التذكرة';
            btn.className = 'btn-ticket-op toggle-status activate-btn';
            showToast('🚫 تم إيقاف التذكرة بنجاح');
        } else {
            // Activate
            card.setAttribute('data-status', 'active');
            card.classList.remove('suspended');
            badge.textContent = 'نشطة';
            badge.className = 'status-badge-ticket active';
            btn.textContent = 'إيقاف التذكرة';
            btn.className = 'btn-ticket-op toggle-status suspended-btn';
            showToast('✅ تم تفعيل التذكرة ونشرها');
        }
        updateStats();
    }

    function updateStats() {
        const activeCards = document.querySelectorAll('.ticket-card-premium[data-status="active"]').length;
        document.getElementById('activeCount').textContent = activeCards + ' فئات';
    }

    function filterTickets() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const cards = document.querySelectorAll('.ticket-card-premium');

        cards.forEach(card => {
            const name = card.getAttribute('data-name').toLowerCase();
            const cardStatus = card.getAttribute('data-status');

            const matchesQuery = name.includes(query);
            const matchesStatus = (status === 'all') || (cardStatus === status);

            if (matchesQuery && matchesStatus) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
