import 'package:tripolizoo/features/doctor/doctor_cases/domain/medical_case.dart';

class MedicalCasesMockRepository {
  static List<MedicalCase> seedCases() {
    return [
      MedicalCase(
        id: 'mc1',
        caseNumber: 'FC-310',
        type: MedicalCaseType.field,
        status: MedicalCaseStatus.active,
        animalId: 'A-217',
        animalType: 'غزال',
        animalGroup: 'آكلات الأعشاب',
        gender: 'أنثى',
        age: '3 سنوات',
        openReason: 'عرج خفيف في القائمة الخلفية',
        openedAt: DateTime(2026, 6, 6, 14, 40),
        updatedAt: DateTime(2026, 6, 6, 15, 20),
        sourceLabel: 'من بلاغ صحي',
        procedures: [
          MedicalProcedure(
            recordedAt: DateTime(2026, 6, 6, 15, 20),
            diagnosis: 'التواء بسيط في المفصل',
            treatment: 'راحة + مضاد التهاب موضعي مرتين يومياً',
            note: 'مراقبة الحركة خلال 48 ساعة',
          ),
        ],
      ),
      MedicalCase(
        id: 'mc2',
        caseNumber: 'FC-305',
        type: MedicalCaseType.field,
        status: MedicalCaseStatus.closed,
        animalId: 'A-330',
        animalType: 'نعامة',
        animalGroup: 'الطيور',
        openReason: 'تساقط ريش غير طبيعي',
        openedAt: DateTime(2026, 6, 4, 10, 15),
        updatedAt: DateTime(2026, 6, 5, 9, 30),
        sourceLabel: 'فتح يدوي',
      ),
      MedicalCase(
        id: 'mc3',
        caseNumber: 'HC-120',
        type: MedicalCaseType.hospital,
        status: MedicalCaseStatus.active,
        animalId: 'A-088',
        animalType: 'نمر',
        animalGroup: 'القططية',
        gender: 'ذكر',
        age: '5 سنوات',
        openReason: 'التهاب رئوي يحتاج علاج داخلي',
        openedAt: DateTime(2026, 6, 5, 8, 0),
        updatedAt: DateTime(2026, 6, 6, 11, 0),
        sourceLabel: 'تحويل من الحالة الميدانية',
        procedures: [
          MedicalProcedure(
            recordedAt: DateTime(2026, 6, 6, 11, 0),
            diagnosis: 'التهاب رئوي خفيف إلى متوسط',
            treatment: 'مضاد حيوي + سوائل وريدية',
            note: 'متابعة حرارة الحيوان كل 6 ساعات',
          ),
        ],
      ),
    ];
  }

  static String nextCaseNumber(
    List<MedicalCase> existing,
    MedicalCaseType type,
  ) {
    final prefix = type.numberPrefix;
    var max = 300;
    for (final c in existing.where((c) => c.type == type)) {
      final digits = c.caseNumber.replaceAll(RegExp(r'[^0-9]'), '');
      final n = int.tryParse(digits);
      if (n != null && n > max) max = n;
    }
    return '$prefix-${max + 1}';
  }
}
