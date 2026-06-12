import 'package:tripolizoo/features/doctor/doctor_quarantine/domain/quarantine_record.dart';

class QuarantineMockRepository {
  static List<QuarantineRecord> seedRecords() {
    return [
      QuarantineRecord(
        id: 'q1',
        tempNumber: 'Q-501',
        animalName: 'قرد مكاك',
        gender: 'ذكر',
        expectedGroup: 'الرئيسيات',
        entryDate: DateTime(2026, 6, 1),
        status: QuarantineStatus.underFollowUp,
        durationDays: 21,
        responsibleDoctor: 'د. أحمد',
        approximateAge: '~ 2 سنة',
        animalSource: 'مصادرة جمركية',
        generalNotes: 'وصل بحالة عامة مقبولة',
        lastVaccine: PreventiveVaccine(
          name: 'تطعيم داء الكلب',
          date: DateTime(2026, 6, 2),
          note: 'بدون مضاعفات',
          doctorName: 'د. أحمد',
        ),
        lastNoteDate: DateTime(2026, 6, 4),
        lastNoteText: 'مراقبة الشهية والنشاط يومياً',
      ),
      QuarantineRecord(
        id: 'q2',
        tempNumber: 'Q-502',
        animalName: 'ببغاء',
        gender: 'أنثى',
        expectedGroup: 'الطيور',
        entryDate: DateTime(2026, 6, 3),
        status: QuarantineStatus.underFollowUp,
        durationDays: 14,
        responsibleDoctor: 'د. أحمد',
        approximateAge: '~ 1 سنة',
        animalSource: 'إدخال جديد',
        generalNotes: 'يحتاج عزل صوتي عن بقية الطيور',
        lastNoteDate: DateTime(2026, 6, 5),
        lastNoteText: 'لا توجد أعراض مرضية ظاهرة',
      ),
    ];
  }
}
