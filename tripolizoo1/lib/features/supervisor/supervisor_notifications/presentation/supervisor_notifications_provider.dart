import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/data/supervisor_notifications_api_repository.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/domain/supervisor_notification.dart';

class SupervisorNotificationsProvider extends ChangeNotifier {
  SupervisorNotificationsProvider({SupervisorNotificationsApiRepository? repository})
      : _repository = repository ?? SupervisorNotificationsApiRepository();

  final SupervisorNotificationsApiRepository _repository;

  List<SupervisorNotification> _items = [];
  bool _loading = false;
  String? _errorMessage;

  List<SupervisorNotification> get items => List.unmodifiable(_items);
  bool get isLoading => _loading;
  String? get errorMessage => _errorMessage;

  int get unreadCount => _items.where((n) => !n.isRead).length;

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

  Future<void> load() => refresh();

  Future<void> refresh({bool silent = false}) async {
    if (!silent) {
      _loading = true;
      _errorMessage = null;
      notifyListeners();
    }

    try {
      final result = await _repository.fetchAll();
      _items = result.items;
      if (silent) {
        _errorMessage = null;
      }
    } catch (error) {
      if (!silent) {
        _errorMessage = error.toString();
      }
    } finally {
      if (!silent) {
        _loading = false;
      }
      notifyListeners();
    }
  }

  Future<void> markAsRead(int id) async {
    final index = _items.indexWhere((n) => n.id == id);
    if (index == -1 || _items[index].isRead) return;

    try {
      await _repository.markRead(id);
      _items[index] = _items[index].copyWith(isRead: true);
      notifyListeners();
    } catch (_) {}
  }

  Future<void> markAllAsRead() async {
    try {
      await _repository.markAllRead();
      _items = _items.map((item) => item.copyWith(isRead: true)).toList();
      notifyListeners();
    } catch (_) {}
  }
}
