import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/domain/receiving_task.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class ReceivingTasksApiRepository {
  ReceivingTasksApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<List<ReceivingTask>> fetchTasks({ReceivingTaskStatus? status}) async {
    final path = status == null
        ? ApiConfig.receivingTasks
        : '${ApiConfig.receivingTasks}?status=${_statusQuery(status)}';

    final json = await _client.get(path);
    final data = json['data'];

    if (data is! List) return [];

    return data
        .whereType<Map<String, dynamic>>()
        .map(_taskFromJson)
        .toList();
  }

  Future<int> fetchPendingCount() async {
    final json = await _client.get(ApiConfig.receivingTasks);
    final count = json['pending_count'];
    return count is int ? count : int.tryParse('$count') ?? 0;
  }

  Future<void> confirmReceipt(String taskId, {String? note}) async {
    await _client.post(
      '${ApiConfig.receivingTasks}/$taskId/confirm',
      body: {
        if (note != null && note.trim().isNotEmpty) 'receipt_note': note.trim(),
      },
    );
  }

  Future<void> recordTemporaryDelay(
    String taskId, {
    required String reason,
    String? extraNote,
  }) async {
    await _client.post(
      '${ApiConfig.receivingTasks}/$taskId/delay',
      body: {
        'delay_reason': reason.trim(),
        if (extraNote != null && extraNote.trim().isNotEmpty)
          'delay_extra_note': extraNote.trim(),
      },
    );
  }

  String _statusQuery(ReceivingTaskStatus status) => switch (status) {
        ReceivingTaskStatus.pending => 'pending',
        ReceivingTaskStatus.temporarilyUnable => 'temporarily_unable',
        ReceivingTaskStatus.received => 'received',
      };

  ReceivingTask _taskFromJson(Map<String, dynamic> json) {
    return ReceivingTask(
      id: json['id']?.toString() ?? '',
      taskNumber: json['task_number']?.toString() ?? '',
      status: _statusFromApi(json['status']?.toString()),
      taskType: _taskTypeFromApi(json['task_type']?.toString()),
      source: _sourceFromApi(json['source']?.toString()),
      animalId: json['animal_id']?.toString() ?? '',
      animalType: json['animal_type']?.toString() ?? '',
      animalGender: json['animal_gender']?.toString(),
      groupName: json['group_name']?.toString() ?? '',
      animalImageUrl: json['animal_image_url']?.toString(),
      createdAt: _parseDate(json['created_at']),
      decisionType: _decisionTypeFromApi(json['decision_type']?.toString()),
      decisionDate: _parseDate(json['decision_date']),
      decisionIssuedBy: json['decision_issued_by']?.toString() ?? '',
      decisionIssuerRole: json['decision_issuer_role']?.toString(),
      decisionNotes: json['decision_notes']?.toString(),
      delayReason: json['delay_reason']?.toString(),
      delayExtraNote: json['delay_extra_note']?.toString(),
      delayRecordedAt: _parseNullableDate(json['delay_recorded_at']),
      receiptNote: json['receipt_note']?.toString(),
    );
  }

  ReceivingTaskStatus _statusFromApi(String? value) => switch (value) {
        'temporarily_unable' => ReceivingTaskStatus.temporarilyUnable,
        'received' => ReceivingTaskStatus.received,
        _ => ReceivingTaskStatus.pending,
      };

  ReceivingTaskType _taskTypeFromApi(String? value) => switch (value) {
        'after_treatment' => ReceivingTaskType.afterTreatment,
        _ => ReceivingTaskType.afterHealthRelease,
      };

  ReceivingTaskSource _sourceFromApi(String? value) => switch (value) {
        'hospital' => ReceivingTaskSource.hospital,
        _ => ReceivingTaskSource.quarantine,
      };

  ReceivingDecisionType _decisionTypeFromApi(String? value) => switch (value) {
        'after_treatment' => ReceivingDecisionType.afterTreatment,
        _ => ReceivingDecisionType.healthRelease,
      };

  DateTime _parseDate(dynamic value) {
    if (value is String && value.isNotEmpty) {
      return DateTime.tryParse(value) ?? DateTime.now();
    }
    return DateTime.now();
  }

  DateTime? _parseNullableDate(dynamic value) {
    if (value is String && value.isNotEmpty) {
      return DateTime.tryParse(value);
    }
    return null;
  }
}
