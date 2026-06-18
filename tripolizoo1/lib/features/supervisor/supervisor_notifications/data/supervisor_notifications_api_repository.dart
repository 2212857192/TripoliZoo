import 'package:tripolizoo/features/supervisor/supervisor_notifications/domain/supervisor_notification.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class SupervisorNotificationsApiRepository {
  SupervisorNotificationsApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<({List<SupervisorNotification> items, int unreadCount})> fetchAll() async {
    final json = await _client.get(ApiConfig.supervisorNotifications);
    final items = (json['data'] as List<dynamic>? ?? [])
        .whereType<Map<String, dynamic>>()
        .map(_mapNotification)
        .toList();

    return (
      items: items,
      unreadCount: _asInt(json['unread_count']),
    );
  }

  Future<void> markRead(int notificationId) async {
    await _client.post(
      '${ApiConfig.supervisorNotifications}/$notificationId/read',
    );
  }

  Future<void> markAllRead() async {
    await _client.post('${ApiConfig.supervisorNotifications}/read-all');
  }

  SupervisorNotification _mapNotification(Map<String, dynamic> json) {
    return SupervisorNotification(
      id: _asInt(json['id']),
      type: _mapType(json['type']?.toString()),
      title: json['title']?.toString() ?? '',
      description: json['message']?.toString() ?? '',
      createdAt: DateTime.tryParse(json['created_at']?.toString() ?? '') ??
          DateTime.now(),
      isRead: json['is_read'] == true,
      targetRoute: _targetRoute(json),
    );
  }

  SupervisorNotificationType _mapType(String? value) {
    return switch (value) {
      'receiving_task' => SupervisorNotificationType.receivingTask,
      'health_report_update' => SupervisorNotificationType.healthReportUpdate,
      'diet_recommendation' => SupervisorNotificationType.dietRecommendation,
      'newborn_follow_up' => SupervisorNotificationType.newbornFollowUp,
      'mortality_approval' => SupervisorNotificationType.mortalityApproval,
      _ => SupervisorNotificationType.receivingTask,
    };
  }

  String? _targetRoute(Map<String, dynamic> json) {
    final type = json['type']?.toString();
    if (type == 'receiving_task') {
      return '/supervisor/receiving-tasks?filter=pending';
    }
    if (type == 'health_report_update') {
      return '/supervisor/health-reports';
    }

    return null;
  }

  int _asInt(dynamic value) {
    if (value is int) return value;
    return int.tryParse('$value') ?? 0;
  }
}
