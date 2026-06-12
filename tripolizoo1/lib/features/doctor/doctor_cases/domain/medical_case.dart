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

  String get numberPrefix => switch (this) {
        MedicalCaseType.field => 'FC',
        MedicalCaseType.hospital => 'HC',
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

class MedicalProcedure {
  MedicalProcedure({
    required this.recordedAt,
    required this.diagnosis,
    required this.treatment,
    this.note,
  });

  final DateTime recordedAt;
  final String diagnosis;
  final String treatment;
  final String? note;
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
    this.gender,
    this.age,
    this.initialNote,
    this.procedures = const <MedicalProcedure>[],
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
  final DateTime openedAt;
  final DateTime updatedAt;
  final String sourceLabel;
  final List<MedicalProcedure> procedures;

  String get animalTitle => '$animalType • $animalId';

  String get reasonLine => 'سبب فتح الحالة: $openReason';

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
