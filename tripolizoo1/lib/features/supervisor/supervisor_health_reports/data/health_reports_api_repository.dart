import 'package:flutter/foundation.dart';
import 'package:image_picker/image_picker.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/domain/health_report.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class HealthReportsApiRepository {
  HealthReportsApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<List<SupervisorAnimal>> fetchSupervisorAnimals() async {
    final json = await _client.get(ApiConfig.supervisorAnimals);
    final data = json['data'];
    if (data is! List) return [];

    return data
        .whereType<Map<String, dynamic>>()
        .map(
          (item) => SupervisorAnimal(
            id: item['id']?.toString() ?? '',
            name: item['name']?.toString() ?? '',
            type: item['type']?.toString(),
            customLabel: item['label']?.toString(),
          ),
        )
        .toList();
  }

  Future<List<HealthReport>> fetchSupervisorReports({
    HealthReportStatus? status,
  }) async {
    final path = status == null
        ? ApiConfig.supervisorHealthReports
        : '${ApiConfig.supervisorHealthReports}?status=${status.name}';

    return _parseList(await _client.get(path));
  }

  Future<List<HealthReport>> fetchDoctorReports({
    HealthReportStatus? status,
  }) async {
    final path = status == null
        ? ApiConfig.doctorHealthReports
        : '${ApiConfig.doctorHealthReports}?status=${status.name}';

    return _parseList(await _client.get(path));
  }

  Future<HealthReport> createReport({
    required String animalCode,
    required String description,
    bool isUrgent = false,
    XFile? attachment,
  }) async {
    final fields = <String, String>{
      'animal_code': animalCode,
      'description': description.trim(),
      'is_urgent': isUrgent ? '1' : '0',
    };

    final Map<String, dynamic> json;
    if (attachment != null) {
      final filename = attachment.name.trim().isNotEmpty
          ? attachment.name
          : 'attachment.jpg';

      if (!kIsWeb && attachment.path.isNotEmpty) {
        json = await _client.postMultipart(
          ApiConfig.supervisorHealthReports,
          fields: fields,
          filePaths: {
            'attachment': MultipartPathUpload(
              path: attachment.path,
              filename: filename,
            ),
          },
        );
      } else {
        final bytes = await attachment.readAsBytes();
        json = await _client.postMultipart(
          ApiConfig.supervisorHealthReports,
          fields: fields,
          files: {
            'attachment': MultipartUpload(
              bytes: bytes,
              filename: filename,
            ),
          },
        );
      }
    } else {
      json = await _client.post(
        ApiConfig.supervisorHealthReports,
        body: {
          'animal_code': animalCode,
          'description': description.trim(),
          'is_urgent': isUrgent,
        },
      );
    }

    return _mapReport(json['data'] as Map<String, dynamic>);
  }

  Future<HealthReport> openDoctorReport(String reportNumber) async {
    final json = await _client.get(
      '${ApiConfig.doctorHealthReports}/$reportNumber',
    );

    return _mapReport(json['data'] as Map<String, dynamic>);
  }

  Future<HealthReport> closeDoctorReport({
    required String reportNumber,
    required String note,
    bool fieldCaseOpened = false,
  }) async {
    final json = await _client.post(
      '${ApiConfig.doctorHealthReports}/$reportNumber/close',
      body: {
        'doctor_note': note.trim(),
        'field_case_opened': fieldCaseOpened,
      },
    );

    return _mapReport(json['data'] as Map<String, dynamic>);
  }

  List<HealthReport> _parseList(Map<String, dynamic> json) {
    final data = json['data'];
    if (data is! List) return [];

    return data
        .whereType<Map<String, dynamic>>()
        .map(_mapReport)
        .toList();
  }

  HealthReport _mapReport(Map<String, dynamic> json) {
    final attachmentUrl = ApiConfig.resolveAssetUrl(
      json['attachment_url']?.toString(),
    );

    return HealthReport(
      id: json['id']?.toString() ?? '',
      reportNumber: json['report_number']?.toString() ?? '',
      animalId: json['animal_id']?.toString() ?? '',
      animalType: json['animal_type']?.toString() ?? '',
      groupName: json['group_name']?.toString() ?? '',
      description: json['description']?.toString() ?? '',
      sentAt: DateTime.tryParse(json['sent_at']?.toString() ?? '') ??
          DateTime.now(),
      status: _mapStatus(json['status']?.toString()),
      assignedDoctorName: json['assigned_doctor_name']?.toString(),
      doctorNote: json['doctor_note']?.toString(),
      doctorUpdatedAt: DateTime.tryParse(
        json['doctor_updated_at']?.toString() ?? '',
      ),
      fieldCaseOpened: json['field_case_opened'] == true,
      hasAttachment: json['has_attachment'] == true,
      attachmentUrl: attachmentUrl,
    );
  }

  HealthReportStatus _mapStatus(String? value) => switch (value) {
        'received' => HealthReportStatus.received,
        'closed' => HealthReportStatus.closed,
        _ => HealthReportStatus.sent,
      };
}
