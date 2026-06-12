enum SupervisorNotificationType {
  dietRecommendation,
  receivingTask,
  healthReportUpdate,
  newbornFollowUp,
  mortalityApproval,
}

extension SupervisorNotificationTypeX on SupervisorNotificationType {
  String get label => switch (this) {
        SupervisorNotificationType.dietRecommendation =>
          'توصية غذائية علاجية',
        SupervisorNotificationType.receivingTask => 'مهمة استلام',
        SupervisorNotificationType.healthReportUpdate => 'بلاغ صحي',
        SupervisorNotificationType.newbornFollowUp => 'متابعة مولود',
        SupervisorNotificationType.mortalityApproval => 'حالة نفوق',
      };
}

class SupervisorNotification {
  const SupervisorNotification({
    required this.id,
    required this.type,
    required this.title,
    required this.description,
    required this.createdAt,
    this.isRead = false,
    this.targetRoute,
  });

  final String id;
  final SupervisorNotificationType type;
  final String title;
  final String description;
  final DateTime createdAt;
  final bool isRead;
  final String? targetRoute;

  SupervisorNotification copyWith({bool? isRead}) {
    return SupervisorNotification(
      id: id,
      type: type,
      title: title,
      description: description,
      createdAt: createdAt,
      isRead: isRead ?? this.isRead,
      targetRoute: targetRoute,
    );
  }
}

enum SupervisorNotificationReadFilter {
  all,
  unread,
  read,
}
