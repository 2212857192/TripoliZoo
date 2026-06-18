import 'package:flutter/foundation.dart';

/// رسائل تتبع API — تظهر في console عند التطوير فقط.
abstract final class ApiDebugLog {
  static void info(String tag, String message) {
    if (kDebugMode) {
      debugPrint('[$tag] $message');
    }
  }

  static void error(String tag, String message, [Object? error]) {
    if (kDebugMode) {
      debugPrint('[$tag] ERROR: $message');
      if (error != null) {
        debugPrint('[$tag] $error');
      }
    }
  }
}
