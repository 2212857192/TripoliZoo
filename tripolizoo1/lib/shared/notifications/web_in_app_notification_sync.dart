import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter/widgets.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/domain/doctor_notification.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/presentation/doctor_notifications_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/domain/supervisor_notification.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/presentation/supervisor_notifications_provider.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/notifications/browser_notification.dart';

/// على الويب لا يعمل FCM — نحدّث صندوق الإشعارات دورياً ونُظهر تنبيه المتصفح.
class WebInAppNotificationSync extends StatefulWidget {
  const WebInAppNotificationSync({super.key, required this.child});

  final Widget child;

  @override
  State<WebInAppNotificationSync> createState() =>
      _WebInAppNotificationSyncState();
}

class _WebInAppNotificationSyncState extends State<WebInAppNotificationSync> {
  Timer? _pollTimer;
  String? _activeRole;
  int _lastUnreadCount = 0;
  AuthProvider? _authProvider;

  static const _pollInterval = Duration(seconds: 20);

  @override
  void initState() {
    super.initState();
    if (kIsWeb) {
      WidgetsBinding.instance.addPostFrameCallback((_) => _attachAuthListener());
    }
  }

  @override
  void dispose() {
    _authProvider?.removeListener(_onAuthChanged);
    _stopPolling();
    super.dispose();
  }

  void _attachAuthListener() {
    if (!mounted || !kIsWeb) return;

    final auth = context.read<AuthProvider>();
    if (_authProvider == auth) return;

    _authProvider?.removeListener(_onAuthChanged);
    _authProvider = auth;
    auth.addListener(_onAuthChanged);
    _onAuthChanged();
  }

  void _onAuthChanged() {
    if (!mounted || !kIsWeb) return;

    final auth = _authProvider;
    if (auth == null || !auth.isAuthenticated) {
      _stopPolling();
      return;
    }

    _syncForRole(auth.user?.role);
  }

  void _stopPolling() {
    _pollTimer?.cancel();
    _pollTimer = null;
    _activeRole = null;
    _lastUnreadCount = 0;
  }

  Future<void> _poll() async {
    if (!mounted || !kIsWeb) return;

    final auth = _authProvider;
    if (auth == null || !auth.isAuthenticated) return;

    final role = auth.user?.role;
    if (role == 'supervisor') {
      final provider = context.read<SupervisorNotificationsProvider>();
      final previousUnread = provider.unreadCount;
      await provider.refresh(silent: true);
      _maybeAlertSupervisor(provider.unreadCount, previousUnread, provider.items);
      return;
    }

    if (role == 'doctor') {
      final provider = context.read<DoctorNotificationsProvider>();
      final previousUnread = provider.unreadCount;
      await provider.refresh(silent: true);
      _maybeAlertDoctor(provider.unreadCount, previousUnread, provider.items);
    }
  }

  void _maybeAlertSupervisor(
    int unreadCount,
    int previousUnread,
    List<SupervisorNotification> items,
  ) {
    if (unreadCount <= previousUnread || unreadCount <= _lastUnreadCount) {
      _lastUnreadCount = unreadCount;
      return;
    }

    _lastUnreadCount = unreadCount;

    final latest = items.where((item) => !item.isRead).toList()
      ..sort((a, b) => b.createdAt.compareTo(a.createdAt));
    if (latest.isEmpty) return;

    final notification = latest.first;
    showBrowserNotification(
      title: notification.title,
      body: notification.description.isEmpty ? null : notification.description,
    );
  }

  void _maybeAlertDoctor(
    int unreadCount,
    int previousUnread,
    List<DoctorNotification> items,
  ) {
    if (unreadCount <= previousUnread || unreadCount <= _lastUnreadCount) {
      _lastUnreadCount = unreadCount;
      return;
    }

    _lastUnreadCount = unreadCount;

    final latest = items.where((item) => !item.isRead).toList()
      ..sort(
        (a, b) => (b.createdAt ?? DateTime(0)).compareTo(a.createdAt ?? DateTime(0)),
      );
    if (latest.isEmpty) return;

    final notification = latest.first;
    showBrowserNotification(
      title: notification.title,
      body: notification.message.isEmpty ? null : notification.message,
    );
  }

  void _syncForRole(String? role) {
    if (!kIsWeb) return;

    if (role != 'supervisor' && role != 'doctor') {
      _stopPolling();
      return;
    }

    if (_activeRole == role && _pollTimer != null) return;

    _stopPolling();
    _activeRole = role;
    _lastUnreadCount = 0;

    unawaited(requestBrowserNotificationPermission());
    unawaited(_poll());

    _pollTimer = Timer.periodic(_pollInterval, (_) {
      unawaited(_poll());
    });
  }

  @override
  Widget build(BuildContext context) => widget.child;
}
