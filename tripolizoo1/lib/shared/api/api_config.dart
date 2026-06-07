/// Backend API configuration — update [baseUrl] when connecting to server.
abstract final class ApiConfig {
  static const String baseUrl = 'https://api.tripolizoo.ly/v1';

  static const String login = '/auth/login';
  static const String register = '/auth/register';
  static const String sendOtp = '/auth/forgot-password';
  static const String verifyOtp = '/auth/verify-otp';
  static const String resetPassword = '/auth/reset-password';
  static const String tickets = '/tickets';
  static const String animals = '/animals';
  static const String profile = '/profile';
}
