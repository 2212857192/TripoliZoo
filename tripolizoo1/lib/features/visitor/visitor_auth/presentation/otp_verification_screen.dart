import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/widgets/app_text_field.dart';
import 'package:tripolizoo/shared/widgets/auth_layout.dart';
import 'package:tripolizoo/shared/widgets/primary_button.dart';

class OtpVerificationScreen extends StatefulWidget {
  const OtpVerificationScreen({super.key});

  @override
  State<OtpVerificationScreen> createState() => _OtpVerificationScreenState();
}

class _OtpVerificationScreenState extends State<OtpVerificationScreen> {
  final _formKey = GlobalKey<FormState>();
  final _otpController = TextEditingController();

  @override
  void dispose() {
    _otpController.dispose();
    super.dispose();
  }

  Future<void> _verify() async {
    if (!_formKey.currentState!.validate()) return;
    final auth = context.read<AuthProvider>();
    final ok = await auth.verifyOtp(_otpController.text.trim());
    if (!mounted) return;
    if (ok) {
      context.push('/reset-password');
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(auth.error ?? 'رمز غير صحيح')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    return AuthLayout(
      title: 'رمز التحقق',
      heroTag: 'VERIFY',
      showBackButton: true,
      child: Form(
        key: _formKey,
        child: Column(
          children: [
            Text(
              'ادخل رمز التحقق المرسل إلى\n${auth.pendingEmail ?? "بريدك الإلكتروني"}',
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.bodyMedium,
            ),
            const SizedBox(height: 28),
            AppTextField(
              controller: _otpController,
              label: 'رمز التحقق (OTP)',
              hint: '0000',
              icon: Icons.pin_outlined,
              keyboardType: TextInputType.number,
              textAlign: TextAlign.center,
              validator: (v) {
                if (v == null || v.length < 4) return 'أدخل رمزاً من 4 أرقام';
                return null;
              },
            ),
            const SizedBox(height: 32),
            PrimaryButton(
              label: 'تحقق',
              isLoading: auth.isLoading,
              onPressed: _verify,
            ),
          ],
        ),
      ),
    );
  }
}
