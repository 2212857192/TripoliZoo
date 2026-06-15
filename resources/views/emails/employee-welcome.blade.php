<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحباً بك — {{ config('tripolizoo.platform_name') }}</title>
</head>
<body style="margin:0;padding:0;background:#F1F5F9;font-family:'Segoe UI',Tahoma,Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F1F5F9;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#FFFFFF;border-radius:16px;overflow:hidden;border:1px solid #E2E8F0;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#1e3a1e,#2d5a27);padding:28px 32px;text-align:center;">
                            <p style="margin:0 0 6px;font-size:13px;font-weight:700;color:rgba(255,255,255,0.85);letter-spacing:1px;">{{ strtoupper(config('tripolizoo.platform_name')) }}</p>
                            <h1 style="margin:0;font-size:22px;font-weight:800;color:#FFFFFF;">{{ config('tripolizoo.platform_name_ar') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 8px;font-size:18px;font-weight:800;color:#1E293B;">أهلاً {{ $employee->name }}،</p>
                            <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#475569;font-weight:600;">
                                نرحّب بك في فريق <strong>{{ config('tripolizoo.platform_name_ar') }}</strong>.
                                تم إنشاء حسابك بنجاح بصفة <strong>{{ $employee->role }}</strong>.
                                @if($employee->assigned_group)
                                    <br>المجموعة المسندة: <strong>{{ $employee->assigned_group }}</strong>.
                                @endif
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:12px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:20px 24px;">
                                        <p style="margin:0 0 14px;font-size:14px;font-weight:800;color:#1e3a1e;">بيانات تسجيل الدخول</p>
                                        <p style="margin:0 0 10px;font-size:14px;color:#64748B;font-weight:600;">البريد الإلكتروني</p>
                                        <p style="margin:0 0 16px;font-size:15px;font-weight:800;color:#1E293B;direction:ltr;text-align:right;">{{ $employee->email }}</p>
                                        <p style="margin:0 0 10px;font-size:14px;color:#64748B;font-weight:600;">كلمة المرور المؤقتة</p>
                                        <p style="margin:0;font-size:16px;font-weight:900;color:#E8651A;letter-spacing:2px;direction:ltr;text-align:right;font-family:monospace;">{{ $plainPassword }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 24px;font-size:13px;line-height:1.6;color:#94A3B8;font-weight:600;">
                                لأسباب أمنية، يُفضّل تغيير كلمة المرور بعد أول تسجيل دخول. لا تشارك هذه البيانات مع أي شخص.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" align="center">
                                <tr>
                                    <td style="border-radius:10px;background:#2E7D32;">
                                        <a href="{{ url(route('login')) }}" target="_blank" style="display:inline-block;padding:14px 32px;font-size:15px;font-weight:800;color:#FFFFFF;text-decoration:none;">
                                            الدخول إلى المنصة
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px;background:#FAFBFC;border-top:1px solid #E2E8F0;text-align:center;">
                            <p style="margin:0;font-size:12px;color:#94A3B8;font-weight:600;">
                                {{ config('tripolizoo.platform_name') }} — رسالة آلية، يرجى عدم الرد عليها.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
