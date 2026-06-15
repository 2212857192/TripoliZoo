import 'package:flutter/foundation.dart';
import 'package:tripolizoo/shared/models/user_model.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/data/api_auth_service.dart';

class AuthProvider extends ChangeNotifier {
  AuthProvider({AuthService? authService})
      : _authService = authService ?? ApiAuthService();

  final AuthService _authService;

  UserModel? _user;
  bool _isLoading = false;
  bool _bootstrapped = false;
  String? _error;
  String? _resetToken;
  String? _pendingEmail;

  UserModel? get user => _user;
  bool get isLoading => _isLoading;
  bool get bootstrapped => _bootstrapped;
  String? get error => _error;
  bool get isAuthenticated => _user != null && !(_user?.isGuest ?? true);
  bool get hasSession => _user != null;
  bool get hasAccount => _user != null && !_user!.isGuest;
  bool get isGuest => _user?.isGuest ?? false;

  /// زائر مسجّل — يمكنه شراء التذاكر.
  bool get canPurchaseTickets {
    final current = _user;
    if (current == null || current.isGuest) return false;
    return current.role == 'visitor';
  }

  String? get resetToken => _resetToken;
  String? get pendingEmail => _pendingEmail;

  Future<void> bootstrap() async {
    if (_bootstrapped) return;
    _isLoading = true;
    notifyListeners();
    try {
      _user = await _authService.restoreSession();
    } catch (_) {
      _user = null;
    } finally {
      _isLoading = false;
      _bootstrapped = true;
      notifyListeners();
    }
  }

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
      _user = UserModel.guest();
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

  void updateProfile({String? name, String? email, String? phone}) {
    if (_user == null || _user!.isGuest) return;
    _user = _user!.copyWith(name: name, email: email, phone: phone);
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

  Future<void> logout() async {
    if (_user != null && !_user!.isGuest) {
      await _authService.logout();
    }
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
