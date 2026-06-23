/// Pure helpers for push notification routing and background display rules.
abstract final class PushNotificationLogic {
  /// In background/killed state, FCM shows [notification] payloads automatically.
  /// We only need a local notification for data-only messages.
  static bool shouldShowLocalInBackground({
    required bool hasNotificationPayload,
  }) {
    return !hasNotificationPayload;
  }

  static String? routeFromData(Map<String, dynamic> data) {
    final route = data['route'];
    if (route is String && route.isNotEmpty) return route;
    return null;
  }

  static String resolveNotificationBody({
    String? notificationBody,
    Map<String, dynamic> data = const {},
    String fallback = 'إشعار جديد من حديقة طرابلس',
  }) {
    final dataBody = data['body'];
    if (notificationBody != null && notificationBody.isNotEmpty) {
      return notificationBody;
    }
    if (dataBody is String && dataBody.isNotEmpty) {
      return dataBody;
    }
    return fallback;
  }
}
