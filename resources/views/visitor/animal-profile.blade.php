<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1e3a1e">
    <title>{{ $profile->visitorDisplayName() }} | حديقة حيوان طرابلس</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --text: #334155;
            --green: #1b4332;
            --green-dark: #1e3a1e;
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

        /* ── Hero (matches app detail screen) ── */
        .hero {
            position: relative;
            height: 48vh;
            min-height: 280px;
            max-height: 420px;
            overflow: hidden;
            background: linear-gradient(135deg, #1e3a1e, #2d5a27, #4a8f40);
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
        }

        .hero-shade {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0, 0, 0, 0) 40%,
                rgba(0, 0, 0, 0.15) 70%,
                rgba(0, 0, 0, 0.85) 100%
            );
        }

        .hero-zoo {
            position: absolute;
            top: 1rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 800;
        }

        .hero-content {
            position: absolute;
            z-index: 2;
            right: 20px;
            left: 20px;
            bottom: 40px;
            color: #fff;
            text-align: right;
        }

        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            background: #dc2626;
            color: #fff;
            font-size: 0.68rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .category-badge.birds { background: #01579b; }
        .category-badge.mammals { background: #4527a0; }
        .category-badge.reptiles { background: #2e7d32; }

        .hero-title {
            font-size: clamp(1.75rem, 7vw, 2.15rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 4px;
            text-shadow: 0 2px 16px rgba(0, 0, 0, 0.35);
        }

        .hero-subtitle {
            font-size: 1rem;
            font-weight: 600;
            font-style: italic;
            color: rgba(255, 255, 255, 0.85);
        }

        /* ── Details card ── */
        .details-wrap {
            margin-top: -20px;
            padding: 0 16px 32px;
            position: relative;
            z-index: 3;
        }

        .details-card {
            background: #fff;
            border-radius: 24px;
            border: 1.5px solid #f1f5f9;
            padding: 24px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
        }

        .description {
            font-size: 0.95rem;
            line-height: 1.9;
            font-weight: 700;
            color: #334155;
            text-align: justify;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 20px;
        }

        .meta-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px;
            text-align: center;
        }

        .meta-label {
            font-size: 0.68rem;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 4px;
        }

        .meta-value {
            font-size: 0.82rem;
            font-weight: 900;
            color: #0f172a;
        }

        .location-box {
            margin-top: 20px;
            padding: 14px 16px;
            border-radius: 16px;
            background: linear-gradient(135deg, #fff7ed, #ffedd5);
            border: 1px solid #fed7aa;
        }

        .location-box strong {
            display: block;
            font-size: 0.78rem;
            font-weight: 900;
            color: #9a3412;
            margin-bottom: 4px;
        }

        .location-box span {
            font-size: 0.9rem;
            font-weight: 800;
            color: #c2410c;
        }

        .btn-map {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 16px;
            padding: 15px 16px;
            border: none;
            border-radius: 16px;
            background: #1b4332;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 900;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(27, 67, 50, 0.25);
        }

        .btn-map:active { transform: scale(0.98); }

        .facts {
            margin-top: 20px;
            display: grid;
            gap: 10px;
        }

        .fact-row {
            padding: 12px 14px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
        }

        .fact-row strong {
            display: block;
            font-size: 0.72rem;
            font-weight: 900;
            color: #2d5a27;
            margin-bottom: 4px;
        }

        .fact-row p {
            font-size: 0.86rem;
            font-weight: 700;
            color: #334155;
            line-height: 1.7;
        }

        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-size: 0.72rem;
            font-weight: 800;
            color: #94a3b8;
        }

        .footer-note span { color: var(--green-dark); }
    </style>
</head>
<body>
@php
    $animalName = $profile->visitorDisplayName();
    $subtitle = $profile->visitorSubtitle();
    $code = $profile->display_code ?? $animal->code ?? '—';
    $img = $profile->imageUrl();
    $age = $animal->formattedAge();

    $category = match ($animal->group) {
        'القططية' => ['label' => 'مفترس', 'class' => 'predators'],
        'الطيور' => ['label' => 'طائر', 'class' => 'birds'],
        default => ['label' => 'ثديي', 'class' => 'mammals'],
    };

    $stats = array_values(array_filter([
        ['label' => 'الرمز', 'value' => $code],
        $animal->group ? ['label' => 'المجموعة', 'value' => $animal->group] : null,
        $animal->gender ? ['label' => 'الجنس', 'value' => $animal->gender] : null,
        ($age && $age !== '—') ? ['label' => 'العمر', 'value' => $age] : null,
    ]));

    $facts = array_values(array_filter([
        $animal->origin ? ['label' => 'الأصل الجغرافي', 'value' => $animal->origin] : null,
        $animal->distinguishing_marks ? ['label' => 'علامات مميزة', 'value' => $animal->distinguishing_marks] : null,
        $animal->prior_history ? ['label' => 'تاريخ سابق', 'value' => $animal->prior_history] : null,
    ]));
@endphp

<div class="shell">
    <header class="hero">
        @if($img)
            <div class="hero-bg" style="background-image: url('{{ $img }}');"></div>
        @endif
        <div class="hero-shade"></div>

        <div class="hero-zoo">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            حديقة حيوان طرابلس
        </div>

        <div class="hero-content">
            <span class="category-badge {{ $category['class'] }}">{{ $category['label'] }}</span>
            <h1 class="hero-title">{{ $animalName }}</h1>
            @if($subtitle)
                <p class="hero-subtitle">{{ $subtitle }}</p>
            @endif
        </div>
    </header>

    <main class="details-wrap">
        <div class="details-card">
            @if($profile->description)
                <p class="description">{{ $profile->description }}</p>
            @endif

            @if(count($stats) > 0)
                <div class="meta-grid">
                    @foreach($stats as $stat)
                        <div class="meta-item">
                            <div class="meta-label">{{ $stat['label'] }}</div>
                            <div class="meta-value">{{ $stat['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($mapLocation)
                <div class="location-box">
                    <strong>موقع الحيوان في الحديقة</strong>
                    <span>{{ $mapLocation->name }}</span>
                </div>
                <a href="{{ url('/app/map?focus='.$mapLocation->id) }}" class="btn-map">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4z"/></svg>
                    أظهر الطريق
                </a>
            @endif

            @if(count($facts) > 0)
                <div class="facts">
                    @foreach($facts as $fact)
                        <div class="fact-row">
                            <strong>{{ $fact['label'] }}</strong>
                            <p>{{ $fact['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <p class="footer-note"><span>حديقة حيوان طرابلس</span> — بطاقة تعريف رسمية</p>
    </main>
</div>
</body>
</html>
