import 'package:tripolizoo/shared/models/user_model.dart';

/// Contract for authentication — swap [MockAuthService] with [ApiAuthService] for production.
abstract class AuthService {
  Future<UserModel> login({required String email, required String password});
  Future<UserModel> register({
    required String firstName,
    required String lastName,
    required String email,
    required String password,
  });
  Future<void> sendOtp({required String email});
  Future<String> verifyOtp({required String email, required String code});
  Future<void> resetPassword({
    required String resetToken,
    required String newPassword,
  });
  Future<UserModel> guestLogin();
}

class AuthException implements Exception {
  final String message;
  const AuthException(this.message);

  @override
  String toString() => message;
}
