/// حالات مهمة الاستلام عند المشرف.
enum ReceivingTaskStatus {
  pending,
  temporarilyUnable,
  received,
}

extension ReceivingTaskStatusX on ReceivingTaskStatus {
  String get label => switch (this) {
        ReceivingTaskStatus.pending => 'بانتظار الاستلام',
        ReceivingTaskStatus.temporarilyUnable => 'تعذر مؤقتًا',
        ReceivingTaskStatus.received => 'تم الاستلام',
      };
}

/// نوع مهمة الاستلام.
enum ReceivingTaskType {
  afterHealthRelease,
  afterTreatment,
}

extension ReceivingTaskTypeX on ReceivingTaskType {
  String get label => switch (this) {
        ReceivingTaskType.afterHealthRelease => 'استلام بعد إفراج صحي',
        ReceivingTaskType.afterTreatment => 'استلام بعد علاج',
      };
}

/// مصدر الاستلام.
enum ReceivingTaskSource {
  quarantine,
  hospital,
}

extension ReceivingTaskSourceX on ReceivingTaskSource {
  String get label => switch (this) {
        ReceivingTaskSource.quarantine => 'الحجر الصحي',
        ReceivingTaskSource.hospital => 'المستشفى',
      };

  String get fromLabel => switch (this) {
        ReceivingTaskSource.quarantine => 'من الحجر الصحي',
        ReceivingTaskSource.hospital => 'من المستشفى',
      };
}

/// نوع القرار المرتبط بالمهمة.
enum ReceivingDecisionType {
  healthRelease,
  afterTreatment,
}

extension ReceivingDecisionTypeX on ReceivingDecisionType {
  String get label => switch (this) {
        ReceivingDecisionType.healthRelease => 'إفراج صحي',
        ReceivingDecisionType.afterTreatment => 'خروج بعد العلاج',
      };
}

/// مهمة استلام حيوان — تُنشأ تلقائياً ولا يُنشئها المشرف.
class ReceivingTask {
  const ReceivingTask({
    required this.id,
    required this.taskNumber,
    required this.status,
    required this.taskType,
    required this.source,
    required this.animalId,
    required this.animalType,
    required this.groupName,
    required this.createdAt,
    required this.decisionType,
    required this.decisionDate,
    required this.decisionIssuedBy,
    this.animalGender,
    this.animalImageUrl,
    this.decisionNotes,
    this.delayReason,
    this.delayExtraNote,
    this.delayRecordedAt,
    this.receiptNote,
  });

  final String id;
  final String taskNumber;
  final ReceivingTaskStatus status;
  final ReceivingTaskType taskType;
  final ReceivingTaskSource source;
  final String animalId;
  final String animalType;
  final String? animalGender;
  final String groupName;
  final String? animalImageUrl;
  final DateTime createdAt;
  final ReceivingDecisionType decisionType;
  final DateTime decisionDate;
  final String decisionIssuedBy;
  final String? decisionNotes;
  final String? delayReason;
  final String? delayExtraNote;
  final DateTime? delayRecordedAt;
  final String? receiptNote;

  String get animalLabel => '$animalId — $animalType';

  String get summaryLine => '${taskType.label} · ${source.fromLabel}';

  bool get canConfirmReceipt =>
      status == ReceivingTaskStatus.pending ||
      status == ReceivingTaskStatus.temporarilyUnable;

  bool get canRecordDelay => status == ReceivingTaskStatus.pending;

  bool get showDelayInfo =>
      status == ReceivingTaskStatus.temporarilyUnable &&
      delayReason != null &&
      delayReason!.trim().isNotEmpty;

  bool matchesQuery(String query) {
    if (query.isEmpty) return true;
    final q = query.trim().toLowerCase();
    return taskNumber.toLowerCase().contains(q) ||
        animalId.toLowerCase().contains(q);
  }

  ReceivingTask copyWith({
    ReceivingTaskStatus? status,
    String? delayReason,
    String? delayExtraNote,
    DateTime? delayRecordedAt,
    String? receiptNote,
    bool clearDelay = false,
  }) {
    return ReceivingTask(
      id: id,
      taskNumber: taskNumber,
      status: status ?? this.status,
      taskType: taskType,
      source: source,
      animalId: animalId,
      animalType: animalType,
      animalGender: animalGender,
      groupName: groupName,
      animalImageUrl: animalImageUrl,
      createdAt: createdAt,
      decisionType: decisionType,
      decisionDate: decisionDate,
      decisionIssuedBy: decisionIssuedBy,
      decisionNotes: decisionNotes,
      delayReason: clearDelay ? null : (delayReason ?? this.delayReason),
      delayExtraNote:
          clearDelay ? null : (delayExtraNote ?? this.delayExtraNote),
      delayRecordedAt:
          clearDelay ? null : (delayRecordedAt ?? this.delayRecordedAt),
      receiptNote: receiptNote ?? this.receiptNote,
    );
  }
}

/// يحوّل query parameter إلى فلتر حالة المهمة.
ReceivingTaskStatus? receivingTaskStatusFromQuery(String? value) {
  return switch (value) {
    'pending' => ReceivingTaskStatus.pending,
    'delayed' || 'temporarily-unable' => ReceivingTaskStatus.temporarilyUnable,
    'received' => ReceivingTaskStatus.received,
    _ => null,
  };
}
