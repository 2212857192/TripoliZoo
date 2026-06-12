import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/doctor/doctor_cases/presentation/medical_cases_provider.dart';
import 'package:tripolizoo/features/doctor/doctor_quarantine/presentation/quarantine_provider.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_form_launcher.dart';
import 'package:tripolizoo/features/doctor/shared/doctor_ui.dart';
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
    final bottomPad = MediaQuery.of(context).padding.bottom;

    const doctorName = 'أحمد';
    final casesProvider = context.watch<MedicalCasesProvider>();
    final quarantineProvider = context.watch<QuarantineProvider>();
    final activeReports = 1;
    final activeFieldCases = casesProvider.activeFieldCount;
    final hospitalCases = casesProvider.activeHospitalCount;
    final quarantineCases = quarantineProvider.activeCount;

    return Scaffold(
      backgroundColor: DoctorUi.background,
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
                // ─── Header Section — White Elegant Design ───
                _buildHeader(context, doctorName),
                const SizedBox(height: 20),

                // ─── Body content ───
                Padding(
                  padding: EdgeInsets.fromLTRB(16, 0, 16, bottomPad + 96),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // ─── Overview Section ───
                      _buildOverviewSection(
                        context,
                        reports: activeReports,
                        fieldCases: activeFieldCases,
                        hospitalCases: hospitalCases,
                        quarantine: quarantineCases,
                      ),
                      const SizedBox(height: 20),

                      // ─── Quick Actions Section ───
                      _buildQuickActionsSection(context),
                      const SizedBox(height: 20),

                      // ─── Alerts Section ───
                      _buildAlertsSection(context),
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

  // ─────────────────────────────────────────────
  // Renders the Light Modern Header
  // ─────────────────────────────────────────────
  Widget _buildHeader(BuildContext context, String name) {
    final topPad = MediaQuery.of(context).padding.top;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: Container(
        padding: EdgeInsets.fromLTRB(16, topPad + 16, 16, 18),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: const BorderRadius.vertical(
            bottom: Radius.circular(28),
          ),
          border: Border.all(
            color: DoctorUi.border,
            width: 1.5,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF1A3521).withValues(alpha: 0.03),
              blurRadius: 18,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // Avatar with initials
            Container(
              width: 46,
              height: 46,
              decoration: BoxDecoration(
                gradient: AppColors.primaryGradient,
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withValues(alpha: 0.25),
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
                    fontSize: 19,
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
                      color: DoctorUi.textPrimary,
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
                        color: AppColors.primary.withValues(alpha: 0.15),
                        width: 1,
                      ),
                    ),
                    child: Text(
                      'القطاع الطبي والرعاية الصحية',
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
            _buildNotificationBell(context),
          ],
        ),
      ),
    );
  }

  Widget _buildNotificationBell(BuildContext context) {
    return GestureDetector(
      onTap: () {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('قريبًا: شاشة الإشعارات الطبية')),
        );
      },
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: const Color(0xFFF4F7F4),
              shape: BoxShape.circle,
              border: Border.all(
                color: DoctorUi.border,
                width: 1.2,
              ),
            ),
            child: const Icon(
              Icons.notifications_outlined,
              color: DoctorUi.textPrimary,
              size: 20,
            ),
          ),
          Positioned(
            top: -1,
            left: -1,
            child: Container(
              width: 15,
              height: 15,
              decoration: BoxDecoration(
                color: AppColors.primary,
                shape: BoxShape.circle,
                border: Border.all(
                  color: Colors.white,
                  width: 2,
                ),
              ),
              child: Center(
                child: Container(
                  width: 5,
                  height: 5,
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
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
    required int reports,
    required int fieldCases,
    required int hospitalCases,
    required int quarantine,
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
                count: reports,
                title: 'البلاغات الصحية',
                subtitle: 'بلاغات جديدة للمراجعة',
                icon: Icons.assignment_outlined,
                onTap: () => context.go('/doctor/reports'),
              ),
              _StatTile(
                count: fieldCases,
                title: 'الحالات الميدانية',
                subtitle: 'حالات نشطة قيد المتابعة',
                icon: Icons.medical_services_outlined,
                onTap: () => context.go('/doctor/cases'),
              ),
              _StatTile(
                count: hospitalCases,
                title: 'حالات المستشفى',
                subtitle: 'حالات داخل المستشفى',
                icon: Icons.local_hospital_outlined,
                onTap: () => context.go('/doctor/cases'),
              ),
              _StatTile(
                count: quarantine,
                title: 'الحجر الصحي',
                subtitle: 'حيوانات تحت الملاحظة',
                icon: Icons.security_outlined,
                onTap: () => context.go('/doctor/quarantine'),
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
          // الزر الرئيسي لفتح حالة طبية ميدانية
          Material(
            color: AppColors.primary,
            borderRadius: BorderRadius.circular(14),
            child: InkWell(
              onTap: () => _openFieldCaseForm(context),
              borderRadius: BorderRadius.circular(14),
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
        ],
      ),
    );
  }

  // ─────────────────────────────────────────────
  // Renders the Alerts Section
  // ─────────────────────────────────────────────
  Widget _buildAlertsSection(BuildContext context) {
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

          // تنبيه 1: عاجل (تنبيه باللون الأحمر الفاتح الخفيف)
          _AlertTile(
            title: 'بلاغ صحي جديد عن الحيوان رقم A-102',
            subtitle: 'أُسند إليك للتشخيص والمتابعة الطبية الفورية.',
            isUrgent: true,
            onTap: () => context.go('/doctor/reports'),
          ),
          const SizedBox(height: 10),

          // تنبيه 2: عادي
          _AlertTile(
            title: 'حالة داخل المستشفى تحتاج متابعة عاجلة',
            subtitle: 'النمر البنغالي - قفص رقم 4 - فحص دوري مجدول اليوم.',
            isUrgent: false,
            onTap: () => context.go('/doctor/cases'),
          ),
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
        color: const Color(0xFFF4F7F4),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: const Color(0xFFE2EBE3),
          width: 1.2,
        ),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(16),
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
    final bgColor = isUrgent ? const Color(0xFFFFF5F5) : const Color(0xFFF4F7F4);
    final borderColor = isUrgent ? const Color(0xFFFFE3E3) : const Color(0xFFE2EBE3);
    final accentColor = isUrgent ? Colors.red.shade700 : AppColors.primary;

    return Container(
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: borderColor, width: 1.2),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(12),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            child: Row(
              children: [
                // Indicator point
                Container(
                  width: 6,
                  height: 6,
                  decoration: BoxDecoration(
                    color: accentColor,
                    shape: BoxShape.circle,
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        title,
                        style: GoogleFonts.cairo(
                          fontSize: 12.5,
                          fontWeight: FontWeight.w800,
                          color: DoctorUi.textPrimary,
                          height: 1.2,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        subtitle,
                        style: GoogleFonts.cairo(
                          fontSize: 10.5,
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
