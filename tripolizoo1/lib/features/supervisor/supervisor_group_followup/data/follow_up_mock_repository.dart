import 'package:tripolizoo/features/supervisor/supervisor_group_followup/domain/follow_up_entry.dart';

/// بيانات تجريبية أولية لسجل المتابعة.
class FollowUpMockRepository {
  static List<FollowUpEntry> seedEntries() {
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    final yesterday = today.subtract(const Duration(days: 1));

    return [
      HealthFollowUpEntry(
        id: 'h1',
        registeredAt: today.add(const Duration(hours: 9, minutes: 30)),
        animalId: 'A-102',
        animalType: 'نمر',
        description: 'خمول وقلة أكل',
        followUpKind: HealthFollowUpKind.needsReferral,
        fullDescription:
            'خمول وقلة أكل منذ الصباح مع تجنب التفاعل مع بقية المجموعة.',
        extraNotes: 'تم عزل الحيوان مؤقتاً في الزاوية الشمالية.',
      ),
      BirthFollowUpEntry(
        id: 'b1',
        registeredAt: today.add(const Duration(hours: 8, minutes: 10)),
        motherId: 'A-055',
        animalType: 'غزال',
        birthDate: today,
        birthCount: 2,
        newborns: const [
          NewbornRecord(gender: NewbornGender.male),
          NewbornRecord(
            gender: NewbornGender.female,
            distinguishingMark: 'بقعة بيضاء على الرأس',
          ),
        ],
      ),
      MortalityFollowUpEntry(
        id: 'm1',
        registeredAt: today.add(const Duration(hours: 11, minutes: 45)),
        victimKind: MortalityVictimKind.zooAnimal,
        animalId: 'A-099',
        animalType: 'قرد',
      ),
      OperationalNoteEntry(
        id: 'o1',
        registeredAt: today.add(const Duration(hours: 12, minutes: 30)),
        noteKind: OperationalNoteKind.feeding,
        summary: 'تأخر وصول الغذاء للمجموعة',
        fullText:
            'تأخر وصول الغذاء للمجموعة بحوالي 45 دقيقة عن الموعد المعتاد بسبب تأخر التوريد.',
      ),
      HealthFollowUpEntry(
        id: 'h2',
        registeredAt: yesterday.add(const Duration(hours: 14, minutes: 15)),
        animalId: 'A-078',
        animalType: 'فهد',
        description: 'جروح سطحية في الرجل الخلفية',
        followUpKind: HealthFollowUpKind.noReferral,
      ),
      OperationalNoteEntry(
        id: 'o2',
        registeredAt: yesterday.add(const Duration(hours: 16, minutes: 40)),
        noteKind: OperationalNoteKind.feeding,
        summary: 'تأخر وصول وجبة الصباح 15 دقيقة',
      ),
      MortalityFollowUpEntry(
        id: 'm2',
        registeredAt: yesterday.add(const Duration(hours: 7, minutes: 20)),
        victimKind: MortalityVictimKind.newbornUnderFollowUp,
        animalId: 'N-012',
        animalType: 'غزال',
        deathCause: 'ضعف ولادة',
      ),
    ];
  }
}
