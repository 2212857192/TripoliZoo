import 'dart:io' show Platform;
import 'package:flutter/foundation.dart';

/// Backend API configuration — [baseUrl] يتكيّف مع بيئة التطوير.
abstract final class ApiConfig {
  static const String login = '/auth/login';
  static const String register = '/auth/register';
  static const String me = '/auth/me';
  static const String logout = '/auth/logout';
  static const String sendOtp = '/auth/forgot-password';
  static const String verifyOtp = '/auth/verify-otp';
  static const String resetPassword = '/auth/reset-password';
  static const String tickets = '/tickets';
  static const String animals = '/animals';
  static const String profile = '/profile';

  static String get baseUrl {
    if (kIsWeb) return 'http://127.0.0.1:8000/api';
    if (Platform.isAndroid) return 'http://10.0.2.2:8000/api';
    return 'http://127.0.0.1:8000/api';
  }

  static Uri uri(String path) => Uri.parse('$baseUrl$path');
}
