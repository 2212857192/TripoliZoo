import 'package:flutter/foundation.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/data/doctor_notifications_api_repository.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/domain/doctor_notification.dart';

class DoctorNotificationsProvider extends ChangeNotifier {
  DoctorNotificationsProvider({DoctorNotificationsApiRepository? repository})
      : _repository = repository ?? DoctorNotificationsApiRepository();

  final DoctorNotificationsApiRepository _repository;

  List<DoctorNotification> _items = [];
  bool _loading = false;
  String? _errorMessage;

  List<DoctorNotification> get items => List.unmodifiable(_items);
  bool get isLoading => _loading;
  String? get errorMessage => _errorMessage;
  int get unreadCount => _items.where((item) => !item.isRead).length;

  List<DoctorNotification> filtered(DoctorNotificationReadFilter filter) {
    final list = List<DoctorNotification>.from(_items)
      ..sort((a, b) => (b.createdAt ?? DateTime(0)).compareTo(a.createdAt ?? DateTime(0)));

    return switch (filter) {
      DoctorNotificationReadFilter.all => list,
      DoctorNotificationReadFilter.unread =>
        list.where((item) => !item.isRead).toList(),
      DoctorNotificationReadFilter.read =>
        list.where((item) => item.isRead).toList(),
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

  Future<void> markAsRead(DoctorNotification notification) async {
    final index = _items.indexWhere(
      (item) => item.id == notification.id && item.type == notification.type,
    );
    if (index == -1 || _items[index].isRead) return;

    try {
      await _repository.markRead(notification);
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
