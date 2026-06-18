import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_animals_data.dart';
import 'package:tripolizoo/shared/api/api_client.dart';
import 'package:tripolizoo/shared/api/api_config.dart';

class MedicalCasesApiRepository {
  MedicalCasesApiRepository({ApiClient? client})
      : _client = client ?? ApiClient();

  final ApiClient _client;

  Future<List<SupervisorAnimal>> fetchGroupAnimals() async {
    final json = await _client.get(ApiConfig.doctorAnimals);
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

  Future<List<MedicalCase>> fetchCases() async {
    final json = await _client.get(ApiConfig.doctorCases);
    final data = json['data'];
    if (data is! List) return [];

    return data
        .whereType<Map<String, dynamic>>()
        .map(MedicalCase.fromJson)
        .toList();
  }

  Future<MedicalCase> fetchCase(String caseId) async {
    final encodedId = Uri.encodeComponent(caseId);
    final json = await _client.get('${ApiConfig.doctorCases}/$encodedId');

    return MedicalCase.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<MedicalCase> registerProcedure({
    required String caseId,
    required String diagnosis,
    required String treatment,
    required MedicalCaseResult caseResult,
    String? note,
    String? nutritionText,
    DateTime? nutritionStart,
    DateTime? nutritionEnd,
    String? nutritionNote,
  }) async {
    final encodedId = Uri.encodeComponent(caseId);
    final body = <String, dynamic>{
      'diagnosis': diagnosis.trim(),
      'treatment': treatment.trim(),
      'case_result': caseResult.apiValue,
      if (note != null && note.trim().isNotEmpty) 'note': note.trim(),
    };

    if (nutritionText != null &&
        nutritionText.trim().isNotEmpty &&
        nutritionStart != null &&
        nutritionEnd != null) {
      body['nutrition'] = {
        'recommendation_text': nutritionText.trim(),
        'start_date':
            '${nutritionStart.year}-${nutritionStart.month.toString().padLeft(2, '0')}-${nutritionStart.day.toString().padLeft(2, '0')}',
        'end_date':
            '${nutritionEnd.year}-${nutritionEnd.month.toString().padLeft(2, '0')}-${nutritionEnd.day.toString().padLeft(2, '0')}',
        if (nutritionNote != null && nutritionNote.trim().isNotEmpty)
          'note': nutritionNote.trim(),
      };
    }

    final json = await _client.post(
      '${ApiConfig.doctorCases}/$encodedId/procedures',
      body: body,
    );

    return MedicalCase.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<MedicalCase> closeFieldCase({
    required String caseId,
  }) async {
    final encodedId = Uri.encodeComponent(caseId);
    final json = await _client.post(
      '${ApiConfig.doctorCases}/$encodedId/close',
      body: const {},
    );

    return MedicalCase.fromJson(json['data'] as Map<String, dynamic>);
  }

  Future<MedicalCase> openFieldCase({
    required String animalCode,
    required String openReason,
    String? initialNote,
  }) async {
    final json = await _client.post(
      ApiConfig.doctorFieldCases,
      body: {
        'animal_code': animalCode.trim(),
        'open_reason': openReason.trim(),
        if (initialNote != null && initialNote.trim().isNotEmpty)
          'initial_note': initialNote.trim(),
      },
    );

    return MedicalCase.fromJson(json['data'] as Map<String, dynamic>);
  }
}
