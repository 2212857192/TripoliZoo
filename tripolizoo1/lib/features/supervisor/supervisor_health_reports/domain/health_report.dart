/// حالات البلاغ عند المشرف — للعرض والمتابعة فقط.
enum HealthReportStatus {
  sent,
  received,
  closed,
}

extension HealthReportStatusX on HealthReportStatus {
  String get label => switch (this) {
        HealthReportStatus.sent => 'مُرسَل',
        HealthReportStatus.received => 'مُستلَم',
        HealthReportStatus.closed => 'مُغلَق',
      };
}

/// بلاغ صحي يُرسل للطبيب — منفصل عن تسجيل الحالة الصحية.
class HealthReport {
  const HealthReport({
    required this.id,
    required this.reportNumber,
    required this.animalId,
    required this.animalType,
    required this.groupName,
    required this.description,
    required this.sentAt,
    required this.status,
    this.assignedDoctorName,
    this.doctorNote,
    this.doctorUpdatedAt,
    this.fieldCaseOpened = false,
    this.hasAttachment = false,
  });

  final String id;
  final String reportNumber;
  final String animalId;
  final String animalType;
  final String groupName;
  final String description;
  final DateTime sentAt;
  final HealthReportStatus status;
  final String? assignedDoctorName;
  final String? doctorNote;
  final DateTime? doctorUpdatedAt;
  final bool fieldCaseOpened;
  final bool hasAttachment;

  bool get hasDoctorFollowUp =>
      (doctorNote != null && doctorNote!.trim().isNotEmpty) ||
      doctorUpdatedAt != null ||
      fieldCaseOpened;

  String get animalLabel => '$animalId — $animalType';

  String get shortDescription {
    final text = description.trim();
    if (text.length <= 72) return text;
    return '${text.substring(0, 72)}…';
  }

  bool matchesQuery(String query) {
    if (query.isEmpty) return true;
    final q = query.trim().toLowerCase();
    return reportNumber.toLowerCase().contains(q) ||
        animalId.toLowerCase().contains(q);
  }
}
