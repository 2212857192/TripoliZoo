import 'package:tripolizoo/features/supervisor/supervisor_health_reports/domain/health_report.dart';

class HealthReportsMockRepository {
  static const _groupName = 'القططية';

  static List<HealthReport> seedReports() {
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);

    return [
      HealthReport(
        id: 'hr1',
        reportNumber: 'RP-2041',
        animalId: 'A-102',
        animalType: 'قط بري',
        groupName: _groupName,
        description: 'خمول واضح وامتناع عن الطعام منذ الصباح',
        sentAt: today.add(const Duration(hours: 10, minutes: 12)),
        status: HealthReportStatus.received,
        assignedDoctorName: 'د. سارة العبيدي',
        doctorNote: 'بدأت الفحص الميداني، يرجى عزل الحيوان مؤقتاً',
        doctorUpdatedAt: today.add(const Duration(hours: 11, minutes: 45)),
        fieldCaseOpened: true,
      ),
      HealthReport(
        id: 'hr2',
        reportNumber: 'RP-2042',
        animalId: 'A-088',
        animalType: 'غزال',
        groupName: _groupName,
        description: 'جروح سطحية في الرجل الخلفية مع تورّم خفيف.',
        sentAt: today.add(const Duration(hours: 8, minutes: 45)),
        status: HealthReportStatus.sent,
      ),
      HealthReport(
        id: 'hr3',
        reportNumber: 'RP-2038',
        animalId: 'A-055',
        animalType: 'غزال',
        groupName: _groupName,
        description: 'سعال متقطع منذ يومين مع انخفاض طفيف في النشاط.',
        sentAt: today
            .subtract(const Duration(days: 1))
            .add(const Duration(hours: 14, minutes: 20)),
        status: HealthReportStatus.closed,
        assignedDoctorName: 'د. سارة العبيدي',
        doctorNote: 'تمت معاينة الحيوان ولا يحتاج إلى إجراء إضافي.',
        doctorUpdatedAt: today
            .subtract(const Duration(days: 1))
            .add(const Duration(hours: 16, minutes: 10)),
      ),
      HealthReport(
        id: 'hr4',
        reportNumber: 'RP-2035',
        animalId: 'A-078',
        animalType: 'فهد',
        groupName: _groupName,
        description: 'حرارة مرتفعة وقلة شهية — يحتاج متابعة عاجلة.',
        sentAt: today
            .subtract(const Duration(days: 2))
            .add(const Duration(hours: 9, minutes: 5)),
        status: HealthReportStatus.closed,
        assignedDoctorName: 'د. سارة العبيدي',
        doctorNote: 'تم فتح حالة طبية ميدانية لمتابعة الحيوان.',
        doctorUpdatedAt: today
            .subtract(const Duration(days: 2))
            .add(const Duration(hours: 11, minutes: 30)),
        fieldCaseOpened: true,
        hasAttachment: true,
      ),
    ];
  }

  static String nextReportNumber(List<HealthReport> existing) {
    var max = 2030;
    for (final report in existing) {
      final digits = report.reportNumber.replaceAll(RegExp(r'[^0-9]'), '');
      final n = int.tryParse(digits);
      if (n != null && n > max) max = n;
    }
    return 'RP-${max + 1}';
  }
}
