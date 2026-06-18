import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_launcher.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_ui.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/domain/health_report.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/forms/health_report_form_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/health_report_detail_sheet.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/health_reports_provider.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/widgets/health_report_card.dart';
import 'package:tripolizoo/features/supervisor/supervisor_health_reports/presentation/widgets/health_report_status_filter.dart';
import 'package:tripolizoo/shared/constants/app_colors.dart';

class HealthReportsScreen extends StatefulWidget {
  const HealthReportsScreen({super.key});

  @override
  State<HealthReportsScreen> createState() => _HealthReportsScreenState();
}

class _HealthReportsScreenState extends State<HealthReportsScreen> {
  final _searchController = TextEditingController();
  HealthReportStatus? _statusFilter;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<HealthReportsProvider>().load(
            audience: HealthReportsAudience.supervisor,
          );
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _openSendForm() async {
    final saved = await HealthReportFormSheet.show(context);
    if (!mounted || saved != true) return;
    showSupervisorSuccessSnackBar(
      context,
      message: 'تم إرسال البلاغ للطبيب',
    );
  }

  void _openDetail(HealthReport report) {
    HealthReportDetailSheet.show(context, report);
  }

  @override
  Widget build(BuildContext context) {
    final topPad = MediaQuery.of(context).padding.top;
    final bottomPad = MediaQuery.of(context).padding.bottom;
    final query = _searchController.text;
    final reports = context.watch<HealthReportsProvider>().filtered(
          status: _statusFilter,
          query: query,
        );

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: SupervisorUi.background,
        body: SafeArea(
          top: false,
          bottom: false,
          child: CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              // ── Premium Header ──
              SliverToBoxAdapter(
                child: AnnotatedRegion<SystemUiOverlayStyle>(
                  value: SystemUiOverlayStyle.dark,
                  child: Container(
                    padding: EdgeInsets.fromLTRB(20, topPad + 18, 20, 20),
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.vertical(
                        bottom: Radius.circular(28),
                      ),
                      border: Border(
                        bottom: BorderSide(color: SupervisorUi.border, width: 1.5),
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Color(0x0D142E1B),
                          blurRadius: 16,
                          offset: Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        Text(
                          'البلاغات الصحية',
                          style: GoogleFonts.cairo(
                            fontSize: 22,
                            fontWeight: FontWeight.w900,
                            color: SupervisorUi.textPrimary,
                            height: 1.2,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'متابعة الحالة الصحية للحيوانات وإرسال بلاغات للأطباء',
                          style: GoogleFonts.cairo(
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: SupervisorUi.muted,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              // ── Search & Filter ──
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(20, 18, 20, 0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Container(
                        decoration: BoxDecoration(
                          boxShadow: SupervisorUi.softShadow,
                        ),
                        child: TextField(
                          controller: _searchController,
                          onChanged: (_) => setState(() {}),
                          style: GoogleFonts.cairo(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: SupervisorUi.textPrimary,
                          ),
                          decoration: InputDecoration(
                            hintText: 'ابحث برقم الحيوان أو رقم البلاغ',
                            hintStyle: GoogleFonts.cairo(
                              fontSize: 13.5,
                              fontWeight: FontWeight.w500,
                              color: SupervisorUi.muted,
                            ),
                            filled: true,
                            fillColor: Colors.white,
                            contentPadding: const EdgeInsets.symmetric(
                              horizontal: 14,
                              vertical: 14,
                            ),
                            prefixIcon: const Icon(
                              Icons.search_rounded,
                              color: SupervisorUi.muted,
                              size: 22,
                            ),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(14),
                              borderSide: const BorderSide(color: SupervisorUi.border, width: 1.5),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(14),
                              borderSide: const BorderSide(color: SupervisorUi.border, width: 1.5),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(14),
                              borderSide: const BorderSide(
                                color: AppColors.primary,
                                width: 1.5,
                              ),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 14),
                      HealthReportStatusFilter(
                        selected: _statusFilter,
                        onChanged: (v) => setState(() => _statusFilter = v),
                      ),
                      const SizedBox(height: 14),
                      _SendReportButton(onTap: _openSendForm),
                      const SizedBox(height: 20),
                    ],
                  ),
                ),
              ),

              // ── List or Empty State ──
              if (reports.isEmpty)
                SliverFillRemaining(
                  hasScrollBody: false,
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 32),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.assignment_turned_in_outlined,
                          size: 48,
                          color: SupervisorUi.muted.withValues(alpha: 0.5),
                        ),
                        const SizedBox(height: 12),
                        Text(
                          query.isNotEmpty || _statusFilter != null
                              ? 'لا توجد بلاغات مطابقة'
                              : 'لا توجد بلاغات صحية مسجلة',
                          textAlign: TextAlign.center,
                          style: GoogleFonts.cairo(
                            fontSize: 14,
                            fontWeight: FontWeight.w700,
                            color: SupervisorUi.muted,
                          ),
                        ),
                      ],
                    ),
                  ),
                )
              else
                SliverPadding(
                  padding: EdgeInsets.fromLTRB(20, 0, 20, bottomPad + 100),
                  sliver: SliverList.separated(
                    itemCount: reports.length,
                    separatorBuilder: (_, __) => const SizedBox(height: 12),
                    itemBuilder: (context, index) {
                      final report = reports[index];
                      return HealthReportCard(
                        report: report,
                        onTap: () => _openDetail(report),
                      );
                    },
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}

class _SendReportButton extends StatelessWidget {
  const _SendReportButton({required this.onTap});

  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.primaryDark,
      borderRadius: BorderRadius.circular(14),
      elevation: 0,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 15),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(
                Icons.send_rounded,
                color: Colors.white,
                size: 20,
              ),
              const SizedBox(width: 8),
              Text(
                'إرسال بلاغ صحي',
                style: GoogleFonts.cairo(
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                  color: Colors.white,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
