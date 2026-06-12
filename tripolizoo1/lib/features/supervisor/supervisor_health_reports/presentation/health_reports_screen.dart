import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:tripolizoo/features/supervisor/shared/supervisor_form_launcher.dart';
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
  static const _bg = Color(0xFFF5F5F5);
  static const _border = Color(0xFFE5E7EB);
  static const _muted = Color(0xFF6B7280);

  final _searchController = TextEditingController();
  HealthReportStatus? _statusFilter;

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

    return Scaffold(
      backgroundColor: _bg,
      body: SafeArea(
        top: false,
        bottom: false,
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(
              child: Padding(
                padding: EdgeInsets.fromLTRB(20, topPad + 20, 20, 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(
                      'البلاغات الصحية',
                      style: GoogleFonts.cairo(
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF1A1A1A),
                        height: 1.2,
                      ),
                    ),
                    const SizedBox(height: 16),
                    TextField(
                      controller: _searchController,
                      onChanged: (_) => setState(() {}),
                      style: GoogleFonts.cairo(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xFF1A1A1A),
                      ),
                      decoration: InputDecoration(
                        hintText: 'ابحث برقم الحيوان أو رقم البلاغ',
                        hintStyle: GoogleFonts.cairo(
                          fontSize: 13.5,
                          fontWeight: FontWeight.w500,
                          color: _muted,
                        ),
                        filled: true,
                        fillColor: Colors.white,
                        contentPadding: const EdgeInsets.symmetric(
                          horizontal: 14,
                          vertical: 14,
                        ),
                        prefixIcon: const Icon(
                          Icons.search_rounded,
                          color: _muted,
                          size: 22,
                        ),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(14),
                          borderSide: const BorderSide(color: _border),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(14),
                          borderSide: const BorderSide(color: _border),
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
                    const SizedBox(height: 14),
                    HealthReportStatusFilter(
                      selected: _statusFilter,
                      onChanged: (v) => setState(() => _statusFilter = v),
                    ),
                    const SizedBox(height: 16),
                    _SendReportButton(onTap: _openSendForm),
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
            if (reports.isEmpty)
              SliverFillRemaining(
                hasScrollBody: false,
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 32),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.assignment_outlined,
                        size: 48,
                        color: _muted.withValues(alpha: 0.5),
                      ),
                      const SizedBox(height: 12),
                      Text(
                        query.isNotEmpty || _statusFilter != null
                            ? 'لا توجد بلاغات مطابقة'
                            : 'لا توجد بلاغات بعد',
                        textAlign: TextAlign.center,
                        style: GoogleFonts.cairo(
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          color: _muted,
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
