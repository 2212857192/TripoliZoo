/// نوع الحالة الطبية.
enum MedicalCaseType {
  field,
  hospital,
}

extension MedicalCaseTypeX on MedicalCaseType {
  String get label => switch (this) {
        MedicalCaseType.field => 'حالة ميدانية',
        MedicalCaseType.hospital => 'حالة داخل المستشفى',
      };

  String get detailLabel => switch (this) {
        MedicalCaseType.field => 'حالة طبية ميدانية',
        MedicalCaseType.hospital => 'حالة داخل المستشفى',
      };
}

/// حالة الحالة عند الطبيب.
enum MedicalCaseStatus {
  active,
  closed,
}

extension MedicalCaseStatusX on MedicalCaseStatus {
  String get label => switch (this) {
        MedicalCaseStatus.active => 'نشطة',
        MedicalCaseStatus.closed => 'مغلقة',
      };
}

enum MedicalCaseFilter {
  all,
  field,
  hospital;

  MedicalCaseType? get type => switch (this) {
        MedicalCaseFilter.field => MedicalCaseType.field,
        MedicalCaseFilter.hospital => MedicalCaseType.hospital,
        MedicalCaseFilter.all => null,
      };
}

enum MedicalCaseResult {
  continueTreatment,
  noResponse,
  readyForDischarge,
}

extension MedicalCaseResultX on MedicalCaseResult {
  String get apiValue => switch (this) {
        MedicalCaseResult.continueTreatment => 'continue_treatment',
        MedicalCaseResult.noResponse => 'no_response',
        MedicalCaseResult.readyForDischarge => 'ready_for_discharge',
      };

  String get label => switch (this) {
        MedicalCaseResult.continueTreatment => 'استمرار العلاج',
        MedicalCaseResult.noResponse => 'لا يستجيب للعلاج',
        MedicalCaseResult.readyForDischarge => 'جاهز للخروج',
      };

  static MedicalCaseResult fromApi(String? value) => switch (value) {
        'no_response' => MedicalCaseResult.noResponse,
        'ready_for_discharge' => MedicalCaseResult.readyForDischarge,
        _ => MedicalCaseResult.continueTreatment,
      };
}

class MedicalNutritionRecommendation {
  const MedicalNutritionRecommendation({
    required this.recommendationText,
    required this.startDate,
    required this.endDate,
    this.note,
  });

  final String recommendationText;
  final DateTime startDate;
  final DateTime endDate;
  final String? note;
}

class MedicalProcedure {
  MedicalProcedure({
    required this.recordedAt,
    required this.diagnosis,
    required this.treatment,
    required this.caseResult,
    this.note,
    this.caseResultLabel,
    this.nutrition,
  });

  final DateTime recordedAt;
  final String diagnosis;
  final String treatment;
  final String? note;
  final MedicalCaseResult caseResult;
  final String? caseResultLabel;
  final MedicalNutritionRecommendation? nutrition;
}

/// حالة طبية — ميدانية أو داخل المستشفى.
class MedicalCase {
  MedicalCase({
    required this.id,
    required this.caseNumber,
    required this.type,
    required this.status,
    required this.animalId,
    required this.animalType,
    required this.animalGroup,
    required this.openReason,
    required this.openedAt,
    required this.updatedAt,
    required this.sourceLabel,
    required this.canRegisterProcedure,
    required this.canClose,
    this.gender,
    this.age,
    this.initialNote,
    this.hospitalStatusLabel,
    this.procedures = const <MedicalProcedure>[],
    this.nutritionRecommendations = const <MedicalNutritionRecommendation>[],
  });

  final String id;
  final String caseNumber;
  final MedicalCaseType type;
  final MedicalCaseStatus status;
  final String animalId;
  final String animalType;
  final String animalGroup;
  final String? gender;
  final String? age;
  final String openReason;
  final String? initialNote;
  final String? hospitalStatusLabel;
  final DateTime openedAt;
  final DateTime updatedAt;
  final String sourceLabel;
  final bool canRegisterProcedure;
  final bool canClose;
  final List<MedicalProcedure> procedures;
  final List<MedicalNutritionRecommendation> nutritionRecommendations;

  String get animalTitle => '$animalType • $animalId';

  String get reasonLine => 'سبب فتح الحالة: $openReason';

  factory MedicalCase.fromJson(Map<String, dynamic> json) {
    final type = json['case_type']?.toString() == 'hospital'
        ? MedicalCaseType.hospital
        : MedicalCaseType.field;
    final status = json['status']?.toString() == 'closed'
        ? MedicalCaseStatus.closed
        : MedicalCaseStatus.active;

    final procedures = (json['procedures'] as List<dynamic>? ?? [])
        .whereType<Map<String, dynamic>>()
        .map(_parseProcedure)
        .toList();

    final nutrition = procedures
        .map((p) => p.nutrition)
        .whereType<MedicalNutritionRecommendation>()
        .toList();

    return MedicalCase(
      id: json['id']?.toString() ?? '',
      caseNumber: json['case_number']?.toString() ?? '',
      type: type,
      status: status,
      animalId: json['animal_id']?.toString() ?? '',
      animalType: json['animal_type']?.toString() ?? '',
      animalGroup: json['animal_group']?.toString() ?? '',
      gender: _nullIfEmpty(json['animal_gender']?.toString()),
      age: _nullIfEmpty(json['animal_age']?.toString()),
      openReason: json['open_reason']?.toString() ?? '',
      initialNote: _nullIfEmpty(json['initial_note']?.toString()),
      hospitalStatusLabel: _nullIfEmpty(json['hospital_status_label']?.toString()),
      openedAt: DateTime.tryParse(json['opened_at']?.toString() ?? '') ??
          DateTime.now(),
      updatedAt: DateTime.tryParse(json['updated_at']?.toString() ?? '') ??
          DateTime.now(),
      sourceLabel: json['source_label']?.toString() ?? '',
      canRegisterProcedure: json['can_register_procedure'] == true,
      canClose: json['can_close'] == true,
      procedures: procedures,
      nutritionRecommendations: nutrition,
    );
  }

  static MedicalProcedure _parseProcedure(Map<String, dynamic> json) {
    MedicalNutritionRecommendation? nutrition;
    final nutritionJson = json['nutrition'];
    if (nutritionJson is Map<String, dynamic>) {
      nutrition = MedicalNutritionRecommendation(
        recommendationText:
            nutritionJson['recommendation_text']?.toString() ?? '',
        startDate: DateTime.tryParse(
              nutritionJson['start_date']?.toString() ?? '',
            ) ??
            DateTime.now(),
        endDate: DateTime.tryParse(
              nutritionJson['end_date']?.toString() ?? '',
            ) ??
            DateTime.now(),
        note: _nullIfEmpty(nutritionJson['note']?.toString()),
      );
    }

    return MedicalProcedure(
      recordedAt: DateTime.tryParse(json['recorded_at']?.toString() ?? '') ??
          DateTime.now(),
      diagnosis: json['diagnosis']?.toString() ?? '',
      treatment: json['treatment']?.toString() ?? '',
      note: _nullIfEmpty(json['note']?.toString()),
      caseResult: MedicalCaseResultX.fromApi(json['case_result']?.toString()),
      caseResultLabel: _nullIfEmpty(json['case_result_label']?.toString()),
      nutrition: nutrition,
    );
  }

  static String? _nullIfEmpty(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    return value.trim();
  }

  bool matchesQuery(String query) {
    if (query.isEmpty) return true;
    final q = query.trim().toLowerCase();
    return caseNumber.toLowerCase().contains(q) ||
        animalId.toLowerCase().contains(q) ||
        animalType.toLowerCase().contains(q);
  }
}

String formatMedicalCaseDateTime(DateTime date) {
  final d = date.day.toString().padLeft(2, '0');
  final m = date.month.toString().padLeft(2, '0');
  final y = date.year;
  final h = date.hour.toString().padLeft(2, '0');
  final min = date.minute.toString().padLeft(2, '0');
  return '$d-$m-$y $h:$min';
}

String formatMedicalCaseDate(DateTime date) {
  final d = date.day.toString().padLeft(2, '0');
  final m = date.month.toString().padLeft(2, '0');
  return '$d/$m/${date.year}';
}
