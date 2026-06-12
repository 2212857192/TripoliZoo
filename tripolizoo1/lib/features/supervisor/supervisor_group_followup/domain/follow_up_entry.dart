/// أنواع التسجيلات في سجل المتابعة.
enum FollowUpEntryType {
  health,
  birth,
  mortality,
  operationalNote,
}

enum HealthFollowUpKind {
  needsReferral,
  noReferral,
}

enum MortalityVictimKind {
  zooAnimal,
  newbornUnderFollowUp,
}

enum OperationalNoteKind {
  feeding,
  general,
}

enum NewbornGender {
  male,
  female,
}

class NewbornRecord {
  const NewbornRecord({
    required this.gender,
    this.distinguishingMark,
    this.note,
  });

  final NewbornGender gender;
  final String? distinguishingMark;
  final String? note;

  String get genderLabel => switch (gender) {
        NewbornGender.male => 'ذكر',
        NewbornGender.female => 'أنثى',
      };
}

/// سجل متابعة واحد — للتوثيق فقط، بدون حالات إدارية.
sealed class FollowUpEntry {
  const FollowUpEntry({
    required this.id,
    required this.registeredAt,
  });

  final String id;
  final DateTime registeredAt;

  FollowUpEntryType get type;
}

class HealthFollowUpEntry extends FollowUpEntry {
  const HealthFollowUpEntry({
    required super.id,
    required super.registeredAt,
    required this.animalId,
    this.animalType,
    required this.description,
    required this.followUpKind,
    this.fullDescription,
    this.extraNotes,
    this.hasAttachment = false,
  });

  final String animalId;
  final String? animalType;
  final String description;
  final HealthFollowUpKind followUpKind;
  final String? fullDescription;
  final String? extraNotes;
  final bool hasAttachment;

  @override
  FollowUpEntryType get type => FollowUpEntryType.health;

  bool get isExpandable =>
      (fullDescription != null && fullDescription!.length > description.length) ||
      (extraNotes != null && extraNotes!.isNotEmpty) ||
      hasAttachment;
}

class BirthFollowUpEntry extends FollowUpEntry {
  const BirthFollowUpEntry({
    required super.id,
    required super.registeredAt,
    required this.motherId,
    this.animalType,
    required this.birthDate,
    required this.birthCount,
    required this.newborns,
  });

  final String motherId;
  final String? animalType;
  final DateTime birthDate;
  final int birthCount;
  final List<NewbornRecord> newborns;

  @override
  FollowUpEntryType get type => FollowUpEntryType.birth;

  bool get isExpandable => newborns.isNotEmpty;
}

class MortalityFollowUpEntry extends FollowUpEntry {
  const MortalityFollowUpEntry({
    required super.id,
    required super.registeredAt,
    required this.victimKind,
    required this.animalId,
    this.animalType,
    this.deathCause,
    this.extraNotes,
    this.hasAttachment = false,
  });

  final MortalityVictimKind victimKind;
  final String animalId;
  final String? animalType;
  final String? deathCause;
  final String? extraNotes;
  final bool hasAttachment;

  @override
  FollowUpEntryType get type => FollowUpEntryType.mortality;

  String get displayCause =>
      (deathCause == null || deathCause!.trim().isEmpty)
          ? 'غير ظاهر'
          : deathCause!;

  bool get isExpandable =>
      (extraNotes != null && extraNotes!.isNotEmpty) || hasAttachment;
}

class OperationalNoteEntry extends FollowUpEntry {
  const OperationalNoteEntry({
    required super.id,
    required super.registeredAt,
    required this.noteKind,
    required this.summary,
    this.fullText,
    this.extraNotes,
    this.hasAttachment = false,
  });

  final OperationalNoteKind noteKind;
  final String summary;
  final String? fullText;
  final String? extraNotes;
  final bool hasAttachment;

  @override
  FollowUpEntryType get type => FollowUpEntryType.operationalNote;

  bool get isExpandable =>
      (fullText != null && fullText!.length > summary.length) ||
      (extraNotes != null && extraNotes!.isNotEmpty) ||
      hasAttachment;
}
