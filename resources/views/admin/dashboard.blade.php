@extends('admin.layout')
@section('title', 'لوحة التحكم | Tripoli Zoo')
@section('page_title', 'لوحة التحكم')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--white);
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.3s var(--ease), box-shadow 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.04);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-icon.green { background: var(--green-light); color: var(--green); }
    .stat-icon.orange { background: #FFEDD5; color: var(--orange); }
    .stat-icon.blue { background: #E0F2FE; color: #0284C7; }

    .stat-info h3 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        margin-bottom: 0.3rem;
    }

    .stat-info p {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 900px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .activity-list, .alert-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--bg-color);
    }
    .activity-item:last-child { border-bottom: none; padding-bottom: 0; }

    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .activity-icon.blue { background: #3B82F6; }
    .activity-icon.green { background: #10B981; }
    .activity-icon.orange { background: #F59E0B; }

    .activity-content h4 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 2px;
    }
    .activity-content p {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .alert-item {
        padding: 1rem;
        border-radius: 12px;
        border-right: 4px solid;
        background: var(--bg-color);
    }
    .alert-item.warning { border-color: #F59E0B; background: #FFFBEB; }
    .alert-item.danger { border-color: #EF4444; background: #FEF2F2; }

    .alert-item h4 {
        font-size: 0.9rem;
        font-weight: 800;
        margin-bottom: 4px;
        color: var(--text-main);
    }
    .alert-item p {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
    }
    
    @media (max-width: 900px) {
        .bottom-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div class="stat-info">
            <h3>45</h3>
            <p>إجمالي الموظفين</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 12H16c-.7 2-2 3-4 3s-3.3-1-4-3H2.5"/><path d="M5.5 5.1L2 12v6c0 1.1.9 2 2 2h16a2 2 0 002-2v-6l-3.4-6.9A2 2 0 0017 5H7a2 2 0 00-1.5.1z"/></svg>
        </div>
        <div class="stat-info">
            <h3>214</h3>
            <p>إجمالي الحيوانات</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </div>
        <div class="stat-info">
            <h3>1,240</h3>
            <p>تذاكر اليوم</p>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">إحصائيات التذاكر (هذا الأسبوع)</h2>
        </div>
        <div style="position: relative; height: 250px;">
            <canvas id="ticketsChart"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">توزيع الحيوانات حسب الفصيلة</h2>
        </div>
        <div style="position: relative; height: 250px;">
            <canvas id="animalsChart"></canvas>
        </div>
    </div>
</div>

<div class="bottom-grid">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">أحدث الأنشطة</h2>
        </div>
        <div class="activity-list">
            <div class="activity-item">
                <div class="activity-icon blue"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></div>
                <div class="activity-content">
                    <h4>تم حجز 4 تذاكر عائلية</h4>
                    <p>منذ 5 دقائق بواسطة سعاد مسعود</p>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>
                <div class="activity-content">
                    <h4>تحديث السجل الطبي للأسد الإفريقي</h4>
                    <p>منذ 45 دقيقة بواسطة د. أحمد سالم</p>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon orange"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg></div>
                <div class="activity-content">
                    <h4>تسجيل موظف جديد</h4>
                    <p>منذ ساعتين بواسطة مدير النظام</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">تنبيهات النظام</h2>
        </div>
        <div class="alert-list">
            <div class="alert-item warning">
                <h4 style="color: #92400E;">موعد التطعيم الدوري</h4>
                <p style="color: #B45309;">الفيل الآسيوي (E-04) يحتاج جرعة التطعيم الدورية الخاصة به غداً في تمام الساعة 10 صباحاً.</p>
            </div>
            <div class="alert-item danger">
                <h4 style="color: #991B1B;">نقص في المخزون</h4>
                <p style="color: #B91C1C;">مخزون اللحوم الحمراء للأسود أوشك على النفاذ (يكفي ليومين فقط). الرجاء تقديم طلب توريد عاجل.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Tickets Line Chart
    const ctx1 = document.getElementById('ticketsChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
            datasets: [{
                label: 'مبيعات التذاكر',
                data: [1200, 1900, 800, 1500, 2000, 3200, 4500],
                borderColor: '#2E7D32',
                backgroundColor: 'rgba(46, 125, 50, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: { titleFont: { family: 'Cairo' }, bodyFont: { family: 'Cairo' } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { font: { family: 'Cairo' } } },
                x: { ticks: { font: { family: 'Cairo' } } }
            }
        }
    });

    // Animals Doughnut Chart
    const ctx2 = document.getElementById('animalsChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['الثدييات', 'الطيور', 'الزواحف', 'البرمائيات'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: ['#E8651A', '#5A2D0C', '#2E7D32', '#3B82F6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Cairo', size: 13 } } },
                tooltip: { titleFont: { family: 'Cairo' }, bodyFont: { family: 'Cairo' } }
            }
        }
    });
</script>
@endsection
