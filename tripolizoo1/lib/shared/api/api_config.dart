import 'dart:io' show Platform;
import 'package:flutter/foundation.dart';
import 'package:tripolizoo/shared/api/api_runtime_config.dart';

/// Backend API configuration — [baseUrl] يتكيّف مع بيئة التطوير.
abstract final class ApiConfig {
  static const String login = '/auth/login';
  static const String register = '/auth/register';
  static const String me = '/auth/me';
  static const String logout = '/auth/logout';
  static const String changePassword = '/auth/change-password';
  static const String sendOtp = '/auth/forgot-password';
  static const String verifyOtp = '/auth/verify-otp';
  static const String resetPassword = '/auth/reset-password';
  static const String deviceTokens = '/auth/device-tokens';
  static const String receivingTasks = '/auth/receiving-tasks';
  static const String supervisorDashboard = '/auth/supervisor/dashboard';
  static const String supervisorNotifications =
      '/auth/supervisor/notifications';
  static const String supervisorAnimals = '/auth/supervisor/animals';
  static const String supervisorHealthReports =
      '/auth/supervisor/health-reports';
  static const String supervisorHealthCases = '/auth/supervisor/health-cases';
  static const String supervisorMortalityCases =
      '/auth/supervisor/mortality-cases';
  static const String supervisorOperationalNotes =
      '/auth/supervisor/operational-notes';
  static const String supervisorAnimalMothers =
      '/auth/supervisor/animals/mothers';
  static const String supervisorAnimalNewborns =
      '/auth/supervisor/animals/newborns';
  static const String supervisorBirthRegistrations =
      '/auth/supervisor/birth-registrations';
  static const String doctorDashboard = '/auth/doctor/dashboard';
  static const String doctorNotifications = '/auth/doctor/notifications';
  static const String doctorQuarantines = '/auth/doctor/quarantines';
  static const String doctorHealthReports = '/auth/doctor/health-reports';
  static const String doctorCases = '/auth/doctor/cases';
  static const String doctorFieldCases = '/auth/doctor/field-cases';
  static const String doctorAnimals = '/auth/doctor/animals';
  static const String tickets = '/tickets';
  static const String ticketTypes = '/ticket-types';
  static const String ticketPurchaseCash = '/tickets/purchase/cash';
  static const String ticketPurchaseElectronicVerify =
      '/tickets/purchase/electronic/verify';
  static const String ticketPurchaseElectronicConfirm =
      '/tickets/purchase/electronic/confirm';
  static const String animals = '/animals';
  static const String animalGroups = '/animal-groups';
  static const String map = '/map';
  static const String mapActive = '/map/active';
  static const String calibrationPoints = '/calibration-points';
  static const String visitInfo = '/visit-info';
  static const String profile = '/profile';

  static String get baseUrl {
    const host = String.fromEnvironment(
      'API_HOST',
      defaultValue: '',
    );
    if (host.isNotEmpty) {
      return 'http://$host:8000/api';
    }

    final runtimeHost = ApiRuntimeConfig.host;
    if (runtimeHost != null && runtimeHost.isNotEmpty) {
      return 'http://$runtimeHost:8000/api';
    }

    if (kIsWeb) return 'http://127.0.0.1:8000/api';
    if (Platform.isAndroid) return 'http://10.0.2.2:8000/api';
    return 'http://127.0.0.1:8000/api';
  }

  static String get _deviceApiHost {
    final uri = Uri.parse(baseUrl);
    return uri.host;
  }

  static Uri uri(String path) => Uri.parse('$baseUrl$path');

  /// يحوّل مسار `/storage/...` أو رابط نسبي إلى URL كامل للعرض في التطبيق.
  static String? resolveAssetUrl(String? path) {
    if (path == null || path.isEmpty) return null;

    if (path.startsWith('http://') || path.startsWith('https://')) {
      return _rewriteLocalhostForDevice(path);
    }

    var cleanPath = path;
    if (cleanPath.startsWith('/storage/')) {
      cleanPath = '/api/storage/' + cleanPath.substring(9);
    } else if (cleanPath.startsWith('storage/')) {
      cleanPath = 'api/storage/' + cleanPath.substring(8);
    }

    final origin = uri('').origin;
    final resolved = cleanPath.startsWith('/')
        ? '$origin$cleanPath'
        : '$origin/$cleanPath';

    return _rewriteLocalhostForDevice(resolved);
  }

  static String _rewriteLocalhostForDevice(String url) {
    if (kIsWeb) return url;

    final deviceHost = _deviceApiHost;

    if (Platform.isAndroid && url.contains('127.0.0.1')) {
      return url.replaceFirst('127.0.0.1', deviceHost);
    }

    if (Platform.isAndroid && url.contains('localhost')) {
      return url.replaceFirst('localhost', deviceHost);
    }

    return url;
  }
}
