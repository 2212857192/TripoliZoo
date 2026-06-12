import 'package:tripolizoo/shared/models/user_model.dart';

/// المسار الافتراضي بعد تسجيل الدخول حسب دور المستخدم.
String postLoginRoute(UserModel? user) {
  switch (user?.role) {
    case 'supervisor':
      return '/supervisor/home';
    case 'doctor':
      return '/doctor';
    default:
      return '/home';
  }
}
