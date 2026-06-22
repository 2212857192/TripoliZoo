enum DoctorNotificationReadFilter { all, unread, read }

enum DoctorNotificationType { quarantine, receivingDelay, healthReport }

class DoctorNotification {
  const DoctorNotification({
    required this.id,
    required this.type,
    required this.title,
    required this.message,
    this.caseNumber,
    this.taskNumber,
    this.reportNumber,
    required this.isRead,
    this.createdAt,
  });

  final int id;
  final DoctorNotificationType type;
  final String title;
  final String message;
  final String? caseNumber;
  final String? taskNumber;
  final String? reportNumber;
  final bool isRead;
  final DateTime? createdAt;

  String? get targetRoute {
    if (type == DoctorNotificationType.healthReport) {
      return '/doctor/reports';
    }

    if (type == DoctorNotificationType.receivingDelay) {
      final caseNumber = this.caseNumber;
      if (caseNumber != null && caseNumber.isNotEmpty) {
        return '/doctor/quarantine/$caseNumber';
      }
      return '/doctor/notifications';
    }

    final caseNumber = this.caseNumber;
    if (caseNumber == null || caseNumber.isEmpty) return null;
    return '/doctor/quarantine/$caseNumber';
  }

  DoctorNotification copyWith({bool? isRead}) {
    return DoctorNotification(
      id: id,
      type: type,
      title: title,
      message: message,
      caseNumber: caseNumber,
      taskNumber: taskNumber,
      reportNumber: reportNumber,
      isRead: isRead ?? this.isRead,
      createdAt: createdAt,
    );
  }
}
