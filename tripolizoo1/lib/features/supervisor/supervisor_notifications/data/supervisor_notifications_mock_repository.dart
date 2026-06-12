import 'package:tripolizoo/features/supervisor/supervisor_notifications/domain/supervisor_notification.dart';

class SupervisorNotificationsMockRepository {
  static List<SupervisorNotification> seedNotifications() {
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);

    return [
      SupervisorNotification(
        id: 'n1',
        type: SupervisorNotificationType.dietRecommendation,
        title: 'توصية غذائية علاجية جديدة',
        description: 'تم إضافة توصية جديدة للحيوان A-088',
        createdAt: today.add(const Duration(hours: 9)),
        targetRoute: '/supervisor/home',
      ),
      SupervisorNotification(
        id: 'n2',
        type: SupervisorNotificationType.healthReportUpdate,
        title: 'تحديث حالة بلاغ صحي',
        description: 'البلاغ RP-2041 أصبح مُستلَم',
        createdAt: today.add(const Duration(hours: 11, minutes: 45)),
        targetRoute: '/supervisor/health-reports',
      ),
      SupervisorNotification(
        id: 'n3',
        type: SupervisorNotificationType.receivingTask,
        title: 'مهمة استلام جديدة',
        description: 'PK-3012 — استلام بعد إفراج صحي',
        createdAt: today.add(const Duration(hours: 7, minutes: 35)),
        isRead: true,
        targetRoute: '/supervisor/receiving-tasks',
      ),
      SupervisorNotification(
        id: 'n4',
        type: SupervisorNotificationType.newbornFollowUp,
        title: 'اكتمال متابعة مولود',
        description: 'تم اعتماد متابعة المولود الجديد للأم A-055',
        createdAt: today
            .subtract(const Duration(days: 1))
            .add(const Duration(hours: 15)),
        isRead: true,
        targetRoute: '/supervisor/group-followup',
      ),
      SupervisorNotification(
        id: 'n5',
        type: SupervisorNotificationType.mortalityApproval,
        title: 'اعتماد حالة نفوق',
        description: 'تم اعتماد تسجيل نفوق الحيوان A-099',
        createdAt: today
            .subtract(const Duration(days: 2))
            .add(const Duration(hours: 10)),
        isRead: true,
        targetRoute: '/supervisor/group-followup',
      ),
    ];
  }
}
