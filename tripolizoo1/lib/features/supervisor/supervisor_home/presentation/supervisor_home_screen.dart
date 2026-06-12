import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/data/supervisor_dashboard_data.dart';
import 'package:tripolizoo/features/supervisor/supervisor_notifications/presentation/supervisor_notifications_provider.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_ui.dart';
import 'package:tripolizoo/features/supervisor/supervisor_home/presentation/widgets/diet_recommendations_section.dart';
import 'package:tripolizoo/features/supervisor/supervisor_home/presentation/widgets/quick_actions_section.dart';
import 'package:tripolizoo/features/supervisor/supervisor_home/presentation/widgets/summary_stat_card.dart';
import 'package:tripolizoo/features/supervisor/supervisor_home/presentation/widgets/supervisor_home_header.dart';
import 'package:tripolizoo/features/supervisor/supervisor_home/presentation/widgets/urgent_health_button.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_launcher.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_type.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class SupervisorHomeScreen extends StatefulWidget {
  const SupervisorHomeScreen({super.key});

  @override
  State<SupervisorHomeScreen> createState() => _SupervisorHomeScreenState();
}

class _SupervisorHomeScreenState extends State<SupervisorHomeScreen>
    with SingleTickerProviderStateMixin {
  late final AnimationController _fadeController;
  late final Animation<double> _fadeAnimation;

  @override
  void initState() {
    super.initState();
    _fadeController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 650),
    );
    _fadeAnimation = CurvedAnimation(
      parent: _fadeController,
      curve: Curves.easeOut,
    );
    _fadeController.forward();
  }

  @override
  void dispose() {
    _fadeController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    const data = SupervisorDashboardData.mock;
    final unreadNotifications =
        context.watch<SupervisorNotificationsProvider>().unreadCount;
    final bottomPad = MediaQuery.of(context).padding.bottom;

    return Scaffold(
      backgroundColor: SupervisorUi.background,
      body: FadeTransition(
        opacity: _fadeAnimation,
        child: SafeArea(
          top: false,
          bottom: false,
          child: SingleChildScrollView(
            physics: const BouncingScrollPhysics(),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // ─── Header — full width, no horizontal padding ───
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: SupervisorHomeHeader(
                    supervisorName: data.supervisorName,
                    groupName: data.groupName,
                    unreadNotifications: unreadNotifications,
                    onNotificationsTap: () =>
                        context.push('/supervisor/notifications'),
                  ),
                ),
                const SizedBox(height: 24),
                // ─── Body content — with horizontal padding ───
                Padding(
                  padding: EdgeInsets.fromLTRB(16, 0, 16, bottomPad + 96),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: SupervisorUi.cardDecoration(),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            const SupervisorSectionTitle(
                              eyebrow: 'Overview',
                              title: 'ملخص اليوم',
                            ),
                            const SizedBox(height: 16),
                            Row(
                              children: [
                                SummaryStatCard(
                                  count: data.pendingReceivingTasks,
                                  title: 'مهام الاستلام',
                                  subtitle: 'بانتظار الاستلام',
                                  icon: Icons.inventory_2_outlined,
                                  iconBg: const Color(0xFFE8F5E9),
                                  onTap: () => context.go(
                                    '/supervisor/receiving-tasks?filter=pending',
                                  ),
                                ),
                                const SizedBox(width: 12),
                                SummaryStatCard(
                                  count: data.activeDietRecommendations,
                                  title: 'التوصيات الغذائية',
                                  subtitle: 'نشطة حالياً',
                                  icon: Icons.restaurant_outlined,
                                  iconBg: const Color(0xFFE8F5E9),
                                  onTap: () => _showComingSoon(
                                    context,
                                    'التوصيات الغذائية العلاجية',
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),
                      QuickActionsSection(
                        actions: [
                          QuickAction(
                            icon: Icons.medical_services_outlined,
                            label: 'تسجيل حالة صحية',
                            iconBg: const Color(0xFFE8F5E9),
                            onTap: () => _openForm(
                              context,
                              SupervisorFormType.health,
                            ),
                          ),
                          QuickAction(
                            icon: Icons.child_care_outlined,
                            label: 'تسجيل ولادة جديدة',
                            iconBg: const Color(0xFFE8F5E9),
                            onTap: () => _openForm(
                              context,
                              SupervisorFormType.birth,
                            ),
                          ),
                          QuickAction(
                            icon: Icons.monitor_heart_outlined,
                            label: 'تسجيل حالة نفوق',
                            iconBg: const Color(0xFFE8F5E9),
                            onTap: () => _openForm(
                              context,
                              SupervisorFormType.mortality,
                            ),
                          ),
                          QuickAction(
                            icon: Icons.edit_note_outlined,
                            label: 'تسجيل ملاحظة تشغيلية',
                            iconBg: const Color(0xFFE8F5E9),
                            onTap: () => _openForm(
                              context,
                              SupervisorFormType.operationalNote,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),
                      UrgentHealthButton(
                        onTap: () => _openUrgentReport(context),
                      ),
                      const SizedBox(height: 24),
                      DietRecommendationsSection(
                        recommendations: data.dietRecommendations,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Future<void> _openForm(BuildContext context, SupervisorFormType type) async {
    final saved = await openSupervisorForm(context, type);
    if (!context.mounted || saved != true) return;
    showSupervisorSuccessSnackBar(
      context,
      message: 'تم التسجيل بنجاح في سجل المتابعة',
    );
  }

  Future<void> _openUrgentReport(BuildContext context) async {
    final sent = await openSupervisorForm(
      context,
      SupervisorFormType.urgentHealth,
    );
    if (!context.mounted || sent != true) return;
    showSupervisorSuccessSnackBar(
      context,
      message: 'تم إرسال البلاغ للطبيب',
    );
  }

  void _showComingSoon(BuildContext context, String feature) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('قريبًا: $feature'),
        backgroundColor: AppColors.primary,
      ),
    );
  }
}
