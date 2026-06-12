import 'package:tripolizoo/features/supervisor/supervisor_receiving_tasks/domain/receiving_task.dart';

class ReceivingTasksMockRepository {
  static const _groupName = 'القططية';
  static const _issuedBy = 'رئيس قسم المستشفى البيطري';

  static List<ReceivingTask> seedTasks() {
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);

    return [
      ReceivingTask(
        id: 'rt1',
        taskNumber: 'PK-3012',
        status: ReceivingTaskStatus.pending,
        taskType: ReceivingTaskType.afterHealthRelease,
        source: ReceivingTaskSource.quarantine,
        animalId: 'A-301',
        animalType: 'ظبي',
        animalGender: 'أنثى',
        groupName: _groupName,
        createdAt: today.add(const Duration(hours: 7, minutes: 30)),
        decisionType: ReceivingDecisionType.healthRelease,
        decisionDate: today.add(const Duration(hours: 7)),
        decisionIssuedBy: _issuedBy,
        decisionNotes: 'اكتملت فترة الحجر بدون أعراض',
      ),
      ReceivingTask(
        id: 'rt2',
        taskNumber: 'PK-3008',
        status: ReceivingTaskStatus.temporarilyUnable,
        taskType: ReceivingTaskType.afterTreatment,
        source: ReceivingTaskSource.hospital,
        animalId: 'A-112',
        animalType: 'نمر',
        animalGender: 'ذكر',
        groupName: _groupName,
        createdAt: today
            .subtract(const Duration(days: 1))
            .add(const Duration(hours: 12, minutes: 10)),
        decisionType: ReceivingDecisionType.afterTreatment,
        decisionDate: today
            .subtract(const Duration(days: 1))
            .add(const Duration(hours: 11, minutes: 30)),
        decisionIssuedBy: _issuedBy,
        decisionNotes: 'انتهى العلاج ويحتاج متابعة في المجموعة',
        delayReason: 'القفص غير جاهز حاليًا',
        delayExtraNote: 'يُتوقع جاهزية القفص غداً صباحاً',
        delayRecordedAt: today
            .subtract(const Duration(days: 1))
            .add(const Duration(hours: 13, minutes: 5)),
      ),
      ReceivingTask(
        id: 'rt3',
        taskNumber: 'PK-3001',
        status: ReceivingTaskStatus.received,
        taskType: ReceivingTaskType.afterHealthRelease,
        source: ReceivingTaskSource.quarantine,
        animalId: 'A-088',
        animalType: 'غزال',
        animalGender: 'أنثى',
        groupName: _groupName,
        createdAt: today
            .subtract(const Duration(days: 3))
            .add(const Duration(hours: 9)),
        decisionType: ReceivingDecisionType.healthRelease,
        decisionDate: today
            .subtract(const Duration(days: 3))
            .add(const Duration(hours: 8, minutes: 30)),
        decisionIssuedBy: _issuedBy,
        decisionNotes: 'إفراج بعد اكتمال فترة المراقبة',
        receiptNote: 'تم استلام الحيوان بحالة جيدة',
      ),
    ];
  }
}
