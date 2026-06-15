import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';

/// شراء التذاكر متاح فقط لزائر مسجّل الدخول (حساب حقيقي، ليس زائراً ضيفاً).
bool canPurchaseTickets(AuthProvider auth) {
  final user = auth.user;
  if (user == null || user.isGuest) return false;
  return user.role == 'visitor';
}

void openTickets(BuildContext context) {
  context.go('/tickets');
}

void promptLoginForPurchase(BuildContext context) {
  ScaffoldMessenger.of(context).showSnackBar(
    const SnackBar(
      content: Text('يجب تسجيل الدخول أو إنشاء حساب لشراء التذاكر'),
    ),
  );
  context.go('/login');
}
