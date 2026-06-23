import 'package:flutter_test/flutter_test.dart';
import 'package:tripolizoo/shared/push/push_notification_logic.dart';

void main() {
  group('PushNotificationLogic background display', () {
    test('server notification payload is shown by OS in background', () {
      expect(
        PushNotificationLogic.shouldShowLocalInBackground(
          hasNotificationPayload: true,
        ),
        isFalse,
      );
    });

    test('data-only payload needs local notification in background', () {
      expect(
        PushNotificationLogic.shouldShowLocalInBackground(
          hasNotificationPayload: false,
        ),
        isTrue,
      );
    });
  });

  group('PushNotificationLogic routing', () {
    test('extracts doctor quarantine route', () {
      expect(
        PushNotificationLogic.routeFromData({
          'route': '/doctor/quarantine/Q-2024-001',
          'click_action': 'FLUTTER_NOTIFICATION_CLICK',
        }),
        '/doctor/quarantine/Q-2024-001',
      );
    });

    test('extracts supervisor home route', () {
      expect(
        PushNotificationLogic.routeFromData({'route': '/supervisor/home'}),
        '/supervisor/home',
      );
    });

    test('extracts supervisor tasks route with query', () {
      expect(
        PushNotificationLogic.routeFromData({
          'route': '/supervisor/receiving-tasks?filter=pending',
        }),
        '/supervisor/receiving-tasks?filter=pending',
      );
    });

    test('returns null when route missing', () {
      expect(PushNotificationLogic.routeFromData({}), isNull);
      expect(PushNotificationLogic.routeFromData({'route': ''}), isNull);
    });
  });

  group('PushNotificationLogic body text', () {
    test('prefers notification body over data body', () {
      expect(
        PushNotificationLogic.resolveNotificationBody(
          notificationBody: 'تنبيه حجر',
          data: {'body': 'ignored'},
        ),
        'تنبيه حجر',
      );
    });

    test('falls back to data body then default', () {
      expect(
        PushNotificationLogic.resolveNotificationBody(
          data: {'body': 'من السيرفر'},
        ),
        'من السيرفر',
      );
      expect(
        PushNotificationLogic.resolveNotificationBody(),
        'إشعار جديد من حديقة طرابلس',
      );
    });
  });

  group('background notification flow (documented behavior)', () {
    test('matches backend FCM payload shape from FcmPushService', () {
      // Backend always sends notification + data + android channel_id.
      const backendData = {
        'route': '/doctor/reports',
        'click_action': 'FLUTTER_NOTIFICATION_CLICK',
      };

      expect(
        PushNotificationLogic.shouldShowLocalInBackground(
          hasNotificationPayload: true,
        ),
        isFalse,
        reason: 'OS displays notification when app is backgrounded/killed',
      );
      expect(
        PushNotificationLogic.routeFromData(backendData),
        '/doctor/reports',
        reason: 'tap should deep-link after reopening the app',
      );
    });
  });
}
