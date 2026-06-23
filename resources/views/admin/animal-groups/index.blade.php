@extends($__layout ?? 'admin.layout')
@section('title', 'المجموعات الحيوانية | Tripoli Zoo')
@section('page_title', 'إدارة المجموعات الحيوانية')

@section('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(226, 232, 240, 0.8);
        --primary-gradient: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        --accent-gradient: linear-gradient(135deg, #E8651A 0%, #f97316 100%);
        --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.06);
    }

    .groups-container {
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
    }

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
        max-width: 520px;
    }

    .search-input,
    .filter-select {
        padding: 10px 16px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.9rem;
        outline: none;
        background: white;
    }

    .search-input { flex: 1; }
    .filter-select { font-weight: 700; min-width: 150px; }

    .search-input:focus,
    .filter-select:focus {
        border-color: var(--orange);
    }

    .btn-premium {
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        font-family: 'Cairo', sans-serif;
        font-weight: 800;
        font-size: 0.9rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(30, 58, 30, 0.2);
    }

    .btn-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(30, 58, 30, 0.3);
    }

    .groups-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .group-card-premium {
        background: white;
        border: 2px solid #B7D803;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
    }

    .group-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px -10px rgba(183, 216, 3, 0.3);
        border-color: #96B302;
    }

    .group-card-premium.suspended {
        border-color: #e2e8f0;
        opacity: 0.92;
    }

    .group-header-gradient {
        padding: 1.4rem 1.5rem;
        text-align: center;
        border-bottom: 1px solid #e2e8f0;
        background: white;
    }

    .group-id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        padding: 4px 12px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        font-weight: 900;
        font-size: 0.78rem;
        margin-bottom: 10px;
    }

    .group-name {
        font-size: 1.2rem;
        font-weight: 900;
        margin: 0 0 8px;
        color: #1e3a1e;
    }

    .group-prefix {
        font-size: 1.5rem;
        font-weight: 900;
        color: #047857;
        letter-spacing: 0.08em;
    }

    .group-details {
        padding: 1.4rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .detail-row-group {
        display: flex;
        justify-content: space-between;
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-main);
        padding-bottom: 8px;
        border-bottom: 1px solid var(--bg-color);
    }

    .detail-row-group:last-child { border-bottom: none; }
    .detail-row-group span:first-child { color: var(--text-muted); }

    .status-badge-group {
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 900;
    }

    .status-badge-group.active { background: #DCFCE7; color: #166534; }
    .status-badge-group.suspended { background: #FEE2E2; color: #991B1B; }

    .group-actions-bar {
        padding: 1rem 1.5rem;
        background: #FAFBFC;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 8px;
    }

    .btn-group-op {
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
        background: white;
    }

    .btn-group-op:hover {
        border-color: var(--orange);
        color: var(--orange);
    }

    .btn-group-op.toggle-status.suspended-btn {
        color: #EF4444;
        border-color: #FEE2E2;
        background: #FFF5F5;
    }

    .btn-group-op.toggle-status.activate-btn {
        color: var(--green);
        border-color: #DCFCE7;
        background: #F4FBF7;
    }

    .empty-groups {
        grid-column: 1 / -1;
        padding: 2rem;
        text-align: center;
        color: var(--text-muted);
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="groups-container">
    <div class="control-panel">
        <div class="search-filter-box">
            <input type="text" id="searchInput" class="search-input" placeholder="البحث باسم المجموعة أو البادئة..." onkeyup="filterGroups()">
            <select id="statusFilter" class="filter-select" onchange="filterGroups()">
                <option value="all">كل الحالات</option>
                <option value="active">مجموعات نشطة</option>
                <option value="suspended">مجموعات معطّلة</option>
            </select>
        </div>
        <a href="{{ route('admin.animal-groups.create') }}" class="btn-premium">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            إضافة مجموعة
        </a>
    </div>

    <div class="groups-grid" id="groupsGrid">
        @forelse($groups as $group)
        <div class="group-card-premium {{ $group->is_active ? '' : 'suspended' }}"
             data-name="{{ $group->name }}"
             data-prefix="{{ $group->code_prefix }}"
             data-status="{{ $group->is_active ? 'active' : 'suspended' }}">
            <div class="group-header-gradient">
                <div class="group-id-badge">#{{ $group->id }}</div>
                <h4 class="group-name">{{ $group->name }}</h4>
                <div class="group-prefix">{{ $group->code_prefix }}</div>
            </div>
            <div class="group-details">
                <div class="detail-row-group">
                    <span>بادئة الرقم</span>
                    <span>{{ $group->code_prefix }}###</span>
                </div>
                <div class="detail-row-group">
                    <span>ترتيب العرض</span>
                    <span>{{ $group->sort_order }}</span>
                </div>
                <div class="detail-row-group">
                    <span>الحيوانات المسجّلة</span>
                    <span>{{ $group->registered_animals_count }}</span>
                </div>
                <div class="detail-row-group">
                    <span>الموظفون المرتبطون</span>
                    <span>{{ $group->linked_employees_count }}</span>
                </div>
                <div class="detail-row-group">
                    <span>الحالة</span>
                    <span class="status-badge-group {{ $group->is_active ? 'active' : 'suspended' }}">
                        {{ $group->is_active ? 'نشطة' : 'معطّلة' }}
                    </span>
                </div>
            </div>
            <div class="group-actions-bar">
                <a href="{{ route('admin.animal-groups.edit', $group) }}" class="btn-group-op">تعديل المجموعة</a>
                <form action="{{ route('admin.animal-groups.toggle', $group) }}" method="POST" class="js-group-toggle-form" data-active="{{ $group->is_active ? '1' : '0' }}" style="flex:1;display:flex;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-group-op toggle-status {{ $group->is_active ? 'suspended-btn' : 'activate-btn' }}" style="width:100%;">
                        {{ $group->is_active ? 'تعطيل المجموعة' : 'تفعيل المجموعة' }}
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty-groups">لا توجد مجموعات حيوانية بعد.</div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterGroups() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const cards = document.querySelectorAll('.group-card-premium');

        cards.forEach(card => {
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const prefix = (card.getAttribute('data-prefix') || '').toLowerCase();
            const cardStatus = card.getAttribute('data-status');
            const matchesQuery = !query || name.includes(query) || prefix.includes(query);
            const matchesStatus = status === 'all' || cardStatus === status;
            card.style.display = (matchesQuery && matchesStatus) ? 'flex' : 'none';
        });
    }

    bindAdminConfirmForms('.js-group-toggle-form', (form) => {
        return form.dataset.active === '1'
            ? 'هل تريد تعطيل هذه المجموعة؟ لن تظهر في القوائم الجديدة.'
            : 'هل تريد تفعيل هذه المجموعة؟';
    });
</script>
@endsection
