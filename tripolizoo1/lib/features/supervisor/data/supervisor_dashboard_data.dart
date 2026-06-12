/// بيانات تجريبية للوحة المشرف — تُستبدل لاحقًا بطبقة API.
class SupervisorDashboardData {
  const SupervisorDashboardData({
    required this.supervisorName,
    required this.groupName,
    required this.unreadNotifications,
    required this.pendingReceivingTasks,
    required this.activeDietRecommendations,
    required this.dietRecommendations,
  });

  final String supervisorName;
  final String groupName;
  final int unreadNotifications;
  final int pendingReceivingTasks;
  final int activeDietRecommendations;
  final List<DietRecommendation> dietRecommendations;

  static const mock = SupervisorDashboardData(
    supervisorName: 'محمد',
    groupName: 'القططية',
    unreadNotifications: 2,
    pendingReceivingTasks: 1,
    activeDietRecommendations: 3,
    dietRecommendations: [
      DietRecommendation(
        animalId: 'A-088',
        animalName: 'غزال',
        daysRemaining: 2,
        instruction: 'تقديم غذاء لين لمدة 3 أيام',
        doctorNote: 'تجنب الأعلاف الخشنة والحبوب الكاملة.',
      ),
      DietRecommendation(
        animalId: 'A-102',
        animalName: 'نمر',
        daysRemaining: 5,
        instruction: 'تقليل البروتين بنسبة 30%',
        doctorNote: 'مراقبة الشهية يومياً وتسجيل كمية الطعام.',
      ),
      DietRecommendation(
        animalId: 'A-115',
        animalName: 'قرد',
        daysRemaining: 1,
        instruction: 'إضافة مكمل فيتامين C',
        doctorNote: 'يُعطى مع الوجبة الصباحية فقط.',
      ),
    ],
  );
}

class DietRecommendation {
  const DietRecommendation({
    required this.animalId,
    required this.animalName,
    required this.daysRemaining,
    required this.instruction,
    required this.doctorNote,
  });

  final String animalId;
  final String animalName;
  final int daysRemaining;
  final String instruction;
  final String doctorNote;
}
