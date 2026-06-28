import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/data/doctor_dashboard_api_repository.dart';
import 'package:tripolizoo/features/doctor/doctor_notifications/presentation/doctor_notifications_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/quarantine_provider.dart';
import 'package:tripolizoo/features/doctor/presentation/doctor_dashboard_provider.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_form_launcher.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
import 'package:tripolizoo/features/visitor/visitor_auth/presentation/auth_provider.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class DoctorHomeScreen extends StatefulWidget {
  const DoctorHomeScreen({super.key});

  @override
  State<DoctorHomeScreen> createState() => _DoctorHomeScreenState();
}

class _DoctorHomeScreenState extends State<DoctorHomeScreen>
    with SingleTickerProviderStateMixin {
  late final AnimationController _fadeController;
  late final Animation<double> _fadeAnimation;
  final _scrollController = ScrollController();
  bool _headerScrolled = false;

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
    _scrollController.addListener(_handleScroll);
    WidgetsBinding.instance.addPostFrameCallback((_) => _refresh());
  }

  void _handleScroll() {
    final isScrolled = _scrollController.offset > 24;
    if (isScrolled != _headerScrolled) {
      setState(() => _headerScrolled = isScrolled);
    }
  }

  Future<void> _refresh() async {
    await Future.wait([
      context.read<DoctorDashboardProvider>().load(),
      context.read<QuarantineProvider>().load(),
    ]);
  }

  String _firstName(String fullName) {
    final trimmed = fullName.trim();
    if (trimmed.isEmpty) return 'طبيب';
    return trimmed.split(RegExp(r'\s+')).first;
  }

  @override
  void dispose() {
    _fadeController.dispose();
    _scrollController.removeListener(_handleScroll);
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final bottomPad = MediaQuery.of(context).padding.bottom;

    final dashboard = context.watch<DoctorDashboardProvider>();
    final authUser = context.watch<AuthProvider>().user;
    final doctorName = _firstName(
      dashboard.data?.doctorName ?? authUser?.name ?? 'طبيب',
    );
    final groupName = dashboard.data?.groupName ?? authUser?.assignedGroup;
    final quarantineProvider = context.watch<QuarantineProvider>();
    final quarantineCases = dashboard.data != null
        ? dashboard.quarantineActiveCount
        : quarantineProvider.activeCount;
    final pendingHealthReports = dashboard.pendingHealthReportsCount;
    final notificationUnread =
        context.watch<DoctorNotificationsProvider>().unreadCount;
    final notificationBadgeCount = notificationUnread > pendingHealthReports
        ? notificationUnread
        : pendingHealthReports;
    final alerts = dashboard.alerts;
    final dashboardError = dashboard.errorMessage;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Directionality(
        textDirection: TextDirection.rtl,
        child: Scaffold(
          backgroundColor: DoctorUi.background,
          body: FadeTransition(
            opacity: _fadeAnimation,
            child: SafeArea(
              top: false,
              bottom: false,
              child: RefreshIndicator(
                onRefresh: _refresh,
                color: AppColors.primary,
                child: CustomScrollView(
                  physics: const AlwaysScrollableScrollPhysics(
                    parent: BouncingScrollPhysics(),
                  ),
                  slivers: [
                  // ─── Header Section ───
                  SliverToBoxAdapter(
                    child: _buildTopHeader(
                      context,
                      doctorName,
                      groupName,
                      notificationBadgeCount,
                    ),
                  ),
                  
                  // ─── Body Content ───
                  SliverPadding(
                    padding: EdgeInsets.fromLTRB(16, 16, 16, bottomPad + 96),
                    sliver: SliverList(
                      delegate: SliverChildListDelegate([
                        if (dashboardError != null) ...[
                          _ErrorBanner(message: dashboardError),
                          const SizedBox(height: 12),
                        ],
                        _buildOverviewSection(
                          context,
                          pendingHealthReports: pendingHealthReports,
                          quarantine: quarantineCases,
                          fieldCases: dashboard.activeFieldCasesCount,
                          hospitalCases: dashboard.activeHospitalCasesCount,
                        ),
                        const SizedBox(height: 20),

                        // ─── Quick Actions Section ───
                        _buildQuickActionsSection(context),
                        const SizedBox(height: 20),

                        // ─── Alerts Section ───
                        _buildAlertsSection(context, alerts),
                      ]),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    ),
    );
  }

  // ─────────────────────────────────────────────
  // Renders the Clean Premium Header
  // ─────────────────────────────────────────────
  Widget _buildTopHeader(
    BuildContext context,
    String name,
    String? groupName,
    int notificationBadgeCount,
  ) {
    final topPad = MediaQuery.of(context).padding.top;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Container(
        padding: EdgeInsets.fromLTRB(16, topPad + 16, 16, 20),
        decoration: BoxDecoration(
          gradient: AppColors.headerGradient,
          borderRadius: const BorderRadius.vertical(
            bottom: Radius.circular(32),
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 16,
              offset: const Offset(0, 8),
            ),
            BoxShadow(
              color: AppColors.primary.withValues(alpha: 0.03),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
          border: Border(
            bottom: BorderSide(
              color: AppColors.primary.withValues(alpha: 0.12),
              width: 1.2,
            ),
          ),
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // Avatar with initials
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                gradient: AppColors.primaryGradient,
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withValues(alpha: 0.2),
                    blurRadius: 8,
                    offset: const Offset(0, 3),
                  ),
                ],
              ),
              child: Center(
                child: Text(
                  name.isNotEmpty ? name[0] : 'أ',
                  style: GoogleFonts.cairo(
                    color: Colors.white,
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 14),
            // Text details
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    'مرحبًا، د. $name',
                    style: GoogleFonts.cairo(
                      fontSize: 16.5,
                      fontWeight: FontWeight.w900,
                      color: AppColors.textPrimary,
                      height: 1.2,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 3.5,
                    ),
                    decoration: BoxDecoration(
                      color: AppColors.primary.withValues(alpha: 0.08),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: AppColors.primary.withValues(alpha: 0.18),
                        width: 1,
                      ),
                    ),
                    child: Text(
                      groupName != null && groupName.isNotEmpty
                          ? 'طبيب مجموعة $groupName'
                          : 'القطاع الطبي والرعاية الصحية',
                      style: GoogleFonts.cairo(
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                        color: AppColors.primaryDark,
                        height: 1,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 12),
            // Notification bell
            _buildNotificationBell(context, notificationBadgeCount),
          ],
        ),
      ),
    );
  }

  Widget _buildNotificationBell(BuildContext context, int unreadCount) {
    return GestureDetector(
      onTap: () => context.push('/doctor/notifications'),
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: Colors.white,
              shape: BoxShape.circle,
              border: Border.all(
                color: const Color(0xFFE2E8F0),
                width: 1.5,
              ),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.03),
                  blurRadius: 4,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: const Icon(
              Icons.notifications_outlined,
              color: Color(0xFF64748B),
              size: 20,
            ),
          ),
          if (unreadCount > 0)
            Positioned(
              top: -1,
              left: -1,
              child: Container(
                width: unreadCount > 9 ? 18 : 16,
                height: 16,
                padding: unreadCount > 9
                    ? const EdgeInsets.symmetric(horizontal: 3)
                    : EdgeInsets.zero,
                decoration: BoxDecoration(
                  color: AppColors.accent,
                  shape: BoxShape.rectangle,
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(
                    color: Colors.white,
                    width: 2,
                  ),
                ),
                child: Center(
                  child: Text(
                    unreadCount > 9 ? '9+' : '$unreadCount',
                    style: GoogleFonts.cairo(
                      color: Colors.white,
                      fontSize: 8,
                      fontWeight: FontWeight.w900,
                      height: 1,
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  // ─────────────────────────────────────────────
  // Renders the 2x2 Overview Grid Card
  // ─────────────────────────────────────────────
  Widget _buildOverviewSection(
    BuildContext context, {
    required int pendingHealthReports,
    required int quarantine,
    required int fieldCases,
    required int hospitalCases,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: DoctorUi.cardDecoration(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          const DoctorSectionTitle(
            eyebrow: 'Overview',
            title: 'ملخص المهام الطبية',
          ),
          const SizedBox(height: 16),
          GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: 2,
            mainAxisSpacing: 10,
            crossAxisSpacing: 10,
            childAspectRatio: 1.35,
            children: [
              _StatTile(
                count: pendingHealthReports,
                title: 'البلاغات',
                subtitle: 'بلاغات جديدة بانتظار الاطلاع',
                icon: Icons.assignment_outlined,
                onTap: () => context.go('/doctor/reports'),
              ),
              _StatTile(
                count: quarantine,
                title: 'الحجر الصحي',
                subtitle: 'حيوانات تحت الملاحظة',
                icon: Icons.security_outlined,
                onTap: () => context.go('/doctor/quarantine'),
              ),
              _StatTile(
                count: fieldCases,
                title: 'الحالات الميدانية',
                subtitle: 'حالات نشطة في المجموعة',
                icon: Icons.medical_services_outlined,
                onTap: () => context.go('/doctor/cases'),
              ),
              _StatTile(
                count: hospitalCases,
                title: 'حالات المستشفى',
                subtitle: 'حالات قيد المتابعة',
                icon: Icons.local_hospital_outlined,
                onTap: () => context.go('/doctor/cases'),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ─────────────────────────────────────────────
  // Renders the Quick Actions Section
  // ─────────────────────────────────────────────
  Widget _buildQuickActionsSection(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: DoctorUi.cardDecoration(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          const DoctorSectionTitle(
            eyebrow: 'Actions',
            title: 'إجراءات سريعة',
          ),
          const SizedBox(height: 16),
          // الزر الرئيسي لفتح حالة طبية ميدانية بتصميم متدرج وظلال
          Container(
            decoration: BoxDecoration(
              gradient: AppColors.primaryGradient,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: AppColors.primary.withValues(alpha: 0.25),
                  blurRadius: 12,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Material(
              color: Colors.transparent,
              borderRadius: BorderRadius.circular(16),
              child: InkWell(
                onTap: () => _openFieldCaseForm(context),
                borderRadius: BorderRadius.circular(16),
                splashColor: Colors.white.withValues(alpha: 0.15),
                highlightColor: Colors.white.withValues(alpha: 0.05),
                child: Container(
                  height: 52,
                  alignment: Alignment.center,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(
                        Icons.add_circle_outline_rounded,
                        color: Colors.white,
                        size: 20,
                      ),
                      const SizedBox(width: 8),
                      Text(
                        'فتح حالة طبية ميدانية',
                        style: GoogleFonts.cairo(
                          fontSize: 14.5,
                          fontWeight: FontWeight.w800,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ─────────────────────────────────────────────
  // Renders the Alerts Section
  // ─────────────────────────────────────────────
  Widget _buildAlertsSection(
    BuildContext context,
    List<DoctorDashboardAlert> alerts,
  ) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: DoctorUi.cardDecoration(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          const DoctorSectionTitle(
            eyebrow: 'Alerts',
            title: 'تنبيهات تحتاج متابعة',
          ),
          const SizedBox(height: 14),
          if (alerts.isEmpty)
            Text(
              'لا توجد تنبيهات جديدة',
              style: GoogleFonts.cairo(
                fontSize: 12.5,
                fontWeight: FontWeight.w600,
                color: DoctorUi.muted,
              ),
            )
          else
            ...alerts.expand((alert) sync* {
              yield _AlertTile(
                title: alert.title,
                subtitle: alert.subtitle,
                isUrgent: alert.urgent,
                onTap: () {
                  final caseNumber = alert.caseNumber;
                  if (caseNumber != null && caseNumber.isNotEmpty) {
                    context.go('/doctor/quarantine/$caseNumber');
                  } else {
                    context.go('/doctor/quarantine');
                  }
                },
              );
              yield const SizedBox(height: 10);
            }),
        ],
      ),
    );
  }

  Future<void> _openFieldCaseForm(BuildContext context) async {
    final caseId = await openDoctorFieldCaseForm(context);
    if (!context.mounted || caseId == null) return;
    showDoctorSuccessSnackBar(context, message: 'تم فتح الحالة بنجاح');
    context.push('/doctor/cases/$caseId');
  }
}

// ─────────────────────────────────────────────
// Error banner
// ─────────────────────────────────────────────
class _ErrorBanner extends StatelessWidget {
  const _ErrorBanner({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: const Color(0xFFFFF5F5),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFFFE3E3)),
      ),
      child: Row(
        children: [
          const Icon(Icons.error_outline, color: Color(0xFFDC2626), size: 20),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              message,
              style: GoogleFonts.cairo(
                fontSize: 12.5,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF991B1B),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────
// Stat Card Private Widget (No Expanded at root)
// ─────────────────────────────────────────────
class _StatTile extends StatelessWidget {
  const _StatTile({
    required this.count,
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.onTap,
  });

  final int count;
  final String title;
  final String subtitle;
  final IconData icon;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAF8),
        borderRadius: BorderRadius.circular(DoctorUi.cardRadiusSm),
        border: Border.all(
          color: DoctorUi.border,
          width: 1.2,
        ),
        boxShadow: DoctorUi.softShadow,
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(DoctorUi.cardRadiusSm),
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Container(
                      width: 32,
                      height: 32,
                      decoration: BoxDecoration(
                        color: AppColors.primary.withValues(alpha: 0.08),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Icon(
                        icon,
                        color: AppColors.primary,
                        size: 16,
                      ),
                    ),
                    const Spacer(),
                    Text(
                      '$count',
                      style: GoogleFonts.cairo(
                        fontSize: 24,
                        fontWeight: FontWeight.w900,
                        color: AppColors.primary,
                        height: 1,
                      ),
                    ),
                  ],
                ),
                const Spacer(),
                Text(
                  title,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.cairo(
                    fontSize: 12,
                    fontWeight: FontWeight.w800,
                    color: DoctorUi.textPrimary,
                    height: 1.2,
                  ),
                ),
                const SizedBox(height: 1),
                Text(
                  subtitle,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.cairo(
                    fontSize: 9.5,
                    fontWeight: FontWeight.w600,
                    color: DoctorUi.muted,
                    height: 1.1,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────
// Alert List Item Widget
// ─────────────────────────────────────────────
class _AlertTile extends StatelessWidget {
  const _AlertTile({
    required this.title,
    required this.subtitle,
    required this.isUrgent,
    required this.onTap,
  });

  final String title;
  final String subtitle;
  final bool isUrgent;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final bgColor = isUrgent ? const Color(0xFFFFF6F6) : Colors.white;
    final borderColor = isUrgent ? const Color(0xFFFEE2E2) : DoctorUi.border;
    final accentColor = isUrgent ? Colors.red.shade700 : AppColors.primary;

    return Container(
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(DoctorUi.cardRadiusSm),
        border: Border.all(color: borderColor, width: 1.2),
        boxShadow: DoctorUi.softShadow,
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(DoctorUi.cardRadiusSm),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            child: Row(
              children: [
                // Indicator point
                Container(
                  width: 8,
                  height: 8,
                  decoration: BoxDecoration(
                    color: accentColor,
                    shape: BoxShape.circle,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        title,
                        style: GoogleFonts.cairo(
                          fontSize: 13,
                          fontWeight: FontWeight.w800,
                          color: DoctorUi.textPrimary,
                          height: 1.2,
                        ),
                      ),
                      const SizedBox(height: 3),
                      Text(
                        subtitle,
                        style: GoogleFonts.cairo(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: DoctorUi.muted,
                          height: 1.2,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 8),
                // "View" text button
                Text(
                  'عرض',
                  style: GoogleFonts.cairo(
                    fontSize: 12,
                    fontWeight: FontWeight.w800,
                    color: accentColor,
                  ),
                ),
                const SizedBox(width: 2),
                Icon(
                  Icons.chevron_left_rounded,
                  color: accentColor,
                  size: 16,
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
