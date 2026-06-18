import 'package:tripolizoo/features/doctor/doctor_notifications/domain/doctor_notification.dart';

import 'package:tripolizoo/shared/api/api_client.dart';

import 'package:tripolizoo/shared/api/api_config.dart';



class DoctorNotificationsApiRepository {

  DoctorNotificationsApiRepository({ApiClient? client})

      : _client = client ?? ApiClient();



  final ApiClient _client;



  Future<({List<DoctorNotification> items, int unreadCount})> fetchAll() async {

    final json = await _client.get(ApiConfig.doctorNotifications);

    final items = (json['data'] as List<dynamic>? ?? [])

        .whereType<Map<String, dynamic>>()

        .map(_mapNotification)

        .toList();



    return (

      items: items,

      unreadCount: _asInt(json['unread_count']),

    );

  }



  Future<void> markRead(DoctorNotification notification) async {

    if (notification.type == DoctorNotificationType.receivingDelay) {

      await _client.post(

        '${ApiConfig.doctorNotifications}/receiving/${notification.id}/read',

      );

      return;

    }



    await _client.post(

      '${ApiConfig.doctorNotifications}/${notification.id}/read',

    );

  }



  Future<void> markAllRead() async {

    await _client.post('${ApiConfig.doctorNotifications}/read-all');

  }



  DoctorNotification _mapNotification(Map<String, dynamic> json) {

    return DoctorNotification(

      id: _asInt(json['id']),

      type: _mapType(json['type']?.toString()),

      title: json['title']?.toString() ?? '',

      message: json['message']?.toString() ?? '',

      caseNumber: json['case_number']?.toString(),

      taskNumber: json['task_number']?.toString(),

      isRead: json['is_read'] == true,

      createdAt: DateTime.tryParse(json['created_at']?.toString() ?? ''),

    );

  }



  DoctorNotificationType _mapType(String? value) {

    return switch (value) {

      'receiving_delay' => DoctorNotificationType.receivingDelay,

      _ => DoctorNotificationType.quarantine,

    };

  }



  int _asInt(dynamic value) {

    if (value is int) return value;

    return int.tryParse('$value') ?? 0;

  }

}

