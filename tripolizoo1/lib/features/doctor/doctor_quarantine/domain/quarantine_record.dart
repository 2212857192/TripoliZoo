enum QuarantineStatus {
  underFollowUp,
  released,
  failed,
}

extension QuarantineStatusX on QuarantineStatus {
  String get label => switch (this) {
        QuarantineStatus.underFollowUp => 'قيد المتابعة داخل الحجر',
        QuarantineStatus.released => 'تم الإفراج الصحي',
        QuarantineStatus.failed => 'لم تجتز الحجر',
      };
}

class QuarantineHealthNote {
  const QuarantineHealthNote({
    required this.date,
    required this.text,
    this.author,
  });

  final DateTime date;
  final String text;
  final String? author;
}

class PreventiveVaccine {
  PreventiveVaccine({
    required this.name,
    required this.date,
    this.note,
    this.doctorName,
  });

  final String name;
  final DateTime date;
  final String? note;
  final String? doctorName;
}

class QuarantineRecord {
  QuarantineRecord({
    required this.id,
    required this.tempNumber,
    required this.animalName,
    required this.gender,
    required this.expectedGroup,
    required this.entryDate,
    required this.status,
    required this.durationDays,
    required this.responsibleDoctor,
    this.animalCode,
    this.species,
    this.approximateAge,
    this.animalSource,
    this.initialHealthStatus,
    this.generalNotes,
    this.lastVaccine,
    this.lastNoteDate,
    this.lastNoteText,
    this.healthNotes = const [],
    this.preventiveVaccines = const [],
    this.canManage = false,
    this.photoUrl,
  });

  final String id;
  final String tempNumber;
  final String animalName;
  final String gender;
  final String expectedGroup;
  final DateTime entryDate;
  final QuarantineStatus status;
  final int durationDays;
  final String responsibleDoctor;
  final String? animalCode;
  final String? species;
  final String? approximateAge;
  final String? animalSource;
  final String? initialHealthStatus;
  final String? generalNotes;
  final PreventiveVaccine? lastVaccine;
  final DateTime? lastNoteDate;
  final String? lastNoteText;
  final List<QuarantineHealthNote> healthNotes;
  final List<PreventiveVaccine> preventiveVaccines;
  final bool canManage;
  final String? photoUrl;

  String get subtitle => '$gender • المجموعة المتوقعة: $expectedGroup';

  bool matchesQuery(String query) {
    if (query.isEmpty) return true;
    final q = query.trim().toLowerCase();
    return tempNumber.toLowerCase().contains(q) ||
        animalName.toLowerCase().contains(q) ||
        (animalCode?.toLowerCase().contains(q) ?? false) ||
        (species?.toLowerCase().contains(q) ?? false);
  }
}

String formatQuarantineDate(DateTime date) {
  final y = date.year;
  final m = date.month.toString().padLeft(2, '0');
  final d = date.day.toString().padLeft(2, '0');
  return '$y-$m-$d';
}
