import 'package:tripolizoo/features/doctor/doctor_quarantine/domain/quarantine_record.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';
import 'package:tripolizoo/shared/api/api_debug_log.dart';

class QuarantineApiRepository {
  QuarantineApiRepository({ApiClient? client}) : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<List<QuarantineRecord>> fetchQuarantines() async {
    ApiDebugLog.info(
      'QuarantineApi',
      'GET ${ApiConfig.doctorQuarantines}',
    );
    final json = await _client.get(ApiConfig.doctorQuarantines);
    final data = _unwrapList(json['data']);
    ApiDebugLog.info('QuarantineApi', 'عدد السجلات المستلمة: ${data.length}');

    return data.map(_recordFromJson).toList();
  }

  Future<QuarantineRecord?> fetchQuarantine(String caseNumber) async {
    final json = await _client.get('${ApiConfig.doctorQuarantines}/$caseNumber');
    final data = _unwrapObject(json['data']);

    if (data == null) return null;

    return _recordFromJson(data);
  }

  Future<QuarantineRecord> addNote(String caseNumber, String note) async {
    final json = await _client.post(
      '${ApiConfig.doctorQuarantines}/$caseNumber/notes',
      body: {'note': note},
    );
    final data = _unwrapObject(json['data']);
    if (data == null) {
      throw Exception(json['message']?.toString() ?? 'تعذّر حفظ الملاحظة');
    }
    return _recordFromJson(data);
  }

  Future<QuarantineRecord> addVaccine({
    required String caseNumber,
    required String name,
    required DateTime administeredAt,
    String? note,
  }) async {
    final json = await _client.post(
      '${ApiConfig.doctorQuarantines}/$caseNumber/vaccines',
      body: {
        'name': name,
        'administered_at':
            '${administeredAt.year}-${administeredAt.month.toString().padLeft(2, '0')}-${administeredAt.day.toString().padLeft(2, '0')}',
        if (note != null && note.isNotEmpty) 'note': note,
      },
    );
    final data = _unwrapObject(json['data']);
    if (data == null) {
      throw Exception(json['message']?.toString() ?? 'تعذّر حفظ الجرعة');
    }
    return _recordFromJson(data);
  }

  Future<String> release(String caseNumber) async {
    final json = await _client.post(
      '${ApiConfig.doctorQuarantines}/$caseNumber/release',
    );
    return json['message']?.toString() ?? 'تم إصدار قرار الإفراج الصحي.';
  }

  Future<String> close({
    required String caseNumber,
    required String closeReason,
    String? closeNotes,
  }) async {
    final json = await _client.post(
      '${ApiConfig.doctorQuarantines}/$caseNumber/close',
      body: {
        'close_reason': closeReason,
        if (closeNotes != null && closeNotes.isNotEmpty)
          'close_notes': closeNotes,
      },
    );
    return json['message']?.toString() ?? 'تم إنهاء حالة الحجر.';
  }

  List<Map<String, dynamic>> _unwrapList(dynamic raw) {
    if (raw is List) {
      return raw.whereType<Map<String, dynamic>>().toList();
    }
    if (raw is Map<String, dynamic> && raw['data'] is List) {
      return (raw['data'] as List).whereType<Map<String, dynamic>>().toList();
    }
    return [];
  }

  Map<String, dynamic>? _unwrapObject(dynamic raw) {
    if (raw is Map<String, dynamic>) {
      if (raw.containsKey('case_number') || raw.containsKey('id')) {
        return raw;
      }
      if (raw['data'] is Map<String, dynamic>) {
        return raw['data'] as Map<String, dynamic>;
      }
    }
    return null;
  }

  QuarantineRecord _recordFromJson(Map<String, dynamic> json) {
    final lastNoteDate = _parseDate(json['last_note_date']);
    final entryDate = _parseDate(json['entry_date']) ?? DateTime.now();
    final healthNotes = (json['health_notes'] as List<dynamic>? ?? [])
        .whereType<Map<String, dynamic>>()
        .map(
          (item) => QuarantineHealthNote(
            date: _parseDate(item['date']) ?? DateTime.now(),
            text: item['text']?.toString() ?? '',
            author: item['author']?.toString(),
          ),
        )
        .toList();
    final preventiveVaccines =
        (json['preventive_vaccines'] as List<dynamic>? ?? [])
            .whereType<Map<String, dynamic>>()
            .map(_vaccineFromJson)
            .toList();
    final lastVaccineJson = json['last_vaccine'];
    final lastVaccine = lastVaccineJson is Map<String, dynamic>
        ? _vaccineFromJson(lastVaccineJson)
        : (preventiveVaccines.isNotEmpty ? preventiveVaccines.first : null);

    return QuarantineRecord(
      id: json['id']?.toString() ?? json['case_number']?.toString() ?? '',
      tempNumber: json['case_number']?.toString() ?? '',
      animalCode: json['animal_code']?.toString(),
      species: json['species']?.toString(),
      animalName:
          json['animal_name']?.toString() ?? json['species']?.toString() ?? '—',
      gender: json['gender']?.toString() ?? '—',
      expectedGroup: json['expected_group']?.toString() ?? '—',
      entryDate: entryDate,
      status: _statusFromApi(json['status']?.toString()),
      durationDays: _asInt(json['duration_days']),
      responsibleDoctor: json['responsible_doctor']?.toString() ?? '—',
      approximateAge: json['approximate_age']?.toString(),
      animalSource: json['animal_source']?.toString(),
      initialHealthStatus: json['initial_health_status']?.toString(),
      generalNotes: json['general_notes']?.toString(),
      lastVaccine: lastVaccine,
      lastNoteDate: lastNoteDate,
      lastNoteText: json['last_note_text']?.toString(),
      healthNotes: healthNotes,
      preventiveVaccines: preventiveVaccines,
      canManage: json['can_manage'] == true,
      photoUrl: json['photo_url']?.toString(),
    );
  }

  PreventiveVaccine _vaccineFromJson(Map<String, dynamic> json) {
    return PreventiveVaccine(
      name: json['name']?.toString() ?? '',
      date: _parseDate(json['date']) ?? DateTime.now(),
      note: json['note']?.toString(),
      doctorName: json['doctor_name']?.toString(),
    );
  }

  QuarantineStatus _statusFromApi(String? value) {
    return switch (value) {
      'health_released' => QuarantineStatus.released,
      'failed' => QuarantineStatus.failed,
      _ => QuarantineStatus.underFollowUp,
    };
  }

  DateTime? _parseDate(dynamic value) {
    if (value is String && value.isNotEmpty) {
      return DateTime.tryParse(value);
    }
    return null;
  }

  int _asInt(dynamic value) {
    if (value is int) return value;
    return int.tryParse('$value') ?? 0;
  }
}
