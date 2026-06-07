<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | حديقة حيوان طرابلس</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --green:   #2E7D32;
            --green2:  #388E3C;
            --brown:   #5A2D0C;
            --brown2:  #3B1A06;
            --orange:  #E8651A;
            --orange2: #BF4F10;
            --white:   #FFFFFF;
            --gray-bg: #F8FAFC;
            --gray-border: #E2E8F0;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --ease:    cubic-bezier(0.4, 0, 0.2, 1);
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

        /* ── Right Side: Form ── */
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
            box-shadow: -10px 0 40px rgba(0,0,0,0.05); /* Soft shadow over the image */
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
            transition: color 0.3s var(--ease);
        }

        .back-nav:hover {
            color: var(--orange);
        }

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
            justify-content: center;
            gap: 12px;
            margin-bottom: 2rem;
        }

        .brand h2 {
            font-size: 1.5rem;
            color: var(--brown);
            font-weight: 800;
            letter-spacing: 4px;
            margin: 0;
            text-transform: uppercase;
        }
        .brand h2 span {
            color: var(--green);
            font-weight: 900;
        }

        .brand-logo {
            width: 90px;
            height: 90px;
            border-radius: 24px;
            background: var(--white);
            padding: 6px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            border: 1px solid var(--gray-border);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 14px;
        }



        .form-title {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .form-title p {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.6;
        }

        .input-group {
            margin-bottom: 1.8rem;
        }

        .input-group label {
            display: block;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.6rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            background-color: var(--white);
            border: 2px solid #CBD5E1;
            padding: 1.1rem 1.2rem;
            border-radius: 16px;
            font-family: 'Cairo', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-main);
            transition: all 0.3s var(--ease);
            outline: none;
        }

        .input-wrapper input::placeholder {
            color: #94A3B8;
            font-weight: 400;
        }

        .input-wrapper input:focus {
            background-color: var(--white);
            border-color: var(--green);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.1);
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.95rem;
            user-select: none;
        }

        .remember input {
            width: 20px;
            height: 20px;
            accent-color: var(--green);
            cursor: pointer;
            border-radius: 6px;
        }

        .forgot-link {
            color: var(--orange);
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--orange2);
            text-decoration: underline;
        }

        .btn-primary {
            width: 100%;
            padding: 1.2rem;
            background: var(--green);
            color: var(--white);
            border: none;
            border-radius: 16px;
            font-family: 'Cairo', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(46, 125, 50, 0.3);
            transition: all 0.3s var(--ease);
            position: relative;
        }

        .btn-primary:hover {
            background: var(--green2);
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(46, 125, 50, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary.loading {
            color: transparent;
            pointer-events: none;
        }

        .btn-primary.loading::after {
            content: '';
            position: absolute;
            left: 50%; top: 50%;
            width: 24px; height: 24px;
            margin: -12px 0 0 -12px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* ── Left Side: Image ── */
        .image-section {
            width: 55%;
            height: 100%;
            position: relative;
            background-color: var(--brown);
            overflow: hidden;
        }

        .image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

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

        .image-content {
            max-width: 500px;
            animation: fadeRight 0.8s var(--ease) 0.3s forwards;
            opacity: 0;
            transform: translateX(-30px);
        }

        .badge {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
        }

        .image-content h2 {
            font-size: 3.2rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .image-content p {
            font-size: 1.2rem;
            line-height: 1.8;
            color: rgba(255,255,255,0.9);
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .form-section { width: 100%; }
            .image-section { display: none; }
            .form-container { max-width: 100%; }
        }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeRight {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- الجزء الأيمن: نموذج التسجيل -->
    <div class="form-section">
        <a href="/" class="back-nav">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            العودة للرئيسية
        </a>

        <div class="form-container">
            <div class="brand">
                <div class="brand-logo">
                    <img src="/logo.jpg" alt="الشعار">
                </div>
                <h2>TRIPOLI <span>ZOO</span></h2>
            </div>

            <div class="form-title">
                <p>يرجى إدخال بياناتك للدخول إلى لوحة التحكم الخاصة بك.</p>
            </div>

            <form onsubmit="handleLogin(event)">
                
                <div class="input-group">
                    <label for="email">البريد الإلكتروني</label>
                    <div class="input-wrapper">
                        <!-- تم إضافة القيمة الافتراضية والاتجاه LTR كما طلبت سابقاً -->
                        <input type="email" id="email" value="admin@wejha.com" placeholder="أدخل بريدك الإلكتروني" required dir="ltr" style="text-align: left;">
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" value="12345678" placeholder="أدخل كلمة المرور" required dir="ltr" style="text-align: left;">
                    </div>
                </div>

                <div class="form-row">
                    <label class="remember">
                        <input type="checkbox">
                        <span>البقاء مسجلاً</span>
                    </label>
                    <a href="#" class="forgot-link">نسيت كلمة المرور؟</a>
                </div>

                <button type="submit" class="btn-primary" id="submitBtn">تسجيل الدخول</button>

            </form>
        </div>
    </div>

    <!-- الجزء الأيسر: الصورة المعبرة -->
    <div class="image-section">
        <img src="/zoo_lion.png" alt="أسد حديقة طرابلس">
        <div class="image-overlay">
            <div class="image-content">
                <div class="badge">TRIPOLI ZOO</div>
                <h2>بيئة آمنة<br>ورعاية متكاملة</h2>
                <p>صُمم هذا النظام ليسهل عليك متابعة أدق التفاصيل في الحديقة، من الحالة الصحية للحيوانات وحتى إدارة فرق العمل بكل احترافية وسهولة.</p>
            </div>
        </div>
    </div>

    <script>
        function handleLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            
            setTimeout(() => {
                window.location.href = '/admin/dashboard';
            }, 800);
        }
    </script>
</body>
</html>
