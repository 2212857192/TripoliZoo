import 'package:flutter/foundation.dart';
import 'package:tripolizoo/shared/models/user_model.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/mock_auth_service.dart';

class AuthProvider extends ChangeNotifier {
  AuthProvider({AuthService? authService})
      : _authService = authService ?? MockAuthService();

  final AuthService _authService;

  UserModel? _user;
  bool _isLoading = false;
  String? _error;
  String? _resetToken;
  String? _pendingEmail;

  UserModel? get user => _user;
  bool get isLoading => _isLoading;
  String? get error => _error;
  bool get isAuthenticated => _user != null;
  bool get isGuest => _user?.isGuest ?? false;
  String? get resetToken => _resetToken;
  String? get pendingEmail => _pendingEmail;

  void clearError() {
    _error = null;
    notifyListeners();
  }

  Future<bool> login(String email, String password) async {
    return _run(() async {
      _user = await _authService.login(email: email, password: password);
    });
  }

  Future<bool> register({
    required String firstName,
    required String lastName,
    required String email,
    required String password,
  }) async {
    return _run(() async {
      _user = await _authService.register(
        firstName: firstName,
        lastName: lastName,
        email: email,
        password: password,
      );
    });
  }

  Future<bool> guestLogin() async {
    return _run(() async {
      _user = await _authService.guestLogin();
    });
  }

  Future<bool> sendOtp(String email) async {
    return _run(() async {
      await _authService.sendOtp(email: email);
      _pendingEmail = email;
    });
  }

  Future<bool> verifyOtp(String code) async {
    if (_pendingEmail == null) {
      _error = 'البريد الإلكتروني غير محدد';
      notifyListeners();
      return false;
    }
    return _run(() async {
      _resetToken = await _authService.verifyOtp(
        email: _pendingEmail!,
        code: code,
      );
    });
  }

  Future<bool> resetPassword(String newPassword) async {
    if (_resetToken == null) {
      _error = 'رمز إعادة التعيين غير صالح';
      notifyListeners();
      return false;
    }
    return _run(() async {
      await _authService.resetPassword(
        resetToken: _resetToken!,
        newPassword: newPassword,
      );
      _resetToken = null;
      _pendingEmail = null;
    });
  }

  void updateProfile({String? name, String? phone}) {
    if (_user == null || _user!.isGuest) return;
    _user = _user!.copyWith(name: name, phone: phone);
    notifyListeners();
  }

  Future<bool> changePassword({
    required String currentPassword,
    required String newPassword,
  }) async {
    if (_user == null || _user!.isGuest) {
      _error = 'يجب تسجيل الدخول أولاً';
      notifyListeners();
      return false;
    }
    return _run(() async {
      if (currentPassword.length < 6) {
        throw const AuthException('كلمة المرور الحالية غير صحيحة');
      }
      if (newPassword.length < 8) {
        throw const AuthException('كلمة المرور الجديدة ضعيفة');
      }
      // Mock — يُستبدل بطلب API لاحقاً.
    });
  }

  void logout() {
    _user = null;
    _resetToken = null;
    _pendingEmail = null;
    _error = null;
    notifyListeners();
  }

  Future<bool> _run(Future<void> Function() action) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      await action();
      _isLoading = false;
      notifyListeners();
      return true;
    } on AuthException catch (e) {
      _error = e.message;
      _isLoading = false;
      notifyListeners();
      return false;
    } catch (e) {
      _error = 'حدث خطأ غير متوقع، حاول مرة أخرى';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }
}
