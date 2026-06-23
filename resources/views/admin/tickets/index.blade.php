@extends($__layout ?? 'admin.layout')
@section('title', 'إدارة التذاكر | Tripoli Zoo')
@section('page_title', 'إدارة فئات التذاكر')

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
</style>
@endsection

@section('content')
<div class="tickets-container">

    <!-- Controls -->
    <div class="control-panel">
        <div class="search-filter-box">
            <input type="text" id="searchInput" class="search-input" placeholder="البحث باسم فئة التذكرة..." onkeyup="filterTickets()">
            <select id="statusFilter" class="filter-select" onchange="filterTickets()">
                <option value="all">كل الحالات</option>
                <option value="active">تذاكر نشطة</option>
                <option value="suspended">تذاكر موقوفة</option>
            </select>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.tickets.create') }}" class="btn-premium primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                إضافة فئة تذكرة
            </a>
        </div>
    </div>

    <!-- Tickets Grid -->
    <div class="tickets-grid" id="ticketsGrid">
        @forelse($ticketTypes as $type)
        <div class="ticket-card-premium {{ $type->is_active ? '' : 'suspended' }}" data-category="{{ $type->target_description }}" data-status="{{ $type->is_active ? 'active' : 'suspended' }}" id="ticket-{{ $type->id }}">
            <div class="ticket-header-gradient">
                <h4 class="ticket-name">{{ $type->target_description }}</h4>
                <div class="ticket-price"><span>{{ number_format((float) $type->price, 2) }}</span> <span>د.ل</span></div>
            </div>
            <div class="ticket-details">
                <div class="detail-row-ticket">
                    <span>الفئة</span>
                    <span>{{ $type->target_description ?: '—' }}</span>
                </div>
                <div class="detail-row-ticket">
                    <span>نوع الزائر</span>
                    <span>{{ $type->visitor_nationality }}</span>
                </div>
                <div class="detail-row-ticket">
                    <span>العمر</span>
                    <span>{{ $type->visitor_age_group }}</span>
                </div>
                <div class="detail-row-ticket">
                    <span>حالة التذكرة</span>
                    <span class="status-badge-ticket {{ $type->is_active ? 'active' : 'suspended' }}">
                        {{ $type->is_active ? 'نشطة' : 'موقوفة' }}
                    </span>
                </div>
            </div>
            <div class="ticket-actions-bar">
                <a href="{{ route('admin.tickets.edit', $type) }}" class="btn-ticket-op">
                    تعديل الفئة
                </a>
                <form action="{{ route('admin.tickets.toggle', $type) }}" method="POST" class="js-ticket-toggle-form" data-active="{{ $type->is_active ? '1' : '0' }}" style="flex:1;display:flex;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-ticket-op toggle-status {{ $type->is_active ? 'suspended-btn' : 'activate-btn' }}" style="width:100%;">
                        {{ $type->is_active ? 'إيقاف التذكرة' : 'تفعيل التذكرة' }}
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;padding:2rem;text-align:center;color:var(--text-muted);font-weight:700;">
            لا توجد فئات تذاكر بعد.
        </div>
        @endforelse
    </div>

</div>
@endsection

@section('scripts')
@include('partials.admin-ticket-form-scripts')
<script>
    function filterTickets() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const cards = document.querySelectorAll('.ticket-card-premium');

        cards.forEach(card => {
            const category = (card.getAttribute('data-category') || '').toLowerCase();
            const cardStatus = card.getAttribute('data-status');

            const matchesQuery = category.includes(query);
            const matchesStatus = status === 'all' || cardStatus === status;

            card.style.display = (matchesQuery && matchesStatus) ? 'flex' : 'none';
        });
    }
</script>
@endsection
