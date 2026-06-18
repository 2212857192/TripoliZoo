<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1e3a1e">
    <title>معلومات الزيارة | حديقة حيوان طرابلس</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --text: #334155;
            --text-muted: #64748b;
            --green: #1b4332;
            --green-dark: #1e3a1e;
            --primary: #2d5a27;
            --accent: #E8651A;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .shell {
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background: var(--bg);
        }

        .hero {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: linear-gradient(135deg, #1e3a1e, #2d5a27, #4a8f40);
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background: url('/zoo_lion.png') center/cover no-repeat;
            opacity: 0.55;
        }

        .hero-shade {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(30,58,30,0.85));
        }

        .back-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 3;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.92);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        }

        .hero-title {
            position: absolute;
            z-index: 2;
            left: 0;
            right: 0;
            bottom: 1rem;
            text-align: center;
            color: #fff;
            font-size: 1.25rem;
            font-weight: 900;
        }

        .content { padding: 1.25rem; }

        .alert-card {
            background: #fef9c3;
            border: 1px solid #fde68a;
            border-radius: 16px;
            padding: 1rem 1.1rem;
            margin-bottom: 1rem;
        }

        .alert-card strong {
            display: block;
            color: #854d0e;
            margin-bottom: 0.35rem;
            font-size: 0.9rem;
        }

        .alert-card p {
            color: #451a03;
            font-size: 0.88rem;
            line-height: 1.7;
            font-weight: 700;
        }

        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .stat-chip {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 20px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
        }

        .stat-chip .label {
            font-size: 0.68rem;
            color: var(--text-muted);
            margin-top: 0.35rem;
        }

        .stat-chip .value {
            font-size: 0.82rem;
            font-weight: 800;
            margin-top: 0.2rem;
        }

        .card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.05rem;
            font-weight: 900;
            color: var(--green-dark);
        }

        .toggle {
            display: flex;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
        }

        .toggle button {
            border: none;
            background: transparent;
            padding: 0.45rem 0.85rem;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.72rem;
            font-weight: 800;
            color: var(--text-muted);
            cursor: pointer;
        }

        .toggle button.active {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .address {
            font-weight: 800;
            margin-bottom: 0.35rem;
        }

        .muted {
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            margin-top: 0.9rem;
            padding: 0.85rem 1rem;
            border-radius: 14px;
            font-family: inherit;
            font-weight: 800;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-outline {
            border: 1.5px solid var(--primary);
            background: #fff;
            color: var(--primary);
        }

        .btn-primary {
            border: none;
            background: var(--primary);
            color: #fff;
        }

        .price-row {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding-bottom: 0.75rem;
        }

        .price-row:last-child { padding-bottom: 0; }

        .price-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #f2f7f2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .price-label { flex: 1; font-weight: 800; }
        .price-value { font-weight: 900; }
        .price-value.free { color: var(--primary); }

        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 0.9rem 0;
        }

        .guideline {
            display: flex;
            gap: 0.65rem;
            margin-bottom: 0.7rem;
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .guideline::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--primary);
            margin-top: 0.55rem;
            flex-shrink: 0;
        }

        .hidden { display: none; }
    </style>
</head>
<body>
@php
    $hours = $visitInfo['hours'] ?? [];
    $status = $visitInfo['status'] ?? [];
@endphp

<div class="shell">
    <header class="hero">
        <div class="hero-bg"></div>
        <div class="hero-shade"></div>
        <a href="/app" class="back-btn" aria-label="رجوع">←</a>
        <h1 class="hero-title">معلومات الزيارة</h1>
    </header>

    <main class="content">
        @if(($status['visible'] ?? false) && !empty($status['text']))
            <div class="alert-card">
                <strong>حالة التشغيل</strong>
                <p>{{ $status['text'] }}</p>
            </div>
        @endif

        @if(!empty($visitInfo['urgent_alert']))
            <div class="alert-card" style="background:#fff7ed;border-color:#fed7aa;">
                <strong style="color:#9a3412;">تنبيه للزوار</strong>
                <p style="color:#7c2d12;">{{ $visitInfo['urgent_alert'] }}</p>
            </div>
        @endif

        <div class="stats">
            <div class="stat-chip">
                <div>🕐</div>
                <div class="label">ساعات العمل</div>
                <div class="value">{{ $hours['working_hours_label'] ?? '—' }}</div>
            </div>
            <div class="stat-chip">
                <div>📅</div>
                <div class="label">أيام العمل</div>
                <div class="value">{{ $hours['working_days_label'] ?? '—' }}</div>
            </div>
        </div>

        @if(!empty($visitInfo['ambulance_phone']) || !empty($visitInfo['security_phone']))
            <section class="card">
                <div class="card-title">📞 أرقام الطوارئ</div>
                @if(!empty($visitInfo['ambulance_phone']))
                    <div class="price-row">
                        <div class="price-icon">🚑</div>
                        <div class="price-label">الإسعاف</div>
                        <a class="price-value" href="tel:{{ $visitInfo['ambulance_phone'] }}">{{ $visitInfo['ambulance_phone'] }}</a>
                    </div>
                @endif
                @if(!empty($visitInfo['security_phone']))
                    <div class="price-row">
                        <div class="price-icon">🛡️</div>
                        <div class="price-label">الأمن</div>
                        <a class="price-value" href="tel:{{ $visitInfo['security_phone'] }}">{{ $visitInfo['security_phone'] }}</a>
                    </div>
                @endif
            </section>
        @endif

        <section class="card">
            <div class="card-title">ℹ️ تعليمات وإرشادات الزيارة</div>
            @forelse($visitInfo['guidelines'] ?? [] as $item)
                <div class="guideline">{{ $item }}</div>
            @empty
                <div class="guideline">يجب الإشراف على الأطفال طوال وقت الزيارة.</div>
                <div class="guideline">الالتزام بالمسارات واللوحات الإرشادية وتعليمات الموظفين.</div>
                <div class="guideline">يمنع إطعام الحيوانات أو الاقتراب من الحواجز.</div>
            @endforelse
            @if(!empty($hours['last_ticket_time_note']))
                <div class="guideline">{{ $hours['last_ticket_time_note'] }}</div>
            @endif
        </section>
    </main>
</div>
</body>
</html>
