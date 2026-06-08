import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/widgets/app_text_field.dart';
import 'package:tripolizoo/shared/widgets/auth_layout.dart';
import 'package:tripolizoo/shared/widgets/primary_button.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _firstNameController = TextEditingController();
  final _lastNameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmController = TextEditingController();
  bool _obscurePassword = true;
  bool _obscureConfirm = true;

  @override
  void dispose() {
    _firstNameController.dispose();
    _lastNameController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _confirmController.dispose();
    super.dispose();
  }

  Future<void> _register() async {
    if (!_formKey.currentState!.validate()) return;
    final auth = context.read<AuthProvider>();
    final ok = await auth.register(
      firstName: _firstNameController.text.trim(),
      lastName: _lastNameController.text.trim(),
      email: _emailController.text.trim(),
      password: _passwordController.text,
    );
    if (!mounted) return;
    if (ok) {
      context.go('/home');
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(auth.error ?? 'فشل إنشاء الحساب')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();

    return AuthLayout(
      title: 'إنشاء حساب',
      heroTag: 'REGISTER',
      showBackButton: true,
      child: Form(
        key: _formKey,
        child: Column(
          children: [
            AppTextField(
              controller: _firstNameController,
              label: 'الاسم الأول',
              hint: 'ادخل الاسم الأول',
              icon: Icons.person_outline,
              validator: (v) =>
                  v == null || v.isEmpty ? 'أدخل الاسم الأول' : null,
            ),
            const SizedBox(height: 16),
            AppTextField(
              controller: _lastNameController,
              label: 'الاسم الثاني',
              hint: 'ادخل الاسم الثاني',
              icon: Icons.person_outline,
              validator: (v) =>
                  v == null || v.isEmpty ? 'أدخل الاسم الثاني' : null,
            ),
            const SizedBox(height: 16),
            AppTextField(
              controller: _emailController,
              label: 'البريد الإلكتروني',
              hint: 'example@gmail.com',
              icon: Icons.email_outlined,
              keyboardType: TextInputType.emailAddress,
              validator: (v) {
                if (v == null || v.isEmpty) return 'أدخل البريد';
                if (!v.contains('@')) return 'بريد غير صالح';
                return null;
              },
            ),
            const SizedBox(height: 16),
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
            const SizedBox(height: 16),
            AppTextField(
              controller: _confirmController,
              label: 'تأكيد كلمة المرور',
              hint: '••••••••',
              icon: Icons.lock_outline,
              obscureText: _obscureConfirm,
              onToggleVisibility: () =>
                  setState(() => _obscureConfirm = !_obscureConfirm),
              validator: (v) {
                if (v != _passwordController.text) {
                  return 'كلمتا المرور غير متطابقتين';
                }
                return null;
              },
            ),
            const SizedBox(height: 28),
            PrimaryButton(
              label: 'إنشاء حساب',
              isLoading: auth.isLoading,
              onPressed: _register,
            ),
            const SizedBox(height: 16),
            TextButton(
              onPressed: () => context.pop(),
              child: const Text('لديك حساب؟ تسجيل الدخول'),
            ),
          ],
        ),
      ),
    );
  }
}
