import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/data/supervisor_notifications_mock_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/domain/supervisor_notification.dart';

class SupervisorNotificationsProvider extends ChangeNotifier {
  SupervisorNotificationsProvider() {
    _items = SupervisorNotificationsMockRepository.seedNotifications();
  }

  List<SupervisorNotification> _items = [];

  int get unreadCount =>
      _items.where((n) => !n.isRead).length;

  List<SupervisorNotification> filtered(SupervisorNotificationReadFilter filter) {
    final list = List<SupervisorNotification>.from(_items)
      ..sort((a, b) => b.createdAt.compareTo(a.createdAt));

    return switch (filter) {
      SupervisorNotificationReadFilter.all => list,
      SupervisorNotificationReadFilter.unread =>
        list.where((n) => !n.isRead).toList(),
      SupervisorNotificationReadFilter.read =>
        list.where((n) => n.isRead).toList(),
    };
  }

  void markAsRead(String id) {
    final index = _items.indexWhere((n) => n.id == id);
    if (index == -1 || _items[index].isRead) return;
    _items[index] = _items[index].copyWith(isRead: true);
    notifyListeners();
  }
}
