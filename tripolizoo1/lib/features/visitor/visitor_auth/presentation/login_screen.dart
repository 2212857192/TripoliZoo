import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/widgets/app_text_field.dart';
import 'package:tripolizoo/shared/widgets/auth_layout.dart';
import 'package:tripolizoo/shared/widgets/primary_button.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (!_formKey.currentState!.validate()) return;
    final auth = context.read<AuthProvider>();
    final ok = await auth.login(
      _emailController.text.trim(),
      _passwordController.text,
    );
    if (!mounted) return;
    if (ok) {
      context.go('/home');
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(auth.error ?? 'فشل تسجيل الدخول')),
      );
    }
  }

  Future<void> _guestLogin() async {
    final auth = context.read<AuthProvider>();
    await auth.guestLogin();
    if (mounted) context.go('/home');
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    return AuthLayout(
      title: 'تسجيل الدخول',
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            AppTextField(
              controller: _emailController,
              label: 'البريد الإلكتروني',
              hint: 'example@gmail.com',
              icon: Icons.email_outlined,
              keyboardType: TextInputType.emailAddress,
              validator: (v) {
                if (v == null || v.isEmpty) return 'أدخل البريد الإلكتروني';
                if (!v.contains('@')) return 'بريد غير صالح';
                return null;
              },
            ),
            const SizedBox(height: 20),
            AppTextField(
              controller: _passwordController,
              label: 'كلمة المرور',
              hint: '••••••••',
              icon: Icons.lock_outline,
              obscureText: _obscurePassword,
              onToggleVisibility: () =>
                  setState(() => _obscurePassword = !_obscurePassword),
              validator: (v) {
                if (v == null || v.length < AppConstants.minPasswordLength) {
                  return '6 أحرف على الأقل';
                }
                return null;
              },
            ),
            const SizedBox(height: 12),
            Align(
              alignment: Alignment.centerLeft,
              child: TextButton(
                onPressed: () => context.push('/forgot-password'),
                child: const Text('هل نسيت كلمة المرور؟'),
              ),
            ),
            const SizedBox(height: 24),
            PrimaryButton(
              label: 'تسجيل الدخول',
              isLoading: auth.isLoading,
              onPressed: _login,
            ),
            const SizedBox(height: 20),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                TextButton(
                  onPressed: () => context.push('/register'),
                  child: const Text('إنشاء حساب'),
                ),
                const Text('مستخدم جديد؟', style: TextStyle(color: Colors.grey)),
              ],
            ),
            const SizedBox(height: 16),
            PrimaryButton(
              label: 'دخول كزائر',
              outlined: true,
              onPressed: auth.isLoading ? null : _guestLogin,
            ),
          ],
        ),
      ),
    );
  }
}
