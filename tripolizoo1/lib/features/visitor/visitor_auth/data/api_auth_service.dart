import 'package:tripolizoo/features/visitor/visitor_auth/data/auth_service.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';
import 'package:tripolizoo/shared/models/user_model.dart';

class ApiAuthService implements AuthService {
  ApiAuthService({ApiClient? client}) : _client = client ?? ApiClient();

  final ApiClient _client;

  @override
  Future<UserModel?> restoreSession() async {
    try {
      final data = await _client.get(ApiConfig.me);
      final userJson = data['user'];
      if (userJson is! Map<String, dynamic>) return null;
      return UserModel.fromJson(userJson);
    } on AuthException catch (e) {
      if (e.message.contains('تسجيل الدخول') ||
          e.message.contains('الجلسة') ||
          e.message.contains('صلاحية')) {
        await _client.clearToken();
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  @override
  Future<UserModel> login({
    required String email,
    required String password,
  }) async {
    final data = await _client.post(
      ApiConfig.login,
      auth: false,
      body: {
        'email': email.trim().toLowerCase(),
        'password': password,
      },
    );
    return _sessionFromResponse(data);
  }

  @override
  Future<UserModel> register({
    required String firstName,
    required String lastName,
    required String email,
    required String password,
  }) async {
    final data = await _client.post(
      ApiConfig.register,
      auth: false,
      body: {
        'first_name': firstName.trim(),
        'last_name': lastName.trim(),
        'email': email.trim().toLowerCase(),
        'password': password,
        'password_confirmation': password,
      },
    );
    return _sessionFromResponse(data);
  }

  @override
  Future<void> sendOtp({required String email}) async {
    await _client.post(
      ApiConfig.sendOtp,
      auth: false,
      body: {'email': email.trim().toLowerCase()},
    );
  }

  @override
  Future<String> verifyOtp({
    required String email,
    required String code,
  }) async {
    final data = await _client.post(
      ApiConfig.verifyOtp,
      auth: false,
      body: {
        'email': email.trim().toLowerCase(),
        'code': code.trim(),
      },
    );
    final token = data['reset_token'];
    if (token is! String || token.isEmpty) {
      throw const AuthException('رمز التحقق غير صحيح أو منتهي الصلاحية');
    }
    return token;
  }

  @override
  Future<void> resetPassword({
    required String resetToken,
    required String newPassword,
  }) async {
    await _client.post(
      ApiConfig.resetPassword,
      auth: false,
      body: {
        'reset_token': resetToken,
        'password': newPassword,
      },
    );
  }

  @override
  Future<UserModel> guestLogin() async {
    throw const AuthException('يجب إنشاء حساب أو تسجيل الدخول لاستخدام التطبيق');
  }

  @override
  Future<void> logout() async {
    try {
      await _client.post(ApiConfig.logout);
    } catch (_) {
      // تجاهل — سنمسح التوكن محلياً في كل الأحوال.
    } finally {
      await _client.clearToken();
    }
  }

  Future<UserModel> _sessionFromResponse(Map<String, dynamic> data) async {
    final token = data['token'];
    final userJson = data['user'];
    if (token is! String || token.isEmpty) {
      throw const AuthException('استجابة غير صالحة من الخادم');
    }
    if (userJson is! Map<String, dynamic>) {
      throw const AuthException('استجابة غير صالحة من الخادم');
    }

    await _client.saveToken(token);
    return UserModel.fromJson(userJson);
  }
}
