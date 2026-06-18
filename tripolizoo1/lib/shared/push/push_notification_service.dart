import 'dart:convert';
import 'dart:io';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:go_router/go_router.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:tripolizoo/shared/api/device_token_service.dart';

const _channelId = 'quarantine_alerts';
const _channelName = 'تنبيهات الحجر الصحي';
const _appDisplayName = 'حديقة حيوان طرابلس';

@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  await PushNotificationService.showLocalFromRemote(message);
}

class PushNotificationService {
  PushNotificationService._();

  static final PushNotificationService instance = PushNotificationService._();

  static final FlutterLocalNotificationsPlugin _localNotifications =
      FlutterLocalNotificationsPlugin();

  static GoRouter? _router;
  static String? pendingRoute;
  static bool _initialized = false;

  static void attachRouter(GoRouter router) {
    _router = router;
    handlePendingRoute();
  }

  static void handlePendingRoute() {
    final route = pendingRoute;
    final router = _router;
    if (route == null || router == null) return;

    pendingRoute = null;
    router.go(route);
  }

  static Future<void> initialize() async {
    if (_initialized || kIsWeb) return;

    try {
      await Firebase.initializeApp();
    } catch (e) {
      debugPrint('Firebase init skipped: $e');
      return;
    }

    await _setupLocalNotifications();

    FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);

    final messaging = FirebaseMessaging.instance;
    await messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
      provisional: false,
      criticalAlert: false,
    );

    if (Platform.isAndroid) {
      await Permission.notification.request();
    }

    await messaging.setForegroundNotificationPresentationOptions(
      alert: true,
      badge: true,
      sound: true,
    );

    FirebaseMessaging.onMessage.listen(showLocalFromRemote);
    FirebaseMessaging.onMessageOpenedApp.listen(_handleOpenedMessage);

    final initial = await messaging.getInitialMessage();
    if (initial != null) {
      _queueRouteFromMessage(initial);
    }

    _initialized = true;
  }

  static Future<void> _setupLocalNotifications() async {
    const androidInit = AndroidInitializationSettings('@mipmap/ic_launcher');
    const iosInit = DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );

    await _localNotifications.initialize(
      const InitializationSettings(android: androidInit, iOS: iosInit),
      onDidReceiveNotificationResponse: (response) {
        final payload = response.payload;
        if (payload == null || payload.isEmpty) return;
        try {
          final data = jsonDecode(payload) as Map<String, dynamic>;
          final route = data['route'] as String?;
          if (route != null) {
            pendingRoute = route;
            handlePendingRoute();
          }
        } catch (_) {}
      },
    );

    final androidPlugin =
        _localNotifications.resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>();

    await androidPlugin?.createNotificationChannel(
      const AndroidNotificationChannel(
        _channelId,
        _channelName,
        description: 'إشعارات الحيوانات الجديدة في الحجر الصحي',
        importance: Importance.max,
        playSound: true,
        enableVibration: true,
        showBadge: true,
      ),
    );
  }

  static Future<void> syncTokenForLoggedInUser({
    required bool isDoctor,
    bool isSupervisor = false,
  }) async {
    if (!_initialized || kIsWeb || (!isDoctor && !isSupervisor)) return;

    try {
      final token = await FirebaseMessaging.instance.getToken();
      if (token == null || token.isEmpty) return;

      final platform = Platform.isIOS ? 'ios' : 'android';
      await DeviceTokenService().register(token: token, platform: platform);
    } catch (e) {
      debugPrint('FCM token sync failed: $e');
    }
  }

  static Future<void> clearToken() async {
    if (!_initialized || kIsWeb) return;

    try {
      final token = await FirebaseMessaging.instance.getToken();
      if (token != null) {
        await DeviceTokenService().unregister(token: token);
      }
      await FirebaseMessaging.instance.deleteToken();
    } catch (_) {}
  }

  static Future<void> showLocalFromRemote(RemoteMessage message) async {
    final notification = message.notification;
    final data = message.data;
    final title = notification?.title ?? _appDisplayName;
    final body = notification?.body ??
        data['body'] ??
        'حيوان جديد في الحجر الصحي';

    final androidDetails = AndroidNotificationDetails(
      _channelId,
      _channelName,
      channelDescription: 'إشعارات الحيوانات الجديدة في الحجر الصحي',
      importance: Importance.max,
      priority: Priority.high,
      visibility: NotificationVisibility.public,
      playSound: true,
      enableVibration: true,
      icon: '@mipmap/ic_launcher',
      largeIcon: const DrawableResourceAndroidBitmap('@mipmap/ic_launcher'),
      ticker: _appDisplayName,
    );

    const iosDetails = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
    );

    await _localNotifications.show(
      message.hashCode,
      title,
      body,
      NotificationDetails(android: androidDetails, iOS: iosDetails),
      payload: jsonEncode(data),
    );
  }

  static void _handleOpenedMessage(RemoteMessage message) {
    _queueRouteFromMessage(message);
    handlePendingRoute();
  }

  static void _queueRouteFromMessage(RemoteMessage message) {
    final route = message.data['route'];
    if (route is String && route.isNotEmpty) {
      pendingRoute = route;
    }
  }
}
