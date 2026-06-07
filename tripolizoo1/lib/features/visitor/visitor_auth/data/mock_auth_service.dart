import 'package:tripolizoo/shared/constants/app_constants.dart';
import 'package:tripolizoo/shared/models/user_model.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';

/// Mock implementation — replace with HTTP calls to [ApiConfig] endpoints.
class MockAuthService implements AuthService {
  String? _pendingOtpEmail;
  String? _resetToken;

  @override
  Future<UserModel> login({
    required String email,
    required String password,
  }) async {
    await Future.delayed(const Duration(milliseconds: 800));
    _validateEmail(email);
    if (password.length < AppConstants.minPasswordLength) {
      throw const AuthException('كلمة المرور يجب أن تكون 6 أحرف على الأقل');
    }
    return UserModel(
      id: 'user_${email.hashCode}',
      name: email.split('@').first,
      email: email,
      phone: '+218 91 000 0000',
    );
  }

  @override
  Future<UserModel> register({
    required String firstName,
    required String lastName,
    required String email,
    required String password,
  }) async {
    await Future.delayed(const Duration(milliseconds: 900));
    _validateEmail(email);
    if (password.length < AppConstants.minPasswordLength) {
      throw const AuthException('كلمة المرور يجب أن تكون 6 أحرف على الأقل');
    }
    return UserModel(
      id: 'user_${DateTime.now().millisecondsSinceEpoch}',
      name: '$firstName $lastName',
      email: email,
      phone: '+218 91 000 0000',
    );
  }

  @override
  Future<void> sendOtp({required String email}) async {
    await Future.delayed(const Duration(milliseconds: 600));
    _validateEmail(email);
    _pendingOtpEmail = email;
  }

  @override
  Future<String> verifyOtp({
    required String email,
    required String code,
  }) async {
    await Future.delayed(const Duration(milliseconds: 500));
    if (code.length < 4) {
      throw const AuthException('رمز التحقق غير صحيح');
    }
    if (_pendingOtpEmail != email) {
      throw const AuthException('البريد الإلكتروني غير مطابق');
    }
    _resetToken = 'reset_${DateTime.now().millisecondsSinceEpoch}';
    return _resetToken!;
  }

  @override
  Future<void> resetPassword({
    required String resetToken,
    required String newPassword,
  }) async {
    await Future.delayed(const Duration(milliseconds: 600));
    if (_resetToken != resetToken) {
      throw const AuthException('رمز إعادة التعيين غير صالح');
    }
    if (newPassword.length < AppConstants.minPasswordLength) {
      throw const AuthException('كلمة المرور يجب أن تكون 6 أحرف على الأقل');
    }
    _resetToken = null;
    _pendingOtpEmail = null;
  }

  @override
  Future<UserModel> guestLogin() async {
    await Future.delayed(const Duration(milliseconds: 300));
    return UserModel.guest();
  }

  void _validateEmail(String email) {
    final regex = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$');
    if (!regex.hasMatch(email)) {
      throw const AuthException('البريد الإلكتروني غير صالح');
    }
  }
}
