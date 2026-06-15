<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز إعادة التعيين — {{ config('tripolizoo.platform_name') }}</title>
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
                            <p style="margin:0 0 8px;font-size:18px;font-weight:800;color:#1E293B;">إعادة تعيين كلمة المرور</p>
                            <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#475569;font-weight:600;">
                                تلقّينا طلباً لإعادة تعيين كلمة المرور لحسابك في <strong>{{ config('tripolizoo.platform_name_ar') }}</strong>.
                                استخدم الرمز التالي لإكمال العملية:
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:12px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:24px;text-align:center;">
                                        <p style="margin:0 0 10px;font-size:14px;color:#64748B;font-weight:600;">رمز التحقق</p>
                                        <p style="margin:0;font-size:32px;font-weight:900;color:#E8651A;letter-spacing:8px;direction:ltr;font-family:monospace;">{{ $code }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 12px;font-size:13px;line-height:1.6;color:#94A3B8;font-weight:600;">
                                صلاحية الرمز: {{ config('tripolizoo.password_reset_code_ttl', 15) }} دقيقة.
                            </p>
                            <p style="margin:0;font-size:13px;line-height:1.6;color:#94A3B8;font-weight:600;">
                                إذا لم تطلب إعادة التعيين، تجاهل هذه الرسالة. لا تشارك الرمز مع أي شخص.
                            </p>
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
