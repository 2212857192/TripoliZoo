<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'تسجيل الدخول | حديقة حيوان طرابلس')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #2E7D32;
            --green2: #388E3C;
            --brown: #5A2D0C;
            --orange: #E8651A;
            --orange2: #BF4F10;
            --white: #FFFFFF;
            --gray-border: #E2E8F0;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --ease: cubic-bezier(0.4, 0, 0.2, 1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--white);
            color: var(--text-main);
            height: 100vh;
            overflow: hidden;
            display: flex;
        }
        .form-section {
            width: 45%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            background-color: var(--white);
            z-index: 10;
            padding: 2rem;
            box-shadow: -10px 0 40px rgba(0,0,0,0.05);
        }
        .back-nav {
            position: absolute;
            top: 2.5rem;
            right: 3rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .back-nav:hover { color: var(--orange); }
        .form-container {
            width: 100%;
            max-width: 420px;
            animation: fadeUp 0.6s var(--ease) forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-bottom: 2rem;
        }
        .brand h2 {
            font-size: 1.5rem;
            color: var(--brown);
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .brand h2 span { color: var(--green); }
        .brand-logo {
            width: 90px;
            height: 90px;
            border-radius: 24px;
            padding: 6px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            border: 1px solid var(--gray-border);
        }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; border-radius: 14px; }
        .form-title { margin-bottom: 2rem; text-align: center; }
        .form-title h3 { font-size: 1.35rem; font-weight: 800; margin-bottom: 0.5rem; }
        .form-title p { color: var(--text-muted); font-size: 1rem; line-height: 1.6; }
        .input-group { margin-bottom: 1.5rem; }
        .input-group label {
            display: block;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.6rem;
        }
        .input-wrapper input {
            width: 100%;
            border: 2px solid #CBD5E1;
            padding: 1.1rem 1.2rem;
            border-radius: 16px;
            font-family: 'Cairo', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            outline: none;
        }
        .input-wrapper input:focus {
            border-color: var(--green);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.1);
        }
        .btn-primary {
            width: 100%;
            padding: 1.2rem;
            background: var(--green);
            color: var(--white);
            border: none;
            border-radius: 16px;
            font-family: 'Cairo', sans-serif;
            font-size: 1.05rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(46, 125, 50, 0.3);
        }
        .btn-primary:hover { background: var(--green2); }
        .btn-link {
            display: inline-block;
            margin-top: 1rem;
            color: var(--orange);
            font-weight: 700;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .btn-link:hover { text-decoration: underline; }
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 1.2rem;
            font-weight: 700;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .alert-success { background: #F0FDF4; border: 1px solid #86EFAC; color: #166534; }
        .alert-error { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
        .code-input {
            letter-spacing: 8px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 900;
            direction: ltr;
        }
        .image-section {
            width: 55%;
            height: 100%;
            position: relative;
            background-color: var(--brown);
            overflow: hidden;
        }
        .image-section img { width: 100%; height: 100%; object-fit: cover; }
        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(90, 45, 12, 0.85) 0%, rgba(46, 125, 50, 0.4) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 6rem;
            color: var(--white);
        }
        .image-content { max-width: 500px; }
        .image-content h2 { font-size: 3rem; font-weight: 900; line-height: 1.1; margin-bottom: 1rem; }
        .image-content p { font-size: 1.1rem; line-height: 1.8; color: rgba(255,255,255,0.9); font-weight: 600; }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 900px) {
            .image-section { display: none; }
            .form-section { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="form-section">
        @yield('back_link')
        <div class="form-container">
            <div class="brand">
                <div class="brand-logo"><img src="/logo.jpg" alt="الشعار"></div>
                <h2>TRIPOLI <span>ZOO</span></h2>
            </div>
            @yield('content')
        </div>
    </div>
    <div class="image-section">
        <img src="/zoo_lion.png" alt="أسد حديقة طرابلس">
        <div class="image-overlay">
            <div class="image-content">
                <h2>
                    @hasSection('hero_title')
                        {!! $__env->yieldContent('hero_title') !!}
                    @else
                        استعادة<br>الوصول
                    @endif
                </h2>
                <p>@yield('hero_text', 'أدخل بريدك الإلكتروني وسنرسل لك رمزاً آمناً لإعادة تعيين كلمة المرور.')</p>
            </div>
        </div>
    </div>
</body>
</html>
