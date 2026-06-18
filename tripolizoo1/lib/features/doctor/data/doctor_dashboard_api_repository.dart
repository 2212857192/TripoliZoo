import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class DoctorDashboardData {
  const DoctorDashboardData({
    required this.doctorName,
    required this.groupName,
    required this.quarantineActiveCount,
    required this.activeFieldCasesCount,
    required this.activeHospitalCasesCount,
    required this.unreadNotifications,
    required this.alerts,
  });

  final String doctorName;
  final String? groupName;
  final int quarantineActiveCount;
  final int activeFieldCasesCount;
  final int activeHospitalCasesCount;
  final int unreadNotifications;
  final List<DoctorDashboardAlert> alerts;
}

class DoctorDashboardAlert {
  const DoctorDashboardAlert({
    required this.title,
    required this.subtitle,
    this.caseNumber,
    this.urgent = false,
  });

  final String title;
  final String subtitle;
  final String? caseNumber;
  final bool urgent;
}

class DoctorDashboardApiRepository {
  DoctorDashboardApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<DoctorDashboardData> fetchDashboard() async {
    final json = await _client.get(ApiConfig.doctorDashboard);
    final alerts = (json['alerts'] as List<dynamic>? ?? [])
        .whereType<Map<String, dynamic>>()
        .map(
          (item) => DoctorDashboardAlert(
            title: item['title']?.toString() ?? '',
            subtitle: item['subtitle']?.toString() ?? '',
            caseNumber: item['case_number']?.toString(),
            urgent: item['urgent'] == true,
          ),
        )
        .toList();

    return DoctorDashboardData(
      doctorName: json['doctor_name']?.toString() ?? '',
      groupName: json['group_name']?.toString(),
      quarantineActiveCount: _asInt(json['quarantine_active_count']),
      activeFieldCasesCount: _asInt(json['active_field_cases_count']),
      activeHospitalCasesCount: _asInt(json['active_hospital_cases_count']),
      unreadNotifications: _asInt(json['unread_notifications']),
      alerts: alerts,
    );
  }

  int _asInt(dynamic value) {
    if (value is int) return value;
    return int.tryParse('$value') ?? 0;
  }
}
